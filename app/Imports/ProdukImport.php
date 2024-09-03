<?php

namespace App\Imports;

use App\Models\Produk\Produk;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;

class ProdukImport implements ToModel, WithUpserts, WithHeadingRow, WithBatchInserts
{
    /**
     * Define the unique key(s) to perform upserts on.
     *
     * @return string|array
     */
    public function uniqueBy()
    {
        return 'plu'; // Kolom unik yang digunakan untuk identifikasi
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $kategori = DB::table('ref_kategori')->where('nama_kategori', $row['nama_kategori'])->first();
        if(!$kategori){
            DB::table('ref_kategori')->insert([
                'nama_kategori' => strtoupper($row['nama_kategori'])
            ]);
            $kategori = DB::table('ref_kategori')->where('nama_kategori', $row['nama_kategori'])->first();
        }

        $jenisUkuran = DB::table('ref_jenis_ukuran')->where('nama_jenis', $row['jenis_ukuran'])->first();
        if(!$jenisUkuran){
            DB::table('ref_jenis_ukuran')->insert([
                'nama_jenis' => $row['jenis_ukuran']
            ]);
        }

        $satuan = DB::table('ref_satuan')->where('nama_satuan', $row['satuan'])->first();
        if(!$satuan){
            DB::table('ref_satuan')->insert([
                'nama_satuan' => $row['satuan']
            ]);
        }

        return new Produk([
            'plu'               => $row['plu'],
            'nama_produk'       => strtoupper($row['nama_produk']),
            'id_kategori'       => $kategori->id,
            'harga_beli'        => $row['harga_beli'],
            'harga_jual'        => $row['harga_jual'],
            'merk'              => strtoupper($row['merk']),
            'stok'              => $row['stok'],
            'addno'             => $row['addno'],
            'jenis_ukuran'      => $row['jenis_ukuran'],
            'satuan'            => strtoupper($row['satuan']),
            'plu_aktif'         => $row['plu_aktif'],
            'jual_minus'        => $row['jual_minus'],
            'retur'             => $row['retur'] ? $row['retur'] : false,
            'kode_supplier'     => strtoupper($row['kode_supplier']),
        ]);
    }

    public function headingRow(): int
    {
        return 2;
    }

    public function batchSize(): int
    {
        return 2000;
    }
}
