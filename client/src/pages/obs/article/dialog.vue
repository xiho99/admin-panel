<template>
	<div class="system-role-dialog-container">
		<el-dialog :title="state.dialog.title" v-model="state.dialog.isShowDialog" width="769px" :close-on-press-escape="false">
			<el-form ref="roleDialogFormRef" :model="state.formData" size="default" label-width="90px">
				<el-row :gutter="35">
					<el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24" class="mb20">
						<el-form-item label="标题">
							<el-input v-model="state.formData.title" placeholder="请输入评论内容" clearable></el-input>
						</el-form-item>
					</el-col>
					<el-col :xs="24" :sm="12" :md="12" :lg="12" :xl="12" class="mb20">
						<el-form-item label="作者">
							<el-select v-loading.lock="state.dataLoading" v-model="state.formData.owner_id" placeholder="请选择" clearable
								class="w100">
								<el-option v-for="ite in state.adminList" :label="ite.nickname" :value="ite.id"></el-option>
							</el-select>
						</el-form-item>
					</el-col>
					<el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24" class="mb20">
						<el-form-item label="广告图">
							<uploadFile v-model:get-file-str="state.formData.img"/>
						</el-form-item>
					</el-col>
					<el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24" class="mb20">
						<el-form-item label="关键词">
							<el-select v-loading.lock="state.dataLoading" @change="tagChange" v-model="state.formData.tag_id" default-first-option filterable allow-create multiple placeholder="请选择" clearable
								class="w100">
								<el-option v-for="ite in state.tagList" :label="ite.name" :value="ite.id+''"></el-option>
							</el-select>
						</el-form-item>
					</el-col>
					<el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24" class="mb20">
						<el-form-item label="导航分类">
							<el-select v-loading.lock="state.dataLoading" v-model="state.formData.nav_group" default-first-option filterable placeholder="请选择导航分类" clearable
								class="w100">
								<el-option v-for="ite in state.navList" :label="ite.name" :value="ite.id+''"></el-option>
							</el-select>
						</el-form-item>
					</el-col>
					<el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24" class="mb20">
						<el-form-item label="描述">
							<el-input v-model="state.formData.describe" placeholder="请输入文章描述" clearable></el-input>
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
					<el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24" class="mb20">
						<el-form-item label="切换编辑模式">
							<el-switch v-model="state.htmlType" inline-prompt active-text="文本" inactive-text="html" :active-value="1" :inactive-value="0"></el-switch>
						</el-form-item>
					</el-col>
					<el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24" class="mb20">
						<el-form-item label="文章内容" v-if="state.htmlType == 1">
							<Editor v-model:get-html="state.formData.content" v-model:get-text="state.textVal"  />
						</el-form-item>
						<el-form-item label="文章内容" v-else>
							<el-input v-model="state.formData.content" :rows="12" type="textarea" placeholder="请输入用户描述"></el-input>
						</el-form-item>
					</el-col>
					<el-col :xs="24" :sm="12" :md="12" :lg="12" :xl="12" class="mb20">
						<el-form-item label="是否发布">
							<el-switch v-model="state.formData.status" inline-prompt active-text="是" inactive-text="否" :active-value="1" :inactive-value="0"></el-switch>
						</el-form-item>
					</el-col>
					<el-col :xs="24" :sm="12" :md="12" :lg="12" :xl="12" class="mb20">
						<el-form-item label="是否置顶">
							<el-switch v-model="state.formData.pinned" inline-prompt active-text="是" inactive-text="否" :active-value="1" :inactive-value="0"></el-switch>
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
import { ElMessage } from 'element-plus';
import { adminList } from '/@/api/admin';
import { saveArticle,getNavList,getTagList } from '/@/api/api';

// 定义子组件向父组件传值/事件
const emit = defineEmits(['refresh']);
// 引入组件
const Editor = defineAsyncComponent(() => import('/@/components/editor/index.vue'));
const uploadFile = defineAsyncComponent(() => import('/@/components/uploadFile/index.vue'));

// 定义变量内容
const roleDialogFormRef = ref();
const state = reactive({
	htmlType:1,
	defaultForm:{
		start_time:'',
		end_time:'',
		title:'',
		tag_id:[],
		describe:'',
		owner_id:'',
		nav_group:'',
		content:'',
		status:0,
		pinned:0
	},
	formData: {},
	menuData: [] as TreeType[],
	content:'',
	textVal:'',
	adminList:[],
	navList:[],
	dataLoading:false,
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
const tagChange = (e) => {
}
// 初始化表格数据
const getOrList = async () => {
	state.dataLoading = true;
	const data = [];
	
	// 使用Promise.all同时触发两个请求
	const [row, row1,row2] = await Promise.all([
	  adminList({ pageSize: 200 }),
	  getTagList(),
	  getNavList({ pageSize: 200 }),
	]);
	
	state.adminList = row.data?.list || [];
	state.tagList = row1.data || [];
	state.navList = row2.data?.list || [];
	state.dataLoading = false;
};
const treeRef = ref();
const getCheckedKeys = () => {
  return treeRef.value!.getCheckedKeys(false);
}
// 打开弹窗
const openDialog = (type: string, row: object = {},name:string = '') => {
	if(!state.adminList.length) getOrList();
	if (type === 'edit') {
		state.formData = JSON.parse(JSON.stringify(row));
		state.formData.tag_id = state.formData?.tag_id?.split(',');
		state.dialog.title = '编辑文章';
		state.dialog.submitTxt = '修 改';
	} else {
		state.formData = state.defaultForm;
		state.dialog.title = '新增文章';
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
		let row = await saveArticle(state.formData);
		if(row.code == 200){
			ElMessage.success('操作成功');
			emit('refresh');
			state.dialog.loading = false;
			closeDialog();
			return;
		}
		ElMessage.error('操作异常');
	}catch(e){
		//TODO handle the exception
	}
	closeDialog();
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
