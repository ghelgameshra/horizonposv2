<div class="col-12" style="z-index: 99; position: relative; top: -20px">
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-12 text-center mb-1">
                    <div class="row g-1">
                        @include('pages.kasir.form-nominal')
                    </div>
                </div>

                <div class="row">
                    <div class="card-datatable table-responsive pt-0">
                        <div class="input-group input-group-sm mb-1 mt-3">
                            <button class="btn btn-sm btn-primary w-full" data-bs-toggle="offcanvas" data-bs-target="#addDataOffcanvas">
                                <i class="menu-icon tf-icons ti ti-search"></i>
                            </button>
                            <input type="text" class="form-control border" placeholder="cari barang ... (ctrl+enter)" id="cariBarang">
                            <input type="text" class="form-control border-0 border-start text-end" value="" id="nameTag" @readonly(true)>
                        </div>
                        @include('pages.kasir.table-selected')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
