import { createRouter, createWebHashHistory } from 'vue-router';
import NProgress from 'nprogress';
import 'nprogress/nprogress.css';
import pinia from '/@/stores/index';
import { storeToRefs } from 'pinia';
import { useKeepALiveNames } from '/@/stores/keepAliveNames';
import { useRoutesList } from '/@/stores/routesList';
import { useThemeConfig } from '/@/stores/themeConfig';
import { Session } from '/@/utils/storage';
import { staticRoutes, notFoundAndNoPower } from '/@/router/route';
import { initFrontEndControlRoutes } from '/@/router/frontEnd';
import { initBackEndControlRoutes } from '/@/router/backEnd';

const storesThemeConfig = useThemeConfig(pinia);
const { themeConfig } = storeToRefs(storesThemeConfig);
const { isRequestRoutes } = themeConfig.value;
export const router = createRouter({
	history: createWebHashHistory(),
	routes: [...notFoundAndNoPower, ...staticRoutes],
});
export function formatFlatteningRoutes(arr: any) {
	if (arr.length <= 0) return false;
	for (let i = 0; i < arr.length; i++) {
		if (arr[i].children) {
			arr = arr.slice(0, i + 1).concat(arr[i].children, arr.slice(i + 1));
		}
	}
	return arr;
}
export function formatTwoStageRoutes(arr: any) {
	if (arr.length <= 0) return false;
	const newArr: any = [];
	const cacheList: Array<string> = [];
	arr.forEach((v: any) => {
		if (v.path === '/') {
			newArr.push({ component: v.component, name: v.name, path: v.path, redirect: v.redirect, meta: v.meta, children: [] });
		} else {
			if (v.path.indexOf('/:') > -1) {
				v.meta['isDynamic'] = true;
				v.meta['isDynamicPath'] = v.path;
			}
			newArr[0].children.push({ ...v });
			if (newArr[0].meta.isKeepAlive && v.meta.isKeepAlive) {
				cacheList.push(v.name);
				const stores = useKeepALiveNames(pinia);
				stores.setCacheKeepAlive(cacheList);
			}
		}
	});
	return newArr;
}

// 路由加载前
router.beforeEach(async (to, from, next) => {
	NProgress.configure({ showSpinner: false });
	if (to.meta.title) NProgress.start();
	const token = Session.get('token');
	if (to.path === '/login' && !token) {
		next();
		NProgress.done();
	} else {
		if (!token) {
			next(`/login?redirect=${to.path}&params=${JSON.stringify(to.query ? to.query : to.params)}`);
			Session.clear();
			NProgress.done();
		} else if (token && to.path === '/login') {
			next('/home');
			NProgress.done();
		} else {
			const storesRoutesList = useRoutesList(pinia);
			const { routesList } = storeToRefs(storesRoutesList);
			if (routesList.value.length === 0) {
				if (isRequestRoutes) {
					// 后端控制路由：路由数据初始化，防止刷新时丢失
					await initBackEndControlRoutes();
					next({ path: to.path, query: to.query });
				} else {
					// https://gitee.com/lyt-top/vue-next-admin/issues/I5F1HP
					await initFrontEndControlRoutes();
					next({ path: to.path, query: to.query });
				}
			} else {
				next();
			}
		}
	}
});

// 路由加载后
router.afterEach(() => {
	NProgress.done();
});

// 导出路由
export default router;
