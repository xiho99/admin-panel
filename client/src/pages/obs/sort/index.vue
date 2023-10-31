<template>
	<div class="table-demo-container layout-padding">
		<div class="table-demo-padding layout-padding-view layout-padding-auto">
			<div style="display: flex; justify-content: space-between;" class="mr10">
				<div style="flex:1;">
					<TableSearch :search="state.tableData.search" @search="getTableData" />
				</div>
				<el-button size="default" type="primary" class="mt10" @click="onOpenAddRole()"> 新增 </el-button>
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
	</div>
</template>

<script setup lang="ts" name="/sort">
	import { defineAsyncComponent, reactive, onMounted, ref } from 'vue';
	import { ElMessageBox, ElMessage } from 'element-plus';
	import { getNavList,deleteNav,changeNavStatus } from '/@/api/api';

	// 引入组件
	const Table = defineAsyncComponent(() => import('/@/components/table/index.vue'));
	const TableSearch = defineAsyncComponent(() => import('/@/components/search.vue'));
	// 引入组件
	const RoleDialog = defineAsyncComponent(() => import('./dialog.vue'));
	const changeNavStatusFun = async (row : Object,status:number) => {
		let res = await changeNavStatus({id:row.id,status:status});
		if(res.code == 200) ElMessage.success('操作成功');
	}
	// 定义变量内容
	const roleDialogRef = ref();
	const state = reactive({
		tableData: {
			data: [],
			total: 0,
			loading: false,
			search: [
				{ label: '导航名称', prop: 'name', placeholder: '请输入导航名称', required: true, type: 'input' },
			],
			// 表头内容（必传，注意格式）
			header: [
				{ key: 'name', colWidth: '', title: '导航名称', type: 'text', isCheck: true },
				{ key: 'sort', colWidth: '', title: '排序', type: 'text', isCheck: true },
				{ key: 'is_link', colWidth: '', title: '是否是外链', type: 'tag', isCheck: true },
				{ key: 'status', colWidth: '', title: '状态',fun:changeNavStatusFun, type: 'switch', isCheck: true },
				{ key: 'create_time', colWidth: '', title: '创建时间', type: 'date', isCheck: true },
			],
			page:{
				page: 1,
				pageSize: 10,
			},
			config:{
				isOperate: [{label:'编辑',key:'edit'},{label:'删除',key:'delete',type:'tip',tipTitle:'确认删除吗？'}], // 是否显示表格操作栏
				loading:false
			},
		},
	});
	// 初始化表格数据
	const getTableData = async (e = {}) => {
		state.tableData.config.loading = true;
		const data = [];
		let row = await getNavList({...e,...state.tableData.page});
		state.tableData.data = row.data?.list;
		state.tableData.total = row.data?.count || 0;
		state.tableData.config.loading = false;
	};
	// 打开新增角色弹窗
	const onOpenAddRole = () => {
		roleDialogRef.value.openDialog();
	};
	// 打开修改角色弹窗
	const onOpenEditRole = ( row : Object) => {
		roleDialogRef.value.openDialog('edit', row);
	};
	const deleteRow = (row:object) => {
		ElMessageBox.confirm(`此操作将永久删除导航，是否继续?`, '提示', {
			confirmButtonText: '确认',
			cancelButtonText: '取消',
			type: 'warning',
		})
			.then(async () => {
				await deleteNav({id:row.id});
				getTableData();
				ElMessage.success('删除成功');
			})
			.catch(() => { });
	}
	// 删除角色
	const onSystem = (row :object,key:string) => {
		let h = {
			edit:onOpenEditRole,
			delete:deleteRow,
		}
		console.log(h,key,'debugm')
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