import { onMounted, reactive } from "vue";
import useVariable from "/@/composables/useVariables";
import useApi from "/@/api/api";
import EnumApiErrorCode from "/@/models/enums/enumApiErrorCode";
import { IAds } from "/@/models/IAds";
import messageBoxHelper from "/@/libraries/elementUiHelpers/messageBoxHelper";
import { useI18n } from "vue-i18n";
import EnumMessageType from "/@/models/enums/enumMessageType";
import { messageNotification } from "/@/libraries/elementUiHelpers/notificationHelper";

export default function useMenuItem() {
	const { isLoading, openDialogRef, } = useVariable();
	const api = useApi();
	const { t } = useI18n();
	const formData = reactive({
		data: <IAds[]>[],
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
	const getMenuItem = async () => {
		isLoading.value = true;
		const response = await api.getMenuItem(formData.paginate);
		if (response.code !== EnumApiErrorCode.success) {
			// eslint-disable-next-line no-console
			console.log(response);
			messageNotification(response.message, EnumMessageType.Error)
		} else {
			formData.data = response.data.data;
			formData.paginate.total = response.data.total;
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
	const handleCurrentChange = (val: number) => {
		formData.paginate.page = val;
		getMenuItem();
	}
	const handleSizeChange = (val: number) => {
		formData.paginate.pageSize = val;
		getMenuItem();
	}
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
		handleSizeChange,
		handleCurrentChange,
	}
}