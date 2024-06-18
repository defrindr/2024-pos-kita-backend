<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Mail;
use App\Mail\OTPMail;


class MailController extends Controller
{
    public function index(){
        $maildata=[
            'title'=>'Test Title',
            'body'=>'Test Body'
        ];
        Mail::to('jovan3duardo@gmail.com')->send(new OTPMail($maildata));
    }
    public function sendEmail($useremail, $otp){
        $maildata=[
            'otp'=>$otp
        ];
        Mail::to($useremail)->send(new OTPMail($maildata));
    }
}

