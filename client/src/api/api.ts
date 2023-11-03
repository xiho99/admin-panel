import request from '/@/utils/request';
import service from "/@/utils/request";

// 评论管理
export default function  useApi() {
	return {
		getConfiguration: (params?: object) => {
			return request({
				url: '/getConfiguration',
				method: 'get',
				params,
			});
		},
		addConfiguration: (params?: object) => {
			return service.post('/saveConfiguration', params);
		},
		updateConfiguration: (params?: object) => {
			return service.post('/updateConfiguration', params);
		},
		deleteConfiguration: async (param?: object) => {
			return await service.post('/deleteConfiguration', param)
		},
		getAds: (params?: object) => {
			return request({
				url: '/getAds',
				method: 'get',
				params,
			});
		},
		addAds: (params?: object) => {
			return service.post('/saveAds', params);
		},
		updateAds: (params?: object) => {
			return service.post('/updateAds', params);
		},
		deleteAds: (params?: object) => {
			return service.post('/deleteAds', params);
		},
		getMenuItem: (params?: object) => {
			return request({
				url: '/getMenuItem',
				method: 'get',
				params,
			});
		},
		addMenuItem: (params?: object) => {
			return service.post('/saveMenuItem', params);
		},
		updateMenuItem: (params?: object) => {
			return service.post('/updateMenuItem', params);
		},
		deleteMenuItem: (params?: object) => {
			return service.post('/deleteMenuItem', params);
		},
	};
}