@extends('pages.layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('lib') }}/assets/vendor/libs/select2/select2.css" />
<link rel="stylesheet" href="{{ asset('highlight/atom-one-dark.min.css') }}">
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-12 mb-3">
            @include('pages.bot.content')
        </div>
        <div class="col-12">
            @include('pages.bot.content-editor')
        </div>
    </div>
</div>
@endsection

@push('js')
<script src="{{ asset('highlight/highlight.min.js') }}"></script>
<script src="{{ asset('highlight/json.min.js') }}"></script>
<script src="{{ asset('lib') }}/assets/vendor/libs/select2/select2.js"></script>
<script>
$(document).ready(function() {
    getBotData();
})

function testBot(){
    notification('info', 'Mengirim pesan dari bot');

    $.get('{{ route('bot.telegram.test') }}')
    .done((response) => {
        notification('success', 'Berhasil kirim pesan dari bot')
    })
    .fail((response) => {
        notification('error', response.responseJSON.message || 'Gagal kirim pesan dari bot')
    })
}

function getBotData(){
     $.get('{{ route('bot.telegram.data') }}')
    .done((response) => {
        createTable(response.data);
    })
    .fail((response) => {
        notification('error', response.responseJSON.message || 'Gagal ambil data bot')
    })
}

function createTable(data) {
    const $selectedBot = $('#selectedBot');
    const $selectedBotChat = $('#bot_telegram_id');

    $selectedBot.children().remove();
    $selectedBotChat.children().remove();

    data.bot.forEach(element => {
        $selectedBot.append(`<option value="${element.bot_token}">${element.bot_name}</option>`);
        $selectedBotChat.append(`<option value="${element.id}">${element.bot_name}</option>`);
    });


    const botTable = $('#botTable');
    const botTableDetail = $('#botTableDetail');

    botTable.DataTable({
        dom: '',
        ordering: false,
        destroy: true,
        data: data.bot,
        columns: [
            {
                data: null,
                render: function (data, type, row, meta) {
                    return meta.row + 1;
                },
                title: 'No'
            },
            {
                data: data => data.bot_name
            },
            {
                data: data => {
                    const tokenId = `token_${data.id}`;
                    const hiddenToken = '•'.repeat(data.bot_token.length); // sesuai panjang token
                    return `
                        <div class="d-flex align-items-center gap-2">
                            <span id="${tokenId}" class="bot-token" data-token="${data.bot_token}" data-visible="false">${hiddenToken}</span>
                        </div>
                    `;
                }
            },
            {
                data: data => {
                    return `
                        <label class="switch switch-square">
                            <input type="checkbox" class="switch-input switch-input-bot" id="status_bot_checkbox_${data.id}" ${data.bot_default ? 'checked' : ''} onclick="changeBotDefault('${data.id}')" />
                            <input type="text" hidden id="status_bot_${data.id}" value="${data.bot_default}">
                            <span class="switch-toggle-slider">
                                <span class="switch-on"></span>
                                <span class="switch-off"></span>
                            </span>
                        </label>
                    `;
                }
            },
            {
                data: data => {
                    const tokenId = `token_${data.id}`;
                    return `
                        <div class="btn-group">
                            <button type="button" class="btn btn-xs btn-outline-primary" onclick="toggleTokenVisibility('${tokenId}', this)">
                                <i class="ti ti-eye"></i>
                            </button>
                            <button class="btn btn-xs btn-outline-danger" onclick="deleteBot(${data.id})">
                                <i class="ti ti-trash d-block"></i>
                            </button>
                        </div>
                    `;
                }
            },
        ]
    });

    /* buat tabel detail */
    const bot = data.bot;
    const botDetail = [];
    data.bot.forEach(element => {
        element.telegram_bot_chat.forEach(el => {
            botDetail.push({
                ...el,
                bot_name: element.bot_name
            });
        });
    });

    botTableDetail.DataTable({
        dom: '',
        ordering: false,
        destroy: true,
        data: botDetail,
        columns: [
            {
                data: null,
                render: function (data, type, row, meta) {
                    return meta.row + 1;
                },
                title: 'No'
            },
            {
                data: data => data.bot_name
            },
            {
                data: data => data.chat_title
            },
            {
                data: data => data.chat_id
            },
            {
                data: data => data.message_thread_id
            },
            {
                data: data => {
                    return `
                        <label class="switch switch-square">
                            <input type="checkbox" class="switch-input" id="status_chat_bot_checkbox_${data.id}" ${data.is_active ? 'checked' : ''} onclick="changeChatStatus('${data.id}')" />
                            <input type="text" hidden id="status_chat_bot_${data.id}" value="${data.is_active}">
                            <span class="switch-toggle-slider">
                                <span class="switch-on"></span>
                                <span class="switch-off"></span>
                            </span>
                        </label>
                    `;
                }
            },
            {
                data: data => {
                    const tokenId = `token_${data.id}`;
                    return `
                        <div class="btn-group">
                            <button class="btn btn-xs btn-outline-danger" onclick="deleteBotChat(${data.id})">
                                <i class="ti ti-trash d-block"></i>
                            </button>
                        </div>
                    `;
                }
            },
        ]
    });
}

// ✅ Fungsi toggle untuk show/hide token
function toggleTokenVisibility(spanId, buttonElement) {
    const span = document.getElementById(spanId);
    const icon = buttonElement.querySelector('i');

    const isVisible = span.getAttribute('data-visible') === 'true';
    const token = span.getAttribute('data-token');

    if (isVisible) {
        span.textContent = '•'.repeat(token.length);
        icon.classList.remove('ti-eye-off');
        icon.classList.add('ti-eye');
        span.setAttribute('data-visible', 'false');
    } else {
        span.textContent = token;
        icon.classList.remove('ti-eye');
        icon.classList.add('ti-eye-off');
        span.setAttribute('data-visible', 'true');
    }
}

function changeBotDefault(id) {
    const $checkbox = $(`#status_bot_checkbox_${id}`);
    const $hiddenInput = $(`#status_bot_${id}`);

    // Jika checkbox sedang dinonaktifkan (tidak dicentang), batalkan
    if (!$checkbox.prop('checked')) {
        notification('info', 'Silakan aktifkan bot lain');
        $checkbox.prop('checked', true); // Kembalikan ke aktif
        return;
    }

    $.ajax({
        url: `{{ route('bot.telegram.change-default-bot') }}/${id}`,
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {
            notification('success', response.message || 'Berhasil ubah default bot');

            // Matikan semua checkbox & reset hidden input
            $('.switch-input-bot').each(function () {
                $(this).prop('checked', false);

                const otherId = $(this).data('id');
                $(`#status_bot_${otherId}`).val('0');
            });

            // Aktifkan checkbox & hidden input yang diklik
            $checkbox.prop('checked', true);
            $hiddenInput.val('1');
        },
        error: function (xhr) {
            let message = 'Gagal ubah default bot';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                message = xhr.responseJSON.message;
            }
            notification('error', message);
        }
    });
}


function changeChatStatus(id){
    $.ajax({
        url: `{{ route('bot.telegram.change-status-chat') }}/${id}`,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        method: 'POST',
        success: function(response) {
            notification('success', response.message || 'Berhasil ubah status chat bot');
        },
        error: function(xhr) {
            let message = 'Gagal ubah status chat bot';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                message = xhr.responseJSON.message;
            }
            notification('error', message);
        }
    });
}

function deleteBot(id){
    $.ajax({
        url: `{{ route('bot.telegram.deleteBot') }}/${id}`,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        method: 'DELETE',
        success: function(response) {
            getBotData();
            notification('success', response.message || 'Berhasil hapus bot');
        },
        error: function(xhr) {
            let message = 'Gagal ubah status chat bot';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                message = xhr.responseJSON.message;
            }
            notification('error', message);
        }
    });
}

function deleteBotChat(id){
    $.ajax({
        url: `{{ route('bot.telegram.deleteChatBot') }}/${id}`,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        method: 'DELETE',
        success: function(response) {
            getBotData();
            notification('success', response.message || 'Berhasil hapus chat bot');
        },
        error: function(xhr) {
            let message = 'Gagal ubah status chat bot';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                message = xhr.responseJSON.message;
            }
            notification('error', message);
        }
    });
}
</script>
@endpush
