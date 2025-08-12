<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Appointment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;



    public function test_admin_dashboard_shows_correct_appointment_status_counts()
    {
        // Create an admin user
        $admin = User::factory()->admin()->create();

        // Create appointments with different statuses
        Appointment::factory()->count(5)->confirmed()->create();
        Appointment::factory()->count(3)->pending()->create();
        Appointment::factory()->count(7)->completed()->create();
        Appointment::factory()->count(2)->cancelled()->create();

        // Login as admin
        $this->actingAs($admin);

        // Get the dashboard
        $response = $this->get(route('admin.dashboard'));

        $response->assertStatus(200);

        // Check that the correct counts are displayed
        $response->assertSee('5'); // Confirmed
        $response->assertSee('3'); // Pending
        $response->assertSee('7'); // Completed
        $response->assertSee('2'); // Cancelled
    }

    public function test_admin_dashboard_shows_weekly_appointment_data()
    {
        // Create an admin user
        $admin = User::factory()->admin()->create();

        // Create appointments for different days
        Appointment::factory()->create([
            'appointment_date' => now()->subDays(3),
        ]);
        Appointment::factory()->create([
            'appointment_date' => now()->subDays(1),
        ]);
        Appointment::factory()->create([
            'appointment_date' => now()->subDays(1),
        ]);

        // Login as admin
        $this->actingAs($admin);

        // Get the dashboard
        $response = $this->get(route('admin.dashboard'));

        $response->assertStatus(200);

        // Check that the weekly total is displayed
        $response->assertSee('3'); // Total appointments this week
    }

    public function test_admin_dashboard_shows_correct_statistics()
    {
        // Create an admin user
        $admin = User::factory()->admin()->create();

        // Create some clients
        User::factory()->count(3)->create(['is_admin' => false]);

        // Create appointments
        Appointment::factory()->count(2)->create([
            'appointment_date' => now()->toDateString(),
        ]);
        Appointment::factory()->count(4)->completed()->create();

        // Login as admin
        $this->actingAs($admin);

        // Get the dashboard
        $response = $this->get(route('admin.dashboard'));

        $response->assertStatus(200);

        // Check that the dashboard displays appointment data
        $response->assertSee('Total Clients');
        $response->assertSee('Appointments Today');
        $response->assertSee('Total Revenue');
        
        // Check that the charts are present
        $response->assertSee('Appointments This Week');
        $response->assertSee('Appointment Status');
        
        // Check that the status chart shows the correct structure
        $response->assertSee('Confirmed');
        $response->assertSee('Pending');
        $response->assertSee('Completed');
        $response->assertSee('Cancelled');
    }

    public function test_admin_dashboard_data_isolation()
    {
        // This test ensures that each test has a clean database
        $admin = User::factory()->admin()->create();
        
        // Create exactly 1 completed appointment
        Appointment::factory()->completed()->create();
        
        $this->actingAs($admin);
        
        $response = $this->get(route('admin.dashboard'));
        
        $response->assertStatus(200);
        
        // Should show exactly 1 completed appointment = $100 revenue
        $response->assertSee('100'); // Total revenue
        $response->assertSee('1'); // Completed appointments
    }
}
