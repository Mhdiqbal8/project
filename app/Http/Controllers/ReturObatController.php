<?php

namespace App\Http\Controllers;

use App\Models\Pasien;
use App\Models\ReturObat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;


class ReturObatController extends Controller
{
    public function index(){
      if(Auth::user()->department_id == 5 && Auth::user()->jabtan_id == 1){
        $retur_obats = Pasien::where('user_id', Auth()->user()->id)->get();
      }else{
        $retur_obats = Pasien::all();
      }

      return view('retur_obat.index', compact('retur_obats'));
    }

    public function create(){
      return view('retur_obat.create');
    }

    public function store(Request $request)
    {
      db::beginTransaction();
      try {
        $data_pasien = new Pasien();
        $data_pasien->user_id = Auth::user()->id;
        $data_pasien->nama_pasien = $request->nama_pasien;
        $data_pasien->no_rm = $request->no_rm;
        $data_pasien->ruangan = $request->ruangan;
        if($data_pasien->save()){
          foreach($request->obat_alkes as $key => $val){
            $data_obat = new ReturObat();
            $data_obat->pasien_id = $data_pasien->id;
            $data_obat->obat_alkes = $val;
            $data_obat->jumlah = $request->jumlah[$key];
            $data_obat->satuan = $request->satuan[$key];
            $data_obat->no_batch = $request->no_batch[$key];
            $data_obat->expired_date = $request->expired_date[$key];
            $data_obat->keterangan = $request->keterangan[$key];
            $data_obat->save();
          }
        }
        DB::commit();
        return redirect('retur_obat')->with('success', 'Retur Obat Success!');
      } catch (\Exception $e) {
        DB::rollback();
        return redirect('retur_obat')->with('success', $e->getMessage());
      }
    }

    public function show($id)
    {
        $pasien = Pasien::find($id);
        $retur_obats = ReturObat::where('pasien_id', $id)->get();

        return view('retur_obat.show', compact('pasien', 'retur_obats'));
    }

}
