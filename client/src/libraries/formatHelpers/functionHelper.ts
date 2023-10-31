// @ts-ignore
import moment from 'moment';
const functionHelper = {
    dateStringTo12Hour( dateString: string, formatString = 'DD-MM-YYYY hh:mm a'): string {
        // const date = new Date( dateString,  );
        return moment(dateString).format(formatString);
    },
    dateStringTo24Hour( dateString: string, formatString = 'DD-MM-YYYY HH:mm' ): string {
        const date = new Date( dateString,  );
        return moment(dateString).format(formatString);
    },
    getNumberKey (e: any)  {
        if (e.charCode === 0 || /\d/.test(String.fromCharCode(e.charCode))) {
            return true
        } else {
            e.preventDefault();
        }
    },
    getAmountOfCount(amount: string, amountText= ''): string {
        return `${amount} ${amountText}`;
    },
    getAmountOfKg(amount: string, amountText = '0'): string {
        return `${amount + amountText} `;
    },
    prefixFile(content: string) {
        const base_url = window.location.origin;
        return content?.replace(/uploads/g, base_url + '/uploads');
    }
}
export default functionHelper;
