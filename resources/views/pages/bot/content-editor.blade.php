<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-12">
                <form action="" id="botRequestForm">
                    <div class="input-group">
                      <span class="input-group-text">API </span>
                      <input type="text" class="form-control" autocomplete="off" id="apiUrl" value="https://api.telegram.org/bot<YOUR_BOT_TOKEN>/getUpdates?offset=-1">
                        <select name="selectedBot" id="selectedBot" class="form-select w-10">
                            <option value="">Pilih bot...</option>
                        </select>
                       <button
                            class="btn btn-sm btn-primary d-flex gap-1 justify-content-center align-content-center"
                            type="submit">
                            <i class="tf-icons ti ti-brand-telegram"></i>
                            <span>Send Request</span>
                        </button>
                    </div>
                </form>
                <pre style="text-align:left; overflow:auto; padding: 0 0 0 0 ; margin: 0 0 0 0">
                    <code class="language-json json" id="resultDataEditor" style="padding: 0 0 0 0; margin: 0 0 0 0"></code>
                </pre>
                <small>Response JSON</small>
            </div>
        </div>
    </div>
</div>

@push('js')
<script>
const el = document.getElementById('resultDataEditor');

$(document).ready(function () {
    const jsonData = {
        name: "bot-test",
        token: "123456:ABC-DEF"
    };

    // Highlight awal
    updateHighlightedContent(jsonData);
});

$('#botRequestForm').on('submit', function (e) {
    e.preventDefault();
    const token = $('#selectedBot').val();
    const url = $('#apiUrl').val().replace("<YOUR_BOT_TOKEN>", token);
    checkBotUpdate(url);
});

function checkBotUpdate(url) {
    $.get(url)
        .done((response) => {
            updateHighlightedContent(response);
        })
        .fail((response) => {
            updateHighlightedContent(response);
            notification('error', "Terjadi kesalahan saat check bot update");
        });
}

function updateHighlightedContent(data) {
    el.removeAttribute('data-highlighted'); // 🚫 reset highlight dulu
    el.textContent = JSON.stringify(data, null, 2); // set JSON
    hljs.highlightElement(el); // 🔥 highlight ulang
}
</script>
@endpush
