import request from '/@/utils/request';

// 评论管理
export default function  useApi() {
	return {
		getAdminMenu: (params?: object) => {
			return request({
				url: '/getMenu',
				method: 'get',
				params,
			});
		},
	};
}