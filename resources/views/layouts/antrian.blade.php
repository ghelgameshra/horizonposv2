<script>
function getDataAntrian(){
    $.get('{{ route('antrian.data') }}')
    .done((res) => {
        $('#tambahAntrian').text(`${res.data.jumlahAntrian+1}`)
        $('#jumlahAntrian').text(`${res.data.jumlahAntrian} Dalam Antrian`);
        $('#antrianAktif').text(`${res.data.antrianAktif} Sudah Dipanggil`);
        $('#panggilAntrian').text(`NO. ${res.data.panggilAntrian}`);
        $('#panggilAntrianLanjut').text(`NO. ${res.data.panggilAntrian + 1}`);
    })
    .fail((err) => {
        notification('error', err.responseJSON.message);
    })
}

function buatAntrian(){
    $.post('{{ route('antrian.create') }}', {
        '_token': '{{ csrf_token() }}'
    })
    .done((res) => {
        getDataAntrian();
        notification('success', res.pesan);
    })
    .fail((err) => {
        notification('error', err.responseJSON.message);
    })
}

function panggilAntrian(){
    $.get('{{ route('antrian.repeat') }}')
    .done((res) => {
        speakAntrian(res.pesan);
        notification('success', 'Sedang memanggil antrian', null, 1000);
    })
    .fail((err) => {
        notification('error', err.responseJSON.message);
    })
}

function panggilAntrianLanjut(){
    $.ajax({
        url: '{{ route('antrian.update') }}',
        type: 'PUT',
        data: {
            '_token': '{{ csrf_token() }}'
        }
    })
    .done((res) => {
        getDataAntrian();
        panggilAntrian();
    })
    .fail((err) => {
        notification('error', err.responseJSON.message);
    })
}

function speakAntrian($text) {
    // Check if browser supports speech synthesis
    if ("speechSynthesis" in window) {
        const utterance = new SpeechSynthesisUtterance($text);

        // Optional: Set voice options
        utterance.lang = "id-ID"; // Set the language
        utterance.pitch = 1; // Pitch of the voice
        utterance.rate = 1; // Speed rate of the speech
        utterance.volume = 1; // Volume level

        // Speak the text
        speechSynthesis.speak(utterance);
    } else {
        notification(
            "error",
            "Sorry, your browser doesn't support text to speech."
        );
    }
}
</script>
