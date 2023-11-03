<template>
  <div class=" layout-pd">
    <div class="shadow bg-white rounded p-5">
      <div class="system-user-search p-3 flex justify-end">
        <el-button type="success" @click="onOpenAddDialog('add')">
          <el-icon>
            <ele-FolderAdd/>
          </el-icon>
          {{ $t('message.table.new') }}
        </el-button>
      </div>
      <el-table :data="formData.data">
        <el-table-column :label="$t('message.router.ads')">
          <template #default="prop">
            <el-image class="h-14 rounded" :src="prop.row.image" alt="loading.."/>
          </template>
        </el-table-column>
        <el-table-column  :label="$t('message.is_visible')">
          <template #default="prop">
            <div v-if="prop.row.is_visible">
              <el-tag class="ml-2" type="success">{{ $t('message.enabled') }}</el-tag>
            </div>
            <div v-else>
              <el-tag class="ml-2" type="info">{{ $t('message.disabled') }}</el-tag>
            </div>
          </template>
        </el-table-column>
        <el-table-column prop="sort" :label="$t('message.sort')"/>
        <el-table-column :label="$t('message.created_at')">
          <template #default="prop">
            {{ prop.row.created_at.split('T')[0] }}
          </template>
        </el-table-column>
        <el-table-column>
          <template #header>
            <el-input v-model="formData.search" size="default" :placeholder="$t('message.name')"/>
          </template>
          <template #default="scope">
            <el-button link type="warning" size="small" @click.prevent="onOpenEditDialog('edit', scope.row)">
              {{ $t('message.table.edit') }}
            </el-button>
            <el-button link type="danger" size="small" @click.prevent="deleteRow(scope.row.id)">
              {{ $t('message.table.delete') }}
            </el-button>
          </template>
        </el-table-column>
      </el-table>
      <adsDialog ref="openDialogRef" @refresh="getGroupCategory()"/>
    </div>
  </div>
</template>
<script lang="ts" setup>
import useGroupCategory from "/@/composables/useGroupCategory";
import { defineAsyncComponent } from "vue";

const adsDialog = defineAsyncComponent(() => import('/@/pages/operation/advertisement/dialog.vue'));
const {
  onOpenAddDialog,
  onOpenEditDialog,
  openDialogRef,
  getGroupCategory,
  formData,
  deleteRow,
} = useGroupCategory();
</script>