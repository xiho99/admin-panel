import { onMounted, reactive, ref } from "vue";
import useVariable from "/@/composables/useVariables";
import useApi from "/@/api/api";
import EnumApiErrorCode from "/@/models/enums/enumApiErrorCode";
import { IAds } from "/@/models/IAds";
import messageBoxHelper from "/@/libraries/elementUiHelpers/messageBoxHelper";
import { useI18n } from "vue-i18n";
import EnumMessageType from "/@/models/enums/enumMessageType";
export default function useAdvertisment() {
    const { isLoading} = useVariable();
    const api = useApi();
    const { t } = useI18n();
    const formData = reactive({
        data: <IAds[]>[],
        currentPage: 1,
        perPage: 10,
        search: '',
        total: 0,
    });
    const openDialogRef = ref();
    const onOpenAddDialog = (type: string) => {
        openDialogRef.value.openDialog(type);
    };
    const onOpenEditDialog = (type: string, row: Object) => {
        openDialogRef.value.openDialog(type, row);
    };
    const getAds = async () => {
        isLoading.value = true;
        const response = await api.getAds();
        if (response.code === EnumApiErrorCode.success) {
            formData.data = response.data.data;
            formData.currentPage = response.data.current_page;
            formData.perPage = response.data.per_page;
            formData.total = response.data.total;
        } else {
            // eslint-disable-next-line no-console
            console.log(response);
        }
        isLoading.value = false;
    };
    const deleteId = ref(0);
    const deleteProcess = async () => {
        const request = {
            id: deleteId.value
        }
        const response = await api.deleteConfiguration(request)
        if (response.code === EnumApiErrorCode.success) {
            getAds();
        }
    };
    const deleteRow = (item: IAds) => {
        deleteId.value = item.id
        messageBoxHelper.confirm(EnumMessageType.Warning, deleteProcess, t('message.areYouSure', t('message.yes')))
    };
    onMounted(() => {
        getAds();
    })
    return {
        isLoading,
        onOpenAddDialog,
        onOpenEditDialog,
        openDialogRef,
        formData,
        getAds,
        deleteRow,
    }
}