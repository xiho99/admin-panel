<template>
	<div class="system-role-dialog-container">
		<el-dialog :title="state.dialog.title" v-model="state.dialog.isShowDialog" width="769px">
			<el-form ref="roleDialogFormRef" :model="state.formData" size="default" label-width="90px">
				<el-row :gutter="35">
					<el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24" class="mb20">
						<el-form-item label="广告图">
							<uploadFile v-model:get-file-str="state.formData.img"/>
						</el-form-item>
					</el-col>
					<el-col :xs="24" :sm="8" :md="8" :lg="8" :xl="8" class="mb20">
						<el-form-item label="是否展示">
							<el-switch v-model="state.formData.status" inline-prompt active-text="是" inactive-text="否" :active-value="1" :inactive-value="0"></el-switch>
						</el-form-item>
					</el-col>
					<el-col :xs="24" :sm="16" :md="16" :lg="16" :xl="16" class="mb20">
						<el-form-item label="跳转地址">
							<el-input v-model="state.formData.link" placeholder="请输入外链地址" clearable></el-input>
						</el-form-item>
					</el-col>
					<el-col :xs="24" :sm="12" :md="12" :lg="12" :xl="12" class="mb20">
						<el-form-item label="开始时间">
							<el-date-picker v-model="state.formData.start_time" type="date" placeholder="请选择"
								class="w100"> </el-date-picker>
						</el-form-item>
					</el-col>
					<el-col :xs="24" :sm="12" :md="12" :lg="12" :xl="12" class="mb20">
						<el-form-item label="结束时间">
							<el-date-picker v-model="state.formData.end_time" type="date" placeholder="请选择"
								class="w100"> </el-date-picker>
						</el-form-item>
					</el-col>
					<el-col :xs="24" :sm="16" :md="16" :lg="16" :xl="16" class="mb20">
						<el-form-item label="排序">
							<el-input-number v-model="state.formData.sort" :min="0" :max="999" controls-position="right" placeholder="请输入排序" class="w100" />
						</el-form-item>
					</el-col>
					<el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24" class="mb20">
						<el-form-item label="备注">
							<el-input v-model="state.formData.describe" type="textarea" placeholder="请输入角色描述" maxlength="150"></el-input>
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
import { reactive, ref,nextTick,defineAsyncComponent } from 'vue';
import { saveAd } from '/@/api/api';
import { ElMessage } from 'element-plus';
import { Plus } from '@element-plus/icons-vue'
const uploadFile = defineAsyncComponent(() => import('/@/components/uploadFile/index.vue'));


// 定义子组件向父组件传值/事件
const emit = defineEmits(['refresh']);

// 定义变量内容
const roleDialogFormRef = ref();
const state = reactive({
	defaultForm: {
		img:'',
		status:0,
		link:'',
		start_time:null,
		end_time:null,
		sort:1,
		describe:'',
	},
	formData:{},
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
const initData = () => {
	state.formData = {};
}
// 打开弹窗
const openDialog = (type: string, row: object) => {
	if (type === 'edit') {
		state.formData = row;
		state.dialog.title = '修改广告图';
		state.dialog.submitTxt = '修 改';
	} else {
		state.formData = state.defaultForm;
		initData();
		state.dialog.title = '新增广告图';
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
	closeDialog();
	// 获取选中的菜单ID
	try{
		let row = await saveAd(state.formData);
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
	// if (state.dialog.type === 'add') { }
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

<style scoped>
.avatar-uploader .avatar {
  width: 178px;
  height: 178px;
  display: block;
}
</style>

<style>
.avatar-uploader .el-upload {
  
  border-radius: 6px;
  cursor: pointer;
  position: relative;
  overflow: hidden;
  transition: var(--el-transition-duration-fast);
}

.avatar-uploader .el-upload:hover {
  border-color:#ccc;
}

.el-icon.avatar-uploader-icon {
  font-size: 28px;
  color: #8c939d;
  width: 178px;
  height: 178px;
  text-align: center;
}
.maxFile .el-upload.el-upload--picture-card{
	display: none;
}
</style>