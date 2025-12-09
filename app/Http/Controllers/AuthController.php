<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\OtpMail;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function showLogin() {
        return view('auth.login');
    }

    public function showVerify() {
        if (!Session::has('pending_user_id')) {
            return redirect('/login');
        }
        $user = User::find(Session::get('pending_user_id'));
        return view('auth.verify', ['email' => $user->email]);
    }

    public function register(Request $request) {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6'
        ]);

        // Generate OTP
        $otp = rand(100000, 999999);
        
        // Log OTP for Debugging (wajib ada untuk tes jika email gagal)
        \Log::info("OTP untuk {$request->email} adalah: {$otp}");

        // Create User (Unverified)
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'client',
            'otp_code' => $otp,
            'otp_expires_at' => now()->addMinutes(10)
        ]);

        // Send Email
        try {
            Mail::to($user->email)->send(new OtpMail($otp));
        } catch (\Exception $e) {
            // Fallback for development if mail fails
            \Log::error("Mail Error: " . $e->getMessage());
        }

        // Save ID to Session for Verification Page
        Session::put('pending_user_id', $user->id);

        return redirect('/verify');
    }

    public function verify(Request $request) {
        $userId = Session::get('pending_user_id');
        if (!$userId) return redirect('/login');

        $user = User::find($userId);
        
        // Combine array inputs
        $otp = is_array($request->otp) ? implode('', $request->otp) : $request->otp;

        // Check if user is already verified
        if ($user->email_verified_at) {
             return response()->json(['success' => true, 'redirect' => '/dashboard']);
        }

        // Security Check: OTP must not be empty inside DB (means something went wrong during register)
        if (empty($user->otp_code)) {
             return response()->json(['success' => false, 'message' => 'Sesi tidak valid, silakan daftar ulang.'], 400);
        }

        // STRICT String Comparison
        if ((string)$user->otp_code === (string)$otp) {
            // Verify Success
            $user->otp_code = null;
            $user->otp_expires_at = null;
            $user->email_verified_at = now();
            $user->save();

            Auth::login($user);
            Session::forget('pending_user_id');

            return response()->json(['success' => true, 'redirect' => '/dashboard']);
        }

        return response()->json(['success' => false, 'message' => 'Kode OTP Salah!'], 400);
    }

    public function login(Request $request) {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            if (Auth::user()->role === 'admin') {
                return redirect('/admin');
            }
            return redirect('/dashboard');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ]);
    }

    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
