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
      <adsDialog ref="openDialogRef" @refresh="getTableData()"/>
    </el-card>
  </div>
</template>
<script lang="ts" setup>
import useAdvertisement from "/@/composables/useAdvertisement";
import { defineAsyncComponent } from "vue";

const Table = defineAsyncComponent(() => import('/@/components/table/index.vue'));
const adsDialog = defineAsyncComponent(() => import('/@/pages/operation/advertisement/dialog.vue'));
const {
  onOpenAddDialog,
  openDialogRef,
  getTableData,
  state,
  onHandlePageChange,
  onSystem,
} = useAdvertisement();
</script>