import request from '/@/utils/request';

/**
 * 登录api接口集合
 * @method signIn 用户登录
 * @method signOut 用户退出登录
 */

export async function  login(params = object) {
	return await request({
		url: 'auth/login',
		method: 'post',
		data: params,
	});
}

export async function  logout(params = object) {
	return await request({
		url: 'auth/logout',
		method: 'post',
		data: params,
	});
}