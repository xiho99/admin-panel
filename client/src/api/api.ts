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
		}
	};
}