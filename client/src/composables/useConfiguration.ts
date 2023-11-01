import { onMounted, reactive } from "vue";
import useVariable from "/@/composables/useVariables";
import useApi from "/@/api/api";


export default function useConfiguration() {
    const { isLoading, dialog} = useVariable();
    const configuration = reactive({
        appNane: '',
        key: '',
        type: '',
        value: '',
    });
    const api = useApi();
    const getConfiguration = async () => {
        isLoading.value = true;
        const response = await api.getAdminMenu();
        // if (re)
    };
    onMounted(() => {
        getConfiguration();
    })
    return {
        configuration,
        isLoading,
        dialog,
    }
}