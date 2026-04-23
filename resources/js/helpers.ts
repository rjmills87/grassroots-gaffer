export const formatDate = (dateString: string) => {
    const date = new Date(dateString);
    return date.toDateString();
};

export const formatDateTime = (dateString: string) => {
    const date = new Date(dateString);
    return date.toLocaleString('en-GB', {
        dateStyle: 'medium',
        timeStyle: 'short',
    });
};

export const formatTime = (dateString: string) => {
    const date = new Date(dateString);
    return date.toLocaleTimeString('en-GB', {
        hour: '2-digit',
        minute: '2-digit',
    });
};

export const formatTimeRange = (startDateString: string, endDateString: string) => {
    return `${formatTime(startDateString)} - ${formatTime(endDateString)}`;
};

export const formatRelativeTime = (dateString: string) => {
    const date = new Date(dateString);
    const now = new Date();
    const diffMs = date.getTime() - now.getTime();
    const diffInMinutes = Math.round(diffMs / 60000);

    if (Math.abs(diffInMinutes) < 1) {
        return 'just now';
    }

    const rtf = new Intl.RelativeTimeFormat('en', { numeric: 'auto' });

    if (Math.abs(diffInMinutes) < 60) {
        return rtf.format(diffInMinutes, 'minute');
    }

    const diffInHours = Math.round(diffInMinutes / 60);
    if (Math.abs(diffInHours) < 24) {
        return rtf.format(diffInHours, 'hour');
    }

    const diffInDays = Math.round(diffInHours / 24);
    return rtf.format(diffInDays, 'day');
};

export const capitalizeFirstLetter = (string: string) => {
    if (!string) return '';
    const firstLetterCapitalized = string.charAt(0).toUpperCase() + string.slice(1);
    return firstLetterCapitalized;
};
