export const formatDate = (dateString: string) => {
    const date = new Date(dateString);
    return date.toDateString();
};

export const capitalizeFirstLetter = (string: string) => {
    if (!string) return '';
    const firstLetterCapitalized = string.charAt(0).toUpperCase() + string.slice(1);
    return firstLetterCapitalized;
};
