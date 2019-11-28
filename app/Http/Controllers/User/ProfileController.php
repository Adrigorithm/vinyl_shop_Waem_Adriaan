<?php

namespace App\Http\Controllers\User;

use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProfileController extends Controller
{
    public function edit(){
        return view('user.profile');
    }

    public function update(Request $request){
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . auth()->id()
        ]);

        $this->updateDb($request->name, $request->email);

        session()->flash('success', 'Your profile has been updated');
        return back();
    }

    private function updateDb($name, $email){
        $user = User::findOrFail(auth()->id());
        $user->name = $name;
        $user->email = $email;
        $user->save();
    }
}
