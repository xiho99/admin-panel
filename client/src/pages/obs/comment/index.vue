<template>
	<div class="table-demo-container layout-padding w100">
		<div class="table-demo-padding layout-padding-view layout-padding-auto">
			<div style="display: flex; justify-content: space-between;" class="mr10">
				<div style="flex:1;">
					<TableSearch :search="state.search" @search="getTableData" />
				</div>
				<!-- <el-button size="default" type="primary" class="mt10" @click="onOpenAddRole"> 新增 </el-button> -->
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
		<ArticleIndex ref="articleIndexRef" />
	</div>
</template>

<script setup lang="ts" name="/comment">
	import { defineAsyncComponent, reactive, onMounted, ref } from 'vue';
	import { ElMessageBox, ElMessage } from 'element-plus';
	import { getCommentList,deleteComment } from '/@/api/api';
	
	let props = defineProps({
		artcleId:{
			type: Number,
			default: 0,
		},
	})
	// 引入组件
	const Table = defineAsyncComponent(() => import('/@/components/table/index.vue'));
	const TableSearch = defineAsyncComponent(() => import('/@/components/search.vue'));
	// 引入组件
	const RoleDialog = defineAsyncComponent(() => import('./dialog.vue'));
	const ArticleIndex = defineAsyncComponent(() => import('../article/articleInfo.vue'));
	// 定义变量内容
	const roleDialogRef = ref();
	const articleIndexRef = ref();
	const showArticleIndex = (row:Object = {}) => {
		articleIndexRef.value.openDialog(row.artcle_id);
	}
	const state = reactive({
		IndexDialog:false,
		artcleId:0,
		tableData: {
			data: [],
			total: 0,
			loading: false,
			search: [
			],
			// 表头内容（必传，注意格式）
			header: [
				{ key: 'id', colWidth: 50, title: 'ID', type: 'text', isCheck: true },
				{ key: 'name', colWidth: '', title: '用户昵称', type: 'text', isCheck: true },
				{ key: 'content', colWidth: '', title: '评论信息', type: 'text', isCheck: true },
				{ key: 'title', colWidth: '', title: '所属文章', type: 'text',fun:showArticleIndex, isCheck: true },
				{ key: 'parent_id', colWidth: '', title: '回复的ID', type: 'text', isCheck: true },
				{ key: 'superiors_id', colWidth: '', title: '首评ID', type: 'text', isCheck: true },
				{ key: 'status', colWidth: '', title: '状态', type: 'text',keyArray:{0:'待审核',1:'审核通过',2:'审核不通过'}, isCheck: true },
				{ key: 'create_time', colWidth: '', title: '创建时间', type: 'date', isCheck: true },
			],
			page:{
				page: 1,
				pageSize: 10,
			},
			config:{
				isOperate: [{label:'添加下级评论',key:'add'},{label:'编辑',key:'edit'},{label:'删除',key:'delete',type:'tip',tipTitle:'确认删除吗？'}], // 是否显示表格操作栏
				loading:false
			},
		},
	});
	// 初始化表格数据
	const getTableData = async (e:Object = {}) => {
		state.tableData.config.loading = true;
		const data = [];
		if(props.artcleId) e.artcleId = props.artcleId;
		let row = await getCommentList({...e,...state.tableData.page});
		state.tableData.data = row.data?.list;
		state.tableData.total = row.data?.count || 0;
		state.tableData.config.loading = false;
	};
	// 打开新增角色弹窗
	const onOpenAddRole = (row:Object = {},name:string = '') => {
		roleDialogRef.value.openDialog('add',row,name);
	};
	// 打开新增角色弹窗
	const onOpenAddLastRole = (row:Object = {}) => {
		console.log(row)
		onOpenAddRole({parent_id:row.id,article_id:row.article_id},row.name);
	};
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
				await deleteComment({id:row.id});
				getTableData();
				ElMessage.success('删除成功');
			})
			.catch(() => { });
	}
	// 删除角色
	const onSystem = (row :object,key:string) => {
		let h = {
			add:onOpenAddLastRole,
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