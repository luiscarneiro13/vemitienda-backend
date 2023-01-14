<?php

namespace App\Traits;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

trait ApiResponser
{
    public function successResponse($params = [])
    {
        $status    = $params['status'] ?? 200;
        $message = $params['message'] ?? 'Operación exitosa';
        $success = isset($params['success']) ? $params['success'] : true;
        $data    = $params['data'] ?? null;

        $responseAttributes = [
            'status'    => $status,
            'message' => $message,
            'success' => $success,
        ];

        if (is_array($data)) {
            $datos['data'] = $data;
            $response = array_merge($responseAttributes, $datos);
        } else {
            $responseAttributes['data'] = $data;
            $response = $responseAttributes;
        }

        return response()->json($response, $status);
    }

    public function errorResponse($params = [])
    {
        $params['success'] = false;
        return $this->successResponse($params);
    }

    public function errorValidation($params = [])
    {
        $params['status'] = 400;
        $params['errors'] = $params;
        return $params;

    }

    public function unauthorizedResponse()
    {
        return $this->errorResponse(['status' => 403, 'message' => 'No autorizado.']);
    }

    public function handleExceptions(Exception $exception)
    {
        if ($exception instanceof AuthenticationException) {
            return $this->errorResponse(['status' => 401, 'message' => 'No autenticado.']);
        }

        if ($exception instanceof AuthorizationException) {
            return $this->unauthorizedResponse();
        }

        if ($exception instanceof HttpException) {
            return $this->errorResponse(['status' => $exception->getStatusCode(), 'message' => $exception->getMessage()]);
        }

        if ($exception instanceof ModelNotFoundException) {
            $modelName = strtolower(class_basename($exception->getModel()));
            return $this->errorResponse(['message' => "No existe un recurso de tipo {$modelName} con el identificador indicado."]);
        }

        if ($exception instanceof NotFoundHttpException) {
            return $this->errorResponse('URL no encontrada.', 404);
        }

        if ($exception instanceof QueryException) {
            $errorCode = $exception->errorInfo[1];

            if ($errorCode == 1451) {
                return $this->errorResponse('No se puede eliminar este recurso de forma permanente. Está relacionado con cualquier otro recurso.', 409);
            }
        }

        if ($exception instanceof ValidationException) {
            $errors = $exception->validator->errors()->getMessages();
            return $this->errorResponse(['status' => 400, 'message' => 'Error de validación.', 'data' => $errors]);
        }

        return $this->errorResponse(['status' => 500, 'message' => 'Error interno del servidor.']);
    }
}
