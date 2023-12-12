<template>
  <div class="layout-container layout-padding">
    <el-card shadow="hover" class="layout-padding-auto">
      <div class=" system-user-search p-3 flex justify-end">
        <el-button type="success" @click="onOpenAddDialog('add')">
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
      <categoryDialog ref="openDialogRef" @refresh="getTableData()"/>
    </el-card>
  </div>
</template>
<script lang="ts" setup>
import { defineAsyncComponent } from "vue";
import useCategory from "/@/composables/useCategory";

const Table = defineAsyncComponent(() => import('/@/components/table/index.vue'));
const categoryDialog = defineAsyncComponent(() => import('/@/pages/operation/category/dialog.vue'));
const {
  onOpenAddDialog,
  openDialogRef,
  getTableData,
  state,
  onHandlePageChange,
  onSystem,
} = useCategory();
</script>