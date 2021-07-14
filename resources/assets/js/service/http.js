import axios from 'axios';
import Ls from './Ls';

// const API_URL =  `/api/v1/`;
// axios.defaults.baseURL = API_URL;
axios.defaults.headers.common.Accept = 'application/json';
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.interceptors.request.use(function (config) {
    const AUTH_TOKEN = Ls.get('auth.token');

    if (AUTH_TOKEN) {
        config.headers.common['Authorization'] = `Bearer ${AUTH_TOKEN}`
    }

    return config
}, function (error) {
    // Do something with request error
    return Promise.reject(error)
});

export default axios;
