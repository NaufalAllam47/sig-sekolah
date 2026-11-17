<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sekolah;

class PetaController extends Controller
{
    // Fungsi untuk menampilkan halaman web (View)
    public function index()
    {
        // Pastikan Anda mengubah 'peta' dengan nama file .blade.php Anda (tanpa ekstensi .blade.php)
        return view('peta'); 
    }

    // Fungsi untuk mengirimkan data koordinat dalam format GeoJSON
    public function geojson(Request $request) 
    { 
        $features = Sekolah::with('profil') 
            ->when($request->jenjang, fn ($q, $j) => $q->where('jenjang', $j)) 
            ->get() 
            ->map(fn ($s) => [ 
                'type' => 'Feature', 
                'geometry' => [ 
                    'type' => 'Point', 
                    // GeoJSON: urutan [longitude, latitude] 
                    'coordinates' => [$s->longitude, $s->latitude], 
                ], 
                'properties' => [ 
                    'npsn'          => $s->npsn, 
                    'nama'          => $s->nama, 
                    'jenjang'       => $s->jenjang, 
                    'status'        => $s->status, 
                    'alamat'        => $s->alamat, 
                    'kecamatan'     => $s->kecamatan, 
                    'telepon'       => $s->telepon, 
                    'kepala_sekolah'=> $s->profil?->kepala_sekolah,
                    'akreditasi'    => $s->profil?->akreditasi,
                    'jumlah_siswa'  => $s->profil?->jumlah_siswa,
                    'jumlah_guru'   => $s->profil?->jumlah_guru,
                ], 
            ]); 
  
        return response()->json([ 
            'type'     => 'FeatureCollection', 
            'features' => $features, 
        ]); 
    } 
}