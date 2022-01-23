import '@/bootstrap';
import { Message } from 'element-ui';
// import { isLogged, setLogged } from '@/utils/auth';

// Create axios instance
console.log('process.env.MIX_BASE_API', process.env.MIX_BASE_API);
const service = window.axios.create({
  baseURL: process.env.MIX_BASE_API,
  timeout: 60000 * 5, // Request timeout
});

// Request intercepter
service.interceptors.request.use(
  config => {
    // const token = isLogged();
    // if (token) {
    //   config.headers['Authorization'] = 'Bearer ' + isLogged(); // Set JWT token
    // }
    return config;
  },
  error => {
    // Do something with request error
    console.log(error); // for debug
    Promise.reject(error);
  }
);

// response pre-processing
service.interceptors.response.use(
  response => {
    // if (response.headers.authorization) {
    //   setLogged(response.headers.authorization);
    //   response.data.token = response.headers.authorization;
    // }

    return response.data;
  },
  error => {
    let message = error.message;
    if (error.response.data && error.response.data.errors) {
      message = error.response.data.errors;
    } else if (error.response.data && error.response.data.error) {
      message = error.response.data.error;
    }
    if (message === 'Request failed with status code 401') {
      message = 'Нет доступа, пожалуста авторизуйтесь <br><b><a href=\'/login\'>Перейти</a></b>';
      Message({
        message: message,
        type: 'error',
        dangerouslyUseHTMLString: true,
        duration: 30 * 1000,
        center: true,
      });
    } else {
      Message({
        message: message,
        type: 'error',
        duration: 5 * 1000,
      });
    }
    return Promise.reject(error);
  }
);

export default service;
