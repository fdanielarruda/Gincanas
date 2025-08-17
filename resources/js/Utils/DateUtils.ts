import { format } from 'date-fns';

export const formatForInput = (dateString: string): string => {
    if (!dateString) return '';
    const date = new Date(dateString);
    return format(date, "yyyy-MM-dd");
};

export const formatDateForDisplay = (datetime: string): string => {
    if (!datetime) return 'Não definida';
    const date = new Date(datetime);
    return date.toLocaleString('pt-BR', {
        day: '2-digit', month: '2-digit', year: 'numeric',
    });
};