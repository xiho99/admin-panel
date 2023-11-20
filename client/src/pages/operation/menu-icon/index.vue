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
      <el-table :data="formData.data" v-loading.lock="isLoading">
        <el-table-column type="index" :label="$t('message.table.numberSign')" width="60"/>
        <el-table-column :label="$t('message.image')">
          <template #default="prop">
            <el-image class="h-14 rounded" :src="prop.row.image" alt="loading.."/>
          </template>
        </el-table-column>
        <el-table-column prop="name" :label="$t('message.name')" min-width="120"/>
        <el-table-column prop="link" :label="$t('message.router.link')" min-width="140">
          <template #default="prop">
            <el-link type="primary" :underline="false"> {{ prop.row.link }}</el-link>
          </template>
        </el-table-column>
        <el-table-column prop="sort" :label="$t('message.sort')"/>
        <el-table-column  :label="$t('message.is_visible')" min-width="120">
          <template #default="prop">
            <div v-if="prop.row.is_visible">
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
          :background="true"
          layout="sizes, prev, pager, next"
          @size-change="handleSizeChange"
          @current-change="handleCurrentChange"
          :total="formData.paginate.total"/>
      <menuDialog ref="openDialogRef" @refresh="getMenuIcon()"/>
    </el-card>
  </div>
</template>
<script lang="ts" setup>
import { defineAsyncComponent } from "vue";
import useMenuIcon from "/src/composables/useMenuIcon";

const menuDialog = defineAsyncComponent(() => import('/@/pages/operation/menu-icon/dialog.vue'));
const {
  isLoading,
  onOpenAddDialog,
  onOpenEditDialog,
  openDialogRef,
  getMenuIcon,
  formData,
  deleteRow,
  handleSizeChange,
  handleCurrentChange,
} = useMenuIcon();
</script>