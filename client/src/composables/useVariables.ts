import { ref } from 'vue';
import { FormInstance } from 'element-plus';
const useVariable = () => {
    const isLoading = ref( false );
    const isProcessing = ref( false );
    const ruleFormRef = ref<FormInstance>();
    const dialog = ref(false);
    // const { width, height } = useWindowSize();
    const resetForm = (formEl: FormInstance | undefined) => {
        if (!formEl) return
        formEl.resetFields();
    };
    return {
        isLoading,
        isProcessing,
        ruleFormRef,
        dialog,
        resetForm,
    };
};

export default useVariable;
