import { RouteRecordRaw } from 'vue-router';
import pinia from '/@/stores/index';
import { useUserInfo } from '/@/stores/userInfo';
import { useRequestOldRoutes } from '/@/stores/requestOldRoutes';
import { Session } from '/@/utils/storage';
import { NextLoading } from '/@/utils/loading';
import { dynamicRoutes, notFoundAndNoPower } from '/@/router/route';
import { formatTwoStageRoutes, formatFlatteningRoutes, router } from '/@/router/index';
import { useRoutesList } from '/@/stores/routesList';
import { useTagsViewRoutes } from '/@/stores/tagsViewRoutes';
import { useMenuApi } from '/@/api/menu/index';

// 引入 api 请求接口
const menuApi = useMenuApi();
const layoutModules : any = import.meta.glob('../layout/routerView/*.{vue,tsx}');
const viewsModules : any = import.meta.glob('../pages/**/*.{vue,tsx}');
const dynamicViewsModules : Record<string, Function> = Object.assign({}, { ...layoutModules }, { ...viewsModules });

export async function initBackEndControlRoutes() {
	// 界面 loading 动画开始执行
	if (window.nextLoading === undefined) NextLoading.start();
	// 无 token 停止执行下一步
	if (!Session.get('token')) return false;
	await useUserInfo().setUserInfos();
	// 获取路由菜单数据
	const res = await getBackEndControlRoutes();
	// 无登录权限时，添加判断
	if (res.data.length <= 0) return Promise.resolve(true);
	res.data = generateMenuTree(res.data);
	// 存储接口原始路由（未处理component），根据需求选择使用
	await useRequestOldRoutes().setRequestOldRoutes(JSON.parse(JSON.stringify(res.data)));
	// 处理路由（component），替换 dynamicRoutes（/@/router/route）第一个顶级 children 的路由
	dynamicRoutes[0].children = await backEndComponent(res.data);
	// if(import.meta.env.MODE == 'development') { // @ts-ignore
	// 	dynamicRoutes[0].children.push(sysRoute);
	// }
	// 添加动态路由
	await setAddRoute();
	await setFilterMenuAndCacheTagsViewRoutes();
}

export async function setFilterMenuAndCacheTagsViewRoutes() {
	const storesRoutesList = useRoutesList(pinia);
	await storesRoutesList.setRoutesList(dynamicRoutes[0].children as any);
	setCacheTagsViewRoutes();
}

export function setCacheTagsViewRoutes() {
	const storesTagsView = useTagsViewRoutes(pinia);
	storesTagsView.setTagsViewRoutes(formatTwoStageRoutes(formatFlatteningRoutes(dynamicRoutes))[0].children);
}

export function setFilterRouteEnd() {
	let filterRouteEnd : any = formatTwoStageRoutes(formatFlatteningRoutes(dynamicRoutes));
	// notFoundAndNoPower 防止 404、401 不在 layout 布局中，不设置的话，404、401 界面将全屏显示
	// 关联问题 No match found for location with path 'xxx'
	filterRouteEnd[0].children = [...filterRouteEnd[0].children, ...notFoundAndNoPower];
	return filterRouteEnd;
}

export async function setAddRoute() {
	await setFilterRouteEnd().forEach((route : RouteRecordRaw) => {
		router.addRoute(route);
	});
}

export async function getBackEndControlRoutes() {
	return await menuApi.getAdminMenu();
}
export function generateMenuTree(menuData: any) {
	const menuMap: any = {}; // 用于构建菜单映射，以便查找父级菜单
	const menuTree: any = []; // 最终生成的菜单树
	// 首先，将所有菜单项添加到菜单映射中
	menuData.forEach((menu: any) => {
		menuMap[menu.name] = menu;
		menu.children = []; // 初始化子菜单
	});// 然后，遍历菜单项，将子菜单插入到其父级的 children 中
	menuData.forEach((menu: any) => {
		if (menu.menuSuperior && menuMap[menu.menuSuperior]) {
			menuMap[menu.menuSuperior].children.push(menu);
		} else {
			menuTree.push(menu); // 如果没有父级，将其作为根级菜单
		}
	});
	return menuTree;
}
export async function setBackEndControlRefreshRoutes() {
	await getBackEndControlRoutes();
}

export function backEndComponent(routes : any) {
	if (!routes) return;
	let rou = routes.map((item : any) => {
		if (item.component) item.component = dynamicImport(dynamicViewsModules, item.component as string);
		item.children && backEndComponent(item.children);
		return item;
	});
	return  rou;
}

export function dynamicImport(dynamicViewsModules : Record<string, Function>, component : string) {
	const keys = Object.keys(dynamicViewsModules);
	const matchKeys = keys.filter((key) => {
		const k = key.replace(/..\/pages|../, '');
		return k.startsWith(`${component}`) || k.startsWith(`/${component}`);
	});
	if (matchKeys?.length === 1) {
		const matchKey = matchKeys[0];
		return dynamicViewsModules[matchKey];
	}
	if (matchKeys?.length > 1) {
		return false;
	}
}