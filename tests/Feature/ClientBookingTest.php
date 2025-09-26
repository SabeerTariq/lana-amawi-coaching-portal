<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Booking;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class ClientBookingTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_client_can_book_new_session()
    {
        // Create a client user
        $client = User::factory()->create([
            'is_admin' => false,
            'phone' => '+1234567890'
        ]);

        // Login as the client
        $this->actingAs($client);

        // Book a new session
        $response = $this->post(route('client.book-session'), [
            'preferred_date' => now()->addDays(7)->toDateString(),
            'preferred_time' => '14:00',
            'message' => 'I would like to discuss career goals'
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Check that the booking was created
        $this->assertDatabaseHas('bookings', [
            'full_name' => $client->name,
            'email' => $client->email,
            'phone' => $client->phone,
            'preferred_time' => '14:00',
            'message' => 'I would like to discuss career goals',
            'status' => 'pending'
        ]);
        
        // Check the date separately since it's stored as datetime
        $booking = Booking::where('email', $client->email)->first();
        $this->assertEquals(now()->addDays(7)->toDateString(), $booking->preferred_date->toDateString());
    }

    public function test_booking_validation_works()
    {
        // Create a client user
        $client = User::factory()->create(['is_admin' => false]);

        // Login as the client
        $this->actingAs($client);

        // Try to book with invalid data
        $response = $this->post(route('client.book-session'), [
            'preferred_date' => now()->subDays(1)->toDateString(), // Past date
            'preferred_time' => '', // Missing time
            'message' => str_repeat('a', 1001) // Too long message
        ]);

        $response->assertSessionHasErrors(['preferred_date', 'preferred_time', 'message']);
    }

    public function test_booking_modal_displays_client_information()
    {
        // Create a client user with phone
        $client = User::factory()->create([
            'is_admin' => false,
            'phone' => '+1234567890'
        ]);

        // Login as the client
        $this->actingAs($client);

        // Get the appointments page
        $response = $this->get(route('client.appointments'));

        $response->assertStatus(200);
        
        // Check that client information is displayed in the modal
        $response->assertSee($client->name);
        $response->assertSee($client->email);
        $response->assertSee($client->phone);
    }

    public function test_booking_modal_displays_without_phone_if_not_set()
    {
        // Create a client user without phone
        $client = User::factory()->create([
            'is_admin' => false,
            'phone' => null
        ]);

        // Login as the client
        $this->actingAs($client);

        // Get the appointments page
        $response = $this->get(route('client.appointments'));

        $response->assertStatus(200);
        
        // Check that client information is displayed in the modal
        $response->assertSee($client->name);
        $response->assertSee($client->email);
        // Phone field should not be displayed
        $response->assertDontSee('Phone Number');
    }
}
