<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Lana Amawi',
            'email' => 'admin@lana-amawi.com',
            'password' => Hash::make('password123'),
            'is_admin' => true,
        ]);

        $this->command->info('Admin user created successfully!');
        $this->command->info('Email: admin@lana-amawi.com');
        $this->command->info('Password: password123');
    }
}
