import dayjs from "dayjs";

export default function formatDate(date, format = 'DD MMM YYYY') {
    return dayjs(date).format(format)
}
