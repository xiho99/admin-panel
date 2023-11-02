<template>
  <div class="home-container layout-pd">
    <div class="shadow bg-white rounded">
      <div class="system-user-search p-3 flex justify-end">
        <el-button type="success" @click="onOpenAddDialog('add')">
          <el-icon>
            <ele-FolderAdd/>
          </el-icon>
          {{ $t('message.table.new') }}
        </el-button>
      </div>
      <el-table :data="filterTableData" v-loading="isLoading">
        <el-table-column type="index" :label="$t('No')" width="80"/>
        <el-table-column prop="appName" :label="$t('message.appName')"/>
        <el-table-column prop="key" :label="$t('message.key')"/>
        <el-table-column :label="$t('message.type')">
          <template #default="prop">
              <div v-if="prop.row.type === 'image'">
                <el-image class="w-24 rounded" :src="prop.row.value" alt="loading.."/>
              </div>
              <div v-else> {{ prop.row.value }}</div>
          </template>
        </el-table-column>
        <el-table-column prop="sort" :label="$t('message.sort')"/>
        <el-table-column label="status">
          <template #header>
            <el-input v-model="data.search" size="default" :placeholder="$t('message.name')"/>
          </template>
          <template #default="scope">
            <el-button link type="primary" size="small" @click.prevent="onOpenEditDialog('edit', scope.row)">{{ $t('message.table.edit') }}</el-button>
            <el-button link type="danger" size="small" @click.prevent="deleteRow(scope.row)">{{ $t('message.table.delete') }}</el-button>
          </template>
        </el-table-column>
      </el-table>
      <configurationDialog  ref="openDialogRef" @refresh="getConfiguration()"/>
    </div>
  </div>
</template>
<script lang="ts" setup>
import useConfiguration from "/@/composables/useConfiguration";
import { defineAsyncComponent } from "vue";

const configurationDialog = defineAsyncComponent(() => import('/@/pages/operation/configuration/dialog.vue'));

const {
  isLoading,
  data,
  onOpenAddDialog,
  onOpenEditDialog,
  openDialogRef,
  getConfiguration,
  filterTableData,
  deleteRow,
} = useConfiguration();
</script>