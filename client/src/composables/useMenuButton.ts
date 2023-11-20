import { onMounted, reactive } from "vue";
import useVariable from "/@/composables/useVariables";
import useApi from "/@/api/api";
import EnumApiErrorCode from "/@/models/enums/enumApiErrorCode";
import messageBoxHelper from "/@/libraries/elementUiHelpers/messageBoxHelper";
import { useI18n } from "vue-i18n";
import EnumMessageType from "/@/models/enums/enumMessageType";
import { messageNotification } from "/@/libraries/elementUiHelpers/notificationHelper";

export default function useMenuButton() {
	const { isLoading, openDialogRef, } = useVariable();
	const api = useApi();
	const { t } = useI18n();
	const formData = reactive({
		data: [],
		search: '',
		id: 0,
		paginate: {
			page: 1,
			pageSize: 10,
			total: 0,
		},
	});
	const onOpenAddDialog = (type: string) => {
		openDialogRef.value.openDialog(type);
	};
	const onOpenEditDialog = (type: string, row: object) => {
		openDialogRef.value.openDialog(type, row);
	};
	const getMenuButton = async () => {
		isLoading.value = true;
		const response = await api.getMenuButton(formData.paginate);
		if (response.code !== EnumApiErrorCode.success) {
			// eslint-disable-next-line no-console
			console.log(response);
		} else {
			formData.data = response.data.data;
			formData.paginate.total = response.data.total;
		}
		isLoading.value = false;
	};
	const deleteRow = (row: object) => {
		messageBoxHelper.confirmDelete(EnumMessageType.Warning, t('message.areYouSure', t('message.yes'))).then( async () => {
			const response = await api.deleteMenuButton(row)
			if (response.code === EnumApiErrorCode.success) {
				messageNotification(t('message.success'), EnumMessageType.Success);
				getMenuButton();
			} else {
				messageNotification(response.message, EnumMessageType.Error);
			}
		});
	};
	const handleCurrentChange = (val: number) => {
		formData.paginate.page = val;
		getMenuButton();
	}
	const handleSizeChange = (val: number) => {
		formData.paginate.pageSize = val;
		getMenuButton();
	}
	onMounted(() => {
		getMenuButton();
	})
	return {
		isLoading,
		onOpenAddDialog,
		onOpenEditDialog,
		openDialogRef,
		formData,
		getMenuButton,
		deleteRow,
		handleSizeChange,
		handleCurrentChange,
	}
}