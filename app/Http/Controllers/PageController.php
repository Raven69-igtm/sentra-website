<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    public function login(){
        return view('backend.login.login');
    }

    public function reset_pw(){
        return view('backend.login.reset_pasword');
    }

    // Halaman lama tanpa pengecekan sesi diarahkan ke route controller OTP
    // agar seluruh alur lupa password melewati validasi yang sama.
    public function otp(){
        return redirect()->route('otp.form');
    }

    public function new_pw(){
        return redirect()->route('password.new');
    }
    

    public function laporan_proses(){
        return view('backend.layout.page_admin.laporan.proses_laporan');
    }
}
