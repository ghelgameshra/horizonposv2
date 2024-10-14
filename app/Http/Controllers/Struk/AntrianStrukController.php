<?php

namespace App\Http\Controllers\Struk;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

require __DIR__ . '../../../../../vendor/autoload.php';
use App\Models\Transaksi\Transaksi;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Crypt;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;

class AntrianStrukController extends Controller
{
    private $noAntrian;
    private $printerStruk;
    private $connector;
    private $printer;
    private $namaAntrian;
    private $toko;

    public function __construct($noAntrian = 0, $namaAntrian = 'test')
    {
        $this->noAntrian = str_pad($noAntrian, 2, '0', STR_PAD_LEFT);
        $this->namaAntrian = strtoupper($namaAntrian);
        $this->toko = DB::table('toko')->first();

        $this->printerStruk = DB::table('setting_printer')
        ->where('jenis_printer', 'ANTRIAN')
        ->where('default_printer', true)->first();

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

        $this->printer->setJustification(Printer::JUSTIFY_CENTER);
        $this->printer->setEmphasis(true);
        $this->printer->text($this->toko->nama_perusahaan . "\n");
        $this->printer->setEmphasis(false);
        $this->printer->text($this->toko->alamat_lengkap . "\n");
        $this->printer->text("================================\n");

        $this->printer->text("$this->namaAntrian\n");
        $this->printer->text( now() . "\n");
        $this->printer->text("\n");

        $this->printer->setEmphasis(true);
        $this->printer->text("NOMOR ANTRIAN\n");
        $this->printer->setTextSize(5,5);
        $this->printer->text("$this->noAntrian\n");
        $this->printer->setEmphasis(false);
        $this->printer->selectPrintMode(); // Reset
        $this->printer->text("\n");
        $this->printer->text("-TERIMA KASIH TELAH MENUNGGU-");

        $this->printer->text("\n");
        $this->printer->text("\n");
        $this->printer->text("\n");
        $this->printer->close();
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
