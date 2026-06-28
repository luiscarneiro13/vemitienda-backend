
export const IP_SERVER = "localhost"
export const SERVER_URL = "https://vemitienda.com.ve"
export const PORT = process.env.PORT || 3977

//******* DATA BASE *************/
export const DB_USER = process.env.DB_USER
export const DB_PASSWORD = process.env.DB_PASSWORD
export const DB_HOST = process.env.DB_HOST || "chatapp.qupx2ln.mongodb.net"
export const DB_PARAMS = "retryWrites=true&w=majority&appName=chatapp"

// JWT
export const JWT_SECRET_KEY = process.env.JWT_SECRET_KEY

export const SERVER_ERROR = "Error del servidor"

// Función para enviar error de servidor
export const responseServerError = (res, errorDetail) => {
  console.log("error", errorDetail)
  return res.status(500).send({
    msg: SERVER_ERROR,
    error: errorDetail?.message || String(errorDetail),
  });
};