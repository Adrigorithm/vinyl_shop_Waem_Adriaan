<?php

namespace App\Http\Controllers\User;

use App\Http\Middleware\Authenticate;
use Auth;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class PasswordController extends Controller
{
    public function edit(){
        return view('user.password');
    }

    public function update(Request $request){
        $this->validate($request,[
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed'
        ]);

        $user = User::findOrFail(auth()->id());
        if(!Hash::check($request->current_password, $user->password)){
            session()->flash('danger', "Your current password doesn't match the password in the database");
            return back();
        }
        $user->password = Hash::make($request->password);
        $user->save();

        Auth::logout();

        session()->flash('success', 'Your password has been updated');
        return view('auth.login');
    }
}
