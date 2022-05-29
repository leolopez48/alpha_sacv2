import axios from "axios";

const reciboApi = axios.create({
    baseURL: "/api/web/receipt",
});

export default reciboApi;
