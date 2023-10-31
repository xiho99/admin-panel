<template>
	<div class="system-user-dialog-container">
		<el-dialog :title="state.dialog.title" v-model="state.dialog.isShowDialog" width="769px">
			<el-form ref="userDialogFormRef" :model="state.ruleForm" size="default" label-width="90px">
				<el-row :gutter="35">
					<el-col :xs="24" :sm="12" :md="12" :lg="12" :xl="12" class="mb20">
						<el-form-item label="账户名称">
							<el-input v-model="state.ruleForm.username" placeholder="请输入账户名称" clearable></el-input>
						</el-form-item>
					</el-col>
					<el-col :xs="24" :sm="12" :md="12" :lg="12" :xl="12" class="mb20">
						<el-form-item label="用户昵称">
							<el-input v-model="state.ruleForm.nickname" placeholder="请输入用户昵称" clearable></el-input>
						</el-form-item>
					</el-col>
					<el-col :xs="24" :sm="12" :md="12" :lg="12" :xl="12" class="mb20">
						<el-form-item label="关联角色">
							<el-select v-model="state.ruleForm.role_ids" multiple placeholder="请选择" clearable
								class="w100">
								<el-option v-for="ite in state.roleList" :label="ite.roleName" :value="ite.id+''"></el-option>
							</el-select>
						</el-form-item>
					</el-col>
					<el-col :xs="24" :sm="12" :md="12" :lg="12" :xl="12" class="mb20">
						<el-form-item label="账户密码">
							<el-input v-model="state.ruleForm.password" placeholder="请输入" type="password"
								clearable></el-input>
						</el-form-item>
					</el-col>
					<el-col :xs="24" :sm="12" :md="12" :lg="12" :xl="12" class="mb20">
						<el-form-item label="账户过期">
							<el-date-picker v-model="state.ruleForm.overdueTime" type="date" placeholder="请选择"
								class="w100"> </el-date-picker>
						</el-form-item>
					</el-col>
					<el-col :xs="24" :sm="12" :md="12" :lg="12" :xl="12" class="mb20">
						<el-form-item label="用户状态">
							<el-switch v-model="state.ruleForm.status" inline-prompt active-text="启"
								inactive-text="禁"></el-switch>
						</el-form-item>
					</el-col>
					<el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24" class="mb20">
						<el-form-item label="用户描述">
							<el-input v-model="state.ruleForm.describe" type="textarea" placeholder="请输入用户描述"
								maxlength="150"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
			</el-form>
			<template #footer>
				<span class="dialog-footer">
					<el-button @click="onCancel" size="default" :loading="state.dialog.loading">取 消</el-button>
					<el-button type="primary" @click="onSubmit" size="default"
						:loading="state.dialog.loading">{{ state.dialog.submitTxt }}</el-button>
				</span>
			</template>
		</el-dialog>
	</div>
</template>

<script setup lang="ts" name="systemUserDialog">
	import { reactive, ref } from 'vue';
	import { saveAdmin } from '/@/api/admin';
	import { getAllRole } from '/@/api/role';
	import { ElMessageBox, ElMessage } from 'element-plus';

	// 定义子组件向父组件传值/事件
	const emit = defineEmits(['refresh']);

	// 定义变量内容
	const userDialogFormRef = ref();
	const state = reactive({
		ruleForm: {
			userName: '', // 账户名称
			userNickname: '', // 用户昵称
			role_ids: [], // 关联角色
			password: '', // 账户密码
			overdueTime: '', // 账户过期
			status: true, // 用户状态
			describe: '', // 用户描述
		},
		dialog: {
			isShowDialog: false,
			type: '',
			title: '',
			submitTxt: '',
			loading: false,
		},
		roleList: {},
	});

	// 打开弹窗
	const openDialog = async (type : string, row : RowUserType) => {
		let rows = await getAllRole();
		state.roleList = rows.data || {};
		if (type === 'edit') {
			state.ruleForm = JSON.parse(JSON.stringify(row));
			state.ruleForm.role_ids = state.ruleForm.role_ids?.split(',');
			state.dialog.title = '修改用户';
			state.dialog.submitTxt = '修 改';
		} else {
			state.dialog.title = '新增用户';
			state.dialog.submitTxt = '新 增';
			// 清空表单，此项需加表单验证才能使用
			// nextTick(() => {
			// 	userDialogFormRef.value.resetFields();
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
		emit('refresh');
		try{
		let form = JSON.parse(JSON.stringify(state.ruleForm));
		form.role_ids = form.role_ids?.join(',');
		await saveAdmin(form);
		ElMessage.success('操作成功');
		}catch(e){
			//TODO handle the exception
		}
		state.dialog.loading = false;
		// if (state.dialog.type === 'add') { }
	};
	// 暴露变量
	defineExpose({
		openDialog,
	});
</script>