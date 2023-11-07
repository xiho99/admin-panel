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
          <el-upload
              v-model:file-list="fileList"
              ref="upload"
              action="#"
              :auto-upload="false"
              :on-exceed="handleExceed"
              :on-remove="handleRemove"
              :on-change="handleChange"
              list-type="picture-card"
              accept=".jpeg,.jpg,.png,.gif,image/jpeg,image/png"
              :limit="1"
          >
            <el-icon>
              <ele-Upload/>
            </el-icon>
          </el-upload>
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
import formHelper, { IRule } from "/src/libraries/elementUiHelpers/formHelper";
import useVariable from "/src/composables/useVariables";
import uploadFileHelper from "/src/libraries/uploadFileHelper";
import useApi from "/src/api/api";
import EnumMessageType from "/src/models/enums/enumMessageType";
import { messageNotification } from "/src/libraries/elementUiHelpers/notificationHelper";
import EnumApiErrorCode from "/src/models/enums/enumApiErrorCode";
import { IAds } from "/@/models/IAds";
import { Hide, View } from '@element-plus/icons-vue'

const api = useApi();
const { isProcessing, ruleFormRef, fileList } = useVariable();
const { t } = useI18n();
const {
  handleExceed, file, upload,
  handleChange, handleRemove,
  renderFile,
} = uploadFileHelper;
const formData = reactive({
  id: 0,
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
  formData.id = 0;
  formData.title = '';
  formData.link = '';
  formData.image = '';
  formData.type = '';
  formData.sort = 0;
  formData.is_visible = true;
  file.value = '';
  fileList.value = [];
}
const rules: Record<string, IRule> = ({
  title: { required: true },
  link: { required: true },
  image: { required: true },
});
const openDialog = async (type: string, row: IAds) => {
  if (type === 'edit') {
    fileList.value = [];
    // 模拟数据，实际请走接口
    fileList.value.push({ name: row.title, url: row.image });
    Object.assign(formData, row)
    formData.image = ''
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
  if (!file.value && formDialog.type !== 'edit') {
    isProcessing.value = false;
    return messageNotification(t('message.imageRequired'), EnumMessageType.Error);
  }
  if (file.value) {
    await renderFile()
  }
  try {
    const request = {
      id: formData.id,
      title: formData.title,
      link: formData.link,
      type: formData.type,
      image: file.value ?? formData.image,
      sort: formData.sort,
      is_visible: formData.is_visible,
    };
    const response = request.id !== 0 ? await api.updateAds(request) : await api.addAds(request);
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