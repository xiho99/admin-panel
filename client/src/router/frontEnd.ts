import { RouteRecordRaw } from 'vue-router';
import { storeToRefs } from 'pinia';
import { formatTwoStageRoutes, formatFlatteningRoutes, router } from '/@/router/index';
import { dynamicRoutes, notFoundAndNoPower } from '/@/router/route';
import pinia from '/@/stores/index';
import { Session } from '/@/utils/storage';
import { useUserInfo } from '/@/stores/userInfo';
import { useTagsViewRoutes } from '/@/stores/tagsViewRoutes';
import { useRoutesList } from '/@/stores/routesList';
import { NextLoading } from '/@/utils/loading';

export async function initFrontEndControlRoutes() {
	// 界面 loading 动画开始执行
	if (window.nextLoading === undefined) NextLoading.start();
	// 无 token 停止执行下一步
	if (!Session.get('token')) return false;
	// 触发初始化用户信息 pinia
	await useUserInfo(pinia).setUserInfos();
	// 无登录权限时，添加判断
	if (useUserInfo().userInfos.roles.length <= 0) return Promise.resolve(true);
	// 添加动态路由
	await setAddRoute();
	setFilterMenuAndCacheTagsViewRoutes();
}

export async function setAddRoute() {
	await setFilterRouteEnd().forEach((route: RouteRecordRaw) => {
		router.addRoute(route);
	});
}

export async function frontEndsResetRoute() {
	await setFilterRouteEnd().forEach((route: RouteRecordRaw) => {
		const routeName: any = route.name;
		router.hasRoute(routeName) && router.removeRoute(routeName);
	});
}

export function setFilterRouteEnd() {
	let filterRouteEnd: any = formatTwoStageRoutes(formatFlatteningRoutes(dynamicRoutes));
	// notFoundAndNoPower 防止 404、401 不在 layout 布局中，不设置的话，404、401 界面将全屏显示
	// 关联问题 No match found for location with path 'xxx'
	filterRouteEnd[0].children = [...setFilterRoute(filterRouteEnd[0].children), ...notFoundAndNoPower];
	return filterRouteEnd;
}

export function setFilterRoute(chil: any) {
	const stores = useUserInfo(pinia);
	const { userInfos } = storeToRefs(stores);
	let filterRoute: any = [];
	chil.forEach((route: any) => {
		if (route.meta.roles) {
			route.meta.roles.forEach((metaRoles: any) => {
				userInfos.value.roles.forEach((roles: any) => {
					if (metaRoles === roles) filterRoute.push({ ...route });
				});
			});
		}
	});
	return filterRoute;
}

export function setCacheTagsViewRoutes() {
	// 获取有权限的路由，否则 tagsView、菜单搜索中无权限的路由也将显示
	const stores = useUserInfo(pinia);
	const storesTagsView = useTagsViewRoutes(pinia);
	const { userInfos } = storeToRefs(stores);
	let rolesRoutes = setFilterHasRolesMenu(dynamicRoutes, userInfos.value.roles);
	// 添加到 pinia setTagsViewRoutes 中
	storesTagsView.setTagsViewRoutes(formatTwoStageRoutes(formatFlatteningRoutes(rolesRoutes))[0].children);
}

export function setFilterMenuAndCacheTagsViewRoutes() {
	const stores = useUserInfo(pinia);
	const storesRoutesList = useRoutesList(pinia);
	const { userInfos } = storeToRefs(stores);
	storesRoutesList.setRoutesList(setFilterHasRolesMenu(dynamicRoutes[0].children, userInfos.value.roles));
	setCacheTagsViewRoutes();
}


export function hasRoles(roles: any, route: any) {
	if (route.meta && route.meta.roles) return roles.some((role: any) => route.meta.roles.includes(role));
	else return true;
}

/**
 * 获取当前用户权限标识去比对路由表，设置递归过滤有权限的路由
 * @param routes 当前路由 children
 * @param roles 用户权限标识，在 userInfos（用户信息）的 roles（登录页登录时缓存到浏览器）数组
 * @returns 返回有权限的路由数组 `meta.roles` 中控制
 */
export function setFilterHasRolesMenu(routes: any, roles: any) {
	const menu: any = [];
	routes.forEach((route: any) => {
		const item = { ...route };
		if (hasRoles(roles, item)) {
			if (item.children) item.children = setFilterHasRolesMenu(item.children, roles);
			menu.push(item);
		}
	});
	return menu;
}
