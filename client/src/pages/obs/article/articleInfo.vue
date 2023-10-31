<template>
	<div class="system-role-dialog-container">
		<el-dialog :title="state.dialog.title" v-model="state.dialog.isShowDialog" width="769px">
			<el-form ref="roleDialogFormRef" :model="state.formData" size="default" label-width="90px">
				<el-row :gutter="35">
					<el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24" class="mb20">
						<el-form-item label="标题">
							<div>{{state.dataInfo.title}}</div>
						</el-form-item>
					</el-col>
					<el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24" class="mb20">
						<el-form-item label="关键词">
							<div>#{{state.dataInfo.tagNames?.join('#')}}</div>
						</el-form-item>
					</el-col>
					<el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24" class="mb20">
						<el-form-item label="导航分类">
							<div>{{state.dataInfo.navName}}</div>
						</el-form-item>
					</el-col>
					<el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24" class="mb20">
						<el-form-item label="描述">
							<div>{{state.dataInfo.describe}}</div>
						</el-form-item>
					</el-col>
					<el-col :xs="24" :sm="12" :md="12" :lg="12" :xl="12" class="mb20">
						<el-form-item label="作者">
							<div>{{state.dataInfo.ownerName}}</div>
						</el-form-item>
					</el-col>
					<el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24" class="mb20">
						<el-form-item label="文章内容">
							<div class="w100" v-html="state.dataInfo.content"></div>
						</el-form-item>
					</el-col>
					<el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24" class="mb20">
						<el-form-item label="是否发布">
							{{['否','是'][state.dataInfo.status]}}
						</el-form-item>
					</el-col>
				</el-row>
			</el-form>
			<template #footer>
				<span class="dialog-footer">
					<el-button @click="onCancel" size="default"  :loading="state.dialog.loading">关 闭</el-button>
				</span>
			</template>
		</el-dialog>
	</div>
</template>

<script setup lang="ts" name="systemRoleDialog">
import { reactive, ref,nextTick } from 'vue';
import { getArticleInfo } from '/@/api/api';
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
	dataInfo:{},
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
const openDialog = (id: number) => {
	state.dialog.isShowDialog = true;
		state.dialog.title = '文章详情';
		getInfo(id);
};
const getInfo = async (id) => {
	state.dialog.loading = true;
	let row = await getArticleInfo({id:id});
	state.dataInfo = row.data;
	state.dialog.loading = false;
}
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
		await saveComment(state.ruleForm);
		ElMessage.success('操作成功');
	}catch(e){
		//TODO handle the exception
	}
	emit('refresh');
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
<style lang="scss">
	
	div[data-w-e-type='video']{
		width: 100%;
		display: flex;
		align-items: center;
		justify-content: center;
		video{
			width: 100%;
		}
	}
</style>