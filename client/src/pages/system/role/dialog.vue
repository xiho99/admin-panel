<template>
	<div class="system-role-dialog-container">
		<el-dialog @close="resetFields" :title="state.dialog.title" v-model="state.dialog.isShowDialog" >
			<el-form ref="roleDialogFormRef" :model="state.ruleForm" size="default" label-width="140px">
				<el-row :gutter="35">
					<el-col :xs="24" :sm="12" :md="12" :lg="12" :xl="12" class="mb20">
						<el-form-item :label="$t('message.roleName')">
							<el-input v-model="state.ruleForm.roleName" :placeholder="$t('message.pleaseEnterRoleName')" clearable></el-input>
						</el-form-item>
					</el-col>
					<el-col :xs="24" :sm="12" :md="12" :lg="12" :xl="12" class="mb20">
						<el-form-item :label="$t('message.roleId')">
							<template #label>
								<el-tooltip effect="dark" content="用于 `router/route.ts` meta.roles" placement="top-start">
									<span>{{ $t('message.roleId') }}</span>
								</el-tooltip>
							</template>
							<el-input v-model="state.ruleForm.roleSign" :placeholder="$t('message.pleaseEnterRoleId')" clearable></el-input>
						</el-form-item>
					</el-col>
					<el-col :xs="24" :sm="12" :md="12" :lg="12" :xl="12" class="mb20">
						<el-form-item :label="$t('message.sort')">
							<el-input-number v-model="state.ruleForm.sort" :min="0" :max="999" controls-position="right" placeholder="请输入排序" class="w100" />
						</el-form-item>
					</el-col>
					<el-col :xs="24" :sm="12" :md="12" :lg="12" :xl="12" class="mb20">
						<el-form-item :label="$t('message.characterStatus')">
							<el-switch v-model="state.ruleForm.status" inline-prompt :active-text="$t('message.start')" :inactive-text="$t('message.ban')" :active-value="1" :inactive-value="0"></el-switch>
						</el-form-item>
					</el-col>
					<el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24" class="mb20">
						<el-form-item :label="$t('message.roleDescription')">
							<el-input v-model="state.ruleForm.describe" type="textarea" :placeholder="$t('message.pleaseEnterRoleDescription')" maxlength="150"></el-input>
						</el-form-item>
					</el-col>
					<el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24" class="mb20">
						<el-form-item :label="$t('message.menuPermission')">
							<el-tree node-key="id" ref="treeRef" :data="state.menuData" :props="state.menuProps" show-checkbox class="menu-data-tree" />
						</el-form-item>
					</el-col>
				</el-row>
			</el-form>
			<template #footer>
				<span class="dialog-footer">
					<el-button @click="onCancel" size="default"  :loading="state.dialog.loading">取 消</el-button>
					<el-button type="primary" @click="onSubmit" size="default" :loading="state.dialog.loading">{{ state.dialog.submitTxt }}</el-button>
				</span>
			</template>
		</el-dialog>
	</div>
</template>

<script setup lang="ts" name="systemRoleDialog">
import { reactive, ref,nextTick } from 'vue';
import { saveRole } from '/@/api/role';
import { useRoutesList } from '/@/stores/routesList';
import { i18n } from '/@/i18n';
import { storeToRefs } from 'pinia';
import { ElMessage } from 'element-plus';

// 定义子组件向父组件传值/事件
const emit = defineEmits(['refresh']);

// 定义变量内容
const roleDialogFormRef = ref();
const state = reactive({
	ruleForm: {
    roleName: '', // 角色名称
		roleSign: '', // 角色标识
		sort: 0, // 排序
		status: 1, // 角色状态
		describe: '', // 角色描述
		menu_ids:'',
	},
	menuData: [] as TreeType[],
	menuProps: {
		children: 'children',
		label: 'title',
	},
	dialog: {
		isShowDialog: false,
		type: '',
		title: '',
		submitTxt: '',
		loading:false
	},
});
const resetFields = () => {
  state.ruleForm = {
    roleName: '',
    roleSign: '',
    sort: 0,
    status: 1,
    describe: '',
    menu_ids:'',
  };
}
const treeRef = ref();
const getCheckedKeys = () => {
  return treeRef.value!.getCheckedKeys(false);
}
const stores = useRoutesList();
const { routesList } = storeToRefs(stores);
// 打开弹窗
const openDialog = (type: string, row: any) => {
	if (type === 'edit') {
    Object.assign(state.ruleForm, row);
		// state.ruleForm = row;
		state.dialog.title = i18n.global.t('message.editRole');
		nextTick(() => {
			treeRef.value!.setCheckedKeys(state.ruleForm.menu_ids.split(','), false);
		});
		state.dialog.submitTxt = i18n.global.t('message.submit');

	} else {
		state.dialog.title = i18n.global.t('message.addNewRole');
		state.dialog.submitTxt = i18n.global.t('message.submit');
		// 清空表单，此项需加表单验证才能使用
		// nextTick(() => {
		// 	roleDialogFormRef.value.resetFields();
		// });
	}
	state.menuData = getMenuData(routesList.value);
	state.dialog.isShowDialog = true;
};
// 关闭弹窗
const closeDialog = () => {
	state.dialog.isShowDialog = false;
};
// 取消
const onCancel = () => {
	closeDialog();
};
// 提交
const onSubmit = async () => {
	state.dialog.loading = true;
	closeDialog();
	// 获取选中的菜单ID
	state.ruleForm.menu_ids = getCheckedKeys()?.join(',');
	try{
		await saveRole(state.ruleForm);
		ElMessage.success('操作成功');
    resetFields();
	}catch(e){
		//TODO handle the exception
	}
	emit('refresh');
	state.dialog.loading = false;
	// if (state.dialog.type === 'add') { }
};
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

// 暴露变量
defineExpose({
	openDialog,
});
</script>

<style scoped lang="scss">
.system-role-dialog-container {
	.menu-data-tree {
		width: 100%;
		border: 1px solid var(--el-border-color);
		border-radius: var(--el-input-border-radius, var(--el-border-radius-base));
		padding: 5px;
	}
}
</style>
