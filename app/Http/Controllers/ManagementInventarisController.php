<?php

namespace App\Http\Controllers;

use App\Models\Inventaris;
use App\Models\JenisInventaris;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class ManagementInventarisController extends Controller
{

    public function index(){

      $inventaries = Inventaris::all();
      $jenis_inventaries = JenisInventaris::all();

      return view('management_inventaris.index', compact('inventaries', 'jenis_inventaries'));
    }

    public function store(Request $request){

      $inventaris = new Inventaris();
      $inventaris->nama = $request->nama;
      $inventaris->jenis_inventaris_id = $request->jenis_inventaris_id;
      $inventaris->no_inventaris = $request->no_inventaris;
        if($inventaris->save()){
          DB::commit();
          return Redirect::back()->with('success', 'Create Data Inventaris Berhasil!');
        }else{
          DB::rollBack();
          return Redirect::back()->with('failed', 'Create Data Inventaris Failed, Cek Kembali Data Anda');
        }
    }

    public function update(Request $request, $id){

      $inventaris = Inventaris::find($id);
      $inventaris->nama = $request->nama;
      $inventaris->jenis_inventaris_id = $request->jenis_inventaris_id;
      $inventaris->no_inventaris = $request->no_inventaris;
        if($inventaris->save()){
          DB::commit();
          return Redirect::back()->with('success', 'Update Data Inventaris Berhasil!');
        }else{
          DB::rollBack();
          return Redirect::back()->with('failed', 'Update Data Inventaris Failed, Cek Kembali Data Anda');
        }
    }
}
