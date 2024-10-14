<script>
function getDataAntrian(){
    $.get('{{ route('antrian.data') }}')
    .done((res) => {
        $('#jumlahAntrianDesain').text(`${res.data.jumlahAntrianDesain - res.data.antrianDesainTerpanggil} antrian desain`);
        $('#jumlahAntrianCetak').text(`${res.data.jumlahAntrianCetak - res.data.antrianCetakTerpanggil} antrian cetak`);

        $('#antrianDesainTerpanggil').text(`desain ${res.data.antrianDesainTerpanggil} dipanggil`);
        $('#antrianCetakTerpanggil').text(`cetak ${res.data.antrianCetakTerpanggil} dipanggil`);

        $('#nomor_meja').children().remove();
        $('#ganti_meja').children().remove();
        $('#nomor_meja').append(`<option value="">Nomor meja</option>`);
        $('#ganti_meja').append(`<option value="">Nomor meja</option>`);

        const nomorMeja = (res.data.nomorMeja).map(item => item.nomor_meja);
        nomorMeja.forEach(element => {
            $('#nomor_meja').append(`<option value="${element}">Meja no. ${element}</option>`);
            $('#ganti_meja').append(`<option value="${element}">Meja no. ${element}</option>`);
        });
    })
    .fail((err) => {
        notification('error', err.responseJSON.message);
    })
}

function buatAntrian(tipe_antrian){
    Swal.fire({
        text: "Lanjut Buat Antrian ?",
        showCancelButton: true,
        confirmButtonText: 'Lanjut',
        customClass: {
            confirmButton: 'btn btn-primary me-3 waves-effect waves-light',
            cancelButton: 'btn btn-label-secondary waves-effect waves-light'
        },
        buttonsStyling: false
    }).then(function (result) {
        if (result.value) {
            $.post('{{ route('antrian.create') }}', {
                '_token': '{{ csrf_token() }}',
                'tipe_antrian': tipe_antrian
            })
            .done((res) => {
                getDataAntrian();
                notification('success', res.pesan);
                $('#modalPilihAntrian').modal('hide');
            })
            .fail((err) => {
                notification('error', err.responseJSON.message);
            })
        }
    });

}

function panggilAntrian(){
    $('#modalUlangAntrian').modal('show');
}

$('#antrianUlangForm').on('submit', function(e){
    e.preventDefault();
    $.get('{{ route('antrian.repeat') }}', {
        "ganti_meja": $('#ganti_meja').val(),
        "jenis_antrian": $('#jenis_antrian').val()
    })
    .done((res) => {
        speakAntrian(res.kalimat);
        notification('success', 'Sedang memanggil antrian', null, 1000);

        $('#antrianUlangForm')[0].reset();
        $('#modalUlangAntrian').modal('hide');
    })
    .fail((err) => {
        notification('error', err.responseJSON.message);
    })
})

function panggilAntrianLanjut(){
    $('#modalPanggilAntrian').modal('show');
}


$('#antrianForm').on('submit', function(e){
    e.preventDefault();

    $.ajax({
        url: '{{ route('antrian.update') }}',
        type: 'PUT',
        data: $(this).serialize()
    })
    .done((res) => {
        getDataAntrian();
        speakAntrian(res.kalimat);

        $('#modalPanggilAntrian').modal('hide');
        $('#antrianForm')[0].reset();
    })
    .fail((err) => {
        notification('error', err.responseJSON.message);
    })
})

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
