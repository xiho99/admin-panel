<template>
	<div class="system-role-dialog-container">
		<el-dialog :title="state.dialog.title" v-model="state.dialog.isShowDialog" width="769px">
			<el-form ref="roleDialogFormRef" :model="state.ruleForm" size="default" label-width="90px">
				<el-row :gutter="35">
					<el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24" class="mb20">
						<el-form-item label="用户名">
							<el-input v-model="state.ruleForm.name" placeholder="请输入用户名" clearable></el-input>
						</el-form-item>
					</el-col>
					<el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24" class="mb20">
						<el-form-item label="评论内容">
							<el-input v-model="state.ruleForm.content" placeholder="请输入评论内容" clearable></el-input>
						</el-form-item>
					</el-col>
					<el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24" class="mb20">
						<el-form-item label="审核">
							  <el-radio-group v-model="state.ruleForm.status">
								  <el-radio :label="0" size="large">未审核</el-radio>
								  <el-radio :label="1" size="large">通过</el-radio>
								  <el-radio :label="2" size="large">不通过</el-radio>
							  </el-radio-group>
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
import { saveComment } from '/@/api/api';
import { i18n } from '/@/i18n/index';
import { ElMessageBox, ElMessage } from 'element-plus';

// 定义子组件向父组件传值/事件
const emit = defineEmits(['refresh']);

// 定义变量内容
const roleDialogFormRef = ref();
const state = reactive({
	ruleForm: {},
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
const openDialog = (type: string, row: object = {},name:string = '') => {
		state.ruleForm = row;
	if (type === 'edit') {
		state.dialog.title = '修改评论';
		state.dialog.submitTxt = '修 改';
	} else {
		state.dialog.title = '添加'+name+'下级评论';
		state.dialog.submitTxt = '新 增';
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
	// 获取选中的菜单ID
	try{
		let row = await saveComment(state.ruleForm);
		if(row.code == 200){
			ElMessage.success('操作成功');
			emit('refresh');
			state.dialog.loading = false;
			closeDialog();
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
