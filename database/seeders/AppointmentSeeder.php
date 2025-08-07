<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Appointment;
use App\Models\User;
use Carbon\Carbon;

class AppointmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get non-admin users
        $clients = User::where('is_admin', false)->get();
        
        if ($clients->isEmpty()) {
            $this->command->info('No clients found. Creating appointments skipped.');
            return;
        }

        // Create sample appointments for the next 30 days
        $statuses = ['pending', 'confirmed', 'completed'];
        $times = ['09:00', '10:00', '11:00', '14:00', '15:00', '16:00', '17:00'];
        
        for ($i = 0; $i < 15; $i++) {
            $date = Carbon::now()->addDays(rand(1, 30));
            $time = $times[array_rand($times)];
            $status = $statuses[array_rand($statuses)];
            $client = $clients->random();
            
            Appointment::create([
                'user_id' => $client->id,
                'program' => $this->getRandomProgram(),
                'appointment_date' => $date,
                'appointment_time' => $time,
                'status' => $status,
                'message' => $this->getRandomNotes(),
            ]);
        }

        $this->command->info('Sample appointments created successfully!');
    }

    private function getRandomProgram(): string
    {
        $programs = [
            'Life Coaching',
            'Career Development',
            'Leadership Training',
            'Personal Development',
            'Stress Management',
            'Communication Skills',
            'Goal Setting',
            'Executive Coaching'
        ];
        
        return $programs[array_rand($programs)];
    }

    private function getRandomNotes(): string
    {
        $notes = [
            'Initial consultation session',
            'Follow-up coaching session',
            'Goal setting and planning',
            'Progress review meeting',
            'Career development discussion',
            'Life coaching session',
            'Personal development workshop',
            'Stress management consultation',
            'Leadership skills development',
            'Communication skills training'
        ];
        
        return $notes[array_rand($notes)];
    }
}
