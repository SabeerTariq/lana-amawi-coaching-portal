<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Appointment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class AppointmentStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_completed_appointments_appear_in_past_appointments()
    {
        // Create a client user
        $client = User::factory()->create(['is_admin' => false]);

        // Create a future appointment
        $futureAppointment = Appointment::factory()->create([
            'user_id' => $client->id,
            'appointment_date' => now()->addDays(7),
            'status' => 'confirmed'
        ]);

        // Create a past appointment that's completed
        $completedAppointment = Appointment::factory()->create([
            'user_id' => $client->id,
            'appointment_date' => now()->subDays(1),
            'status' => 'completed'
        ]);

        // Create a past appointment that's not completed
        $pastAppointment = Appointment::factory()->create([
            'user_id' => $client->id,
            'appointment_date' => now()->subDays(2),
            'status' => 'confirmed'
        ]);

        // Login as the client
        $this->actingAs($client);

        // Get the appointments page
        $response = $this->get(route('client.appointments'));

        $response->assertStatus(200);

        // Check that completed appointment appears in past appointments
        $response->assertSee($completedAppointment->appointment_date->format('l, F j, Y'));
        
        // Check that past but not completed appointment also appears in past appointments
        $response->assertSee($pastAppointment->appointment_date->format('l, F j, Y'));
        
        // Check that future appointment appears in upcoming appointments
        $response->assertSee($futureAppointment->appointment_date->format('l, F j, Y'));
    }

    public function test_appointment_scopes_work_correctly()
    {
        $client = User::factory()->create(['is_admin' => false]);

        // Create appointments with different statuses
        $upcomingAppointment = Appointment::factory()->create([
            'user_id' => $client->id,
            'appointment_date' => now()->addDays(7),
            'status' => 'confirmed'
        ]);

        $completedAppointment = Appointment::factory()->create([
            'user_id' => $client->id,
            'appointment_date' => now()->addDays(1),
            'status' => 'completed'
        ]);

        $pastAppointment = Appointment::factory()->create([
            'user_id' => $client->id,
            'appointment_date' => now()->subDays(1),
            'status' => 'confirmed'
        ]);

        // Test upcoming scope
        $upcoming = Appointment::where('user_id', $client->id)->upcoming()->get();
        $this->assertCount(1, $upcoming);
        $this->assertTrue($upcoming->contains($upcomingAppointment));

        // Test past scope
        $past = Appointment::where('user_id', $client->id)->past()->get();
        $this->assertCount(2, $past);
        $this->assertTrue($past->contains($completedAppointment));
        $this->assertTrue($past->contains($pastAppointment));

        // Test completed scope
        $completed = Appointment::where('user_id', $client->id)->completed()->get();
        $this->assertCount(1, $completed);
        $this->assertTrue($completed->contains($completedAppointment));
    }
}
