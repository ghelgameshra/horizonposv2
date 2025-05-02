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
                return {
                    results: data.data,
                };
            },
            cache: true,
        },
    })
}

function terbilangRupiah(angka) {
    angka = parseInt(angka);
    const baca = ['', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan', 'sepuluh', 'sebelas'];

    function ratusan(angka) {
        if (angka === 100) {
            return "seratus";
        } else if (angka > 100 && angka < 200) {
            return "seratus " + terbilangRupiah(angka - 100);
        } else if (angka >= 200) {
            if (angka % 100 === 0) {
                return terbilangRupiah(Math.floor(angka / 100)) + ' ratus';
            } else {
                return terbilangRupiah(Math.floor(angka / 100)) + ' ratus ' + terbilangRupiah(angka - Math.floor(angka / 100) * 100);
            }
        }
        return '';
    }

    function ribuan(angka) {
        if (angka === 1000) {
            return "seribu";
        } else if (angka > 1000 && angka < 2000) {
            return "seribu " + terbilangRupiah(angka - 1000);
        } else if (angka >= 200) {
            if (angka % 1000 === 0) {
                return terbilangRupiah(Math.floor(angka / 1000)) + ' ribu';
            } else {
                return terbilangRupiah(Math.floor(angka / 1000)) + ' ribu ' + terbilangRupiah(angka - Math.floor(angka / 1000) * 1000);
            }
        }
        return '';
    }

    function jutaan(angka) {
        if (angka === 1000000) {
            return "satu juta";
        } else if (angka > 1000000 && angka < 2000000) {
            return "satu juta " + terbilangRupiah(angka - 1000000);
        } else if (angka >= 200) {
            if (angka % 1000000 === 0) {
                return terbilangRupiah(Math.floor(angka / 1000000)) + ' juta';
            } else {
                return terbilangRupiah(Math.floor(angka / 1000000)) + ' juta ' + terbilangRupiah(angka - Math.floor(angka / 1000000) * 1000000);
            }
        }
        return '';
    }

    function miliaran(angka) {
        if (angka === 1000000000) {
            return "satu miliar";
        } else if (angka > 1000000000 && angka < 2000000000) {
            return "satu miliar " + terbilangRupiah(angka - 1000000000);
        } else if (angka >= 200) {
            if (angka % 1000000000 === 0) {
                return terbilangRupiah(Math.floor(angka / 1000000000)) + ' miliar';
            } else {
                return terbilangRupiah(Math.floor(angka / 1000000000)) + ' miliar ' + terbilangRupiah(angka - Math.floor(angka / 1000000000) * 1000000000);
            }
        }
        return '';
    }

    if (angka < 12) {
        return baca[angka];
    } else if (angka >= 12 && angka < 20) {
        return baca[angka - 10] + " belas";
    } else if (angka >= 20 && angka < 100) {
        return baca[Math.floor(angka / 10)] + ' puluh ' + terbilangRupiah(angka - Math.floor(angka / 10) * 10);
    } else if (angka >= 100 && angka < 1000) {
        return ratusan(angka);
    } else if (angka >= 1000 && angka < 1000000) {
        return ribuan(angka);
    } else if (angka >= 1000000 && angka < 1000000000) {
        return jutaan(angka);
    } else if (angka >= 1000000000 && angka < 1000000000000) {
        return miliaran(angka);
    } else {
        return 'Inputan terlau besar dan tidak wajar, silahkan cek inputan';
    }
}
