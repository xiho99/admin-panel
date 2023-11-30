import { onMounted, reactive } from "vue";
import useVariable from "/@/composables/useVariables";
import useApi from "/@/api/api";
import EnumApiErrorCode from "/@/models/enums/enumApiErrorCode";
import { useI18n } from "vue-i18n";
import EnumMessageType from "/@/models/enums/enumMessageType";
import { messageNotification } from "/@/libraries/elementUiHelpers/notificationHelper";
import { ICategory } from "/@/models/ICategory";

export default function userCategory() {
	const { isLoading, openDialogRef, } = useVariable();
	const api = useApi();
	const { t } = useI18n();
	const state = reactive({
		// Header content (required, pay attention to the format)
		tableData: {
			header: [
				{ key: 'name', colWidth: '', title: 'message.name', width: 150, height: 100, type: 'text', isCheck: true, },
				{ key: 'key', colWidth: '', title: 'message.key', type: 'text', isCheck: true },
				{ key: 'sort', colWidth: '', title: 'message.sort', type: 'text', isCheck: true },
				{ key: 'is_visible', colWidth: '', title: 'message.is_visible', type: 'tag', isCheck: true },
				{ key: 'created_at', colWidth: '', title: 'message.created_at', type: 'date', isCheck: true },
				// { key: 'updated_at', colWidth: '', title: 'message.updated_at', type: 'date', isCheck: true },
			],
			data: <ICategory[]>[],
			total: 0,
			page: {
				page: 1,
				pageSize: 10,
			},
			config: {
				search: '',
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
	const onOpenAddDialog = (type: string) => {
		openDialogRef.value.openDialog(type);
	};
	const onOpenEditDialog = (type: string, row: object) => {
		openDialogRef.value.openDialog(type, row);
	};
	const onSystem = (row: object, key: string) => {
		if (key === 'edit') {
			onOpenEditDialog(key, row);
		} else {
			deleteRow(row)
		}
	};
	const getTableData = async () => {
		state.tableData.config.loading = true;
		const response = await api.getCategory(state.tableData.page);
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
		const response = await api.deleteCategory(row)
		if (response.code === EnumApiErrorCode.success) {
			messageNotification(t('message.success'), EnumMessageType.Success);
			getTableData();
		} else {
			messageNotification(response.message, EnumMessageType.Error);
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
		isLoading,
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