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

export {
    showSelect2
}
