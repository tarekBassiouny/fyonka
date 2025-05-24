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
        if (!User::where('username', 'admin')->exists()) {
            User::create([
                'name' => 'Admin',
                'username' => 'admin',
                'email' => 'admin@fyonka.com',
                'password' => Hash::make(env('DEFAULT_ADMIN_PASSWORD', 'password')),
                'source' => 'dashboard'
            ]);
        }

        if (!User::where('username', 'api')->exists()) {
            User::create([
                'name' => 'API User',
                'username' => 'api',
                'email' => 'api@fyonka.com',
                'password' => Hash::make(env('DEFAULT_API_PASSWORD', 'password')),
                'source' => 'api'
            ]);
        }

        if (!User::where('username', 'dev')->exists()) {
            User::create([
                'name' => 'Dev User',
                'username' => 'dev',
                'email' => 'dev@fyonka.com',
                'password' => Hash::make(env('DEFAULT_ADMIN_PASSWORD', 'password')),
                'source' => 'dashboard'
            ]);
        }

        if (!TransactionType::where('name', 'income')->exists()) {
            TransactionType::firstOrCreate(['name' => 'income']);
            TransactionType::firstOrCreate(['name' => 'outcome']);
        }
    }
}
