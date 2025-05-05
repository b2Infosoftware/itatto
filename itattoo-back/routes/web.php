<?php

use App\Mail\WelcomeEmail;
use App\Models\SignedDocument;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    // $customer = Customer::first();
    // $appointment = Appointment::first();
    // $email = EmailTemplate::where('type', 'appointment_created')->firstOrFail();
    // return view('emails.general')->with([
    //     'email' => $email,
    //     'customer' => $customer,
    //     'appointment' => $appointment,
    // ]);
    return redirect()->to(config('app.client'));
});

Route::get('/pdf', function () {
    return SignedDocument::generateFromRequest();
});

Route::get('/email', function () {
    $u = App\Models\Staff::first();
    Mail::mailer('postmarkmarketing')->to('mironmg@yahoo.com')->queue(new WelcomeEmail($u, 'demo123'));
});
