<template>
  <div class="system-user-dialog-container">
    <el-dialog @close="resetFields" destroy-on-close v-model="formDialog.isShowDialog" :title="formDialog.title">
      <el-form :label-position="'top'"
               ref="ruleFormRef"
               :model="formData"
               :rules="adsRules"
               class="grid md:grid-cols-2 grid-cols-1 gap-5"
      >
        <el-form-item prop="title" :label="$t('message.name')">
          <el-input type="text" v-model="formData.title"/>
        </el-form-item>
        <el-form-item prop="link" :label="$t('message.router.link')">
          <el-input type="text" v-model="formData.link"/>
        </el-form-item>
        <el-form-item prop="sort" :label="$t('message.sort')">
          <el-input type="number" v-model="formData.sort"/>
        </el-form-item>
        <el-form-item :label="$t('message.is_visible')">
          <el-switch
              v-model="formData.is_visible"
              :active-action-icon="View"
              :inactive-action-icon="Hide"
          />
        </el-form-item>
        <el-form-item :label="$t('message.image')">
          <uploadFile  v-model:get-file-str="formData.image"/>
        </el-form-item>
      </el-form>
      <template #footer>
        <div class="dialog-footer">
          <el-button type="primary" @click="onSubmit(ruleFormRef)" :loading="isProcessing">
            {{ $t(formDialog.submit) }}
          </el-button>
        </div>
      </template>
    </el-dialog>
  </div>
</template>
<script setup lang="ts">
import { defineAsyncComponent, reactive } from "vue";
import { useI18n } from "vue-i18n";
import formHelper, { IRule } from "/@/libraries/elementUiHelpers/formHelper";
import useVariable from "/@/composables/useVariables";
import useApi from "/@/api/api";
import EnumMessageType from "/@/models/enums/enumMessageType";
import { messageNotification } from "/@/libraries/elementUiHelpers/notificationHelper";
import EnumApiErrorCode from "/@/models/enums/enumApiErrorCode";
import { IAds } from "/@/models/IAds";
import { Hide, View } from '@element-plus/icons-vue'

const uploadFile = defineAsyncComponent(() => import('/@/components/uploadFile/index.vue'));
const api = useApi();
const { isProcessing, ruleFormRef } = useVariable();
const { t } = useI18n();
const formData = reactive({
  id: null,
  title: '',
  link: '',
  image: '',
  sort: 0,
  type: '',
  color: 0,
  is_visible: true,
});
const formDialog = reactive({
  title: '',
  submit: '',
  cancel: '',
  isShowDialog: false,
  type: '',
})
const emit = defineEmits(['refresh']);
const resetFields = () => {
  formData.id = null;
  formData.title = '';
  formData.link = '';
  formData.image = '';
  formData.type = '';
  formData.sort = 0;
  formData.is_visible = true;
}
const rules: Record<string, IRule> = ({
  title: { required: true },
  link: { required: true },
  image: { required: true },
});
const openDialog = async (type: string, row: IAds) => {
  if (type === 'edit') {
    // fileList.value.push({ name: row.title, url: row.image });
    Object.assign(formData, row)
    formDialog.title = t('message.table.edit');
    formDialog.submit = t('message.table.submit');
  } else {
    formDialog.title = t('message.table.add');
    formDialog.submit = t('message.table.submit');
  }
  formDialog.type = type;
  formDialog.isShowDialog = true;
};

const submitProcess = async () => {
  isProcessing.value = true;
  try {
    const request = {
      id: formData.id,
      title: formData.title,
      link: formData.link,
      type: formData.type,
      image: formData.image,
      sort: formData.sort,
      is_visible: formData.is_visible,
    };
    const response = await api.addAds(request);
    if (response.code !== EnumApiErrorCode.success) {
      messageNotification(t(response.message), EnumMessageType.Error);
    } else {
      messageNotification(t('message.success'), EnumMessageType.Success);
      resetFields();
      formDialog.isShowDialog = false;
      emit('refresh');
    }
  } catch (e) {
    //TODO handle the exception
  }
  isProcessing.value = false;
};

const adsRules = formHelper.getRules(rules);
const onSubmit = formHelper.getSubmitFunction(submitProcess);
defineExpose({
  openDialog,
});
</script>