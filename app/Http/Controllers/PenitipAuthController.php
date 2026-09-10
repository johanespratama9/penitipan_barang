<?php

namespace App\Http\Controllers;

use App\Models\Consignor;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class PenitipAuthController extends Controller
{
    /**
     * Tampilkan formulir pendaftaran khusus calon mitra penitip.
     */
    public function showRegisterForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('titip.index');
        }

        return view('public.register-penitip');
    }

    /**
     * Proses pendaftaran akun penitip baru.
     */
    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'                  => ['required', 'string', 'max:255'],
            'phone'                 => ['required', 'string', 'max:50'],
            'email'                 => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'address'               => ['nullable', 'string', 'max:500'],
            'identity_number'       => ['nullable', 'string', 'max:50'],
            'password'              => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'name.required'         => 'Nama lengkap wajib diisi.',
            'phone.required'        => 'Nomor WhatsApp / HP wajib diisi.',
            'email.required'        => 'Alamat email wajib diisi.',
            'email.email'           => 'Format alamat email tidak valid.',
            'email.unique'          => 'Alamat email ini sudah terdaftar. Silakan gunakan email lain atau login.',
            'password.required'     => 'Password wajib diisi.',
            'password.min'          => 'Password minimal harus terdiri dari 6 karakter.',
            'password.confirmed'    => 'Konfirmasi password tidak cocok.',
        ]);

        return DB::transaction(function () use ($validated) {
            // Pastikan role penitip tersedia
            Role::firstOrCreate(['name' => 'penitip', 'guard_name' => 'web']);

            // 1. Buat Akun User untuk Login
            $user = User::create([
                'name'     => $validated['name'],
                'email'    => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            $user->assignRole('penitip');

            // 2. Buat atau hubungkan Data Profil Penitip (Consignor)
            $consignor = Consignor::where('phone', $validated['phone'])->first();

            if ($consignor) {
                $consignor->update([
                    'user_id'         => $user->id,
                    'name'            => $validated['name'],
                    'email'           => $validated['email'],
                    'address'         => $validated['address'] ?? $consignor->address,
                    'identity_number' => $validated['identity_number'] ?? $consignor->identity_number,
                    'status'          => 'active',
                ]);
            } else {
                $consignor = Consignor::create([
                    'user_id'         => $user->id,
                    'name'            => $validated['name'],
                    'phone'           => $validated['phone'],
                    'email'           => $validated['email'],
                    'address'         => $validated['address'] ?? null,
                    'identity_number' => $validated['identity_number'] ?? null,
                    'status'          => 'active',
                ]);
            }

            // 3. Otomatis login user penitip
            Auth::login($user);

            return redirect()->route('titip.index')->with('welcome_penitip', [
                'name' => $user->name,
                'code' => $consignor->code,
            ]);
        });
    }

    /**
     * Logout sesi penitip.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('penitip.register')->with('info', 'Anda telah berhasil keluar dari akun.');
    }
}

