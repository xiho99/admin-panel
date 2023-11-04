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
        <el-table-column type="expand" v-loading="isLoading">
          <template #default="props">
              <el-table :data="props.row.group">
                <el-table-column :label="$t('message.image')">
                  <template #default="prop">
                    <el-image class="h-14 rounded" :src="prop.row.image" alt="loading.."/>
                  </template>
                </el-table-column>
                <el-table-column :label="$t('message.name')" prop="name" />
                <el-table-column :label="$t('message.router.link')">
                  <template #default="prop">
                    <el-link :underline="false" type="primary" :href="prop.row.link" target="_blank">
                      {{ prop.row.link }}
                    </el-link>
                  </template>
                </el-table-column>
                <el-table-column :label="$t('message.sort')" prop="sort" />
                <el-table-column :label="$t('message.created_at')" prop="created_at" />
                <el-table-column>
                  <template #default="prop">
                    <el-button link type="warning" size="small" @click.prevent="onOpenEditDialog('edit', prop.row)">
                      {{ $t('message.table.edit') }}
                    </el-button>
                    <el-button link type="danger" size="small" @click.prevent="deleteRow(prop.row)">
                      {{ $t('message.table.delete') }}
                    </el-button>
                  </template>
                </el-table-column>
              </el-table>
          </template>
        </el-table-column>
        <el-table-column prop="name" :label="$t('message.groupName')"/>
        <el-table-column prop="key" :label="$t('message.key')"/>
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
        <el-table-column prop="sort" :label="$t('message.sort')" width="80"/>
        <el-table-column prop="created_at" :label="$t('message.created_at')"/>
      </el-table>
      <groupCatDialog ref="openDialogRef" :categories="formData.data" @refresh="getGroupCategory()"/>
    </div>
  </div>
</template>
<script lang="ts" setup>
import useGroupCategory from "/@/composables/useGroupCategory";
import { defineAsyncComponent } from "vue";

const groupCatDialog = defineAsyncComponent(() => import('/@/pages/operation/group-category/dialog.vue'));
const {
  isLoading,
  onOpenAddDialog,
  onOpenEditDialog,
  openDialogRef,
  getGroupCategory,
  formData,
  deleteRow,
} = useGroupCategory();
</script>