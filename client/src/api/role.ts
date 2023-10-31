import request from '/@/utils/request';

/**
 * 登录api接口集合
 * @method signIn 用户登录
 * @method signOut 用户退出登录
 */

export async function  roleList(params = null) {
	return await request({
		url: 'roleList',
		method: 'post',
		data: params,
	});
}

export async function  saveRole(params = null) {
	return await request({
		url: 'saveRole',
		method: 'post',
		data: params,
	});
}
export async function  deleteRole(params = null) {
	return await request({
		url: 'deleteRole',
		method: 'post',
		data: params,
	});
}
export async function  getAllRole(params = null) {
	return await request({
		url: 'getAllRole',
		method: 'post',
		data: params,
	});
}
