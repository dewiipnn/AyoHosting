<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Cek jika admin sudah ada biar ga duplikat
        if (!User::where('email', 'admin@ayohost.com')->exists()) {
            User::create([
                'name' => 'Super Admin',
                'email' => 'admin@ayohost.com',
                'password' => Hash::make('admin'), // Passwordnya: admin
                'role' => 'admin',
                'email_verified_at' => Carbon::now(), // Langsung terverifikasi tanpa OTP
                'otp_code' => null
            ]);
            $this->command->info('Akun Admin Berhasil Dibuat! (Email: admin@ayohost.com / Pass: admin)');
        } else {
            $this->command->warn('Akun Admin sudah ada.');
        }
    }
}
