<template>
  <div class="layout-container layout-padding">
    <el-card shadow="hover" class="layout-padding-auto">
      <div class=" system-user-search p-3 flex justify-end">
        <el-button type="success" @click="onOpenAddDialog('add')">
          <el-icon>
            <ele-FolderAdd/>
          </el-icon>
          {{ $t('message.table.new') }}
        </el-button>
      </div>
      <el-table :data="formData.data" v-loading.lock="isLoading" >
        <el-table-column type="index" :label="$t('message.table.numberSign')" width="60"/>
        <el-table-column prop="name" :label="$t('message.name')"/>
        <el-table-column prop="key" :label="$t('message.key')" min-width="160"/>
        <el-table-column prop="sort" :label="$t('message.sort')"/>
        <el-table-column  :label="$t('message.is_visible')" min-width="120">
          <template #default="prop">
            <div v-if="prop.row.is_visible" >
              <el-tag class="ml-2" type="success">{{ $t('message.enabled') }}</el-tag>
            </div>
            <div v-else>
              <el-tag class="ml-2" type="info">{{ $t('message.disabled') }}</el-tag>
            </div>
          </template>
        </el-table-column>
        <el-table-column :label="$t('message.created_at')" min-width="120">
           <template #default="prop">
             {{ prop.row.created_at.split('T')[0] }}
           </template>
        </el-table-column>
        <el-table-column min-width="120">
          <template #header>
            <el-input v-model="formData.search" size="default" :placeholder="$t('message.name')"/>
          </template>
          <template #default="scope">
            <el-button link type="warning" size="small" @click.prevent="onOpenEditDialog('edit', scope.row)">
              {{ $t('message.table.edit') }}
            </el-button>
            <el-button link type="danger" size="small" @click.prevent="deleteRow(scope.row)">
              {{ $t('message.table.delete') }}
            </el-button>
          </template>
        </el-table-column>
      </el-table>
      <el-pagination class="mt15"
          v-model:current-page="formData.paginate.page"
          v-model:page-size="formData.paginate.pageSize"
          :page-sizes="[10, 25, 50, 75, 100]"
          :small="true"
          background
          layout="total, sizes, prev, pager, next, jumper"
          @size-change="handleSizeChange"
          @current-change="handleCurrentChange"
          :total="formData.paginate.total"
      />
      <categoryDialog ref="openDialogRef" @refresh="getCategory()"/>
    </el-card>
  </div>
</template>
<script lang="ts" setup>
import { defineAsyncComponent } from "vue";
import userCategory from "/@/composables/userCategory";

const categoryDialog = defineAsyncComponent(() => import('/@/pages/operation/category/dialog.vue'));
const {
  isLoading,
  onOpenAddDialog,
  onOpenEditDialog,
  openDialogRef,
  getCategory,
  formData,
  deleteRow,
  handleSizeChange,
  handleCurrentChange,
} = userCategory();
</script>