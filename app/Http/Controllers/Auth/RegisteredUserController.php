<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Gender;
use App\Models\Jabatan;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
      $departments = Department::all();
      $genders = Gender::all();
      $jabatans = Jabatan::all();

      return view('auth.register', compact('departments','genders','jabatans'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        // dd($request->_token);
        $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // $user = User::create([
        //     'nama' => $request->nama,
        //     'email' => $request->email,
        //     'username' => $request->username,
        //     'remember_token' => $request->_token,
        //     'password' => Hash::make($request->password),
        // ]);
        $user = new User;
        $user->nama = $request->nama;
        $user->email = $request->email;
        $user->username = $request->username;
        $user->remember_token = $request->_token;
        $user->password = Hash::make($request->password);
        $user->status_id = 1;
        // dd($user);
        // event(new Registered($user));
        if($user->save()){
          Auth::login($user);

        return redirect(route('home'));
        }

    }
}
