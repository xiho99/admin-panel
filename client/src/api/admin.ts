import request from '/@/utils/request';

/**
 * 登录api接口集合
 * @method signIn 用户登录
 * @method signOut 用户退出登录
 */

export async function  adminList(params = null) {
	return await request({
		url: 'adminList',
		method: 'post',
		data: params,
	});
}

export async function  saveAdmin(params = null) {
	return await request({
		url: 'saveAdmin',
		method: 'post',
		data: params,
	});
}
export async function  deleteAdmin(params = null) {
	return await request({
		url: 'deleteAdmin',
		method: 'post',
		data: params,
	});
}