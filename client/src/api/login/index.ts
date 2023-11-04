import request from '/@/utils/request';


export function login(params?: object) {
	return request({
		url: 'login',
		method: 'post',
		data: params,
	});
}

export function logout(params?: object) {
	return request({
		url: 'logout',
		method: 'post',
		data: params,
	});
}