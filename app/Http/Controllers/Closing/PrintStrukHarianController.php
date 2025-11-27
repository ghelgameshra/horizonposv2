<?php

namespace App\Http\Controllers\Closing;

use App\Http\Controllers\Controller;
use App\Models\Administrasi\TutupHarian;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Http\Exceptions\HttpResponseException;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Carbon\Carbon;

class PrintStrukHarianController extends Controller
{
    protected $toko;
    protected $printerStruk;
    protected $connector;
    protected $dataHarian;

    public function __construct($tanggalHarian = null)
    {
        $this->toko = DB::table('toko')->first();

        $this->printerStruk = DB::table('setting_printer')
            ->where('jenis_printer', 'STRUK')
            ->where('default_printer', true)
            ->first();

        if (!$tanggalHarian) {
            $tanggalHarian = now()->format('Y-m-d');
        }

        $this->dataHarian = TutupHarian::with('tutupHarianDetail')
            ->whereDate('tanggal_harian', $tanggalHarian)
            ->first();

        if (!$this->dataHarian) {
            throw new HttpResponseException(response([
                'message' => "Data tutup harian tidak ditemukan untuk tanggal {$tanggalHarian}"
            ], 404));
        }

        try {
            $protocol = $this->printerStruk->protocol_printer;
            $username = Crypt::decryptString($this->printerStruk->username_printer);
            $password = Crypt::decryptString($this->printerStruk->password_printer);
            $ip = $this->printerStruk->ip_printer;
            $namaPrinter = $this->printerStruk->nama_printer;

            if ($protocol === "LINUXUSB") {
                $this->connector = new FilePrintConnector($ip);
            }

            if ($protocol === "SMB") {
                $this->connector = new WindowsPrintConnector("smb://$username:$password@$ip/$namaPrinter");
            }
        } catch (\Throwable $th) {
            return 'Error: ' . $th->getMessage();
        }
    }

    public function print()
    {
        $printer = new Printer($this->connector);
        $detail = $this->dataHarian->tutupHarianDetail;

        try {
            // Header toko
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setEmphasis(true);
            $printer->text(strtoupper($this->toko->nama_perusahaan) . "\n");
            $printer->feed();

            // Judul laporan
            $printer->setEmphasis(true);
            $printer->text("LAPORAN TUTUP HARIAN\n");
            $printer->setEmphasis(false);
            $printer->feed();

            // Info utama
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text("INVNO   : {$this->dataHarian->invno}\n");
            $printer->text("Tanggal : " . Carbon::parse($this->dataHarian->tanggal_harian)->format('d-m-Y') . "\n");
            $printer->text("User    : {$this->dataHarian->user}\n");
            $printer->feed();

            // Detail transaksi
            $printer->text("Jumlah Sales   : {$detail->jumlah_sales}\n");
            $printer->text("Pesanan Selesai: {$detail->pesanan_selesai}\n");
            $printer->text("Pesanan Cancel : {$detail->pesanan_cancel}\n");
            $printer->feed();

            $printer->text("Total Sales    : Rp " . number_format($detail->total_nominal_sales, 0, ',', '.') . "\n");
            $printer->text("Diskon         : Rp " . number_format($detail->total_nominal_diskon, 0, ',', '.') . "\n");
            $printer->feed();

            $printer->text("Pembayaran Tunai\n");
            $printer->text(" DPCSH : {$detail->jumlah_bayar_dpcsh} trx, Rp " . number_format($detail->total_bayar_dpcsh, 0, ',', '.') . "\n");
            $printer->text(" CSH   : {$detail->jumlah_bayar_csh} trx, Rp " . number_format($detail->total_bayar_csh, 0, ',', '.') . "\n");
            $printer->feed();

            $printer->text("Pembayaran Transfer\n");
            $printer->text(" DPTRF : {$detail->jumlah_bayar_dptrf} trx, Rp " . number_format($detail->total_bayar_dptrf, 0, ',', '.') . "\n");
            $printer->text(" TRF   : {$detail->jumlah_bayar_trf} trx, Rp " . number_format($detail->total_bayar_trf, 0, ',', '.') . "\n");
            $printer->feed();

            $printer->text("Png. Csh       : Rp " . number_format($detail->pengeluaran_csh, 0, ',', '.') . "\n");
            $printer->text("Png. Trf       : Rp " . number_format($detail->pengeluaran_trf, 0, ',', '.') . "\n");
            $printer->text("Png. Total     : Rp " . number_format($detail->pengeluaran_csh + $detail->pengeluaran_trf, 0, ',', '.') . "\n");
            $printer->feed();

            $printer->text("Piutang        : Rp " . number_format($detail->piutang, 0, ',', '.') . "\n");
            $printer->text("Uang Fisik     : Rp " . number_format($detail->rptotal, 0, ',', '.') . "\n");
            $printer->text("Selisih Fisik  : Rp " . number_format($detail->selisih_fisik, 0, ',', '.') . "\n");

            // Footer
            $printer->feed(2);
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("Dicetak: " . now()->format('d-m-Y H:i:s') . "\n");
            $printer->cut();
        } finally {
            $printer->close();
        }

        return response()->json(['message' => 'Struk tutup harian berhasil dicetak']);
    }
}
