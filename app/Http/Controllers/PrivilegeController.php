<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Akses;
use Illuminate\Http\Request;

class PrivilegeController extends Controller
{
    /**
     * Tampilkan semua user dan akses mereka
     */
   public function index(Request $request)
{
    $unitFilter = $request->input('unit');
    $search = $request->input('search');

    $users = User::with(['akses', 'unit']) // pastikan relasi 'unit' udah ada di model User
        ->when($unitFilter, function ($query) use ($unitFilter) {
            $query->where('unit_id', $unitFilter);
        })
        ->when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%");
            });
        })
        ->get();

    $units = \App\Models\Unit::orderBy('nama_unit')->get();

    return view('privileges.index', compact('users', 'units', 'unitFilter', 'search'));
}
    /**
     * Tampilkan form edit akses user
     */
    public function edit($id)
    {
        $user = User::with('akses')->findOrFail($id);
        $allAkses = Akses::all();

        return view('privileges.edit', compact('user', 'allAkses'));
    }

    /**
     * Update akses user
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Ambil hanya ID numerik dari checkbox akses[]
        $akses = collect($request->input('akses', []))
            ->filter(fn($val) => is_numeric($val))
            ->map(fn($val) => intval($val))
            ->all();

        // Sync akses ke user
        $user->akses()->sync($akses);

        return redirect()->route('privileges.index')->with('success', 'Privilege berhasil diperbarui.');
    }
}
