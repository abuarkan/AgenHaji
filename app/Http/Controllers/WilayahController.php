<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WilayahController extends Controller
{
    /**
     * Search administrative regions (kelurahan/desa) dynamically.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $search = $request->input('q');

        if (!$search || strlen($search) < 3) {
            return response()->json([]);
        }

        // Optimized self-join query to retrieve Kelurahan (length 13)
        // and resolve parent names (Kecamatan, Kota, Provinsi) in one call.
        $results = DB::table('wilayah as kel')
            ->select([
                'kel.kode as kel_kode',
                'kel.nama as kel_nama',
                'kec.nama as kec_nama',
                'kot.nama as kot_nama',
                'prov.nama as prov_nama',
                'kec.kode as kec_kode',
                'kot.kode as kot_kode',
                'prov.kode as prov_kode'
            ])
            ->leftJoin('wilayah as kec', 'kec.kode', '=', DB::raw('SUBSTRING(kel.kode, 1, 8)'))
            ->leftJoin('wilayah as kot', 'kot.kode', '=', DB::raw('SUBSTRING(kel.kode, 1, 5)'))
            ->leftJoin('wilayah as prov', 'prov.kode', '=', DB::raw('SUBSTRING(kel.kode, 1, 2)'))
            ->whereRaw('CHAR_LENGTH(kel.kode) = 13')
            ->where('kel.nama', 'LIKE', '%' . $search . '%')
            ->limit(10)
            ->get();

        // Format search items for autocomplete UI
        $formatted = $results->map(function ($row) {
            return [
                'kode_provinsi' => $row->prov_kode,
                'nama_provinsi' => $row->prov_nama,
                'kode_kota' => $row->kot_kode,
                'nama_kota' => $row->kot_nama,
                'kode_kecamatan' => $row->kec_kode,
                'nama_kecamatan' => $row->kec_nama,
                'kode_kelurahan' => $row->kel_kode,
                'nama_kelurahan' => $row->kel_nama,
                'label' => sprintf(
                    '%s (Kec. %s, %s, %s)',
                    $row->kel_nama,
                    $row->kec_nama ?? '-',
                    $row->kot_nama ?? '-',
                    $row->prov_nama ?? '-'
                )
            ];
        });

        return response()->json($formatted);
    }
}
