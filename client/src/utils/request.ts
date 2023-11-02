import axios, { AxiosInstance } from 'axios';
import { ElMessage, ElMessageBox } from 'element-plus';
import { Session } from '/@/utils/storage';
import qs from 'qs';

// 配置新建一个 axios 实例
const service: AxiosInstance = axios.create({
	baseURL: import.meta.env.VITE_API_URL,
	timeout: 50000,
	headers: { 'Content-Type': 'application/json' },
	paramsSerializer: {
		serialize(params) {
			return qs.stringify(params, { allowDots: true });
		},
	},
});

// 添加请求拦截器
service.interceptors.request.use(
	(config) => {
		// 在发送请求之前做些什么 token
		if (Session.get('token')) {
			config.headers!['Authorization'] = `${Session.get('token')}`;
		}
		return config;
	},
	(error) => {
		// 对请求错误做些什么
		return Promise.reject(error);
	}
);

// 添加响应拦截器
service.interceptors.response.use(
	(response) => {
		// 对响应数据做点什么
		const res = response.data;
		if (res.code && res.code !== 200) {
			// `token` 过期或者账号已在别处登录
			if (res.code === -10004 || res.code === -20004) {
				Session.clear(); // 清除浏览器全部临时缓存
				ElMessageBox.alert('You have been logged out, please log in again', 'hint', {})
					.then(() => {window.location.href = '/';})
					.catch(() => {});
			}else if(res.code === -10001){
					ElMessage.error(res.message);
			}
			return Promise.reject(service.interceptors.response);
		} else {
			return res;
		}
	},
	(error) => {
		// 对响应错误做点什么
		if (error.message.indexOf('timeout') != -1) {
			ElMessage.error('network timeout');
		} else if (error.message == 'Network Error') {
			ElMessage.error('Network connection error');
		} else {
			if (error.response.data) ElMessage.error(error.response.statusText);
			else ElMessage.error('Interface path not found');
		}
		return Promise.reject(error);
	}
);

// 导出 axios 实例
export default service;
