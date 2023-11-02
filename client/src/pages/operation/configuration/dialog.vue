<template>
  <div class="system-user-dialog-container">
    <el-dialog @close="resetFields" v-model="configuration.dialog.isShowDialog" :title="configuration.dialog.title">
      <el-form :label-position="'top'"
               :model="configuration"
               :rules="formRule"
               ref="ruleFormRef"
               class="grid md:grid-cols-2 grid-cols-1 gap-5"
      >
        <el-form-item prop="appName" :label="$t('message.appName')">
          <el-input type="text" v-model="configuration.appName"/>
        </el-form-item>
        <el-form-item prop="key" :label="$t('message.key')">
          <el-input type="text" v-model="configuration.key"/>
        </el-form-item>
        <el-form-item prop="type" :label="$t('message.type')">
          <el-select v-model="configuration.type" class="w-full" placeholder="Select">
            <el-option
                v-for="item in options"
                :key="item.value"
                :label="item.label"
                :value="item.value"
            />
          </el-select>
        </el-form-item>
        <el-form-item prop="value" :label="$t('message.value')">
          <div v-if="configuration.type === 'image'">
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
          </div>
           <div v-else class="w-full">
             <el-input type="text" v-model="configuration.value"/>
           </div>
        </el-form-item>
      </el-form>
      <template #footer>
        <div class="dialog-footer">
          <el-button type="primary" @click="onSubmit()" :loading="isProcessing">
            {{ $t(configuration.dialog.submit) }}
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
const configuration = reactive({
  id: 0,
  appName: '',
  key: '',
  type: 'image',
  value: '',
  sort: 0,
  dialog: {
    title: '',
    submit: '',
    cancel: '',
    isShowDialog: false,
    type: '',
  }
});
const emit = defineEmits(['refresh']);
const options = ref([
  {
    value: 'image',
    label: t('message.image'),
  },
  {
    value: 'text',
    label: t('message.text'),
  },
]);
const resetFields = () => {
     configuration.appName = '';
     configuration.key = '';
     configuration.type = '';
     configuration.value = '';
     configuration.sort = 0;
     configuration.dialog.isShowDialog = false;
}
const { isProcessing } = useVariable();
const rules: Record<string, IRule> = ({
  appName: {required: true},
  key: {required: true},
  type: {required: true},
  value: {required: false}
});
const formRule = formHelper.getRules(rules);
const openDialog = async (type: string, row: IConfiguration) => {
  if (type === 'edit') {
    fileList.value = [];
    // 模拟数据，实际请走接口
    fileList.value.push( { name: row.appName, url: row.value});
    Object.assign(configuration, row)
    configuration.dialog.title = t('message.table.edit');
    configuration.dialog.submit = t('message.table.submit');
  } else {
    configuration.dialog.title = t('message.table.add');
    configuration.dialog.submit = t('message.table.submit');
  }
  configuration.dialog.type = type;
  configuration.dialog.isShowDialog = true;
};

const onSubmit = async () => {
  isProcessing.value = true;
  await renderFile ()
  try{
    const request = {
      id: configuration.id,
      appName: configuration.appName,
      key: configuration.key,
      type: configuration.type,
      value: configuration.type === 'image' ? file.value : configuration.value,
      sort: configuration.sort,
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
  emit('refresh');
};
defineExpose({
  openDialog,
});
</script>