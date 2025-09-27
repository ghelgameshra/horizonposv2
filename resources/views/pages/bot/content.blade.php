<div class="card mb-3">
    <div class="card-body">
        <div class="row mt-3">
            <div class="col-12">
                <div class="card-datatable table-responsive pt-0">
                    <div class="row">
                        <form action="" id="formTambahBot" class="d-flex input-group">
                            <input type="text" class="form-control" autocomplete="off" name="bot_name" placeholder="Nama bot">
                            <input type="text" class="form-control" autocomplete="off" name="bot_token" placeholder="Token">

                            <button
                                class="btn btn-sm btn-primary d-flex gap-1 justify-content-center align-content-center"
                                type="submit">
                                <i class="tf-icons ti ti-robot"></i>
                                <span>Tambah Bot</span>
                            </button>
                            <button
                                class="btn btn-sm btn-success d-flex gap-1 justify-content-center align-content-center"
                                type="button" onclick="testBot()">
                                <i class="tf-icons ti ti-brand-telegram"></i>
                                <span>Test Bot</span>
                            </button>
                        </form>
                    </div>
                    <table class="datatables-basic table text-nowrap" id="botTable">
                        <thead>
                            <tr>
                                <th>no</th>
                                <th>nama bot</th>
                                <th>token</th>
                                <th>Default</th>
                                <th>
                                    <i class="menu-icon tf-icons ti ti-settings"></i>
                                </th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <small>Bot {{ ucfirst($botType) }} Chat</small>
        <div class="row mt-3">
            <div class="col-12">
                <div class="card-datatable table-responsive pt-0">
                    <div class="row">
                        <form action="" id="formTambahChatBot" class="d-flex input-group">
                            <select name="bot_telegram_id" id="bot_telegram_id" class="form-select">
                                <option value="">Pilih bot ... </option>
                            </select>
                            <input type="text" class="form-control" autocomplete="off" name="chat_title" placeholder="Chat Title">
                            <input type="number" class="form-control" autocomplete="off" name="chat_id" placeholder="Chat ID">
                            <input type="number" class="form-control" autocomplete="off" name="message_thread_id" placeholder="Thread ID">

                            <button
                                class="btn btn-sm btn-primary d-flex gap-1 justify-content-center align-content-center"
                                type="submit">
                                <i class="tf-icons ti ti-message-code"></i>
                                <span>Tambah Chat Bot</span>
                            </button>
                        </form>
                    </div>
                    <table class="table text-nowrap" id="botTableDetail">
                        <thead>
                            <tr>
                                <th>no</th>
                                <th>nama bot</th>
                                <th>title</th>
                                <th>chat id</th>
                                <th class="text-center">message thread id</th>
                                <th class="text-center">send message</th>
                                <th class="text-center">
                                    <i class="menu-icon tf-icons ti ti-settings"></i>
                                </th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('js')
<script>
$('#formTambahBot').on('submit', function (e) {
    e.preventDefault();

    const form = this;
    const formData = new FormData(form);

    $.ajax({
        url: `{{ route('bot.telegram.addBot') }}`,
        method: 'POST',
        data: formData,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        processData: false,  // penting untuk FormData
        contentType: false,  // penting untuk FormData
        success: function (response) {
            notification('success', response.message || 'Berhasil tambah bot');
            form.reset(); // optional: reset form setelah berhasil
            getBotData();
        },
        error: function (xhr) {
            let message = 'Gagal tambah bot';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                message = xhr.responseJSON.message;
            }
            notification('error', message);
        }
    });
});

$('#formTambahChatBot').on('submit', function (e) {
    e.preventDefault();

    const form = this;
    const formData = new FormData(form);

    $.ajax({
        url: `{{ route('bot.telegram.addChatBot') }}`,
        method: 'POST',
        data: formData,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        processData: false,  // penting untuk FormData
        contentType: false,  // penting untuk FormData
        success: function (response) {
            notification('success', response.message || 'Berhasil tambah bot');
            form.reset(); // optional: reset form setelah berhasil
            getBotData();
        },
        error: function (xhr) {
            let message = 'Gagal tambah bot';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                message = xhr.responseJSON.message;
            }
            notification('error', message);
        }
    });
});
</script>
@endpush
