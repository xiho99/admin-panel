<template>
	<div class="system-menu-container layout-pd">
    <el-card shadow="hover" class="layout-padding-auto">
			<div class="system-menu-search mb15">
				<el-input size="default" v-model="state.tableData.search" :placeholder="$t('message.table.enterUserName')" style="max-width: 180px"> </el-input>
				<el-button size="default" type="primary" class="ml10">
					<el-icon>
						<ele-Search />
					</el-icon>
          {{ $t('message.table.search') }}
				</el-button>
				<el-button size="default" type="success" class="ml10" @click="onOpenAddMenu">
					<el-icon>
						<ele-FolderAdd />
					</el-icon>
          {{ $t('message.table.newMenu') }}
				</el-button>
			</div>
			<el-table
				:data="dataFilterPaginationSearch"
				v-loading.lock="state.tableData.loading"
				style="width: 100%"
				row-key="path"
				:tree-props="{ children: 'children', hasChildren: 'hasChildren' }"
			>
				<el-table-column :label="$t('message.menuName')" show-overflow-tooltip>
					<template #default="scope">
						<SvgIcon :name="scope.row.meta.icon" />
						<span class="ml10">{{ $t(scope.row.meta.title) }}</span>
					</template>
				</el-table-column>
				<el-table-column prop="path" :label="$t('message.routePath')" show-overflow-tooltip></el-table-column>
				<el-table-column prop="name" :label="$t('message.routeName')" show-overflow-tooltip></el-table-column>
				<el-table-column :label="$t('message.componentPath')" show-overflow-tooltip>
					<template #default="scope">
						<span>{{ scope.row.component }}</span>
					</template>
				</el-table-column>
				<el-table-column :label="$t('message.permissionId')" show-overflow-tooltip>
					<template #default="scope">
						<span>{{ scope.row.meta.roles }}</span>
					</template>
				</el-table-column>
				<el-table-column :label="$t('message.sort')" show-overflow-tooltip width="80">
					<template #default="scope">
						{{ scope.$index }}
					</template>
				</el-table-column>
				<el-table-column :label="$t('message.type')" show-overflow-tooltip width="100">
					<template #default="scope">
						<el-tag type="success" size="small">{{ scope.row.menuType === 'menu' ? $t('message.menu') : $t('message.button') }}</el-tag>
					</template>
				</el-table-column>
				<el-table-column :label="$t('message.operate')" show-overflow-tooltip width="140">
					<template #default="scope">
						<el-button size="small" text type="primary" @click="onOpenAddMenu('add')">{{ $t('message.table.add') }}</el-button>
						<el-button size="small" text type="warning" @click="onOpenEditMenu('edit', scope.row)">{{ $t('message.table.edit') }}</el-button>
						<el-button size="small" text type="danger" @click="onTabelRowDel(scope.row)">{{ $t('message.table.delete') }}</el-button>
					</template>
				</el-table-column>
			</el-table>
      <el-pagination class="mt-5"
          v-model:current-page="state.tableData.paginate.page"
          v-model:page-size="state.tableData.paginate.pageSize"
          :page-sizes="[10, 25, 50, 75, 100]"
          background
          layout="total, sizes, prev, pager, next, jumper"
          :total="state.tableData.data.length"/>
		</el-card>
		<MenuDialog ref="menuDialogRef" @refresh="getTableData()" />
	</div>
</template>

<script setup lang="ts" name="systemMenu">
import { defineAsyncComponent, ref, onMounted, reactive, computed } from 'vue';
import { RouteRecordRaw } from 'vue-router';
import { ElMessageBox, ElMessage } from 'element-plus';
import { storeToRefs } from 'pinia';
import { useRoutesList } from '/@/stores/routesList';
import { useMenuApi } from '/@/api/menu';
import {initBackEndControlRoutes} from '/@/router/backEnd'
const menuApi = useMenuApi();

const MenuDialog = defineAsyncComponent(() => import('/@/pages/system/menu/dialog.vue'));
// 定义变量内容
const stores = useRoutesList();
const { routesList } = storeToRefs(stores);
const menuDialogRef = ref();
const state = reactive({
	tableData: {
		data: [] as RouteRecordRaw[],
		loading: true,
    search: '',
    paginate: {
      page: 1,
      pageSize: 10,
      total: 0,
    },
  },
});
const getTableData = async () => {
	state.tableData.loading = true;
	await initBackEndControlRoutes();
	state.tableData.data = routesList.value;
	state.tableData.loading = false;
};
const dataFilterPaginationSearch = computed(() => {
  return state.tableData.data.slice(state.tableData.paginate.pageSize * state.tableData.paginate.page - state.tableData.paginate, state.tableData.paginate.pageSize * state.tableData.paginate.page)
      .filter((data) =>!state.tableData.search || data.meta?.title.toLowerCase().includes(state.tableData.search.toLowerCase()));
});

// 打开新增菜单弹窗
const onOpenAddMenu = (type: string) => {
	menuDialogRef.value.openDialog(type);
};
// 打开编辑菜单弹窗
const onOpenEditMenu = (type: string, row: RouteRecordRaw) => {
	menuDialogRef.value.openDialog(type, row);
};
// 删除当前行
const onTabelRowDel = (row: RouteRecordRaw) => {
	ElMessageBox.confirm(`此操作将永久删除路由：${row.path}, 是否继续?`, '提示', {
		confirmButtonText: '删除',
		cancelButtonText: '取消',
		type: 'warning',
	})
		.then(async () => {
			ElMessage.success('删除成功');
			await menuApi.deleteMenu({id:row.id});
			getTableData();
		})
		.catch(() => {});
};
// 页面加载时
onMounted(() => {
	getTableData();
});
</script>
