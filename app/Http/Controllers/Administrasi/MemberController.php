<?php

namespace App\Http\Controllers\Administrasi;

use App\Http\Controllers\Controller;
use App\Http\Requests\Insert\MemberInsertRequest;
use App\Models\Administrasi\Member;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MemberController extends Controller
{
    public function get(): JsonResponse
    {
        $data = DB::table('member')->orderBy('created_at', 'desc')->get();
        return response()->json([
            'data'      => $data,
            'pesan'   => 'berhasil ambil data member'
        ], 200);
    }

    public function insert(MemberInsertRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['nama_lengkap'] = strtoupper($data['nama_lengkap']);
        $data['alamat_lengkap'] = ucwords($data['alamat_lengkap']);

        $seqMember = DB::table('const')->where('key', 'MMBR')->first();

        if(!$seqMember){
            throw new HttpResponseException(response([
                'message' => 'key MMBR tidak ditemukan. silahkan hubungi tim IT'
            ], 402));
        }

        $newMember = Member::create($data);
        $newMember->update([
            'kode_member'   => 'MBR' . now()->format('ymd') . str_pad($seqMember->docno, 4, '0', STR_PAD_LEFT)
        ]);

        DB::table('const')->where('key', 'MMBR')->update([
            'docno' => $seqMember->docno + 1
        ]);

        return response()->json([
            'pesan' => 'data member berhasil ditambah'
        ], 201);
    }

    public function destroy(Request $request): JsonResponse
    {
        $member = Member::where('kode_member', $request->kode_member)->first();
        $member->delete();

        return response()->json([
            'pesan'     => 'member berhasil dihapus',
            'member'    => $member
        ], 200);
    }
}
