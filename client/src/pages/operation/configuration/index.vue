<template>
  <div class="layout-container layout-padding">
    <el-card shadow="hover" class="layout-padding-auto">
      <div class="system-user-search pr-3 flex justify-end">
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
      <configurationDialog ref="openDialogRef" @refresh="getTableData()"/>
    </el-card>
  </div>
</template>
<script lang="ts" setup>
import useConfiguration from "/@/composables/useConfiguration";
import { defineAsyncComponent } from "vue";

const Table = defineAsyncComponent(() => import('/@/components/table/index.vue'));
const configurationDialog = defineAsyncComponent(() => import('/@/pages/operation/configuration/dialog.vue'));
const {
  onOpenAddDialog,
  openDialogRef,
  getTableData,
  state,
  onHandlePageChange,
  onSystem,
} = useConfiguration();
</script>