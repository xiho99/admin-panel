import request from '/@/utils/request';

// 评论管理
export async function  saveComment(params = null) {
	return await request({
		url: 'saveComment',
		method: 'post',
		data: params,
	});
}
export async function  deleteComment(params = null) {
	return await request({
		url: 'deleteComment',
		method: 'post',
		data: params,
	});
}
export async function  getCommentList(params = null) {
	return await request({
		url: 'getCommentList',
		method: 'post',
		data: params,
	});
}
// 关键词位管理
export async function  saveTag(params = null) {
	return await request({
		url: 'saveTag',
		method: 'post',
		data: params,
	});
}
export async function  deleteTag(params = null) {
	return await request({
		url: 'deleteTag',
		method: 'post',
		data: params,
	});
}
export async function  getTagList(params = null) {
	return await request({
		url: 'getTagList',
		method: 'post',
		data: params,
	});
}
// 广告位管理
export async function  deleteAd(params = null) {
	return await request({
		url: 'deleteAd',
		method: 'post',
		data: params,
	});
}
export async function  saveAd(params = null) {
	return await request({
		url: 'saveAd',
		method: 'post',
		data: params,
	});
}
export async function  getAdList(params = null) {
	return await request({
		url: 'getAdList',
		method: 'post',
		data: params,
	});
}
export async function  changeAdStatus(params = null) {
	return await request({
		url: 'changeAdStatus',
		method: 'post',
		data: params,
	});
}
// 导航管理
export async function  deleteNav(params = null) {
	return await request({
		url: 'deleteNav',
		method: 'post',
		data: params,
	});
}
export async function  saveNav(params = null) {
	return await request({
		url: 'saveNav',
		method: 'post',
		data: params,
	});
}
export async function  getNavList(params = null) {
	return await request({
		url: 'getNavList',
		method: 'post',
		data: params,
	});
}
export async function  changeNavStatus(params = null) {
	return await request({
		url: 'changeNavStatus',
		method: 'post',
		data: params,
	});
}
// 搜索词管理
export async function  getSearchList(params = null) {
	return await request({
		url: 'getSearchList',
		method: 'post',
		data: params,
	});
}
export async function  saveSearch(params = null) {
	return await request({
		url: 'saveSearch',
		method: 'post',
		data: params,
	});
}
export async function  deleteSearch(params = null) {
	return await request({
		url: 'deleteSearch',
		method: 'post',
		data: params,
	});
}
// 搜索词管理
export async function  getArticleList(params = null) {
	return await request({
		url: 'getArticleList',
		method: 'post',
		data: params,
	});
}
// 搜索词管理
export async function  getArticleInfo(params = null) {
	return await request({
		url: 'getArticleInfo',
		method: 'post',
		data: params,
	});
}
export async function  saveArticle(params = null) {
	return await request({
		url: 'saveArticle',
		method: 'post',
		data: params,
	});
}
export async function  deleteArticle(params = null) {
	return await request({
		url: 'deleteArticle',
		method: 'post',
		data: params,
	});
}
export async function  changeArticleStatus(params = null) {
	return await request({
		url: 'changeArticleStatus',
		method: 'post',
		data: params,
	});
}
export async function  saveConfigurationInfo(params = null) {
	return await request({
		url: 'saveConfigurationInfo',
		method: 'post',
		data: params,
	});
}
export async function  saveConfigurationGroupInfo(params = null) {
	return await request({
		url: 'saveConfigurationGroupInfo',
		method: 'post',
		data: params,
	});
}
export async function  getConfigurationList(params = null) {
	return await request({
		url: 'getConfigurationList',
		method: 'post',
		data: params,
	});
}