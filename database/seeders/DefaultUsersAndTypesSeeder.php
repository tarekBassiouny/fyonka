<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\TransactionType;
use Illuminate\Support\Facades\Hash;

class DefaultUsersAndTypesSeeder extends Seeder
{
    public function run(): void
    {
        if (User::where('email', 'admin@example.com')->exists()) {
            return;
        }

        User::create([
            'name' => 'Admin',
            'username' => 'admin',
            'email' => 'admin@fyonka.com',
            'password' => Hash::make(env('DEFAULT_ADMIN_PASSWORD', 'password')),
            'source' => 'dashboard'
        ]);

        User::create([
            'name' => 'API User',
            'username' => 'api',
            'email' => 'api@fyonka.com',
            'password' => Hash::make(env('DEFAULT_API_PASSWORD', 'password')),
            'source' => 'api'
        ]);

        TransactionType::firstOrCreate(['name' => 'income']);
        TransactionType::firstOrCreate(['name' => 'outcome']);
    }
}

