<div class="row row-gap-2 mb-3">
    <div class="col-12">
        <p>Drop file</p>
    </div>

    <div class="col-12 col-md-6">
        <form action="{{ route('toko.changeLogo', ['jenis'=>'logo']) }}" class="dropzone needsclick border-1 rounded" id="dropzone-upload-logo" enctype="multipart/form-data">
            @method('put')
            @csrf
            <div class="dz-message needsclick row">
                <i class="menu-icon tf-icons ti ti-upload"></i>
                <span>Upload file logo</span>
            </div>
        </form>
    </div>
    <div class="col-12 col-md-6">
        <form action="{{ route('toko.changeLogo', ['jenis'=>'qr']) }}" class="dropzone needsclick border-1 rounded" id="dropzone-upload-qr" enctype="multipart/form-data">
            @method('put')
            @csrf
            <div class="dz-message needsclick row">
                <i class="menu-icon tf-icons ti ti-upload"></i>
                <span>Upload file QR Whatsapp</span>
            </div>
        </form>
    </div>

</div>

<form id="infoToko" method="POST">


    <div class="row">
        <input type="text" name="qr_wa_text" id="qr_wa_text" hidden>
        <div class="mb-2 col-12 col-md-6">
            <label for="nama_perusahaan" class="form-label">Nama Perusahaan</label>
            <input class="form-control" autocomplete="off" type="text" id="nama_perusahaan" name="nama_perusahaan" autofocus placeholder="nama perusahaan" />
        </div>
        <div class="mb-2 col-12 col-md-6">
            <label for="email" class="form-label">E-mail</label>
            <input class="form-control" autocomplete="off" type="text" id="email" name="email" placeholder="john.doe@example.com" />
        </div>
        <div class="mb-2 col-12 col-md-6">
            <label for="telepone" class="form-label">Nomor Telepone</label>
            <input class="form-control" autocomplete="off" type="text" id="telepone" name="telepone" placeholder="081233445566" />
        </div>
        <div class="mb-2 col-12 col-md-6">
            <label for="whatsapp" class="form-label">Whatsapp</label>
            <input class="form-control" autocomplete="off" type="text" id="whatsapp" name="whatsapp" placeholder="081233445566" />
        </div>
        <div class="mb-2 col-12 col-md-6">
            <label for="instagram" class="form-label">Instagram</label>
            <input class="form-control" autocomplete="off" type="text" id="instagram" name="instagram" placeholder="@instagram" />
        </div>
        <div class="mb-2 col-12 col-md-6">
            <label for="facebook" class="form-label">Facebook</label>
            <input class="form-control" autocomplete="off" type="text" id="facebook" name="facebook" placeholder="@facebook" />
        </div>
        <div class="mb-2 col-12 col-md-6">
            <label for="tiktok" class="form-label">TikTok</label>
            <input class="form-control" autocomplete="off" type="text" id="tiktok" name="tiktok" placeholder="@tiktok" />
        </div>
        <div class="mb-2 col-12 col-md-6">
            <label for="web" class="form-label">Web</label>
            <input class="form-control" autocomplete="off" type="text" id="web" name="web" placeholder="https://" />
        </div>
        <div class="mb-2 col-12 col-md-6">
            <label for="alamat_lengkap" class="form-label">Alamat Lengkap</label>
            <textarea name="alamat_lengkap" id="alamat_lengkap" rows="3" class="form-control" autocomplete="off" placeholder="alamat lengkap"></textarea>
        </div>
        <div class="mt-3">
            <button type="submit" class="btn btn-primary me-2">Save changes</button>
            <button type="reset" class="btn btn-label-secondary">Cancel</button>
        </div>
    </div>
</form>
