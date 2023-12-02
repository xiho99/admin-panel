<template>
  <el-upload v-if="!props.multipleUpload"
      ref="upload"
      action="#"
      v-model:file-list="state.fileList"
      :auto-upload="false"
      :on-exceed="handleExceed"
      :on-remove="handleRemove"
      :on-change="handleChange"
      :on-preview="handlePreview"
      list-type="picture-card" accept="image/*" :limit="1">
    <el-icon>
      <ele-Upload/>
    </el-icon>
  </el-upload>
  <el-upload v-else
      ref="upload"
      action="#"
      :auto-upload="false"
      :on-preview="handlePreview"
      :on-exceed="handleExceed"
      :on-remove="handleRemove"
      :on-change="multiChange"
      list-type="picture-card"
      multiple
      :limit="10"
      accept=".jpg,.jpeg,.png,.JPG,.JPEG,.gif"
  >
    <Icon name="ep:plus" size="20px"/>
  </el-upload>
  <el-dialog v-model="dialogVisible">
    <img class="w-full" :src="dialogImageUrl" alt="Preview Image"/>
  </el-dialog>
</template>
<script setup lang="ts">
import uploadFileHelper from "/@/libraries/uploadFileHelper";
import { onMounted, reactive, ref } from "vue";

const props = defineProps({
  multipleUpload: {
    type: Boolean,
    default: () => false,
  },
  fileList: {
    type: Array,
    default: () => [],
  },
});
const state = reactive({
  fileList: []
})
onMounted(() => {
  state.fileList = props.fileList as any;
})
const {
  upload,
  handleExceed,
  handleChange,
  multiChange,
  handleRemove,
  renderFile,
  renderFiles
} = uploadFileHelper;
const dialogImageUrl = ref('');
const dialogVisible = ref(false);
const handlePreview = (uploadFile: { url: string; }) => {
  dialogImageUrl.value = uploadFile.url!
  dialogVisible.value = true
}
defineExpose({
  renderFile,
  renderFiles,
});
</script>