<?php

namespace App\Http\Controllers;

use App\Mail\ContactMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactUsController extends Controller
{
    public function show(){
        return view('contact');
    }

    public function sendEmail(Request $request){
        $this->validate($request,['name' => 'required', 'email' => 'required|email', 'contact' => 'required', 'message' => 'required|min:10']);
        $email = new ContactMail($request);
        Mail::to($request->email)->send($email);

        session()->flash('success', 'Thank you for your message.<br>We\'ll contact you as soon as possible.');
        return redirect('contact-us');
    }
}
