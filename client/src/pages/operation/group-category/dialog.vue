<template>
  <div class="system-user-dialog-container">
    <el-dialog @close="resetFields" destroy-on-close v-model="formDialog.isShowDialog" :title="formDialog.title">
      <el-form :label-position="'top'"
               ref="ruleFormRef"
               :model="formData"
               :rules="groupCatRules"
               class="grid md:grid-cols-2 grid-cols-1 gap-5"
      >
        <el-form-item prop="cat_id" :label="$t('message.category')">
          <el-select v-model="formData.cat_id" class="w-full" placeholder="Select">
            <el-option
                v-for="(item, index) in props.categories"
                :key="index"
                :label="`${item.name} (${item.key})`"
                :value="item.id"
            />
          </el-select>
        </el-form-item>
        <el-form-item prop="name" :label="$t('message.name')">
          <el-input type="text" v-model="formData.name"/>
        </el-form-item>
        <el-form-item prop="link" :label="$t('message.router.link')">
          <el-input type="text" v-model="formData.link"/>
        </el-form-item>
        <el-form-item prop="sort" :label="$t('message.sort')">
           <div class="flex gap-5">
             <div><el-input type="number" v-model="formData.sort"/></div>
             <div class="flex gap-5">
               <span>{{ $t('message.is_visible') }}</span>
                <el-switch
                    v-model="formData.is_visible"
                    :active-action-icon="View"
                    :inactive-action-icon="Hide"
                />
              </div>
           </div>
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
import { Hide, View } from '@element-plus/icons-vue'
import { IGroup } from "/@/models/IGroupCategory";

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
  cat_id: null,
  name: '',
  link: '',
  image: '',
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
const props = defineProps(['categories']);
const emit = defineEmits(['refresh']);
const resetFields = () => {
  formData.id = 0;
  formData.cat_id = null;
  formData.name = '';
  formData.link = '';
  formData.image = '';
  formData.sort = 0;
  formData.is_visible = true;
  file.value = '';
  fileList.value = [];
}
const rules: Record<string, IRule> = ({
  name: { required: true },
  link: { required: true },
  cat_id: { required: true },
  sort: { required: true },
  image: { required: false },
});
const openDialog = async (type: string, row: IGroup) => {
  if (type === 'edit') {
    fileList.value = [];
    // 模拟数据，实际请走接口
    fileList.value.push({ name: row.name, url: row.image });
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
      cat_id: formData.cat_id,
      name: formData.name,
      link: formData.link,
      image: file.value ?? formData.image,
      sort: formData.sort,
      is_visible: formData.is_visible,
    };
    const response = request.id !== 0 ? await api.updateGroupCategory(request) : await api.addGroupCategory(request);
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

const groupCatRules = formHelper.getRules(rules);
const onSubmit = formHelper.getSubmitFunction(submitProcess);
defineExpose({
  openDialog,
});
</script>