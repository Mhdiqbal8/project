<?php

namespace App\Http\Controllers;

use App\Models\{Department, Gender, Jabatan, User, Status, Unit};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Hash, Redirect, Storage};

class ManagementUserController extends Controller
{
 public function index(Request $request)
{
    $search = $request->input('search');
    $unitFilter = $request->input('unit');

    $users = User::with([
            'unit.kepalaUnit',
            'unit.supervisorUnit',
            'unit.managerUnit',
            'gender',
            'department',
            'jabatan',
            'status'
        ])
        ->where('username', 'NOT LIKE', '%_superadmin')
        ->when($search, function ($query) use ($search) {
            $query->where('nama', 'like', "%$search%")
                ->orWhere('nik', 'like', "%$search%")
                ->orWhere('username', 'like', "%$search%")
                ->orWhereHas('unit', function ($q) use ($search) {
                    $q->where('nama_unit', 'like', "%$search%");
                });
        })
        ->when($unitFilter, function ($query) use ($unitFilter) {
            $query->whereHas('unit', function ($q) use ($unitFilter) {
                $q->where('id', $unitFilter);
            });
        })
        ->latest()
        ->get();

    $units = Unit::orderBy('nama_unit')->get();

  return view('management_user.index', [
    'users' => $users,
    'units' => $units,
    'genders' => Gender::all(),
    'departments' => Department::all(),
    'statuses' => Status::whereIn('id', [1, 2])->get(),
    'jabatans' => Jabatan::all(),
    'allUsers' => User::with('unit')->get()
]);

}


    public function store(Request $request)
    {
        $request->validate([
            'password' => ['required'],
            'password_confirmed' => ['required', 'same:password'],
        ]);

        DB::beginTransaction();

        if (User::where('nik', $request->nik)->exists()) {
            DB::rollback();
            return Redirect::back()->with('failed', 'NIK sudah terdaftar!');
        }

        if (User::where('username', $request->username)->exists()) {
            DB::rollback();
            return Redirect::back()->with('failed', 'Username sudah digunakan!');
        }

        // ❌ VALIDASI: Jika jabatan Staff, tidak boleh jadi kepala/spv/manajer
        $jabatanStaffId = 1;
        if ($request->jabatan_id == $jabatanStaffId) {
            if (
                $request->jabatan_id == 1 &&
                in_array($request->jabatan_id, [2, 3, 4])
            ) {
                DB::rollback();
                return back()->with('failed', 'Jabatan Staff tidak boleh menjadi Kepala, Supervisor, atau Manager Unit.');
            }
        }

        $user = new User;
        $user->nama = $request->nama;
        $user->username = $request->username;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->remember_token = $request->_token;
        $user->status_id = 1;
        $user->nik = $request->nik;
        $user->gender_id = $request->gender_id;
        $user->department_id = $request->department_id;
        $user->jabatan_id = $request->jabatan_id;
        $user->unit_id = $request->unit_id;

        if ($request->hasFile('ttd')) {
            $user->ttd_path = $request->file('ttd')->store('ttd_users', 'public');
        }

        if ($user->save()) {
            $unit = Unit::find($user->unit_id);
            if ($unit) {
                if ($user->jabatan_id == 2) {
                    $unit->kepala_unit_id = $user->id;
                }
                if ($user->jabatan_id == 3) {
                    $unit->supervisor_unit_id = $user->id;
                }
                if ($user->jabatan_id == 4) {
                    $unit->manager_unit_id = $user->id;
                }
                $unit->save();
            }

            DB::commit();
            return Redirect::back()->with('success', 'User berhasil ditambahkan!');
        } else {
            DB::rollBack();
            return Redirect::back()->with('failed', 'Gagal menambahkan user!');
        }
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        $request->validate([
            'password_confirmed' => ['same:password'],
        ]);

        if (User::where('nik', $request->nik)->where('id', '<>', $id)->exists()) {
            DB::rollback();
            return Redirect::back()->with('failed', 'NIK sudah terdaftar!');
        }

        if (User::where('username', $request->username)->where('id', '<>', $id)->exists()) {
            DB::rollback();
            return Redirect::back()->with('failed', 'Username sudah digunakan!');
        }

        $user = User::findOrFail($id);
        $user->nama = $request->nama;
        $user->username = $request->username;
        $user->email = $request->email;
        $user->nik = $request->nik;
        $user->gender_id = $request->gender_id;
        $user->department_id = $request->department_id;
        $user->jabatan_id = $request->jabatan_id;
        $user->unit_id = $request->unit_id;
        $user->status_id = $request->status_id;
        $user->remember_token = $request->_token;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        if ($request->hasFile('ttd')) {
            if ($user->ttd_path && Storage::exists($user->ttd_path)) {
                Storage::delete($user->ttd_path);
            }
            $user->ttd_path = $request->file('ttd')->store('ttd_users', 'public');
        }

        // ❌ VALIDASI JIKA Staff tidak boleh diset sebagai pejabat struktural
        $jabatanStaffId = 1;
        if ($user->jabatan_id == $jabatanStaffId) {
            $isNaikJabatan = in_array($user->id, [
                $request->kepala_unit_id,
                $request->supervisor_unit_id,
                $request->manager_unit_id
            ]);

            if ($isNaikJabatan) {
                DB::rollBack();
                return back()->with('failed', 'User dengan jabatan Staff tidak boleh menjadi Kepala, SPV, atau Manajer Unit. Harap perbarui jabatan terlebih dahulu.');
            }
        }

        if ($user->save()) {
            $unit = Unit::find($request->unit_id);
            if ($unit) {
                // Manual override dari form
                $unit->kepala_unit_id = $request->kepala_unit_id;
                $unit->supervisor_unit_id = $request->supervisor_unit_id;
                $unit->manager_unit_id = $request->manager_unit_id;

                // Auto assign berdasarkan jabatan
                if ($user->jabatan_id == 2) {
                    $unit->kepala_unit_id = $user->id;
                }
                if ($user->jabatan_id == 3) {
                    $unit->supervisor_unit_id = $user->id;
                }
                if ($user->jabatan_id == 4) {
                    $unit->manager_unit_id = $user->id;
                }

                $unit->save();
            }

            DB::commit();
            return Redirect::back()->with('success', 'User berhasil diperbarui!');
        } else {
            DB::rollBack();
            return Redirect::back()->with('failed', 'Gagal memperbarui user!');
        }
    }

    public function hapusTTD($id)
    {
        $user = User::findOrFail($id);

        if ($user->ttd_path && Storage::exists($user->ttd_path)) {
            Storage::delete($user->ttd_path);
        }

        $user->ttd_path = null;
        $user->save();

        return back()->with('success', 'Tanda tangan berhasil dihapus.');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $genders = Gender::all();
        $departments = Department::all();
        $jabatans = Jabatan::all();
        $statuses = Status::all();
        $units = Unit::all();
        $allUsers = User::with('unit')->get();

        return view('management_user.edit', compact(
            'user', 'genders', 'departments', 'jabatans', 'statuses', 'units', 'allUsers'
        ));
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->ttd_path && Storage::exists($user->ttd_path)) {
            Storage::delete($user->ttd_path);
        }

        $user->delete();

        return back()->with('success', 'User berhasil dihapus.');
    }
}
