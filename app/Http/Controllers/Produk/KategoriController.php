<?php

namespace App\Http\Controllers\Produk;

use App\Http\Controllers\Controller;
use App\Http\Requests\Insert\KategoriInsertRequest;
use App\Models\Produk\Kategori;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    public function insert(KategoriInsertRequest $request)
    {
        $data = $request->validated();
        $data['nama_kategori'] = strtoupper($data['nama_kategori']);
        $data['addid']         = $request->ip();

        Kategori::create($data);

        return response()->json([
            'pesan' => 'data kategori berhasil ditambah'
        ], 200);
    }
}
