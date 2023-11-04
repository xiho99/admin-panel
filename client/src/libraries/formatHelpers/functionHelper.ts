import moment from 'moment';

const functionHelper = {
    dateStringTo12Hour(dateString: string, formatString = 'YYYY-MM-DD') {
        return moment(dateString).format(formatString);
    },
    dateStringTo24Hour(dateString: string, formatString = 'YYYY-MM-DD HH:mm') {
        return moment(dateString).format(formatString);
    },
    dateStringTo12HourWithTime( dateString: string, formatString = 'DD-MM-YYYY hh:mm a'): string {
        return moment(dateString).format(formatString);
    },
    dateStringTo24HourWithTime( dateString: string, formatString = 'DD-MM-YYYY HH:mm' ): string {
        return moment(dateString).format(formatString);
    },
    prefixFile(content: string) {
        const base_url = window.location.origin;
        return content?.replace(/uploads/g, base_url + '/uploads');
    }
}
export default functionHelper;