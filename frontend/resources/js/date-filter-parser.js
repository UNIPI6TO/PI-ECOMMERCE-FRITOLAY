export const dateFilterParser = (inputStr) => {
    const now = new Date();
    const startDate = new Date(now);
    const endDate = new Date(now);
    let label = 'Hoy';

    const str = inputStr.toLowerCase().trim();

    if (str === 'hoy') {
        startDate.setHours(0,0,0,0);
    } else if (str === 'ayer') {
        startDate.setDate(startDate.getDate() - 1);
        startDate.setHours(0,0,0,0);
        endDate.setDate(endDate.getDate() - 1);
        endDate.setHours(23,59,59,999);
        label = 'Ayer';
    } else {
        const daysMatch = str.match(/^(\d+)d$/);
        if (daysMatch) {
            const days = parseInt(daysMatch[1], 10);
            if (days > 30) return { error: 'Rango máximo de 30 días' };
            startDate.setDate(startDate.getDate() - days);
            label = `Últimos ${days} días`;
        } else {
            const weeksMatch = str.match(/^(\d+)w$/);
            if (weeksMatch) {
                const weeks = parseInt(weeksMatch[1], 10);
                const days = weeks * 7;
                if (days > 30) return { error: 'Rango máximo de 30 días' };
                startDate.setDate(startDate.getDate() - days);
                label = `Últimas ${weeks} semanas`;
            } else if (str === 'custom') {
                label = 'Personalizado';
            }
        }
    }

    return {
        fechaInicio: startDate,
        fechaFin: endDate,
        label
    };
};
