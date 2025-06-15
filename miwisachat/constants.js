
export const IP_SERVER = "localhost"
export const PORT = process.env.port || 3977

//******* DATA BASE *************/
export const DB_USER = "carneiroluis2"
export const DB_PASSWORD = "tXHU7LnmfgxTZr5t"
export const DB_HOST = "chatapp.qupx2ln.mongodb.net"
export const DB_PARAMS = "retryWrites=true&w=majority&appName=chatapp"

// JWT
export const JWT_SECRET_KEY = "kjJKJSKjkjSAJjasdjHBujsedb879asdfjeawr122"

export const SERVER_ERROR = "Error del servidor"

// Función para enviar error de servidor
export const responseServerError = (res, errorDetail) => {
  console.log("error", errorDetail)
  return res.status(500).send({
    msg: SERVER_ERROR,
    // Si deseas enviar información adicional del error, se incluye aquí
    error: errorDetail,
  });
};