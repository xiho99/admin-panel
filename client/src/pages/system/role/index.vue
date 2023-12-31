<template>
	<div class="system-role-container layout-padding">
		<div class="system-role-padding layout-padding-auto layout-padding-view">
			<div class="system-user-search mb15 ">
				<el-input v-model="state.tableData.param.name"  size="default" :placeholder="$t('message.table.enterUserName')" style="max-width: 180px"> </el-input>
				<el-button size="default" type="primary" class="ml10" @click="getTableData">
					<el-icon>
						<ele-Search />
					</el-icon>
          {{ $t('message.table.search') }}
				</el-button>
				<el-button size="default" type="success" class="ml10" @click="onOpenAddRole('add')">
					<el-icon>
						<ele-FolderAdd />
					</el-icon>
          {{ $t('message.table.newRole') }}
				</el-button>
			</div>
			<el-table :data="state.tableData.data" v-loading.lock="state.tableData.loading" style="width: 100%">
        <el-table-column type="index" :label="$t('message.table.numberSign')" width="60"/>
				<el-table-column prop="roleName" :label="$t('message.roleName')" show-overflow-tooltip></el-table-column>
				<el-table-column prop="roleSign" :label="$t('message.roleSign')" show-overflow-tooltip></el-table-column>
				<el-table-column prop="sort" :label="$t('message.sort')" show-overflow-tooltip></el-table-column>
				<el-table-column prop="status" :label="$t('message.status')" show-overflow-tooltip>
					<template #default="scope">
						<el-tag type="success" v-if="scope.row.status">{{ $t('message.enabled') }}</el-tag>
						<el-tag type="info" v-else>{{ $t('message.disabled') }}</el-tag>
					</template>
				</el-table-column>
				<el-table-column prop="describe" :label="$t('message.describe')" show-overflow-tooltip></el-table-column>
				<el-table-column prop="createTime" :label="$t('message.table.createdAt')" show-overflow-tooltip></el-table-column>
				<el-table-column :label="$t('message.operate')" width="100">
					<template #default="scope">
						<el-button :disabled="scope.row.roleName === 'superAdmin'" size="small" text type="warning" @click="onOpenEditRole('edit', scope.row)"
							>{{ $t('message.table.edit') }}</el-button
						>
						<el-button :disabled="scope.row.roleName === 'superAdmin'" size="small" text type="danger" @click="onRowDel(scope.row)">{{ $t('message.table.delete') }}</el-button>
					</template>
				</el-table-column>
			</el-table>
			<el-pagination class="mt15"
				@size-change="onHandleSizeChange"
				@current-change="onHandleCurrentChange"
        :small="true"
				:page-sizes="[10, 20, 30]"
				v-model:current-page="state.tableData.param.page"
				background
				v-model:page-size="state.tableData.param.pageSize"
				layout="total, sizes, prev, pager, next, jumper"
				:total="state.tableData.total"
			>
			</el-pagination>
		</div>
		<RoleDialog ref="roleDialogRef" @refresh="getTableData()" />
	</div>
</template>

<script setup lang="ts" name="systemRole">
import { defineAsyncComponent, reactive, onMounted, ref } from 'vue';
import { ElMessageBox, ElMessage } from 'element-plus';
import { roleList,deleteRole } from '/@/api/role';

// 引入组件
const RoleDialog = defineAsyncComponent(() => import('/@/pages/system/role/dialog.vue'));

// 定义变量内容
const roleDialogRef = ref();
const state = reactive({
	tableData: {
		data: [],
		total: 0,
		loading: false,
		param: {
			name: '',
			page: 1,
			pageSize: 10,
		},
	},
});
// 初始化表格数据
const getTableData = async () => {
	state.tableData.loading = true;
	let row = await roleList(state.tableData.param);
	state.tableData.data = row.data?.data;
	state.tableData.total = row.data?.count || 0;
	state.tableData.loading = false;
};
// 打开新增角色弹窗
const onOpenAddRole = (type: string) => {
	roleDialogRef.value.openDialog(type);
};
// 打开修改角色弹窗
const onOpenEditRole = (type: string, row: Object) => {
	roleDialogRef.value.openDialog(type, row);
};
// 删除角色
const onRowDel = (row: RowRoleType) => {
	ElMessageBox.confirm(`此操作将永久删除角色名称：“${row.roleName}”，是否继续?`, '提示', {
		confirmButtonText: '确认',
		cancelButtonText: '取消',
		type: 'warning',
	})
		.then(async () => {
			await deleteRole({id:row.id});
			getTableData();
			ElMessage.success('删除成功');
		})
		.catch(() => {});
};
// 分页改变
const onHandleSizeChange = (val: number) => {
	state.tableData.param.pageSize = val;
	getTableData();
};
// 分页改变
const onHandleCurrentChange = (val: number) => {
	state.tableData.param.page = val;
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
