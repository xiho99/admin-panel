<template>
	<div class="system-user-dialog-container">
		<el-dialog @close="resetFields" destroy-on-close :title="state.dialog.title" v-model="state.dialog.isShowDialog" >
			<el-form ref="userDialogFormRef" :model="state.ruleForm" size="default" label-width="140px">
				<el-row :gutter="35">
					<el-col :xs="24" :sm="12" :md="12" :lg="12" :xl="12" class="mb20">
						<el-form-item :label="$t('message.table.accountName')">
							<el-input v-model="state.ruleForm.userName" :placeholder="$t('message.pleaseEnterAccountName')" clearable></el-input>
						</el-form-item>
					</el-col>
					<el-col :xs="24" :sm="12" :md="12" :lg="12" :xl="12" class="mb20">
						<el-form-item :label="$t('message.userNickname')">
							<el-input v-model="state.ruleForm.nickname" :placeholder="$t('message.pleaseEnterUserNickname')" clearable></el-input>
						</el-form-item>
					</el-col>
					<el-col :xs="24" :sm="12" :md="12" :lg="12" :xl="12" class="mb20">
						<el-form-item :label="$t('message.associatedRoles')">
							<el-select v-model="state.ruleForm.role_ids" multiple :placeholder="$t('message.pleaseChoose')" clearable
								class="w100">
								<el-option v-for="(ite, index) in state.roleList" :key="index" :label="$t(`message.${ite.roleName}`)" :value="ite.id+''"/>
							</el-select>
						</el-form-item>
					</el-col>
					<el-col :xs="24" :sm="12" :md="12" :lg="12" :xl="12" class="mb20">
						<el-form-item :label="$t('message.accountPassword')">
							<el-input v-model="state.ruleForm.password" :placeholder="$t('message.pleaseEnter')" type="password"
								clearable></el-input>
						</el-form-item>
					</el-col>
					<el-col :xs="24" :sm="12" :md="12" :lg="12" :xl="12" class="mb20">
						<el-form-item :label="$t('message.accountExpired')">
							<el-date-picker v-model="state.ruleForm.overdueTime" type="date" :placeholder="$t('message.pleaseChoose')"
								class="w100"> </el-date-picker>
						</el-form-item>
					</el-col>
					<el-col :xs="24" :sm="12" :md="12" :lg="12" :xl="12" class="mb20">
						<el-form-item :label="$t('message.userStatus')">
							<el-switch v-model="state.ruleForm.status" inline-prompt :active-text="$t('message.start')"
								:inactive-text="$t('message.ban')"></el-switch>
						</el-form-item>
					</el-col>
					<el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24" class="mb20">
						<el-form-item :label="$t('message.userDescription')">
							<el-input v-model="state.ruleForm.describe" type="textarea" :placeholder="$t('message.pleaseEnterUserDescription')"
								maxlength="150"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
			</el-form>
			<template #footer>
				<span class="dialog-footer">
					<el-button @click="onCancel" size="default" :loading="state.dialog.loading">{{ $t('message.cancel') }}</el-button>
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
	import { ElMessage } from 'element-plus';
  import { useI18n } from "vue-i18n";

	// 定义子组件向父组件传值/事件
	const emit = defineEmits(['refresh']);
  const { t } = useI18n();
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
  const resetFields = () => {
    state.ruleForm.userName = '';
    state.ruleForm.userNickname = '';
    state.ruleForm.role_ids = [];
    state.ruleForm.password = '';
    state.ruleForm.overdueTime = '';
    state.ruleForm.status = true;
    state.ruleForm.describe = '';
  };

	// 打开弹窗
	const openDialog = async (type : string, row?: RowUserType) => {
		const rows = await getAllRole();
		Object.assign(state.roleList, rows.data || {});
		if (type === 'edit') {
			state.ruleForm = JSON.parse(JSON.stringify(row));
			state.ruleForm.role_ids = state.ruleForm.role_ids?.split(',');
			state.dialog.title = t('message.editUser');
			state.dialog.submitTxt = t('message.submit');
		} else {
			state.dialog.title = t('message.newUser');
			state.dialog.submitTxt = t('message.submit');
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
      resetFields();
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