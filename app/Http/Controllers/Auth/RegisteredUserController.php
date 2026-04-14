<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Student;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;


class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nisn' => ['required'],
            'phone' => ['required'],
            'tanggal_lahir' => ['required'],
            'jenis_kelamin' => ['required'],
            'photo' => ['required', 'image'],
            'password' => ['required', 'confirmed'],
        ]);

        // Validate NISN exists in students table
        $student = Student::where('nisn', $request->nisn)->first();

        if (!$student) {
            return back()->withErrors(['nisn' => 'NISN tidak terdaftar. Hubungi admin untuk mendaftarkan NISN kamu.'])->withInput();
        }

        if ($student->is_registered) {
            return back()->withErrors(['nisn' => 'NISN ini sudah digunakan untuk mendaftar akun lain.'])->withInput();
        }

        // Check if NISN already exists in users table
        if (User::where('nisn', $request->nisn)->exists()) {
            return back()->withErrors(['nisn' => 'NISN sudah terdaftar di sistem.'])->withInput();
        }

        $photoPath = $request->file('photo')->store('users', 'public');

        $user = User::create([
            'nisn' => $student->nisn,
            'name' => $student->name,
            'kelas' => $student->kelas,
            'phone' => $request->phone,
            'tanggal_lahir' => $request->tanggal_lahir,
            'jenis_kelamin' => $request->jenis_kelamin,
            'photo' => $photoPath,
            'password' => Hash::make($request->password),
            'student_id' => $student->id,
            'registration_status' => 'approved', // Auto-approved karena NISN sudah di-whitelist
        ]);

        // Mark student as registered
        $student->update(['is_registered' => true]);

        event(new Registered($user));

        // Langsung bisa login, tidak perlu menunggu admin
        return redirect()->route('login')->with('success', 'Registrasi berhasil! Silakan login.');
    }
}
