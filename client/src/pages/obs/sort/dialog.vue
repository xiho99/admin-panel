<template>
	<div class="system-role-dialog-container">
		<el-dialog :title="state.dialog.title" v-model="state.dialog.isShowDialog" width="769px">
			<el-form ref="roleDialogFormRef" :model="state.ruleForm" size="default" label-width="90px">
				<el-row :gutter="35">
					<el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24" class="mb20">
						<el-form-item label="导航名称">
							<el-input v-model="state.ruleForm.name" placeholder="请输入导航名称" clearable></el-input>
						</el-form-item>
					</el-col>
					<el-col :xs="24" :sm="8" :md="8" :lg="8" :xl="8" class="mb20">
						<el-form-item label="是否展示">
							<el-switch v-model="state.ruleForm.status" inline-prompt active-text="是" inactive-text="否" :active-value="1" :inactive-value="0"></el-switch>
						</el-form-item>
					</el-col>
					<el-col :xs="24" :sm="16" :md="16" :lg="16" :xl="16" class="mb20">
						<el-form-item label="排序">
							<el-input-number v-model="state.ruleForm.sort" :min="0" :max="999" controls-position="right" placeholder="请输入排序" class="w100" />
						</el-form-item>
					</el-col>
					<el-col :xs="24" :sm="8" :md="8" :lg="8" :xl="8" class="mb20">
						<el-form-item label="是否是外链">
							<el-switch v-model="state.ruleForm.is_link" inline-prompt active-text="是" inactive-text="否" :active-value="1" :inactive-value="0"></el-switch>
						</el-form-item>
					</el-col>
					<el-col :xs="24" :sm="16" :md="16" :lg="16" :xl="16" class="mb20">
						<el-form-item label="外链地址">
							<el-input v-model="state.ruleForm.link" placeholder="请输入外链地址" clearable></el-input>
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
import { saveNav } from '/@/api/api';
import { ElMessageBox, ElMessage } from 'element-plus';

// 定义子组件向父组件传值/事件
const emit = defineEmits(['refresh']);

// 定义变量内容
const roleDialogFormRef = ref();
const state = reactive({
	ruleForm: {
		name: '', // 角色名称
		sort: 0, // 排序
		status: 1, // 状态
		is_link: '', // 角色描述
		link:'',
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
const treeRef = ref();
const getCheckedKeys = () => {
  return treeRef.value!.getCheckedKeys(false);
}
// 打开弹窗
const openDialog = (type: string, row: object) => {
	if (type === 'edit') {
		state.ruleForm = row;
		state.dialog.title = '修改导航';
		state.dialog.submitTxt = '修 改';
		
	} else {
		state.dialog.title = '新增导航';
		state.dialog.submitTxt = '新 增';
		// 清空表单，此项需加表单验证才能使用
		// nextTick(() => {
		// 	roleDialogFormRef.value.resetFields();
		// });
	}
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
	try{
		let row = await saveNav(state.ruleForm);
		if(row.code == 200){
			ElMessage.success('操作成功');
			emit('refresh');
			state.dialog.loading = false;
			return ;
		}
		ElMessage.error('操作异常');
	}catch(e){
		//TODO handle the exception
	}
	state.dialog.loading = false;
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
