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
    const data = reactive({
        configurations: <IConfiguration[]>[],
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
    const { t } = useI18n();
    const getConfiguration = async () => {
        isLoading.value = true;
        const response = await api.getConfiguration();
        if (response.code === EnumApiErrorCode.success) {
            data.configurations = response.data.data;
            data.currentPage = response.data.current_page;
            data.perPage = response.data.per_page;
            data.total = response.data.total;
        } else {
            // eslint-disable-next-line no-console
            console.log(response);
        }
        isLoading.value = false;
    };
    const filterTableData = computed(() =>
        data.configurations.filter(
            (item) =>
                !data.search ||
                item.appName.toLowerCase().includes(data.search.toLowerCase())
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
    const handleSizeChange = (val: number) => {
        console.log(`${val} items per page`)
    }
    const handleCurrentChange = (val: number) => {
        data.currentPage = val;
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
        data,
        getConfiguration,
        filterTableData,
        deleteRow,
        handleSizeChange,
        handleCurrentChange,
    }
}