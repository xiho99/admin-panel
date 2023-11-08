import { onMounted, reactive } from "vue";
import useVariable from "/@/composables/useVariables";
import useApi from "/@/api/api";
import EnumApiErrorCode from "/@/models/enums/enumApiErrorCode";
import messageBoxHelper from "/@/libraries/elementUiHelpers/messageBoxHelper";
import { useI18n } from "vue-i18n";
import EnumMessageType from "/@/models/enums/enumMessageType";
import { messageNotification } from "/@/libraries/elementUiHelpers/notificationHelper";
import { ICategory } from "/@/models/ICategory";
export default function userCategory() {
	const { isLoading, openDialogRef, } = useVariable();
	const api = useApi();
	const { t } = useI18n();
	const formData = reactive({
		data: <ICategory[]>[],
		search: '',
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
	const getCategory = async () => {
		isLoading.value = true;
		const response = await api.getCategory(formData.paginate);
		if (response.code !== EnumApiErrorCode.success) {
			messageNotification(response.message, EnumMessageType.Error);
		} else {
			formData.data = response.data.data;
			formData.paginate.total = response.data.total;
		}
		isLoading.value = false;
	};
	const deleteRow = (row: Object) => {
		messageBoxHelper.confirmDelete(EnumMessageType.Warning, t('message.areYouSure', t('message.yes'))).then( async () => {
			const response = await api.deleteCategory(row)
			if (response.code === EnumApiErrorCode.success) {
				messageNotification(t('message.success'), EnumMessageType.Success);
				getCategory();
			} else {
				messageNotification(response.message, EnumMessageType.Error);
			}
		})
	};
	const handleCurrentChange = (val: number) => {
		formData.paginate.page = val;
		getCategory();
	}
	const handleSizeChange = (val: number) => {
		formData.paginate.pageSize = val;
		getCategory();
	}
	onMounted(() => {
		getCategory();
	})
	return {
		isLoading,
		onOpenAddDialog,
		onOpenEditDialog,
		openDialogRef,
		formData,
		getCategory,
		deleteRow,
		handleSizeChange,
		handleCurrentChange,
	}
}