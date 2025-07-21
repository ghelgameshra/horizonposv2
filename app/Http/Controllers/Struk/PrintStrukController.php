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
            ->where('default_printer', true)
            ->first();

        if ($this->transaksi?->status_order === 'CANCEL SALES') {
            throw new HttpResponseException(response([
                'message' => 'Pesanan cancel sales tidak bisa print struk'
            ], 422));
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

        $this->printer->text("\n\n");
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

        $this->printLine("Tanggal", $this->transaksi->created_at->locale('id')->translatedFormat("D, d M Y"));
        $this->printLine("Jam", $this->transaksi->created_at->locale('id')->translatedFormat("H:i:s"));
        $this->printLine("Customer", $this->transaksi->nama_customer);
        $this->printLine("Telp", $this->transaksi->nomor_telepone);
        $this->printLine("Invoice", $this->transaksi->invno);
        $this->printLine("Kasir", $this->transaksi->kasir->name);

        $this->printer->text("================================\n\n");

        foreach ($this->transaksi->transaksiLog as $i => $item) {
            $harga = $item->harga_ukuran > 0 ? $item->harga_ukuran : $item->harga_jual;
            $namaProduk = $item->namafile ? "{$item->namafile} {$item->ukuran} {$item->nama_produk}" : $item->nama_produk;

            $this->printer->text(($i + 1) . ". {$namaProduk}\n");
            $this->printer->text(str_pad($this->formatRupiah($harga), 12));
            $this->printer->text(str_pad("x{$item->jumlah}", 6));
            $this->printer->text($this->formatRupiah($item->total) . "\n\n");
        }

        $this->printer->text("================================\n");
        $this->printer->setEmphasis(true);

        $this->printLine("Subtotal", $this->formatRupiah($this->transaksi->subtotal));
        if ($this->transaksi->diskon > 0) {
            $this->printLine("Diskon", $this->formatRupiah($this->transaksi->diskon));
        }
        $this->printLine("Total", $this->formatRupiah($this->transaksi->total));

        $uangMuka = $this->transaksi->uang_muka;
        $terima = $this->transaksi->terima;
        $total = $this->transaksi->total;
        $tipeBayar = $this->transaksi->tipe_bayar;
        $tipePelunasan = $this->transaksi->tipe_bayar_pelunasan;

        if ($tipePelunasan === null) {
            $this->printLine("DP", $this->formatRupiah($uangMuka));
            $this->printLine("Tipe Bayar", $tipeBayar);
            $this->printLine("Pelunasan", $this->formatRupiah($uangMuka - $total));
            $this->printLine("Status", "BELUM LUNAS");
        }

        if (in_array($tipeBayar, ["DPTRF", "DPCSH"]) && $tipePelunasan) {
            $pelunasan = $total - $uangMuka;
            $terimaPelunasan = $terima - $uangMuka;
            $kembali = $terima - $total;

            $this->printLine("DP", $this->formatRupiah($uangMuka));
            $this->printLine("Tipe Bayar", $tipeBayar);
            $this->printLine("Pelunasan", $this->formatRupiah($pelunasan));
            $this->printLine("Terima", $this->formatRupiah($terimaPelunasan));
            $this->printLine("Kembali", $this->formatRupiah($kembali));
            $this->printLine("Pelunasan", $tipePelunasan);
            $this->printLine("Status", "LUNAS");
        }

        if($tipeBayar === $tipePelunasan) {
            $kembali = $terima - $total;
            $this->printLine("Terima", $this->formatRupiah($terima));
            $this->printLine("Kembali", $this->formatRupiah($kembali));
            $this->printLine("Tipe Bayar", $tipeBayar);
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
        $text = $this->toko->qr_wa_text ?: 'https://wa.me/qr/QPPU3B7C6PMUM1';
        $this->printer->setJustification(Printer::JUSTIFY_CENTER);
        $this->printer->qrCode($text);
    }

    public function test(): void
    {
        $this->printer = new Printer($this->connector);

        $this->printer->setJustification(Printer::JUSTIFY_CENTER);
        $this->printer->setEmphasis(true);
        $this->printer->text($this->toko->nama_perusahaan . "\n");
        $this->printer->setEmphasis(false);
        $this->printer->text($this->toko->alamat_lengkap . "\n");
        $this->printer->text("\n\n\n");
        $this->printer->close();
    }

    private function printLine(string $label, string $value): void
    {
        $this->printer->text(str_pad($label, 10, ' ', STR_PAD_RIGHT));
        $this->printer->text(": {$value}\n");
    }

    private function formatRupiah($value): string
    {
        return 'Rp.' . number_format($value, 0, ',', '.');
    }
}
