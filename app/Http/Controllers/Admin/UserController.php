<?php

namespace App\Http\Controllers\Admin;

use App\User;
use Facades\App\Helpers\Json;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.users.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', $user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        if ($user->id != auth()->id()){
            $user->name = $request->name;
            $user->email = $request->email;
            $user->active = ($request->active == null) ? 0 : 1;
            $user->admin = ($request->admin == null) ? 0 : 1;
            $user->save();

            return response()->json([
                'type' => 'success',
                'text' => "The user <b>$user->name</b> has been updated"
            ]);
        }
        return response()->json([
            'type' => 'error',
            'text' => "We saved you there, you could've locked yourself out!"
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        if ($user->id != auth()->id()){
            $user->delete();
            return response()->json([
                'type' => 'success',
                'text' => "The user <b>$user->name</b> has been deleted"
            ]);
        }
        return response()->json([
            'type' => 'error',
            'text' => "We saved you there, you could've locked yourself out!"
        ]);
    }

    public function qryUsers($nameemail, $userVar, $ascdesc)
    {
        $nameemailL = $nameemail ?? '%';
        $userVarL = $userVar ?? 'id';
        $ascdescL = $ascdesc ?? 'asc';

        $users = User::where(function ($query) use ($nameemailL){
            $query->where('name', 'like', '%' . $nameemailL . '%')->orWhere('email', 'like', '%' . $nameemailL . '%');
        })->orderBy($userVarL, $ascdescL)->simplePaginate(10);

        //$users = User::select('id', 'name', 'email', 'active', 'admin')->simplePaginate(10);
        $result = compact('users');
        return $result;
    }
}
