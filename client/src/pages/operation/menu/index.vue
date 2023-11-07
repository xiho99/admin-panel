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
      <el-table :data="formData.data" v-loading.lock="isLoading">
        <el-table-column :label="$t('message.image')">
          <template #default="prop">
            <el-image  v-show="prop.row.type === 'icon'" class="h-14 rounded" :src="prop.row.image" alt="loading.."/>
          </template>
        </el-table-column>
        <el-table-column prop="name" :label="$t('message.name')"/>
        <el-table-column prop="link" :label="$t('message.router.link')">
          <template #default="prop">
            <el-link type="primary" :underline="false"> {{ prop.row.link }}</el-link>
          </template>
        </el-table-column>
        <el-table-column prop="color" :label="$t('message.color')">
          <template #default="prop">
            <span :style="{ color: prop.row.color }">
              {{ prop.row.color }}
            </span>
          </template>
        </el-table-column>
        <el-table-column prop="type" :label="$t('message.type')"/>
        <el-table-column prop="sort" :label="$t('message.sort')"/>
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
      <div class=" mt-5 flex justify-between text-2xl items-center">
        <el-pagination
            v-model:current-page="formData.paginate.currentPage"
            v-model:page-size="formData.paginate.pageSize"
            :page-sizes="[10, 25, 50, 75, 100]"
            :small="true"
            :background="true"
            layout="sizes, prev, pager, next"
            @size-change="handleSizeChange"
            @current-change="handleCurrentChange"
            :total="formData.paginate.total"/>
      </div>
      <menuDialog ref="openDialogRef" @refresh="getMenuItem()"/>
    </div>
  </div>
</template>
<script lang="ts" setup>
import { defineAsyncComponent } from "vue";
import useMenuItem from "/@/composables/useMenuItem";

const menuDialog = defineAsyncComponent(() => import('/@/pages/operation/menu/dialog.vue'));
const {
  isLoading,
  onOpenAddDialog,
  onOpenEditDialog,
  openDialogRef,
  getMenuItem,
  formData,
  deleteRow,
  handleSizeChange,
  handleCurrentChange,
} = useMenuItem();
</script>