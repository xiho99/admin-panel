import service from "/@/utils/request";

// 评论管理
export default function  useApi() {
	return {
		getConfiguration: (params?: object) => {
			return service({
				url: 'getConfiguration',
				method: 'get',
				params,
			});
		},
		addConfiguration: (params?: object) => {
			return service.post('saveConfiguration', params);
		},
		updateConfiguration: (params?: object) => {
			return service.post('updateConfiguration', params);
		},
		deleteConfiguration: async (param?: object) => {
			return await service.post('deleteConfiguration', param)
		},
		getAds: (params?: object) => {
			return service({
				url: 'getAdsBanner',
				method: 'get',
				params,
			});
		},
		addAds: (params?: object) => {
			return service.post('saveAds', params);
		},
		updateAds: (params?: object) => {
			return service.post('updateAds', params);
		},
		deleteAds: (params?: object) => {
			return service.post('deleteAds', params);
		},
		getMenuItem: (params?: object) => {
			return service({
				url: 'getMenuItem',
				method: 'get',
				params,
			});
		},
		addMenuItem: (params?: object) => {
			return service.post('saveMenuItem', params);
		},
		updateMenuItem: (params?: object) => {
			return service.post('updateMenuItem', params);
		},
		deleteMenuItem: (params?: object) => {
			return service.post('deleteMenuItem', params);
		},
		getCategory: (params?: object) => {
			return service({
				url: 'getCategory',
				method: 'get',
				params,
			});
		},
		addCategory: (params?: object) => {
			return service.post('saveCategory', params);
		},
		updateCategory: (params?: object) => {
			return service.post('updateCategory', params);
		},
		deleteCategory: (params?: object) => {
			return service.post('deleteCategory', params);
		},
		getGroupCategory: (params?: object) => {
			return service({
				url: 'getGroupCategory',
				method: 'get',
				params,
			});
		},
		addGroupCategory: (params?: object) => {
			return service.post('saveGroupCategory', params);
		},
		updateGroupCategory: (params?: object) => {
			return service.post('updateGroupCategory', params);
		},
		deleteGroupCategory: (params?: object) => {
			return service.post('deleteGroupCategory', params);
		},
	};
}