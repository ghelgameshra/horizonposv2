<div class="row px-3">
    <div class="accordion">

        <div class="accordion-item {{ Request::is('pengaturan/printer') ? 'd-none' : '' }}">
            <h2 class="accordion-header">
                <button class="accordion-button bg-body" type="button" data-bs-toggle="collapse"
                    data-bs-target="#panelsStayOpen-collapseOne" aria-expanded="true"
                    aria-controls="panelsStayOpen-collapseOne">
                    Logo & QR
                </button>
            </h2>
            <div id="panelsStayOpen-collapseOne" class="accordion-collapse collapse show">
                <div class="accordion-body">
                    <div class="row mt-3">
                        <div class="col-6 text-center">
                            <picture>
                                <img class="img-fluid img-thumbnail" alt="..." id="logoPerusahaanActive">
                            </picture>
                            <small>Logo Perusahaan</small>
                        </div>
                        <div class="col-6 text-center">
                            <picture>
                                <img class="img-fluid img-thumbnail" alt="..." id="qrWaPerusahaanActive">
                            </picture>
                            <small>QR Whatsappp</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button {{ Request::is('pengaturan/printer') ? '' : 'collapsed' }} bg-body" type="button" data-bs-toggle="collapse"
                    data-bs-target="#previewStrukMenu" aria-expanded="false"
                    aria-controls="previewStrukMenu">
                    Preview Struk
                </button>
            </h2>
            <div id="previewStrukMenu" class="accordion-collapse {{ Request::is('pengaturan/printer') ? 'show' : 'collapse' }}">
                <div class="col-12 bg-body px-3">
                    <div class="row text-center" id="header_struk">
                        <picture class="mb-2" id="logo_struk">
                            <img src="" alt="struk_logo_perusahaan" id="struk_logo_perusahaan" width="30%" style="filter: grayscale(1)">
                        </picture>
                        <p id="struk_nama_perusahaan" class="fw-bold mb-0">Nama Perusahaan</p>
                        <p id="struk_alamat_perusahaan" class="mb-0">Alamat perusahaan</p>
                    </div>

                    <div class="col pt-4">
                        <table class="table table-sm text-left table-borderless">
                            <tbody>
                                <tr>
                                    <th scope="row" class="p-0">Tanggal</th>
                                    <td class="p-0">: {{ now() }}</td>
                                </tr>
                                <tr>
                                    <th scope="row" class="p-0">Customer</th>
                                    <td class="p-0">: {{ auth()->user()->name }}</td>
                                </tr>
                                <tr>
                                    <th scope="row" class="p-0">Invoice</th>
                                    <td class="p-0">: INV24091500000002</td>
                                </tr>
                                <tr>
                                    <th scope="row" class="p-0">Kasir</th>
                                    <td class="p-0">: {{ auth()->user()->name }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="col my-2 text-center">
                        <p class="mb-0">=============================</p>
                    </div>

                    <div class="col" id="isi_struk">
                        <div class="row mb-2">
                            <div class="col-8">
                                <p class="mb-0">1. SELAMAT DATANG (110x100) BANNER A</p>
                                <p class="mb-0">Rp. 33.000 x 1</p>
                            </div>
                            <div class="col-4">
                                <p class="mb-0">-</p>
                                <p class="mb-0 ml-auto">Rp. 60.000</p>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-8">
                                <p class="mb-0">2. STICKER MADU AP160</p>
                                <p class="mb-0">Rp. 5.000 x 10</p>
                            </div>
                            <div class="col-4">
                                <p class="mb-0">-</p>
                                <p class="mb-0 ml-auto">Rp. 50.000</p>
                            </div>
                        </div>
                    </div>

                    <div class="col my-4 text-center">
                        <p class="mb-0">=============================</p>
                    </div>

                    <div class="row text-center" id="footer_struk">
                        <p id="struk_telepone_perusahaan" class="mb-0">Telp. </p>
                        <p id="struk_wa_perusahaan" class="mb-0">Wa. </p>
                        <p id="struk_email_perusahaan" class="mb-0">email perusahaan</p>
                    </div>

                    <div class="row text-center my-3">
                        <p class="mb-0">- TERIMA KASIH -</p>
                    </div>

                    <div class="row text-center" id="pesan_struk"></div>

                    <div class="row text-center mt-3" id="footer_qrwa_struk">
                        <picture class="mb-2" id="struk">
                            <img src="" alt="struk_qr_wa" id="struk_qrwa" width="30%" style="filter: grayscale(.7)">
                        </picture>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
