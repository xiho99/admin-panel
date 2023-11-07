import request from '/@/utils/request';
import service from "/@/utils/request";

export function login(params?: object) {
	return service.post('auth/login', params)
}

export function logout(params?: object) {
	return request({
		url: 'logout',
		method: 'auth/post',
		data: params,
	});
}