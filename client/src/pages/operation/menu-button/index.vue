<template>
  <div class="layout-container layout-padding">
    <el-card shadow="hover" class="layout-padding-auto">
      <div class="system-user-search p-3 flex justify-end">
        <el-button type="success" @click="onOpenAddDialog('add')">
          <el-icon>
            <ele-FolderAdd/>
          </el-icon>
          {{ $t('message.table.new') }}
        </el-button>
      </div>
      <Table
          ref="tableRef"
          v-bind="state.tableData"
          class="table-demo"
          @onSystem="onSystem"
          @pageChange="onHandlePageChange"
      />
      <menuDialog ref="openDialogRef" @refresh="getTableData()"/>
    </el-card>
  </div>
</template>
<script lang="ts" setup>
import { defineAsyncComponent } from "vue";
import useMenuButton from "/@/composables/useMenuButton";

const Table = defineAsyncComponent(() => import('/@/components/table/index.vue'));
const menuDialog = defineAsyncComponent(() => import('/@/pages/operation/menu-button/dialog.vue'));
const {
  onOpenAddDialog,
  openDialogRef,
  getTableData,
  state,
  onHandlePageChange,
  onSystem,
} = useMenuButton();
</script>