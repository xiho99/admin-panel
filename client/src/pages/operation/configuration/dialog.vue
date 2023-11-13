<template>
  <div class="system-user-dialog-container">
    <el-dialog @close="resetFields"  destroy-on-close v-model="configuration.dialog.isShowDialog" :title="configuration.dialog.title">
      <el-form :label-position="'top'"
               ref="ruleFormRef"
               :model="configuration"
               :rules="configRules"
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
            <el-option value="text" :label="$t('message.text')"/>
            <el-option value="colorPicker" :label="$t('message.colorPicker')"/>
            <el-option value="image" :label="$t('message.image')"/>
          </el-select>
        </el-form-item>
        <el-form-item prop="sort" :label="$t('message.sort')">
          <el-input type="number" v-model="configuration.sort"/>
        </el-form-item>
        <el-form-item prop="value" :label="configuration.type? $t('message.value') : null">
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
          <div v-if="configuration.type === 'colorPicker'">
            <el-color-picker v-model="configuration.value" />
          </div>
          <div  v-if="configuration.type === 'text'" class="w-full">
            <el-input type="text" v-model="configuration.value"/>
          </div>
        </el-form-item>
        <el-form-item :label="$t('message.is_visible')">
          <div class="flex gap-5">
            <el-switch
                v-model="configuration.is_visible"
                :active-action-icon="View"
                :inactive-action-icon="Hide"
            />
          </div>
        </el-form-item>
      </el-form>
      <template #footer>
        <div class="dialog-footer">
          <el-button type="primary" @click="onSubmit(ruleFormRef)" :loading="isProcessing">
            {{ $t(configuration.dialog.submit) }}
          </el-button>
        </div>
      </template>
    </el-dialog>
  </div>
</template>
<script setup lang="ts">
import { reactive } from "vue";
import { IConfiguration } from "/@/models/IConfiguration";
import { Hide, View } from "@element-plus/icons-vue";
import { useI18n } from "vue-i18n";
import formHelper, { IRule } from "/@/libraries/elementUiHelpers/formHelper";
import useVariable from "/@/composables/useVariables";
import uploadFileHelper from "/@/libraries/uploadFileHelper";
import useApi from "/@/api/api";
import EnumMessageType from "/@/models/enums/enumMessageType";
import { messageNotification } from "/@/libraries/elementUiHelpers/notificationHelper";
import EnumApiErrorCode from "/@/models/enums/enumApiErrorCode";

const api = useApi();
const { t } = useI18n();
const {
  handleExceed, file, upload,
  handleChange, handleRemove,
  renderFile,
} = uploadFileHelper;
const { isProcessing, ruleFormRef, fileList } = useVariable();
const configuration = reactive({
  id: 0,
  appName: '',
  key: '',
  type: '',
  value: '',
  sort: 0,
  is_visible: true,
  dialog: {
    title: '',
    submit: '',
    cancel: '',
    isShowDialog: false,
    type: '',
  }
});
const emit = defineEmits(['refresh']);
const resetFields = () => {
  configuration.id = 0;
  configuration.appName = '';
  configuration.key = '';
  configuration.type = '';
  configuration.value = '';
  configuration.is_visible = true;
  configuration.sort = 0;
  configuration.dialog.isShowDialog = false;
  file.value = '';
  fileList.value = []
}
const rules: Record<string, IRule> = ({
  appName: { required: true },
  key: { required: true },
  type: { required: true },
  sort: { required: true },
  value: { required: false }
});
const openDialog = async (type: string, row: IConfiguration) => {
  if (type === 'edit') {
    fileList.value = [];
    file.value = '';
    // 模拟数据，实际请走接口
    fileList.value.push({ name: row.appName, url: row.value });
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

const submitProcess = async () => {
  isProcessing.value = true;
  await renderFile()
  try {
    const request = {
      id: configuration.id,
      appName: configuration.appName,
      key: configuration.key,
      type: configuration.type,
      value: configuration.type === 'image' ? file.value : configuration.value,
      sort: configuration.sort,
      is_visible: configuration.is_visible,
    };
    const response = request.id !== 0 ? await api.updateConfiguration(request) : await api.addConfiguration(request);
    if (response.code !== EnumApiErrorCode.success) {
      messageNotification(t(response.message), EnumMessageType.Error);
    } else {
      messageNotification(t('message.success'), EnumMessageType.Success);
      resetFields();
      configuration.dialog.isShowDialog = false;
      emit('refresh');
    }
  } catch (e) {
    //TODO handle the exception
  }
  isProcessing.value = false;
};
const configRules = formHelper.getRules(rules);
const onSubmit = formHelper.getSubmitFunction(submitProcess);
defineExpose({
  openDialog,
});
</script>