<template>
  <div class=" layout-pd">
    <el-card shadow="hover">
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
        <el-table-column :label="$t('message.router.ads')">
          <template #default="prop">
            <el-image class="h-14 rounded" :src="prop.row.image" alt="loading.."/>
          </template>
        </el-table-column>
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
        <el-table-column prop="sort" :label="$t('message.sort')"/>
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
            <el-button link type="danger" size="small" @click.prevent="deleteRow(scope.row.id)">
              {{ $t('message.table.delete') }}
            </el-button>
          </template>
        </el-table-column>
      </el-table>
      <div class="mt-5">
        <el-pagination
            v-model:current-page="formData.paginate.page"
            v-model:page-size="formData.paginate.pageSize"
            :page-sizes="[10, 25, 50, 75, 100]"
            :small="true"
            :background="true"
            layout="sizes, prev, pager, next"
            @size-change="handleSizeChange"
            @current-change="handleCurrentChange"
            :total="formData.paginate.total"/>
      </div>
      <adsDialog ref="openDialogRef" @refresh="getAds()"/>
    </el-card>
  </div>
</template>
<script lang="ts" setup>
import useAdvertisement from "/@/composables/useAdvertisement";
import { defineAsyncComponent } from "vue";

const adsDialog = defineAsyncComponent(() => import('/@/pages/operation/advertisement/dialog.vue'));
const {
  isLoading,
  onOpenAddDialog,
  onOpenEditDialog,
  openDialogRef,
  getAds,
  formData,
  deleteRow,
  handleSizeChange,
  handleCurrentChange,
} = useAdvertisement();
</script>