import {onMounted, reactive } from "vue";
import EnumApiErrorCode from "/@/models/enums/enumApiErrorCode";
import EnumMessageType from "/@/models/enums/enumMessageType";
import { ICategory } from "/@/models/ICategory";
import { messageNotification } from "/@/libraries/elementUiHelpers/notificationHelper";
import useVariables from "/@/composables/useVariables";

export default function useConfiguration() {
    const { t, api, openDialogRef, onOpenAddDialog, onOpenEditDialog } = useVariables();
    const state = reactive({
        // Header content (required, pay attention to the format)
        tableData: {
            header: [
                { key: 'appName', colWidth: '', title: 'message.name', width: 150, height: 100, type: 'text', isCheck: true, },
                { key: 'key', colWidth: '', title: 'message.key', type: 'text', isCheck: true },
                { key: 'type', colWidth: '', title: 'message.type', type: 'any', isCheck: true },
                { key: 'sort', colWidth: '', title: 'message.sort', type: 'number', isCheck: true },
                { key: 'is_visible', colWidth: '', title: 'message.is_visible', type: 'tag', isCheck: true },
                { key: 'created_at', colWidth: '', title: 'message.created_at', type: 'date', isCheck: true },
                // { key: 'updated_at', colWidth: '', title: 'message.updated_at', type: 'date', isCheck: true },
            ],
            data: <ICategory[]>[],
            page: {
                page: 1,
                pageSize: 10,
            },
            total: 0,
            config: {
                keySearch: 'appName',
                isBorder: false,
                isOperate: [
                    {
                        label: 'message.table.edit',
                        key: 'edit',
                    },
                    {
                        label: 'message.table.delete',
                        key: 'delete',
                        type: 'tip',
                        tipTitle: 'message.areYouSure',
                    }
                ],
                loading: false,
            },
        }
    });
    const getTableData = async () => {
        state.tableData.config.loading = true;
        const response = await api.getConfiguration(state.tableData.page);
        if (response.code !== EnumApiErrorCode.success) {
            // eslint-disable-next-line no-console
            console.log(response);
        } else {
            state.tableData.data = response.data.data;
            state.tableData.total = response.data.total;
        }
        state.tableData.config.loading = false;
    };
    const deleteRow = async (row: Object) => {
        const response = await api.deleteConfiguration(row)
        if (response.code === EnumApiErrorCode.success) {
            messageNotification(t('message.success'), EnumMessageType.Success);
            getTableData();
        } else {
            messageNotification(response.message, EnumMessageType.Error);
        }
    };
    const onSystem = (row: object, key: string) => {
        if (key === 'edit') {
            onOpenEditDialog(key, row);
        } else {
            deleteRow(row)
        }
    };
    const onHandlePageChange = (data: any) => {
        state.tableData.page.pageSize = data.pageSize;
        state.tableData.page.page = data.page;
        getTableData();
    };
    onMounted(() => {
        getTableData();
    })
    return {
        onOpenAddDialog,
        onOpenEditDialog,
        openDialogRef,
        state,
        getTableData,
        deleteRow,
        onHandlePageChange,
        onSystem,
    }
}