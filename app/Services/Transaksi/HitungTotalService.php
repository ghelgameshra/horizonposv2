<?php

namespace App\Services\Transaksi;

class HitungTotalService
{
    /**
     * Mapping satuan ke metode perhitungan
     */
    protected array $rumus = [
        'cm'   => [self::class, 'hitungKelilingCm'],
        'cm2'  => [self::class, 'hitungLuasCm2'],
        'm'    => [self::class, 'hitungKelilingM'],
        'm2'   => [self::class, 'hitungLuasM2'],
        'pcs'  => [self::class, 'hitungPcs'],
        'unit' => [self::class, 'hitungPcs'],
        'buku' => [self::class, 'hitungPcs'],
        'lmbr' => [self::class, 'hitungPcs'], // alias lembar
        'roll' => [self::class, 'hitungRoll'], // meter lari
    ];

    /**
     * Fungsi utama untuk menghitung total pembelian
     */
    public function hitung(array $data): array
    {
        $ukuran = $data['ukuran'] ?? '';
        $satuan = strtolower(trim($data['satuan'] ?? 'pcs'));
        $harga  = (float) ($data['harga'] ?? 0);
        $qty    = (int) ($data['qty'] ?? 1);

        // Ambil metode perhitungan, fallback ke hitungPcs jika tidak dikenal
        $method = $this->rumus[$satuan] ?? [self::class, 'hitungPcs'];

        // Jalankan rumus perhitungan nilai ukuran
        $nilai = call_user_func($method, $ukuran);

        // Total harga berdasarkan qty dan harga
        $subtotal = $nilai * $harga * $qty;

        return [
            'nilai'    => round($nilai, 4),
            'subtotal' => round($subtotal, 2),
            'satuan'   => $satuan,
        ];
    }

    /**
     * Helper parsing ukuran string seperti "200x100"
     * return selalu array [panjang, lebar]
     */
    protected static function parseUkuran(?string $ukuran): array
    {
        if (!$ukuran) {
            return [0, 0];
        }

        $parts = explode('x', strtolower(trim($ukuran)));
        $nums = array_map(fn($v) => is_numeric($v) ? (float)$v : 0, $parts);

        return array_pad($nums, 2, 0); // selalu dua elemen
    }

    // ============================
    // 🔹 RUMUS PERHITUNGAN
    // ============================

    /** Keliling dalam cm → hasil meter */
    protected static function hitungKelilingCm(string $ukuran): float
    {
        [$p, $l] = self::parseUkuran($ukuran);
        return (($p + $l) * 2);
    }

    /** Luas dalam cm² → hasil m² */
    protected static function hitungLuasCm2(string $ukuran): float
    {
        [$p, $l] = self::parseUkuran($ukuran);
        return ($p * $l);
    }

    /** Keliling dalam meter (user input tetap cm, dikonversi ke m) */
    protected static function hitungKelilingM(string $ukuran): float
    {
        [$p, $l] = self::parseUkuran($ukuran);
        $p /= 100;
        $l /= 100;
        return ($p + $l) * 2;
    }

    /** Luas dalam m² (user input cm dikonversi ke m) */
    protected static function hitungLuasM2(string $ukuran): float
    {
        [$p, $l] = self::parseUkuran($ukuran);
        $p /= 100;
        $l /= 100;
        return $p * $l;
    }

    /** Per biji / unit / lembar */
    protected static function hitungPcs(): float
    {
        return 1.0;
    }

    /** Roll dihitung per meter lari (ambil panjang) */
    protected static function hitungRoll(string $ukuran): float
    {
        [$p, $l] = self::parseUkuran($ukuran);
        return $p * $l / 100;
    }
}
