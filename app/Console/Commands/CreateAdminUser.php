<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateAdminUser extends Command
{
    protected $signature = 'admin:create {--email= : Email admin} {--name= : Nama admin}';
    protected $description = 'Membuat user admin baru';

    public function handle()
    {
        $email = $this->option('email') ?: $this->ask('Email admin?');
        $name = $this->option('name') ?: $this->ask('Nama admin?');

        // Validasi input
        $validator = Validator::make([
            'email' => $email,
            'name' => $name,
        ], [
            'email' => 'required|email|unique:users,email',
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }
            return 1;
        }

        // Minta password dan konfirmasi
        $password = $this->secret('Password admin? (min. 8 karakter)');
        if (strlen($password) < 8) {
            $this->error('Password harus minimal 8 karakter.');
            return 1;
        }

        $confirmPassword = $this->secret('Konfirmasi password:');
        if ($password !== $confirmPassword) {
            $this->error('Konfirmasi password tidak cocok.');
            return 1;
        }

        // Buat user admin
        $admin = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'is_admin' => true,
        ]);

        $this->info("User admin {$admin->name} berhasil dibuat!");
        return 0;
    }
}
