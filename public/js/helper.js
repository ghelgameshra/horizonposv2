function notification(status, message, title = null, timer = 5000) {
    const options = {
        timeOut: timer,
        progressBar: true
    };

    if (status === 'error') {
        toastr.error(message, title, options);
    } else if (status === 'success') {
        toastr.success(message, title, options);
    }
}

function formatRupiah(value) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        currencyDisplay: 'symbol', // Menampilkan simbol mata uang (Rp)
        minimumFractionDigits: 0
    }).format(value);
}

function reloadDataTable(item){
    item.DataTable().ajax.reload();
}
