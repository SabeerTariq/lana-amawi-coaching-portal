<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Program;

class ProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $programs = [
            [
                'name' => 'Life Coaching Program',
                'description' => 'Comprehensive life coaching program designed to help healthcare professionals achieve personal and professional goals, improve work-life balance, and enhance overall well-being.',
                'price' => 299.00,
                'duration_weeks' => 8,
                'sessions_included' => 6,
                'is_active' => true,
                'features' => [
                    'One-on-one coaching sessions',
                    'Personalized action plans',
                    'Goal setting and tracking',
                    'Work-life balance strategies',
                    'Stress management techniques',
                    'Career development guidance',
                    'Email support between sessions',
                    'Progress assessment reports'
                ]
            ],
            [
                'name' => 'Career Development Program',
                'description' => 'Specialized program focused on advancing healthcare careers, leadership development, and professional growth within the medical field.',
                'price' => 399.00,
                'duration_weeks' => 12,
                'sessions_included' => 8,
                'is_active' => true,
                'features' => [
                    'Career assessment and planning',
                    'Leadership skill development',
                    'Networking strategies',
                    'Interview preparation',
                    'Resume and CV optimization',
                    'Professional branding',
                    'Mentorship guidance',
                    'Industry insights and trends'
                ]
            ],
            [
                'name' => 'Wellness & Self-Care Program',
                'description' => 'Holistic wellness program designed specifically for healthcare professionals to prevent burnout, manage stress, and maintain optimal health.',
                'price' => 249.00,
                'duration_weeks' => 6,
                'sessions_included' => 4,
                'is_active' => true,
                'features' => [
                    'Burnout prevention strategies',
                    'Stress management techniques',
                    'Mindfulness and meditation',
                    'Nutrition guidance for busy schedules',
                    'Sleep optimization',
                    'Physical wellness planning',
                    'Emotional regulation tools',
                    'Self-care routine development'
                ]
            ],
            [
                'name' => 'Leadership Excellence Program',
                'description' => 'Advanced leadership program for healthcare professionals in management roles, focusing on team leadership, decision-making, and organizational effectiveness.',
                'price' => 499.00,
                'duration_weeks' => 16,
                'sessions_included' => 10,
                'is_active' => true,
                'features' => [
                    'Leadership style assessment',
                    'Team management strategies',
                    'Conflict resolution skills',
                    'Change management',
                    'Strategic thinking development',
                    'Communication excellence',
                    'Performance management',
                    'Organizational culture building',
                    'Executive coaching techniques',
                    '360-degree feedback analysis'
                ]
            ]
        ];

        foreach ($programs as $program) {
            Program::create($program);
        }
    }
}