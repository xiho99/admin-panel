import { computed, onMounted, reactive, ref } from "vue";
import useVariable from "/@/composables/useVariables";
import useApi from "/@/api/api";
import EnumApiErrorCode from "/@/models/enums/enumApiErrorCode";
import { IConfiguration } from "/@/models/IConfiguration";
import messageBoxHelper from "/@/libraries/elementUiHelpers/messageBoxHelper";
import { useI18n } from "vue-i18n";
import EnumMessageType from "/@/models/enums/enumMessageType";

export default function useConfiguration() {
    const { isLoading} = useVariable();

    const api = useApi();
    const formData = reactive({
        data: <IConfiguration[]>[],
        search: '',
        paginate: {
            page: 1,
            pageSize: 10,
            total: 0,
        }
    });
    const openDialogRef = ref();
    const onOpenAddDialog = (type: string) => {
        openDialogRef.value.openDialog(type);
    };
    const onOpenEditDialog = (type: string, row: Object) => {
        openDialogRef.value.openDialog(type, row);
    };
    const { t } = useI18n();
    const getConfiguration = async () => {
        isLoading.value = true;
        const response = await api.getConfiguration(formData.paginate);
        if (response.code !== EnumApiErrorCode.success) {
            // eslint-disable-next-line no-console
            console.log(response);
        } else {
            formData.data = response.data.data;
            formData.paginate.total = response.data.total;
        }
        isLoading.value = false;
    };
    const filterTableData = computed(() =>
        formData.data.filter(
            (item) =>
                !formData.search ||
                item.appName.toLowerCase().includes(formData.search.toLowerCase())
        )
    );
    const deleteId = ref(0);
    const deleteProcess = async () => {
        const request = {
            id: deleteId.value
        }
        const response = await api.deleteConfiguration(request)
        if (response.code === EnumApiErrorCode.success) {
            getConfiguration();
        }
    };
    const deleteRow = (item: IConfiguration) => {
        deleteId.value = item.id
        messageBoxHelper.confirm(EnumMessageType.Warning, deleteProcess, t('message.areYouSure', t('message.yes')))
    };
    const handleCurrentChange = (val: number) => {
        formData.paginate.page = val;
        getConfiguration();
    }
    const handleSizeChange = (val: number) => {
        formData.paginate.pageSize = val;
        getConfiguration();
    }
    onMounted(() => {
        getConfiguration();
    })
    return {
        isLoading,
        onOpenAddDialog,
        onOpenEditDialog,
        openDialogRef,
        formData,
        getConfiguration,
        filterTableData,
        deleteRow,
        handleSizeChange,
        handleCurrentChange,
    }
}