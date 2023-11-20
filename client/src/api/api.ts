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
		getMenuIcon: (params?: object) => {
			return service({
				url: 'getMenuIcon',
				method: 'get',
				params,
			});
		},
		addMenuIcon: (params?: object) => {
			return service.post('saveMenuIcon', params);
		},
		deleteMenuIcon: (params?: object) => {
			return service.post('deleteMenuIcon', params);
		},
		getCategory: (params?: object) => {
			return service({
				url: 'getCategory',
				method: 'get',
				params,
			});
		},
		getMenuButton: (params?: object) => {
			return service({
				url: 'getMenuButton',
				method: 'get',
				params,
			});
		},
		addMenuButton: (params?: object) => {
			return service.post('saveMenuButton', params);
		},
		deleteMenuButton: (params?: object) => {
			return service.post('deleteMenuButton', params);
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