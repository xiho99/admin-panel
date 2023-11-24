<template>
  <div class="editor-container">
    <Toolbar :editor="editorRef" :mode="mode" />
    <Editor :mode="mode" :defaultConfig="state.editorConfig" :style="{ height }" @change="handleEditorChange"
            v-model="state.editorVal"
            @onCreated="handleCreated" @onChange="handleChange"/>
  </div>
</template>

<script setup lang="ts" name="wngEditor">
// https://www.wangeditor.com/v5/for-frame.html#vue3
import { i18nChangeLanguage } from '@wangeditor/editor'
import '@wangeditor/editor/dist/css/style.css';
import { reactive, shallowRef, watch, onBeforeUnmount } from 'vue';
import { IDomEditor } from '@wangeditor/editor';
import { Toolbar, Editor } from '@wangeditor/editor-for-vue';
import { useThemeConfig} from "/@/stores/themeConfig";

const themeConfig = useThemeConfig();
i18nChangeLanguage(themeConfig.themeConfig.globalI18n);
// 定义父组件传过来的值
const props = defineProps({
  // 是否禁用
  disable: {
    type: Boolean,
    default: () => false,
  },
  // 内容框默认 placeholder
  placeholder: {
    type: String,
    default: () => 'Please enter content...',
  },
  // 内容框默认 placeholder
  uploadImgServer: {
    type: String,
    default: () => '',
  },

  // https://www.wangeditor.com/v5/getting-started.html#mode-%E6%A8%A1%E5%BC%8F
  // 模式，可选 <default|simple>，默认 default
  mode: {
    type: String,
    default: () => 'default',
  },
  // 高度
  height: {
    type: String,
    default: () => '310px',
  },
  // 双向绑定，用于获取 editor.getHtml()
  getHtml: String,
  // 双向绑定，用于获取 editor.getText()
  getText: String,
});

// 定义子组件向父组件传值/事件
const emit = defineEmits(['update:getHtml', 'update:getText']);

const handleEditorChange = (content, e = null) => {
  // 处理编辑器内容变化
  // eslint-disable-next-line no-console
  console.log(content, e)
}
// 定义变量内容
const editorRef = shallowRef();
const state = reactive({
  editorConfig: {
    placeholder: props.placeholder,
    MENU_CONF: {
      uploadImage: {
        server: 'api/uploadFile',
        timeout: 5 * 1000, // 5s,
        fieldName: 'file',
        meta: { token: '', a: 100 },
        metaWithUrl: false, // join params to url
        headers: { Accept: 'text/x-json' },

        maxFileSize: 10 * 1024 * 1024, // 10M

        base64LimitSize: 1 * 1024, // insert base64 format, if file's size less than 5kb
        onBeforeUpload(file) {
          return file
        },
        onProgress(progress) {
        },
        onSuccess(file, res) {
        },
        onFailed(file, res) {
          alert(res.message)
        },
        onError(file, err, res) {
          alert(err.message)
        },
        customInsert(res, insertFn) { // TS 语法
          insertFn(res.data?.url, res.data?.url, '')
        }
      },
      uploadVideo: {
        server: 'api/uploadFile',
        fieldName: "file",
        // 单个文件的最大体积限制，默认为 10M
        maxFileSize: 20 * 1024 * 1024, // 5M
        // 最多可上传几个文件，默认为 5
        maxNumberOfFiles: 3,
        // 选择文件时的类型限制，默认为 ['video/*'] 。如不想限制，则设置为 []
        allowedFileTypes: ["video/*"],
        // 自定义上传参数，例如传递验证的 token 等。参数会被添加到 formData 中，一起上传到服务端。
        meta: { dataKey: 0, bizType: "video" },
        // 将 meta 拼接到 url 参数中，默认 false
        metaWithUrl: false,
        // 自定义增加 http  header
        // headers: {
        //   Accept: "text/x-json",
        //   otherKey: "xxx"
        // },
        // 跨域是否传递 cookie ，默认为 false
        withCredentials: true,
        // 超时时间，默认为 30 秒
        timeout: 30 * 1000, // 15 秒
        // 上传进度的回调函数
        onProgress(progress) {
          console.log("progress", progress);
        },
        // 单个文件上传成功之后
        onSuccess(file, res) {
          console.log(`${ file.name } 上传成功`, res);
        },
        // 单个文件上传失败
        onFailed(file, res) {
          console.log(`${ file.name } 上传失败`, res);
        },
        // 上传错误，或者触发 timeout 超时
        onError(file, err, res) {
          console.log(`${ file.name } 上传出错`, err, res);
        },
        // 上传完成处理方法
        customInsert: function (result, insertVideo) {
          if (result.code === 200) {
            insertVideo(result.data?.url);
          }
        },
      }
    }
  },
  editorVal: props.getHtml,
});

// 编辑器回调函数
const handleCreated = (editor: IDomEditor) => {
  editorRef.value = editor;
};
// 编辑器内容改变时
const handleChange = (editor: IDomEditor) => {
  emit('update:getHtml', editor.getHtml());
  emit('update:getText', editor.getText());
};
// 页面销毁时
onBeforeUnmount(() => {
  const editor = editorRef.value;
  if (editor == null) return;
  editor.destroy();
});
// 监听是否禁用改变
// https://gitee.com/lyt-top/vue-next-admin/issues/I4LM7I
watch(
    () => props.disable,
    (bool) => {
      const editor = editorRef.value;
      if (editor == null) return;
      bool ? editor.disable() : editor.enable();
    },
    {
      deep: true,
    }
);
// 监听双向绑定值改变，用于回显
watch(
    () => props.getHtml,
    (val) => {
      state.editorVal = val;
    },
    {
      deep: true,
    }
);
</script>
<style lang="scss">
.editor-container {
  width: 100%;
}

.w-e-textarea-video-container {
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: center;

  video {
    width: 100%;
  }
}
</style>