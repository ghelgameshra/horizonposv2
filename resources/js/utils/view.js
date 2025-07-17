function notification(status, message, title = null, timer = 5000) {
    const options = {
        timeOut: timer,
        progressBar: true,
    };

    if (status === "error") {
        toastr.error(message, title, options);
    }

    if (status === "success") {
        toastr.success(message, title, options);
    }

    if (status === "info") {
        toastr.info(message, title, options);
    }
}

function formatTanggal(createdAt) {
    const date = new Date(createdAt);
    const options = {
        weekday: 'long',
        day: '2-digit',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        hour12: false,
        timeZone: 'Asia/Jakarta'
    };

    const formatted = new Intl.DateTimeFormat('id-ID', options).format(date);
    return formatted.replace(' pukul ', " ");
}

export {
    notification, formatTanggal
}
