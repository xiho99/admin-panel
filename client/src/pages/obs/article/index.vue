<template>
	<div class="table-demo-container layout-padding w100">
		<div class="table-demo-padding layout-padding-view layout-padding-auto">
			<div style="display: flex; justify-content: space-between;" class="mr10">
				<div style="flex:1;">
					<TableSearch :search="state.tableData.search" @search="getTableData" />
				</div>
				<el-button size="default" type="primary" class="mt10" @click="onOpenAddRole"> 新增 </el-button>
			</div>
			<Table
				ref="tableRef"
				v-bind="state.tableData"
				class="table-demo"
				@onSystem="onSystem"
				@pageChange="onHandlePageChange"
			/>
		</div>
		<RoleDialog ref="roleDialogRef" @refresh="getTableData" />
		<RoleCommentDialog ref="roleCommentDialogRef" @refresh="getTableData" />
		<el-dialog v-model="state.commentIndexDialog" width="80%" style="height: 80%;    background: #fff;" show-close>
			<commentIndex :artcleId="state.artcleId" />
		</el-dialog>
	</div>
</template>

<script setup lang="ts" name="/article">
	import { defineAsyncComponent, reactive, onMounted, ref } from 'vue';
	import { ElMessageBox, ElMessage } from 'element-plus';
	import { getArticleList,deleteArticle,changeArticleStatus } from '/@/api/api';

	// 引入组件
	const Table = defineAsyncComponent(() => import('/@/components/table/index.vue'));
	const TableSearch = defineAsyncComponent(() => import('/@/components/search.vue'));
	// 引入组件
	const RoleDialog = defineAsyncComponent(() => import('./dialog.vue'));
	const RoleCommentDialog = defineAsyncComponent(() => import('../comment/dialog.vue'));
	const commentIndex = defineAsyncComponent(() => import('../comment/index.vue'));

	const changeStatusFun = async (row : Object,status:number) => {
		let res = await changeArticleStatus({id:row.id,status:status});
		if(res.code == 200) ElMessage.success('操作成功');
	}
	// 定义变量内容
	const roleDialogRef = ref();
	const roleCommentDialogRef = ref();
	const state = reactive({
		tagList:[],
		navList:[],
		tableData: {
			artcleId:0,
			data: [],
			total: 0,
			loading: false,
			search: [
				{ label: '文章名称', prop: 'title', placeholder: '文章名称', required: false, type: 'input' },
				{ label: '导航分类', prop: 'nav_id',type:'select',options:[], placeholder: '导航分类', required: false },
				{ label: '关键词', prop: 'tag_id',type:'select', options:[], placeholder: '关键词', required: false },
			],
			// 表头内容（必传，注意格式）
			header: [
				{ key: 'title', colWidth: '', title: '文章标题', type: 'text', isCheck: true },
				{ key: 'owner', colWidth: '', title: '作者', type: 'text', isCheck: true },
				{ key: 'describe', colWidth: '', title: '文章描述', type: 'text', isCheck: true },
				{ key: 'start_time', colWidth: '', title: '开始时间', type: 'date', isCheck: true },
				{ key: 'end_time', colWidth: '', title: '结束时间', type: 'date', isCheck: true },
				{ key: 'view_num', colWidth: '', title: '展示次数', type: 'text', isCheck: true },
				{ key: 'click_num', colWidth: '', title: '预览详情次数', type: 'text', isCheck: true },
				{ key: 'comment_num', colWidth: '', title: '评论数', type: 'text', isCheck: true },
				{ key: 'status', colWidth: '', title: '是否发布(是/否)',fun:changeStatusFun, type: 'switch', isCheck: true },
				{ key: 'pinned', colWidth: '', title: '是否置顶(是/否)',fun:changeStatusFun, type: 'switch', isCheck: true },
				{ key: 'create_time', colWidth: '', title: '创建时间', type: 'date', isCheck: true },
			],
			page:{
				page: 1,
				pageSize: 10,
			},
			config:{
				isOperate: [{label:'查看评论',key:'seeComment'},{label:'添加评论',key:'addComment'},{label:'编辑',key:'edit'},{label:'删除',key:'delete',type:'tip',tipTitle:'确认删除吗？'}], // 是否显示表格操作栏
				loading:false
			},
		},
		commentIndexDialog:false
	});
	// 初始化表格数据
	const getTableData = async (e = {}) => {
		state.tableData.config.loading = true;
		const data = [];
		let row = await getArticleList({...e,...state.tableData.page});
		state.tableData.data = row.data?.list;
		state.tableData.total = row.data?.count || 0;
		state.tagList = row.data.tagList;
		state.navList = row.data.navList;
		state.tableData.search.forEach(e => {
			if(e.prop === 'nav_id'){
				e.options = state.navList;
			}else if(e.prop === 'tag_id'){
				e.options = state.tagList;
			}
		})
		state.tableData.config.loading = false;
	};
	// 打开新增角色弹窗
	const onOpenAddRole = (row:Object = {},name:string = '') => {
		roleDialogRef.value.openDialog('add',row,name);
	};
	// 打开新增角色弹窗
	const onOpenCommentLastRole = (row:Object = {}) => {
		roleCommentDialogRef.value.openDialog('add',{article_id:row.id},row.name);
	};
	const onOpenCommentIndex = (row:Object) => {
		state.artcleId = row.id;
		state.commentIndexDialog=true;
	}
	// 打开修改角色弹窗
	const onOpenEditRole = ( row : Object) => {
		roleDialogRef.value.openDialog('edit', row);
	};
	const deleteRow = (row:object) => {
		ElMessageBox.confirm(`此操作将永久删除，是否继续?`, '提示', {
			confirmButtonText: '确认',
			cancelButtonText: '取消',
			type: 'warning',
		})
			.then(async () => {
				await deleteArticle({id:row.id});
				getTableData();
				ElMessage.success('删除成功');
			})
			.catch(() => { });
	}
	// 删除角色
	const onSystem = (row :object,key:string) => {
		let h = {
			seeComment:onOpenCommentIndex,
			addComment:onOpenCommentLastRole,
			add:onOpenAddRole,
			edit:onOpenEditRole,
			delete:deleteRow,
		}
		if(h[key]) h[key](row);
	};
	// 分页改变
	const onHandlePageChange = (data :object) => {
		state.tableData.page.pageSize = data.pageSize;
		state.tableData.page.page = data.page;
		getTableData();
	};
	// 页面加载时
	onMounted(() => {
		getTableData();
	});
</script>

<style scoped lang="scss">
	.system-role-container {
		.system-role-padding {
			padding: 15px;

			.el-table {
				flex: 1;
			}
		}
	}
</style>
<style>
	.el-dialog__headerbtn {
	    z-index: 9;
	}
</style>