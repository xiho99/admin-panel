<template>
	<div class="system-menu-dialog-container">
		<el-dialog @close="initRuleFrom" destroy-on-close :title="state.dialog.title" v-model="state.dialog.isShowDialog">
			<el-form ref="menuDialogFormRef" :model="state.ruleForm" size="default" label-width="120px">
				<el-row :gutter="35">
					<el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24" class="mb20">
						<el-form-item :label="$t('message.menuCategory')">
							<el-cascader
								:options="state.menuData"
								:props="{ checkStrictly: true, value: 'path', label: 'title' }"
								:placeholder="$t('message.pleaseSelectTheUpperLevelMenu')"
								clearable
								class="w100"
								v-model="state.ruleForm.menuSuperiorPath"
							>
								<template #default="{ node, data }">
									<span>{{ data.title }}</span>
									<span v-if="!node.isLeaf"> ({{ data.children.length }}) </span>
								</template>
							</el-cascader>
						</el-form-item>
					</el-col>
					<el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24" class="mb20">
						<el-form-item :label="$t('message.menuType')">
							<el-radio-group v-model="state.ruleForm.menuType">
								<el-radio label="menu">{{ $t('message.menu') }}</el-radio>
								<el-radio label="btn">{{ $t('message.button') }}</el-radio>
							</el-radio-group>
						</el-form-item>
					</el-col>
					<el-col :xs="24" :sm="12" :md="12" :lg="12" :xl="12" class="mb20">
						<el-form-item :label="$t('message.menuName')">
							<el-input v-model="state.ruleForm.meta.title" placeholder="Format：message.router.xxx" clearable></el-input>
						</el-form-item>
					</el-col>
					<template v-if="state.ruleForm.menuType === 'menu'">
						<el-col :xs="24" :sm="12" :md="12" :lg="12" :xl="12" class="mb20">
							<el-form-item :label="$t('message.routeName')">
								<el-input v-model="state.ruleForm.name" :placeholder="$t('message.nameValueInRoute')" clearable></el-input>
							</el-form-item>
						</el-col>
						<el-col :xs="24" :sm="12" :md="12" :lg="12" :xl="12" class="mb20">
							<el-form-item :label="$t('message.routePath')">
								<el-input v-model="state.ruleForm.path" placeholder="path value in route" clearable></el-input>
							</el-form-item>
						</el-col>
						<el-col :xs="24" :sm="12" :md="12" :lg="12" :xl="12" class="mb20">
							<el-form-item :label="$t('message.redirect')">
								<el-input v-model="state.ruleForm.redirect" :placeholder="$t('message.pleaseEnterRouteRedirect')" clearable></el-input>
							</el-form-item>
						</el-col>
						<el-col :xs="24" :sm="12" :md="12" :lg="12" :xl="12" class="mb20">
							<el-form-item :label="$t('message.menuIcon')">
								<IconSelector :placeholder="$t('message.pleaseEnterMenuIcon')" v-model="state.ruleForm.meta.icon" />
							</el-form-item>
						</el-col>
						<el-col :xs="24" :sm="12" :md="12" :lg="12" :xl="12" class="mb20">
							<el-form-item :label="$t('message.linkAddress')">
								<el-input
									v-model="state.ruleForm.meta.isLink"
									:placeholder="$t('message.linkAddressEmbedded')"
									clearable
									:disabled="!state.ruleForm.isLink"
								>
								</el-input>
							</el-form-item>
						</el-col>
					</template>
					<template v-if="state.ruleForm.menuType === 'btn'">
						<el-col :xs="24" :sm="12" :md="12" :lg="12" :xl="12" class="mb20">
							<el-form-item label="权限标识">
								<el-input v-model="state.ruleForm.btnPower" placeholder="请输入权限标识" clearable></el-input>
							</el-form-item>
						</el-col>
					</template>
					<el-col :xs="24" :sm="12" :md="12" :lg="12" :xl="12" class="mb20">
						<el-form-item :label="$t('message.menuSort')">
							<el-input-number v-model="state.ruleForm.menuSort" controls-position="right" placeholder="请输入排序" class="w100" />
						</el-form-item>
					</el-col>
					<template v-if="state.ruleForm.menuType === 'menu'">
						<el-col :xs="24" :sm="12" :md="12" :lg="12" :xl="12" class="mb20">
							<el-form-item :label="$t('message.menuType')">
								<el-radio-group v-model="state.ruleForm.meta.isHide">
									<el-radio :label="true">{{ $t('message.hide') }}</el-radio>
									<el-radio :label="false">{{ $t('message.notHide') }}</el-radio>
								</el-radio-group>
							</el-form-item>
						</el-col>
						<el-col :xs="24" :sm="12" :md="12" :lg="12" :xl="12" class="mb20">
							<el-form-item :label="$t('message.pageCache')">
								<el-radio-group v-model="state.ruleForm.meta.isKeepAlive">
									<el-radio :label="true">{{ $t('message.cache') }}</el-radio>
									<el-radio :label="false">{{ $t('message.doNotCache') }}</el-radio>
								</el-radio-group>
							</el-form-item>
						</el-col>
						<el-col :xs="24" :sm="12" :md="12" :lg="12" :xl="12" class="mb20">
							<el-form-item :label="$t('message.isItFix')">
								<el-radio-group v-model="state.ruleForm.meta.isAffix">
									<el-radio :label="true">{{ $t('message.fix') }}</el-radio>
									<el-radio :label="false">{{ $t('message.notFix') }}</el-radio>
								</el-radio-group>
							</el-form-item>
						</el-col>
						<el-col :xs="24" :sm="12" :md="12" :lg="12" :xl="12" class="mb20">
							<el-form-item :label="$t('message.whetherToExternalLink')">
								<el-radio-group v-model="state.ruleForm.isLink" :disabled="state.ruleForm.meta.isIframe">
									<el-radio :label="true">{{ $t('message.yes') }}</el-radio>
									<el-radio :label="false">{{ $t('message.no') }}</el-radio>
								</el-radio-group>
							</el-form-item>
						</el-col>
						<el-col :xs="24" :sm="12" :md="12" :lg="12" :xl="12" class="mb20">
							<el-form-item :label="$t('message.whetherToEmbed')">
								<el-radio-group v-model="state.ruleForm.meta.isIframe" @change="onSelectIframeChange">
									<el-radio :label="true">{{ $t('message.yes') }}</el-radio>
									<el-radio :label="false">{{ $t('message.no') }}</el-radio>
								</el-radio-group>
							</el-form-item>
						</el-col>
					</template>
				</el-row>
			</el-form>
			<template #footer>
				<span class="dialog-footer">
					<el-button @click="onCancel" size="default" :loading="state.dialog.loading">{{ $t('message.cancel') }}</el-button>
					<el-button type="primary" @click="onSubmit" :loading="state.dialog.loading" size="default">{{ state.dialog.submitTxt }}</el-button>
				</span>
			</template>
		</el-dialog>
	</div>
</template>

<script setup lang="ts" name="systemMenuDialog">
import { defineAsyncComponent, reactive, onMounted, ref, watch } from 'vue';
import { storeToRefs } from 'pinia';
import { useRoutesList } from '/@/stores/routesList';
import { i18n } from '/@/i18n';
import { useMenuApi } from '/@/api/menu';
import { ElMessage } from 'element-plus';
import { useI18n } from "vue-i18n";
const menuApi = useMenuApi();
const { t } = useI18n();

// 定义子组件向父组件传值/事件
const emit = defineEmits(['refresh']);

// 引入组件
const IconSelector = defineAsyncComponent(() => import('/@/components/iconSelector/index.vue'));

// 定义变量内容
const menuDialogFormRef = ref();
const stores = useRoutesList();
const { routesList } = storeToRefs(stores);
const state = reactive({
	// 参数请参考 `/src/router/route.ts` 中的 `dynamicRoutes` 路由菜单格式
	ruleForm: {
    menuSuperior: null,
		menuSuperiorPath: [], // 上级菜单
		menuType: 'menu', // 菜单类型
		name: '', // 路由名称
		isLink: false, // 是否外链
		menuSort: 0, // 菜单排序
		path: '', // 路由路径
		redirect: '', // 路由重定向，有子集 children 时
		meta: {
			title: '', // 菜单名称
			icon: '', // 菜单图标
			isHide: false, // 是否隐藏
			isKeepAlive: true, // 是否缓存
			isAffix: false, // 是否固定
			isLink: '', // 外链/内嵌时链接地址（http:xxx.com），开启外链条件，`1、isLink: 链接地址不为空`
			isIframe: false, // 是否内嵌，开启条件，`1、isIframe:true 2、isLink：链接地址不为空`
		},
		btnPower: '', // 菜单类型为按钮时，权限标识
	},
	menuData: [] as RouteItems, // 上级菜单数据
	dialog: {
		loading: false,
		isShowDialog: false,
		type: '',
		title: '',
		submitTxt: '',
	},
});
// 获取 pinia 中的路由
const getMenuData = (routes: RouteItems) => {
	const arr: RouteItems = [];
	routes.map((val: RouteItem) => {
		val['title'] = i18n.global.t(val.meta?.title as string);
		arr.push({ ...val });
		if (val.children) getMenuData(val.children);
	});
	return arr;
};
const initRuleFrom  = () =>{
	state.ruleForm = {
      menuSuperior: null,
			menuSuperiorPath: [], // 上级菜单
      btnPower: "",
			menuType: 'menu', // 菜单类型
			name: '', // 路由名称
			isLink: false, // 是否外链
			menuSort: 0, // 菜单排序
			path: '', // 路由路径
			redirect: '', // 路由重定向，有子集 children 时
			meta: {
				title: '', // 菜单名称
				icon: '', // 菜单图标
				isHide: false, // 是否隐藏
				isKeepAlive: true, // 是否缓存
				isAffix: false, // 是否固定
				isLink: '', // 外链/内嵌时链接地址（http:xxx.com），开启外链条件，`1、isLink: 链接地址不为空`
				isIframe: false, // 是否内嵌，开启条件，`1、isIframe:true 2、isLink：链接地址不为空`
			}
	}
};
// 打开弹窗
const openDialog = (type: string, row?: any) => {
	if (type === 'edit') {
		// 模拟数据，实际请走接口
		row.menuType = 'menu';
		// row.menuSort = Math.floor(Math.random() * 100);
		state.ruleForm = JSON.parse(JSON.stringify(row));
		state.dialog.title = t('message.modifyMenu');
		state.dialog.submitTxt = t('message.submit');
	} else {
		state.dialog.title = t('message.addMenu');
		state.dialog.submitTxt = t('message.submit');
	}
	state.dialog.type = type;
	state.dialog.isShowDialog = true;
};
// 关闭弹窗
const closeDialog = () => {
	state.dialog.isShowDialog = false;
};
// 是否内嵌下拉改变
const onSelectIframeChange = () => {
	state.ruleForm.isLink = !!state.ruleForm.meta.isIframe;
};
// 取消
const onCancel = () => {
	closeDialog();
};
// 提交
const onSubmit = async () => {
	// state.dialog.loading = true;
	try{
		let data = JSON.parse(JSON.stringify(state.ruleForm));
		data.meta = JSON.stringify(data.meta);
		await menuApi.saveMenu(data);
		ElMessage.success(t('message.success'));
    initRuleFrom();
	}catch(e){
		//TODO handle the exception
	}
	state.dialog.loading = false;
	closeDialog(); // 关闭弹窗
	emit('refresh');
	// if (state.dialog.type === 'add') { }
	// setBackEndControlRefreshRoutes() // 刷新菜单，未进行后端接口测试
};
watch(() => state.ruleForm.menuSuperiorPath, (value) => {
  const findItem = state.menuData.find((item) => item.path === value[0]);
  if (findItem) {
    state.ruleForm.menuSuperior= findItem.id;
  }
});
// 页面加载时
onMounted(() => {
	state.menuData = getMenuData(routesList.value);
});
// 暴露变量
defineExpose({
	openDialog,
});
</script>
