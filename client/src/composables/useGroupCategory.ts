import { onMounted, reactive } from "vue";
import useVariable from "/@/composables/useVariables";
import useApi from "/@/api/api";
import EnumApiErrorCode from "/@/models/enums/enumApiErrorCode";
import messageBoxHelper from "/@/libraries/elementUiHelpers/messageBoxHelper";
import { useI18n } from "vue-i18n";
import EnumMessageType from "/@/models/enums/enumMessageType";
import { messageNotification } from "/@/libraries/elementUiHelpers/notificationHelper";
import { GroupCategory, IGroupCategory } from "/@/models/IGroupCategory";
export default function useGroupCategory() {
    const { isLoading, openDialogRef, } = useVariable();
    const api = useApi();
    const { t } = useI18n();
    const formData = reactive({
        data: <IGroupCategory[]>[],
        search: '',
        id: 0,
        paginate: {
            currentPage: 1,
            pageSize: 10,
            total: 0,
        }
    });
    const onOpenAddDialog = (type: string) => {
        openDialogRef.value.openDialog(type);
    };
    const onOpenEditDialog = (type: string, row: object) => {
        openDialogRef.value.openDialog(type, row);
    };
    const getGroupCategory = async () => {
        isLoading.value = true;
        const response = await api.getGroupCategory(formData.paginate);
        if (response.code === EnumApiErrorCode.success) {
            formData.data = response.data.data.map((item: IGroupCategory) => new GroupCategory(item));
            formData.paginate.total = response.data.count;
        } else {
            // eslint-disable-next-line no-console
            console.log(response);
        }
        isLoading.value = false;
    };
    const deleteRow = (row: Object) => {
        messageBoxHelper.confirmDelete(EnumMessageType.Warning, t('message.areYouSure', t('message.yes'))).then( async () => {
            const response = await api.deleteGroupCategory(row)
            if (response.code === EnumApiErrorCode.success) {
                messageNotification(t('message.success'), EnumMessageType.Success);
                getGroupCategory();
            } else {
                messageNotification(response.message, EnumMessageType.Error);
            }
        })
    };
    const handleSizeChange = (val: number) => {
        formData.paginate.pageSize = val;
        getGroupCategory();
    }
    const handleCurrentChange = (val: number) => {
        formData.paginate.currentPage = val;
        getGroupCategory();
    }
    onMounted(() => {
        getGroupCategory();
    })
    return {
        isLoading,
        onOpenAddDialog,
        onOpenEditDialog,
        openDialogRef,
        formData,
        getGroupCategory,
        deleteRow,
        handleCurrentChange,
        handleSizeChange,
    }
}