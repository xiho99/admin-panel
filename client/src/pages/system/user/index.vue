<template>
  <div class="system-user-container layout-padding">
    <el-card shadow="hover" class="layout-padding-auto">
      <div class="system-user-search mb15">
        <el-input size="default" placeholder="请输入用户名称" style="max-width: 180px"></el-input>
        <el-button size="default" type="primary" class="ml10">
          <el-icon>
            <ele-Search/>
          </el-icon>
          查询
        </el-button>
        <el-button size="default" type="success" class="ml10" @click="onOpenAddUser('add')">
          <el-icon>
            <ele-FolderAdd/>
          </el-icon>
          新增用户
        </el-button>
      </div>
      <el-table :data="state.tableData.data" v-loading.lock="state.tableData.loading" style="width: 100%">
        <el-table-column type="index" label="序号" width="60"/>
        <el-table-column label="账户名称" show-overflow-tooltip>
          <template #default="scope">
            <div> {{ scope.row.userName }}</div>
          </template>
        </el-table-column>
        <el-table-column prop="nickname" label="用户昵称" show-overflow-tooltip></el-table-column>
        <el-table-column prop="role_ids" center label="关联角色" show-overflow-tooltip>
          <template #default="scope">
            <span v-for="(ite, index) in (scope.row.role_ids?.split(',') || [])" :key="index"
                  style="background:#f9d83a;margin:0 5px;padding:3px 5px;border-radius: 4px;">
              {{ state.roleList[ite]?.roleName }}
            </span>
          </template>
        </el-table-column>
        <el-table-column prop="status" label="用户状态" show-overflow-tooltip>
          <template #default="scope">
            <el-tag type="success" v-if="scope.row.status">启用</el-tag>
            <el-tag type="info" v-else>禁用</el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="describe" label="用户描述" show-overflow-tooltip></el-table-column>
        <el-table-column prop="create_time" label="创建时间" show-overflow-tooltip>
          <template #default="scope">
            <div>{{ dayjs(scope.row.create_time).format('YYYY-MM-DD') }}</div>
          </template>
        </el-table-column>
        <el-table-column label="操作" width="100">
          <template #default="scope">
            <el-button :disabled="scope.row.userName === 'admin'" size="small" text type="warning"
                       @click="onOpenEditUser('edit', scope.row)"
            >{{ $t('message.table.edit') }}
            </el-button
            >
            <el-button :disabled="scope.row.userName === 'admin'" size="small" text type="danger"
                       @click="onRowDel(scope.row)">{{ $t('message.table.delete') }}
            </el-button>
          </template>
        </el-table-column>
      </el-table>
      <el-pagination
          @size-change="onHandleSizeChange"
          @current-change="onHandleCurrentChange"
          class="mt15"
          :pager-count="5"
          :page-sizes="[10, 20, 30]"
          v-model:current-page="state.tableData.param.pageNum"
          background
          v-model:page-size="state.tableData.param.pageSize"
          layout="total, sizes, prev, pager, next, jumper"
          :total="state.tableData.total"
      >
      </el-pagination>
    </el-card>
    <UserDialog ref="userDialogRef" @refresh="getTableData()"/>
  </div>
</template>

<script setup lang="ts" name="systemUser">
import { defineAsyncComponent, reactive, onMounted, ref } from 'vue';
import { ElMessageBox, ElMessage } from 'element-plus';
import { adminList } from '/@/api/admin';
import { getAllRole } from '/@/api/role';
import dayjs from 'dayjs';

// 引入组件
const UserDialog = defineAsyncComponent(() => import('/@/pages/system/user/dialog.vue'));

// 定义变量内容
const userDialogRef = ref();
const state = reactive({
  tableData: {
    data: [],
    total: 0,
    loading: false,
    param: {
      pageNum: 1,
      pageSize: 10,
    },
  },
  roleList: {},
});

// 初始化表格数据
const getTableData = async () => {
  state.tableData.loading = true;
  let row = await adminList(state.tableData.param);
  state.tableData.data = row.data?.list;
  state.tableData.total = row.data?.count || 0;
  state.tableData.loading = false;
};
// 打开新增用户弹窗
const onOpenAddUser = (type: string) => {
  userDialogRef.value.openDialog(type);
};
// 打开修改用户弹窗
const onOpenEditUser = (type: string, row: RowUserType) => {
  userDialogRef.value.openDialog(type, row);
};
// 删除用户
const onRowDel = (row: RowUserType) => {
  ElMessageBox.confirm(`此操作将永久删除账户名称：“${row?.username}”，是否继续?`, '提示', {
    confirmButtonText: '确认',
    cancelButtonText: '取消',
    type: 'warning',
  })
      .then(() => {
        getTableData();
        ElMessage.success('删除成功');
      })
      .catch(() => {
      });
};
// 分页改变
const onHandleSizeChange = (val: number) => {
  state.tableData.param.pageSize = val;
  getTableData();
};
// 分页改变
const onHandleCurrentChange = (val: number) => {
  state.tableData.param.pageNum = val;
  getTableData();
};
// 页面加载时
onMounted(async () => {
  let row = await getAllRole();
  state.roleList = row.data || {};
  getTableData();
});
</script>

<style scoped lang="scss">
.system-user-container {
  :deep(.el-card__body) {
    display: flex;
    flex-direction: column;
    flex: 1;
    overflow: auto;

    .el-table {
      flex: 1;
    }
  }
}
</style>
