<template>
  <div class=" layout-pd">
    <div class="shadow bg-white rounded">
      <div class="system-user-search p-3 flex justify-end">
        <el-button type="success" @click="onOpenAddDialog('add')">
          <el-icon>
            <ele-FolderAdd/>
          </el-icon>
          {{ $t('message.table.new') }}
        </el-button>
      </div>
      <el-table :data="formData.data" v-loading="isLoading">
        <el-table-column :label="$t('message.type')">
          <template #default="prop">
            <el-image class="w-24 rounded" :src="prop.row.value" alt="loading.."/>
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
<!--      <adsDialog ref="openDialogRef" @refresh="getAds()"/>-->
    </div>
  </div>
</template>
<script lang="ts" setup>
import useAds from "/@/composables/useAds";
import { defineAsyncComponent } from "vue";

// const adsDialog = defineAsyncComponent(() => import('/@/pages/operation/ads/dialog.vue'));
const {
  isLoading,
  data,
  onOpenAddDialog,
  onOpenEditDialog,
  // openDialogRef,
  // getAds,
  formData,
  deleteRow,
} = useAds();
</script>