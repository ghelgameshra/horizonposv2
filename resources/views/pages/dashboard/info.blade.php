<div class="row mb-3 g-2">
    <div class="col-12 col-md-3">
        <div class="d-flex flex-column gap-1">
            <div>
                <button class="w-100 btn btn-sm btn-success d-flex gap-1 text-uppercase fw-bold">
                    <span class="flex-fill" id="jumlahAntrianDesain">0 antrian desain</span>
                    <span class="flex-fill">
                        <i class="ti ti-arrows-sort"></i>
                    </span>
                </button>
            </div>
            <div>
                <button class="w-100 btn btn-sm btn-success d-flex gap-1 text-uppercase fw-bold">
                    <span class="flex-fill" id="jumlahAntrianCetak">0 antrian cetak</span>
                    <span class="flex-fill">
                        <i class="ti ti-sort-ascending-numbers"></i>
                    </span>
                </button>
            </div>
        </div>
    </div>


    <div class="col-12 col-md-3">
        <div class="d-flex flex-column gap-1">
            <div>
                <button class="w-100 btn btn-sm btn-info d-flex gap-1 text-uppercase fw-bold">
                    <span class="flex-fill" id="antrianDesainTerpanggil">desain 0 dipanggil</span>
                    <span class="flex-fill">
                        <i class="ti ti-list"></i>
                    </span>
                </button>
            </div>
            <div>
                <button class="w-100 btn btn-sm btn-info d-flex gap-1 text-uppercase fw-bold">
                    <span class="flex-fill" id="antrianCetakTerpanggil">cetak 0 dipanggil</span>
                    <span class="flex-fill">
                        <i class="ti ti-list-numbers"></i>
                    </span>
                </button>
            </div>
        </div>
    </div>


    <div class="col-12 col-md-3">
        <div class="d-flex flex-column gap-1">
            <div>
                <button class="w-100 btn btn-sm btn-warning d-flex gap-1 text-uppercase fw-bold" onclick="panggilAntrianLanjut()">
                    <span class="flex-fill">selanjutnya</span>
                    <span class="flex-fill">
                        <i class="ti ti-volume"></i>
                    </span>
                </button>
            </div>
            <div>
                <button class="w-100 btn btn-sm btn-warning d-flex gap-1 text-uppercase fw-bold" onclick="panggilAntrian()">
                    <span class="flex-fill">panggil ulang</span>
                    <span class="flex-fill">
                        <i class="ti ti-refresh"></i>
                    </span>
                </button>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-3">
        <div class="d-flex flex-column gap-1">
            <div>
                <button class="w-100 btn btn-sm btn-primary btn-info d-flex gap-1 text-uppercase fw-bold" onclick="buatAntrian('ATRD')">
                    <span class="flex-fill">antrian desain</span>
                    <span class="flex-fill">
                        <i class="ti ti-circle-plus"></i>
                    </span>
                </button>
            </div>
            <div>
                <button class="w-100 btn btn-sm btn-primary btn-info d-flex gap-1 text-uppercase fw-bold" onclick="buatAntrian('ATRS')">
                    <span class="flex-fill">antrian cetak</span>
                    <span class="flex-fill">
                        <i class="ti ti-circle-plus"></i>
                    </span>
                </button>
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
