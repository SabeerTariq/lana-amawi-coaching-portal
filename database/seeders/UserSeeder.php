<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Dr. Sarah Johnson',
            'email' => 'demo@example.com',
            'phone' => '+1 (555) 123-4567',
            'address' => '123 Medical Center Dr, Suite 200, New York, NY 10001',
            'date_of_birth' => '1985-03-15',
            'gender' => 'female',
            'age' => 39,
            'languages_spoken' => ['English', 'Spanish'],
            'institution_hospital' => 'New York General Hospital',
            'position' => 'Cardiologist',
            'position_as_of_date' => '2020-01-15',
            'specialty' => 'Interventional Cardiology',
            'graduation_date' => '2010-06-15',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'is_admin' => 0,
        ]);

        User::create([
            'name' => 'Dr. Michael Chen',
            'email' => 'test@example.com',
            'phone' => '+1 (555) 987-6543',
            'address' => '456 Healthcare Ave, Floor 5, Los Angeles, CA 90210',
            'date_of_birth' => '1982-07-22',
            'gender' => 'male',
            'age' => 42,
            'languages_spoken' => ['English', 'Chinese', 'French'],
            'institution_hospital' => 'Cedars-Sinai Medical Center',
            'position' => 'Emergency Medicine Physician',
            'position_as_of_date' => '2018-09-01',
            'specialty' => 'Emergency Medicine',
            'graduation_date' => '2008-05-20',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'is_admin' => 0,
        ]);
    }
}
