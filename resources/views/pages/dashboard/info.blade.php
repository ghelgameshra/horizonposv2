<div class="row mb-3 g-2">
    <div class="col-12 col-md-3">
        <div class="card bg-success">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="content-left">
                        <h4 class="mb-0 text-white" id="jumlahAntrian">0</h4>
                        <small class="text-white">Jumlah Antrian Hari Ini</small>
                    </div>
                    <span class="badge bg-label-primary rounded-circle p-2">
                        <i class="ti ti-list-numbers ti-md"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-3">
        <div class="card bg-info">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="content-left">
                        <h4 class="mb-0 text-white" id="antrianAktif">0</h4>
                        <small class="text-white">Antrian Terpanggil</small>
                    </div>
                    <span class="badge bg-label-primary rounded-circle p-2">
                        <i class="ti ti-list-check ti-md"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-2">
        <div class="card bg-warning" onmouseover="this.style.cursor='pointer'" onclick="panggilAntrianLanjut()">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="content-left">
                        <h4 class="mb-0 text-white" id="panggilAntrianLanjut">0</h4>
                        <small class="text-white">Selanjutnya</small>
                    </div>
                    <span class="badge bg-label-primary rounded-circle p-2">
                        <i class="ti ti-volume ti-md"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-2">
        <div class="card bg-warning" onmouseover="this.style.cursor='pointer'" onclick="panggilAntrian()">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="content-left">
                        <h4 class="mb-0 text-white" id="panggilAntrian">0</h4>
                        <small class="text-white">Panggil Antrian</small>
                    </div>
                    <span class="badge bg-label-primary rounded-circle p-2">
                        <i class="ti ti-refresh ti-md"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-2">
        <div class="card bg-primary" onmouseover="this.style.cursor='pointer'" onclick="buatAntrian()">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="content-left">
                        <h4 class="mb-0 text-white" id="tambahAntrian">0</h4>
                        <small class="text-white">Buat Antrian</small>
                    </div>
                    <span class="badge bg-label-primary rounded-circle p-2">
                        <i class="ti ti-plus ti-md"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-3 g-1">
    <div class="col-12 col-md-5">
        <div class="input-group">
            <span class="input-group-text">Periode Awal</span>
            <input type="text" id="periode_awal" class="form-control dob-picker" placeholder="YYYY-MM-DD" value="{{ now()->format('Y-m-d') }}" />
        </div>
    </div>
    <div class="col-12 col-md-5">
        <div class="input-group">
            <span class="input-group-text">Periode Akhir</span>
            <input type="text" id="periode_akhir" class="form-control dob-picker" placeholder="YYYY-MM-DD" value="{{ now()->format('Y-m-d') }}" />
        </div>
    </div>
    <div class="col-12 col-md-2">
        <div class="input-group">
            <span class="input-group-text">Interval</span>
            <select id="interval_get_data" class="form-select">
                <option value="1">1m</option>
                <option value="3">3m</option>
                <option value="5">5m</option>
                <option value="10">10m</option>
                <option value="15">15m</option>
            </select>
        </div>
    </div>
</div>

<div class="row mb-4 g-2">
    <div class="col-sm-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="content-left">
                        <h4 class="mb-0" id="pendapatanCash">Rp 0</h4>
                        <small>Pendapatan Cash</small>
                    </div>
                    <span class="badge bg-label-primary rounded-circle p-2">
                        <i class="ti ti-currency-dollar ti-md"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="content-left">
                        <h4 class="mb-0" id="pendapatanNonTunai">Rp 0</h4>
                        <small>Pendapatan Non Tunai</small>
                    </div>
                    <span class="badge bg-label-dark rounded-circle p-2">
                        <i class="ti ti-currency-dollar ti-md"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card bg-primary">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="content-left">
                        <h4 class="mb-0 text-white" id="totalPendapatan">Rp 0</h4>
                        <small class="text-white">Pendapatan Cash + Non</small>
                    </div>
                    <span class="badge bg-label-success rounded-circle p-2">
                        <i class="ti ti-currency-dollar ti-md"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card bg-warning">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="content-left">
                        <h4 class="mb-0 text-white" id="piutang">Rp 0</h4>
                        <small class="text-white">Piutang</small>
                    </div>
                    <span class="badge bg-label-danger rounded-circle p-2">
                        <i class="ti ti-currency-dollar ti-md"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="content-left">
                        <h4 class="mb-0" id="totalPesanan">0</h4>
                        <small>Total Pesanan</small>
                    </div>
                    <span class="badge bg-label-warning rounded-circle p-2">
                        <i class="ti ti-packages ti-md"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="content-left">
                        <h4 class="mb-0" id="totalPesananSelesai">0</h4>
                        <small>Pesanan Selesai</small>
                    </div>
                    <span class="badge bg-label-info rounded-circle p-2">
                        <i class="ti ti-package ti-md"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card bg-success">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="content-left">
                        <h4 class="mb-0 text-white" id="totalPesananDiambil">0</h4>
                        <small class="text-white">Pesanan Diambil</small>
                    </div>
                    <span class="badge bg-label-success rounded-circle p-2">
                        <i class="ti ti-package-export ti-md"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card bg-danger">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="content-left">
                        <h4 class="mb-0 text-white" id="totalPesananDibatalkan">0</h4>
                        <small class="text-white">Pesanan Dibatalkan</small>
                    </div>
                    <span class="badge bg-label-danger rounded-circle p-2">
                        <i class="ti ti-box-off ti-md"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
