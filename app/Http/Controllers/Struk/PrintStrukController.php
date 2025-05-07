<?php

namespace App\Http\Controllers\Struk;

require __DIR__ . '../../../../../vendor/autoload.php';
use App\Http\Controllers\Controller;
use App\Models\Transaksi\Transaksi;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;

class PrintStrukController extends Controller
{
    private $toko;
    private $settingStruk;
    private $pesanStruk;
    private $printerStruk;
    private $transaksi;
    private $connector;
    private $printer;

    public function __construct($invno = 0)
    {
        $this->toko = DB::table('toko')->first();
        $this->settingStruk = DB::table('setting_struk')->where('key', '!=', 'AUPR')->get();
        $this->pesanStruk = DB::table('setting_pesan_struk')->get();
        $this->transaksi = Transaksi::with(['transaksiLog', 'kasir'])->where('invno', $invno)->first();

        $this->printerStruk = DB::table('setting_printer')
        ->where('jenis_printer', 'STRUK')
        ->where('default_printer', true)->first();

        if($this->transaksi->status_order === "CANCEL SALES") {
            throw new HttpResponseException(response([
                'message'   => 'Pesanan cancel sales tidak bisa print struk'
            ], 422));
        }

        try {
            $protocol = $this->printerStruk->protocol_printer;
            $username = Crypt::decryptString($this->printerStruk->username_printer);
            $password = Crypt::decryptString($this->printerStruk->password_printer);
            $ip = $this->printerStruk->ip_printer;
            $namaPrinter = $this->printerStruk->nama_printer;

            if($protocol === "LINUXUSB"){
                $this->connector = new FilePrintConnector($ip);
            }

            if($protocol === "SMB"){
                $this->connector = new WindowsPrintConnector("smb://$username:$password@$ip/$namaPrinter");
            }
        } catch (\Throwable $th) {
            throw new HttpResponseException(response([
                'message' => 'Error: ' . $th->getMessage()
            ]));
        }
    }

    public function print(): void
    {
        $this->printer = new Printer($this->connector);
        $header = $this->settingStruk->where('key', 'HEDS')->first();
        $body = $this->settingStruk->where('key', 'ISIS')->first();
        $footer = $this->settingStruk->where('key', 'FOOS')->first();
        $message = $this->settingStruk->where('key', 'PESS')->first();
        $qr = $this->settingStruk->where('key', 'QRSK')->first();

        if ($header->status) $this->printHeader();
        if ($body->status) $this->printBody();
        if ($footer->status) $this->printFooter();
        if ($message->status) $this->printMessage();
        if ($qr->status) $this->printQr();

        $this->printer->text("\n");
        $this->printer->text("\n");
        $this->printer->close();
    }

    private function printHeader(): void
    {
        $this->printer->setJustification(Printer::JUSTIFY_CENTER);
        $this->printer->setEmphasis(true);
        $this->printer->text($this->toko->nama_perusahaan . "\n");
        $this->printer->setEmphasis(false);
        $this->printer->text($this->toko->alamat_lengkap . "\n");
    }

    private function printBody(): void
    {
        $this->printer->text("================================\n");
        $this->printer->setJustification(Printer::JUSTIFY_LEFT);
        $this->printer->text( str_pad("Tanggal", 10, ' ', STR_PAD_RIGHT) );
        $this->printer->text(": " . $this->transaksi->tanggal_transaksi . "\n");

        $this->printer->text( str_pad("Customer", 10, ' ', STR_PAD_RIGHT) );
        $this->printer->text(": " . $this->transaksi->nama_customer . "\n");

        $this->printer->text( str_pad("Invoice", 10, ' ', STR_PAD_RIGHT) );
        $this->printer->text(": " . $this->transaksi->invno . "\n");

        $this->printer->text( str_pad("Kasir", 10, ' ', STR_PAD_RIGHT) );
        $this->printer->text(": " . $this->transaksi->kasir->name . "\n");
        $this->printer->text("================================\n");
        $this->printer->text("\n");

        foreach ($this->transaksi->transaksiLog as $key => $value) {
            $hargaTemp = "Rp." . number_format($value->harga_ukuran > 0 ? $value->harga_ukuran : $value->harga_jual, 0, ',', '.');
            $totalTemp = "Rp." . number_format($value->total, 0, ',', '.');
            $namaFileTemp = $value->namafile ? $value->namafile . " " . $value->ukuran . " $value->nama_produk" : $value->nama_produk;
            $key += 1;
            $this->printer->text("$key.$namaFileTemp\n");
            $this->printer->text(str_pad($hargaTemp, 12, ' ', STR_PAD_RIGHT));
            $this->printer->text(str_pad("x$value->jumlah", 6, ' ', STR_PAD_RIGHT));
            $this->printer->text("$totalTemp\n");
            $this->printer->text("\n");
        }

        $subtotal = number_format($this->transaksi->subtotal, 0, ',', '.');
        $diskon = number_format($this->transaksi->diskon, 0, ',', '.');
        $total = number_format($this->transaksi->total, 0, ',', '.');
        $terima = number_format(($this->transaksi->uang_muka - $this->transaksi->total), 0, ',', '.');
        $kembali = number_format($this->transaksi->kembali, 0, ',', '.');
        $uangMuka = number_format($this->transaksi->uang_muka, 0, ',', '.');

        $this->printer->text("================================\n");
        $this->printer->setEmphasis(true);
        $this->printer->text( str_pad("Subtotal", 10, ' ', STR_PAD_RIGHT) );
        $this->printer->text(": Rp." . $subtotal . "\n");

        if ($this->transaksi->diskon > 0){
            $this->printer->text( str_pad("Diskon", 10, ' ', STR_PAD_RIGHT) );
            $this->printer->text(": Rp." . $diskon . "\n");
        }

        $this->printer->text( str_pad("Total", 10, ' ', STR_PAD_RIGHT) );
        $this->printer->text(": Rp." . $total . "\n");

        if($this->transaksi->uang_muka > 0 && $this->transaksi->tipe_bayar_pelunasan === null){
            $this->printer->text( str_pad("DP", 10, ' ', STR_PAD_RIGHT) );
            $this->printer->text(": Rp." . $uangMuka . "\n");
            $this->printer->text( str_pad("Tipe Bayar", 10, ' ', STR_PAD_RIGHT) );
            $this->printer->text(": " . $this->transaksi->tipe_bayar . "\n");
            $this->printer->text( str_pad("Pelunasan", 10, ' ', STR_PAD_RIGHT) );
            $this->printer->text(": Rp." . $terima . "\n");
            $this->printer->text( str_pad("status", 10, ' ', STR_PAD_RIGHT) );
            $this->printer->text(": " . "BELUM LUNAS" . "\n");
        }

        $kurangBayar = number_format($this->transaksi->uang_muka - $this->transaksi->total, 0, ',', '.');
        $kembaliKurangBayar = number_format($this->transaksi->terima - $this->transaksi->total, 0, ',', '.');
        if($this->transaksi->uang_muka > 0 && $this->transaksi->terima > 0){
            $terimaPelunasan = number_format($this->transaksi->terima - $this->transaksi->uang_muka, 0, ',', '.');

            $this->printer->text( str_pad("DP", 10, ' ', STR_PAD_RIGHT) );
            $this->printer->text(": Rp." . $uangMuka . "\n");
            $this->printer->text( str_pad("Tipe Bayar", 10, ' ', STR_PAD_RIGHT) );
            $this->printer->text(": " . $this->transaksi->tipe_bayar . "\n");
            $this->printer->text( str_pad("Pelunasan", 10, ' ', STR_PAD_RIGHT) );
            $this->printer->text(": Rp." . $kurangBayar . "\n");
            $this->printer->text( str_pad("Terima", 10, ' ', STR_PAD_RIGHT) );
            $this->printer->text(": Rp." . $terimaPelunasan . "\n");
            $this->printer->text( str_pad("Kembali", 10, ' ', STR_PAD_RIGHT) );
            $this->printer->text(": Rp." . $kembaliKurangBayar . "\n");
            $this->printer->text( str_pad("Pelunasan", 10, ' ', STR_PAD_RIGHT) );
            $this->printer->text(": " . $this->transaksi->tipe_bayar_pelunasan . "\n");
            $this->printer->text( str_pad("status", 10, ' ', STR_PAD_RIGHT) );
            $this->printer->text(": " . "LUNAS" . "\n");
        }

        if($this->transaksi->uang_muka === 0 && $this->transaksi->terima > 0) {
            $terima = number_format(($this->transaksi->terima), 0, ',', '.');
            $kembali = number_format(($this->transaksi->terima - $this->transaksi->total), 0, ',', '.');

            $this->printer->text( str_pad("Terima", 10, ' ', STR_PAD_RIGHT) );
            $this->printer->text(": Rp." . $terima . "\n");
            $this->printer->text( str_pad("Kembali", 10, ' ', STR_PAD_RIGHT) );
            $this->printer->text(": Rp." . $kembali . "\n");
            $this->printer->text( str_pad("Tipe Bayar", 10, ' ', STR_PAD_RIGHT) );
            $this->printer->text(": " . $this->transaksi->tipe_bayar . "\n");
        }
        $this->printer->setEmphasis(false);
        $this->printer->text("================================\n");
    }

    private function printFooter(): void
    {
        $this->printer->setJustification(Printer::JUSTIFY_CENTER);
        $this->printer->text("Telp. " . $this->toko->telepone . "\n");
        $this->printer->text("Wa. " . $this->toko->whatsapp . "\n");
        $this->printer->text("\n");
        $this->printer->text("- TERIMA KASIH -\n");
        $this->printer->text("\n");
    }

    private function printMessage(): void
    {
        $this->printer->setJustification(Printer::JUSTIFY_CENTER);
        foreach ($this->pesanStruk as $value) {
            $this->printer->text($value->pesan . "\n");
        }
        $this->printer->text("\n");
    }

    private function printQr(): void
    {
        $text = $this->toko->qr_wa_text;
        if(!$this->toko->qr_wa_text){
            $text = 'https://wa.me/qr/QPPU3B7C6PMUM1';
        }
        $this->printer->setJustification(Printer::JUSTIFY_CENTER);
        $this->printer -> qrCode($text);
    }

    public function test(): void
    {
        $this->printer = new Printer($this->connector);

        $this->printer->setJustification(Printer::JUSTIFY_CENTER);
        $this->printer->setEmphasis(true);
        $this->printer->text($this->toko->nama_perusahaan . "\n");
        $this->printer->setEmphasis(false);
        $this->printer->text($this->toko->alamat_lengkap . "\n");
        $this->printer->text("\n");
        $this->printer->text("\n");
        $this->printer->text("\n");
        $this->printer->close();
    }

}
