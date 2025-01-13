<!-- Menu -->
<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="index.html" class="app-brand-link">
            <span class="app-brand-logo demo">
                <svg width="32" height="22" viewBox="0 0 32 22" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M0.00172773 0V6.85398C0.00172773 6.85398 -0.133178 9.01207 1.98092 10.8388L13.6912 21.9964L19.7809 21.9181L18.8042 9.88248L16.4951 7.17289L9.23799 0H0.00172773Z"
                        fill="#7367F0" />
                    <path opacity="0.06" fill-rule="evenodd" clip-rule="evenodd"
                        d="M7.69824 16.4364L12.5199 3.23696L16.5541 7.25596L7.69824 16.4364Z"
                        fill="#161616" />
                    <path opacity="0.06" fill-rule="evenodd" clip-rule="evenodd"
                        d="M8.07751 15.9175L13.9419 4.63989L16.5849 7.28475L8.07751 15.9175Z"
                        fill="#161616" />
                    <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M7.77295 16.3566L23.6563 0H32V6.88383C32 6.88383 31.8262 9.17836 30.6591 10.4057L19.7824 22H13.6938L7.77295 16.3566Z"
                        fill="#7367F0" />
                </svg>
            </span>
            <span class="app-brand-text demo menu-text fw-bold">Vuexy</span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
            <i class="ti menu-toggle-icon d-none d-xl-block ti-sm align-middle"></i>
            <i class="ti ti-x d-block d-xl-none ti-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        @can('show dashboard')
        <!-- Dashboards -->
        <li class="menu-item {{ Request::is('dashboard') ? 'active' : '' }}">
            <a href="{{ route('dashboard') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-smart-home"></i>
                <div data-i18n="Dashboards">Dashboards</div>
            </a>
        </li>
        @endcan

        @can('show kasir')
        <li class="menu-item">
            <a href="{{ route('kasir.index') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-calculator"></i>
                <div data-i18n="Kasir">Kasir</div>
            </a>
        </li>
        @endcan

        <!-- Layouts -->
        @can('show master')
        <li class="menu-item {{ Request::is('*master/*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ti ti-database"></i>
                <div data-i18n="Master">Master</div>
            </a>

            <ul class="menu-sub">
                @can('show produk')
                <li class="menu-item {{ Request::is('master/produk') ? 'active' : '' }}">
                    <a href="{{ route('produk.index') }}" class="menu-link">
                        <div data-i18n="Produk & Kategori">Produk & Kategori</div>
                    </a>
                </li>
                @endcan
                @can('show member')
                <li class="menu-item {{ Request::is('master/member') ? 'active' : '' }}">
                    <a href="{{ route('member.index') }}" class="menu-link">
                        <div data-i18n="Member">Member</div>
                    </a>
                </li>
                @endcan
                @can('show promosi')
                <li class="menu-item {{ Request::is('master/promosi') ? 'active' : '' }}">
                    <a href="{{ route('promosi.index') }}" class="menu-link">
                        <div data-i18n="Promosi">Promosi</div>
                    </a>
                </li>
                @endcan
                @can('show karyawan')
                <li class="menu-item {{ Request::is('master/karyawan') ? 'active' : '' }}">
                    <a href="{{ route('karyawan.index') }}" class="menu-link">
                        <div data-i18n="Pegawai">Pegawai</div>
                    </a>
                </li>
                @endcan
            </ul>
        </li>
        @endcan

        @can('show transaksi')
        <li class="menu-item {{ Request::is('*transaksi/*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ti ti-report-analytics"></i>
                <div data-i18n="Transaksi">Transaksi</div>
            </a>

            <ul class="menu-sub">
                @can('show pelunasan')
                <li class="menu-item {{ Request::is('transaksi/pelunasan') ? 'active' : '' }}">
                    <a href="{{ route('pelunasan.index') }}" class="menu-link">
                        <div data-i18n="Pelunasan">Pelunasan</div>
                    </a>
                </li>
                @endcan
                @can('show reprint')
                <li class="menu-item {{ Request::is('transaksi/reprint-transaksi') ? 'active' : '' }}">
                    <a href="{{ route('transaksi.reprint') }}" class="menu-link">
                        <div data-i18n="Reprint & Cancel">Reprint & Cancel</div>
                    </a>
                </li>
                @endcan
            </ul>
        </li>
        @endcan

        @can('show pesanan')
        <li class="menu-item {{ Request::is('*pesanan/*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ti ti-arrows-sort"></i>
                <div data-i18n="Pesanan">Pesanan</div>
            </a>

            <ul class="menu-sub">
                @can('show ambil pesanan')
                <li class="menu-item {{ Request::is('pesanan/pengambilan') ? 'active' : '' }}">
                    <a href="{{ route('pengambilan.index') }}" class="menu-link">
                        <div data-i18n="Ambil Pesanan">Ambil Pesanan</div>
                    </a>
                </li>
                @endcan
                @can('show work order')
                <li class="menu-item {{ Request::is('pesanan/work-order') ? 'active' : '' }}">
                    <a href="javascript:void(0);" class="menu-link menu-toggle">
                        <div data-i18n="Work Order">Work Order</div>
                    </a>
                    <ul class="menu-sub" id="work_order_list">

                    </ul>
                </li>
                @endcan
            </ul>
        </li>
        @endcan

        @can('show laporan')
        <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ti ti-report"></i>
                <div data-i18n="Laporan">Laporan</div>
            </a>

            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('member.index') }}" class="menu-link">
                        <div data-i18n="Ringkasan Penjualan">Ringkasan Penjualan</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('produk.index') }}" class="menu-link">
                        <div data-i18n="Laporan Penjualan">Laporan Penjualan</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('produk.index') }}" class="menu-link">
                        <div data-i18n="Laporan Pengeluaran">Laporan Pengeluaran</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('member.index') }}" class="menu-link">
                        <div data-i18n="Laporan Kas">Laporan Kas</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('member.index') }}" class="menu-link">
                        <div data-i18n="Piutang">Piutang</div>
                    </a>
                </li>
            </ul>
        </li>
        @endcan

        @can('show pengeluaran')
        <li class="menu-item {{ Request::is('pengeluaran') ? 'active' : '' }}">
            <a href="{{ route('pengeluaran.index') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-coins"></i>
                <div data-i18n="Pengeluaran">Pengeluaran</div>
            </a>
        </li>
        @endcan

        @can('show tutup harian')
        <li class="menu-item {{ Request::is('tutup-harian') ? 'active' : '' }}">
            <a href="{{ route('tutupHarian.index') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-checkup-list"></i>
                <div data-i18n="Tutup Harian">Tutup Harian</div>
            </a>
        </li>
        @endcan

        <!-- Apps & Pages -->
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text" data-i18n="Apps & Pages">Apps &amp; Pages</span>
        </li>

        @can('show pengaturan')
        <li class="menu-item {{ Request::is('*pengaturan/*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ti ti-settings"></i>
                <div data-i18n="Pengaturan">Pengaturan</div>
            </a>

            <ul class="menu-sub">
                @can('show lisensi')
                <li class="menu-item">
                    <a href="{{ route('member.index') }}" class="menu-link">
                        <div data-i18n="Lisensi">Lisensi</div>
                    </a>
                </li>
                @endcan
                <li class="menu-item {{ Request::is('pengaturan/toko') ? 'active open' : '' }}">
                    <a href="{{ route('toko.index') }}" class="menu-link">
                        <div data-i18n="Toko">Toko</div>
                    </a>
                </li>
                @can('show printer')
                <li class="menu-item {{ Request::is('pengaturan/printer') ? 'active open' : '' }}">
                    <a href="{{ route('printer.index') }}" class="menu-link">
                        <div data-i18n="Printer">Printer</div>
                    </a>
                </li>
                @endcan
                @can('show server')
                <li class="menu-item">
                    <a href="{{ route('member.index') }}" class="menu-link">
                        <div data-i18n="Server">Server</div>
                    </a>
                </li>
                @endcan
                @can('show user')
                <li class="menu-item {{ Request::is('pengaturan/user') ? 'active open' : '' }}">
                    <a href="{{ route('user.index') }}" class="menu-link">
                        <div data-i18n="User">User</div>
                    </a>
                </li>
                @endcan
            </ul>
        </li>
        @endcan

        <li class="menu-item">
            <a href="app-email.html" class="menu-link">
                <i class="menu-icon tf-icons ti ti-lifebuoy"></i>
                <div data-i18n="Bantuan">Bantuan</div>
            </a>
        </li>

        <li class="menu-item">
            <a href="app-chat.html" class="menu-link">
                <i class="menu-icon tf-icons ti ti-messages"></i>
                <div data-i18n="Chat">Chat</div>
            </a>
        </li>

        <li class="menu-header small text-uppercase {{ Request::is('kasir') ? '' : 'd-none' }}">
            <span class="menu-header-text" data-i18n="User">User</span>
        </li>
        <li class="menu-item {{ Request::is('kasir') || Request::is('user-permission-failed') ? '' : 'd-none' }}">
            <a href="#" class="menu-link" id="logOutButton">
                <i class="menu-icon tf-icons ti ti-logout"></i>
                <div data-i18n="Log Out">Log Out</div>
            </a>
        </li>
    </ul>
</aside>
<!-- / Menu -->

@push('js')
<script>
$(function(){
    $.get(`{{ route('select2JenisKategori') }}`)
    .done((response) => {
        setListCategory(response.data);
    })
})

function setListCategory(data){
    $('#work_order_list').children().remove();
    data.forEach(element => {
        let kategori_temp = (element.text).toLowerCase();
        if(element.text !== 'JASA' && element.text !== 'FINISHING'){
            $('#work_order_list').append(`<li class="menu-item">
                <a href="{{ route('workOrder.index') }}?category=${element.id}&name=${kategori_temp}" class="menu-link">
                    <div class='text-capitalize'>${kategori_temp}</div>
                </a>
            </li>`)
        }
    });
}

$('#logOutButton').on('click', function(){
    $('#formLogOut').submit();
})
</script>
@endpush
