<template>
  <div class="system-user-dialog-container">
    <el-dialog @close="resetFields" v-model="formDialog.isShowDialog" :title="formDialog.title">
      <el-form :label-position="'top'"
               :model="formData"
               :rules="formRule"
               ref="ruleFormRef"
               class="grid md:grid-cols-2 grid-cols-1 gap-5"
      >
        <el-form-item prop="name" :label="$t('message.name')">
          <el-input type="text" v-model="formData.title"/>
        </el-form-item>
        <el-form-item prop="link" :label="$t('message.link')">
          <el-input type="text" v-model="formData.link"/>
        </el-form-item>
        <el-form-item prop="value" :label="$t('message.value')">
          <el-upload
              v-model:file-list="fileList"
              ref="upload"
              action="#"
              :auto-upload="false"
              :on-exceed="handleExceed"
              :on-remove="handleRemove"
              :on-change="handleChange"
              list-type="picture-card"
              accept=".jpeg,.jpg,.png,image/jpeg,image/png"
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
          <el-button type="primary" @click="onSubmit" :loading="isProcessing">
            {{ $t(formDialog.submit) }}
          </el-button>
        </div>
      </template>
    </el-dialog>
  </div>
</template>
<script setup lang="ts">
import { ref, reactive } from "vue";
import { IConfiguration } from "/@/models/IConfiguration";
import { useI18n } from "vue-i18n";
import formHelper, { IRule } from "/@/libraries/elementUiHelpers/formHelper";
import useVariable from "/@/composables/useVariables";
import uploadFileHelper from "/@/libraries/uploadFileHelper";
import useApi from "/@/api/api";
import EnumMessageType from "/@/models/enums/enumMessageType";
import { messageNotification } from "/@/libraries/elementUiHelpers/notificationHelper";
import EnumApiErrorCode from "/@/models/enums/enumApiErrorCode";

const fileList = ref([]);
const api = useApi();
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
});
const formDialog = reactive({
  title: '',
  submit: '',
  cancel: '',
  isShowDialog: false,
  type: '',
})
const emit = defineEmits(['refresh']);
emit('refresh');
const resetFields = () => {
     formData.title = '';
     formData.link = '';
     formData.image = '';
     formData.sort = 0;
     formDialog.isShowDialog = false;
}
const { isProcessing } = useVariable();
const rules: Record<string, IRule> = ({
  title: {required: true},
  link: {required: true},
  image: {required: true},
});
const formRule = formHelper.getRules(rules);
const openDialog = async (type: string, row: IConfiguration) => {
  if (type === 'edit') {
    fileList.value = [];
    // 模拟数据，实际请走接口
    fileList.value.push( { name: row.appName, url: row.value});
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

const submitProccess = async () => {
  isProcessing.value = true;
  await renderFile ()
  try{
    const request = {
      id: formData.id,
      title: formData.title,
      link: formData.link,
      image: file.value,
      sort: formData.sort,
    };
    const response = request.id !== 0 ? await api.updateConfiguration(request) : await api.addConfiguration(request);
    if (response.code === EnumApiErrorCode.success) {
      messageNotification(t('message.success'), EnumMessageType.Success);
      resetFields();
      isProcessing.value = false;
    }
  }catch(e){
    //TODO handle the exception
  }
  const onSubmit = formHelper.getSubmitFunction(submitProccess);
};
defineExpose({
  openDialog,
});
</script>