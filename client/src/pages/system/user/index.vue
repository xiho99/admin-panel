<template>
  <div class="system-user-container layout-padding">
    <el-card shadow="hover" class="layout-padding-auto">
      <div class="system-user-search mb15">
        <el-input size="default" :placeholder="$t('message.table.enterUserName')" style="max-width: 180px"></el-input>
        <el-button size="default" type="primary" class="ml10">
          <el-icon>
            <ele-Search/>
          </el-icon>
          {{ $t('message.table.search') }}
        </el-button>
        <el-button size="default" type="success" class="ml10" @click="onOpenAddUser('add')" :loading="state.tableData.loading">
          <el-icon>
            <ele-FolderAdd/>
          </el-icon>
          {{ $t('message.table.newUser') }}
        </el-button>
      </div>
      <el-table :data="state.tableData.data" v-loading.lock="state.tableData.loading" style="width: 100%">
        <el-table-column type="index" :label="$t('message.table.numberSign')" width="60"/>
        <el-table-column :label="$t('message.table.accountName')" show-overflow-tooltip>
          <template #default="scope">
            <div> {{ scope.row.userName }}</div>
          </template>
        </el-table-column>
        <el-table-column prop="nickname" :label="$t('message.table.nickName')" show-overflow-tooltip></el-table-column>
        <el-table-column prop="role_ids" center :label="$t('message.table.associatedRole')" show-overflow-tooltip>
          <template #default="scope">
            <span v-for="(ite, index) in (scope.row?.role_ids?.split(',') || [])" :key="index"
                  style="background:#f9d83a;margin:0 5px;padding:3px 5px;border-radius: 4px;">
              {{ $t(`message.${state.roleList[ite]?.roleName}`) }}
            </span>
          </template>
        </el-table-column>
        <el-table-column :label="$t('message.table.userStatus')" show-overflow-tooltip>
          <template #default="scope">
<!--            {{ scope.row.status }}-->
            <el-tag type="success" v-if="scope.row.status">{{ $t('message.enabled') }}</el-tag>
            <el-tag type="info" v-else>{{ $t('message.disabled') }}</el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="describe" :label="$t('message.table.userDescription')" show-overflow-tooltip></el-table-column>
        <el-table-column prop="create_time" :label="$t('message.table.createdAt')" show-overflow-tooltip>
          <template #default="scope">
            <div>{{ dayjs(scope.row.create_time).format('YYYY-MM-DD') }}</div>
          </template>
        </el-table-column>
        <el-table-column :label="$t('message.table.operate')" width="100">
          <template #default="scope">
            <el-button :disabled="scope.row.userName === 'admin'" size="small" text type="warning"
                       @click="onOpenEditUser('edit', scope.row)" :loading="state.tableData.loading"
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
import { adminList, deleteAdmin } from '/@/api/admin';
import { getAllRole } from '/@/api/role';
import dayjs from 'dayjs';
import { useI18n } from "vue-i18n";
import EnumApiErrorCode from "/@/models/enums/enumApiErrorCode";

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
const { t } = useI18n();
// 初始化表格数据
const getTableData = async () => {
  state.tableData.loading = true;
  let row = await adminList(state.tableData.param);
  state.tableData.data = row.data?.data;
  state.tableData.total = row.data?.total || 0;
  state.tableData.loading = false;
};
getTableData();
// 打开新增用户弹窗
const onOpenAddUser = (type: string) => {
  state.tableData.loading = true;
  userDialogRef.value.openDialog(type);
  state.tableData.loading = false;
};
// 打开修改用户弹窗
const onOpenEditUser = (type: string, row: RowUserType) => {
  state.tableData.loading = true
  userDialogRef.value.openDialog(type, row);
  state.tableData.loading = false;
};
// 删除用户
const onRowDel = (row: RowUserType) => {
  ElMessageBox.confirm(`此操作将永久删除账户名称：“${row?.userName}”，是否继续?`, '提示', {
    confirmButtonText: t('message.yes'),
    cancelButtonText: t('message.no'),
    type: 'warning',
  })
      .then( async () => {
        const response = await deleteAdmin(row);
        if (response.code === EnumApiErrorCode.success) {
          ElMessage.success(t('message.success'));
        } else {
          ElMessage.success(t('message.fail'));
        }
        getTableData();
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
const getRole = async () => {
  const response  = await getAllRole();
  if (response.code !== EnumApiErrorCode.success) {
    // eslint-disable-next-line no-console
    console.log(response);
  } else {
    state.roleList = response.data || {};
  }
};
// 页面加载时
onMounted(() => {
  getRole();
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
