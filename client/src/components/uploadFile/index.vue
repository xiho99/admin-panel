<template>
  <div class="h100 priUp">
    <el-upload :on-success="uploadSuccess" :class="{maxFile:state.fileList.length >= limit}" class="h100"
               action="/api/uploadFile" :limit="limit" v-model:file-list="state.fileList" list-type="picture-card"
               :on-preview="handlePictureCardPreview">
      <el-icon class="avatar-uploader-icon">
        <Plus/>
      </el-icon>
    </el-upload>
    <el-dialog v-model="dialogVisible">
      <img class="w-full" :src="dialogImageUrl" alt="Preview Image"/>
    </el-dialog>
  </div>
</template>

<script setup lang="ts" name="uploadFile">
import { onMounted, reactive, ref, watch } from 'vue';
import { Plus } from '@element-plus/icons-vue'

const props = defineProps({
  limit: {
    type: Number,
    default: 1
  },
  getFileStr: {
    type: String,
    defautl: () => ''
  }
})
// 定义子组件向父组件传值/事件
const emit = defineEmits(['update:getFileStr']);
const state = reactive({
  fileList: [],
})
const uploadSuccess = (e: any) => {
  // state.fileList[0] = {url:e.data.url,name:'a.jpg'};
  // eslint-disable-next-line no-console
  console.log(e);
}
onMounted(() => {
  if (props.getFileStr) {
    let d = props.getFileStr?.split(',');
    d?.forEach(er => {
      state.fileList.push({ url: er })
    })
  }
})
// 内容发生改变
watch(() => state.fileList, (e) => {
  let imgStr: any[] = [];
  e.forEach(e => {
    if (e.response?.code == 200) {
      imgStr.push(e.response?.data?.url);
    } else if (!e.response) {
      imgStr.push(e?.url);
    }
  })
  emit('update:getFileStr', imgStr.join(','));
}, { deep: true })
const dialogImageUrl = ref('');
const dialogVisible = ref(false);
const handlePictureCardPreview = (uploadFile: { url: string; }) => {
  dialogImageUrl.value = uploadFile.url!
  dialogVisible.value = true
}
</script>

<style>
.avatar-uploader .el-upload {

  border-radius: 6px;
  cursor: pointer;
  position: relative;
  overflow: hidden;
  transition: var(--el-transition-duration-fast);
}

.avatar-uploader .el-upload:hover {
  border-color: #ccc;
}

.priUp {
  height: 178px;
}

.el-icon.avatar-uploader-icon {
  font-size: 28px;
  color: #8c939d;
  width: 178px;
  height: 178px;
  text-align: center;
}

.maxFile .el-upload.el-upload--picture-card {
  display: none;
}
</style>