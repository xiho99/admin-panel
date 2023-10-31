import enumMessageStatus from '../../models/enums/enumMessageType';
import { ElNotification, ElMessage } from 'element-plus';

const notification = (message: string, type: enumMessageStatus, title?: string): void => {
    ElNotification({
        title,
        message: message,
        type,
        duration: 3000,
    });
};
const messageNotification = (message: string, type: enumMessageStatus) => {
    ElMessage({
        message: message,
        type: type,
    });
}
export {
    notification,
    messageNotification
};
