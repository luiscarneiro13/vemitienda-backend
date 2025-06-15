<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login()
    {
        return view('login');
    }

    public function loguear()
    {
        $credentials = request()->validate([
            'email'    => ['required', 'email', 'string'],
            'password' => ['required', 'string'],
        ]);
        $remember = request()->filled('remember'); // Cambia a true y false
        if (Auth::attempt($credentials, $remember)) {
            request()->session()->regenerate(); // Para avitar ataques
            return redirect('admin/usuarios');
        }

        return redirect('login');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        return redirect()->route('login');
    }
}
