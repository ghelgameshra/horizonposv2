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

function formatRupiah(value) {
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        currencyDisplay: "symbol", // Menampilkan simbol mata uang (Rp)
        minimumFractionDigits: 0,
    }).format(value);
}

function reloadDataTable(item) {
    item.DataTable().ajax.reload();
}

function showSelect2(idInput, dropdownParent, url) {
    $(`#${idInput}`).select2({
        dropdownParent: `#${dropdownParent}`,
        ajax: {
            url: url,
            data: function (params) {
                var query = {
                    search: params.term,
                };
                return query;
            },
            processResults: function (data) {
                console.log(data.data);
                return {
                    results: data.data,
                };
            },
            cache: true,
        },
    })
}
