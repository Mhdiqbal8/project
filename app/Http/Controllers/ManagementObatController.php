<?php

namespace App\Http\Controllers;

use App\Models\Obat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;



class ManagementObatController extends Controller
{
    public function index(){
      $obats = Obat::all();

      return view('management_obat.index', compact('obats'));
    }


    public function store(Request $request){

      $obat = new Obat();
      $obat->nama_obat = $request->nama_obat;
        if($obat->save()){
          DB::commit();
          return Redirect::back()->with('success', 'Create Data Obat Berhasil!');
        }else{
          DB::rollBack();
          return Redirect::back()->with('failed', 'Create Data Obat Failed, Cek Kembali Data Anda');
        }
    }

    public function update(Request $request, $id){

      $obat = Obat::find($id);
      $obat->nama_obat = $request->nama_obat;
        if($obat->save()){
          DB::commit();
          return Redirect::back()->with('success', 'Update Data Obat Berhasil!');
        }else{
          DB::rollBack();
          return Redirect::back()->with('failed', 'Update Data Obat Failed, Cek Kembali Data Anda');
        }
    }

}
