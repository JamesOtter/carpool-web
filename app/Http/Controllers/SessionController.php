<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class SessionController extends Controller
{
    public function create(){
        return view('auth.login');
    }
    public function store(Request $request){

        $attributes = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if(! Auth::attempt($attributes)){
            return response()->json(['success' => false, 'message' => 'Credentials does not match our records.']);
        }

        request()->session()->regenerate();

        return response()->json(['success' => true]);
    }
    public function destroy(){

        Auth::logout();

        return redirect('/');
    }
}

