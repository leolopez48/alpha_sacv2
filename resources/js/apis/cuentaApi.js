import axios from "axios";

const cuentaApi = axios.create({
    baseURL: "/api/web/account",
});

export default cuentaApi;
