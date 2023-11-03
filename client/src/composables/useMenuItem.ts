import { onMounted, reactive } from "vue";
import useVariable from "/@/composables/useVariables";
import useApi from "/@/api/api";
import EnumApiErrorCode from "/@/models/enums/enumApiErrorCode";
import { IAds } from "/@/models/IAds";
import messageBoxHelper from "/@/libraries/elementUiHelpers/messageBoxHelper";
import { useI18n } from "vue-i18n";
import EnumMessageType from "/@/models/enums/enumMessageType";
import { messageNotification } from "/@/libraries/elementUiHelpers/notificationHelper";
export default function UseMenuItem() {
	const { isLoading, openDialogRef, } = useVariable();
	const api = useApi();
	const { t } = useI18n();
	const formData = reactive({
		data: <IAds[]>[],
		currentPage: 1,
		perPage: 10,
		search: '',
		total: 0,
		id: 0
	});
	const onOpenAddDialog = (type: string) => {
		openDialogRef.value.openDialog(type);
	};
	const onOpenEditDialog = (type: string, row: object) => {
		openDialogRef.value.openDialog(type, row);
	};
	const getMenuItem = async () => {
		isLoading.value = true;
		const response = await api.getMenuItem();
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
	const deleteProcess = async () => {
		const request = {
			id: formData.id
		}
		const response = await api.deleteMenuItem(request)
		if (response.code === EnumApiErrorCode.success) {
			messageNotification(t('message.success'), EnumMessageType.Success);
			getMenuItem();
		}
	};
	const deleteRow = (id: number) => {
		formData.id = id
		messageBoxHelper.confirm(EnumMessageType.Warning, deleteProcess, t('message.areYouSure', t('message.yes')))
	};
	onMounted(() => {
		getMenuItem();
	})
	return {
		isLoading,
		onOpenAddDialog,
		onOpenEditDialog,
		openDialogRef,
		formData,
		getMenuItem,
		deleteRow,
	}
}