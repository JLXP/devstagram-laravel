<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LoginController extends Controller
{
    //
    public function index()
    {
        return view('auth.login');
    }

    public function store(Request $request)
    {     
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        //with sirve para llenar la session
        //back es para que regreses a la pagina anterior
        if (!auth()->attempt($request->only('email', 'password'), $request->remember)) {
            return back()->with('mensaje', 'Credenciales Incorrectas');
        }

        return redirect()->route('posts.index',auth()->user()->username);
    }
}
