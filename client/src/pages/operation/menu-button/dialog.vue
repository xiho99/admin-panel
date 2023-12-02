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
        <el-form-item prop="link" :label="$t('message.router.link')">
          <el-input type="text" v-model="formData.link"/>
        </el-form-item>
        <el-form-item prop="sort" :label="$t('message.sort')">
          <el-input type="number" v-model="formData.sort"/>
        </el-form-item>
        <el-form-item>
          <div class="flex gap-10">
            <div class="flex gap-5">
              <span>{{ $t('message.is_visible') }}</span>
              <el-switch
                  v-model="formData.is_visible"
                  :active-action-icon="View"
                  :inactive-action-icon="Hide"
              />
            </div>
            <div class="flex gap-5" >
              <span>{{ $t('message.buttonColor') }}</span>
              <el-color-picker v-model="formData.color" />
            </div>
          </div>
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
const { isProcessing, ruleFormRef } = useVariable();
const { t } = useI18n();
const formData = reactive({
  id: 0,
  name: '',
  link: '',
  color: '#409EFF',
  is_visible: true,
  sort: 0,
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
  formData.link = '';
  formData.sort = 0;
  formData.color= '#409EFF'
  formData.is_visible = true;
}
const rules: Record<string, IRule> = ({
  name: { required: true },
  link: { required: true },
  type: { required: true },
  sort: { required: true },
  image: { required: true },
});
const openDialog = async (type: string, row: IMenu) => {
  if (type === 'edit') {
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
      link: formData.link,
      color: formData.color,
      is_visible: formData.is_visible,
      sort: formData.sort,
    };
    const response = await api.addMenuButton(request);
    if (response.code === EnumApiErrorCode.success) {
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