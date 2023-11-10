<template>
  <div class="system-user-dialog-container">
    <el-dialog @close="resetFields" destroy-on-close v-model="formDialog.isShowDialog" :title="formDialog.title">
      <el-form :label-position="'top'"
               ref="ruleFormRef"
               :model="formData"
               :rules="menuItemRules"
               class="grid md:grid-cols-2 grid-cols-1 gap-5"
      >
        <el-form-item prop="name" :label="$t('message.name')">
          <el-input type="text" v-model="formData.name"/>
        </el-form-item>
        <el-form-item prop="key" :label="$t('message.key')">
          <el-input type="text" v-model="formData.key"/>
        </el-form-item>
        <el-form-item prop="sort" :label="$t('message.sort')">
          <el-input type="number" v-model="formData.sort"/>
        </el-form-item>
        <el-form-item prop="is_visible" :label="$t('message.is_visible')">
          <el-switch
              v-model="formData.is_visible"
              :active-action-icon="View"
              :inactive-action-icon="Hide"
          />
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
import { reactive } from "vue";
import { useI18n } from "vue-i18n";
import formHelper, { IRule } from "/@/libraries/elementUiHelpers/formHelper";
import useVariable from "/@/composables/useVariables";
import useApi from "/@/api/api";
import EnumMessageType from "/@/models/enums/enumMessageType";
import { messageNotification } from "/@/libraries/elementUiHelpers/notificationHelper";
import EnumApiErrorCode from "/@/models/enums/enumApiErrorCode";
import { IMenu } from "/@/models/IMenu";
import { Hide, View } from '@element-plus/icons-vue'

const api = useApi();
const { isProcessing, ruleFormRef, fileList } = useVariable();
const { t } = useI18n();
const formData = reactive({
  id: 0,
  category_id: null,
  name: '',
  key: '',
  sort: 0,
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
  formData.id = 0;
  formData.name = '';
  formData.key = '';
  formData.sort = 0;
  formData.is_visible = true;
}
const rules: Record<string, IRule> = ({
  name: { required: true },
  category_id: { required: true },
  link: { required: true },
  sort: { required: true },
});
const openDialog = async (type: string, row: IMenu) => {
  if (type === 'edit') {
    fileList.value = [];
    Object.assign(formData, row);
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
      name: formData.name,
      key: formData.key,
      is_visible: formData.is_visible,
      sort: formData.sort,
    };
    const response = request.id !== 0 ? await api.updateCategory(request) : await api.addCategory(request);
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
const menuItemRules = formHelper.getRules(rules);
const onSubmit = formHelper.getSubmitFunction(submitProcess);
defineExpose({
  openDialog,
});
</script>