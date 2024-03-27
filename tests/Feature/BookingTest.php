<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Room;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class BookingTest extends TestCase
{
    use DatabaseMigrations;

    public function test_create_booking_invalid()
    {
        $response = $this->postJson('/api/bookings', []);
        $response->assertInvalid(['room_id', 'customer', 'guests', 'start', 'end']);
    }

    public function test_create_booking_noDateAvailable()
    {
        $booking = Booking::factory()->hasAttached(Room::factory())->create();
        $booking->start = '2024-03-30';
        $booking->end = '2024-04-04';
        $booking->save();

        $response = $this->postJson('/api/bookings', [
            'room_id' => 1,
            'customer' => 'sarah',
            'guests' => 1,
            'start' => '2024-04-02',
            'end' => '2024-04-05',
        ]);

        $response->assertStatus(400)
            ->assertJson(function (AssertableJson $json) {
                $json->hasAll(['message'])
                    ->whereType('message', 'string');
            });
    }

    public function test_createBooking_startAfterEnd()
    {
        Room::factory()->create();

        $response = $this->postJson('/api/bookings', [
            'room_id' => 1,
            'customer' => 'sarah',
            'guests' => 1,
            'start' => '2024-04-06',
            'end' => '2024-04-05',
        ]);

        $response->assertStatus(400)
            ->assertJson(function (AssertableJson $json) {
                $json->hasAll(['message'])
                    ->whereType('message', 'string');
            });
    }

    public function test_createBooking_GuestSize_Invalid()
    {
        $room = Room::factory()->create();
        $room->max_capacity = '2';
        $room->save();

        $response = $this->postJson('/api/bookings', [
            'room_id' => 1,
            'customer' => 'sarah',
            'guests' => 3,
            'start' => '2024-04-06',
            'end' => '2024-04-05',
        ]);

        $response->assertStatus(400)
            ->assertJson(function (AssertableJson $json) {
                $json->hasAll(['message'])
                    ->whereType('message', 'string');
            });
    }

    public function test_create_booking_success()
    {

        $booking = Booking::factory()->has(Room::factory())->create();
        $booking->start = '2024-03-27';
        $booking->end = '2024-03-28';
        $booking->save();

        $response = $this->postJson('/api/bookings', [
            'room_id' => 1,
            'customer' => 'sarah',
            'guests' => 1,
            'start' => '2024-04-05',
            'end' => '2024-04-07',
        ]);

        $response->assertStatus(201)
            ->assertJson(function (AssertableJson $json) {
                $json->hasAll(['message'])
                    ->whereType('message', 'string');
            });
    }

    public function test_getAllBookingsSuccess(): void
    {
        $booking = Booking::factory()->has(Room::factory())->create();
        $booking->start = '2024-03-27';
        $booking->end = '2024-03-28';
        $booking->save();

        $response = $this->getJson('/api/bookings');
        $response->assertStatus(200)
            ->assertJson(function (AssertableJson $json) {
                $json->hasAll(['message', 'data'])
                    ->whereType('message', 'string')
                    ->has('data', 1, function (AssertableJson $json) {
                        $json->hasAll(['id', 'customer', 'start', 'end', 'created_at', 'rooms'])
                            ->whereAllType([
                                'id' => 'integer',
                                'customer' => 'string',
                                'start' => 'string',
                                'end' => 'string',
                                'created_at' => 'string',
                            ])
                            ->has('rooms', 1, function (AssertableJson $json) {
                                $json->hasAll(['id', 'name'])
                                    ->whereAllType([
                                        'id' => 'integer',
                                        'name' => 'string',
                                    ]);
                            });
                    });
            });
    }

    public function test_delete_booking_success()
    {
        $booking = Booking::factory()->create();
        $booking->customer = 'missing';
        $booking->save();

        $response = $this->deleteJson('/api/bookings/1');

        $response->assertOk()
            ->assertJson(function (AssertableJson $json) {
                $json->hasAll(['message'])
                    ->whereType('message', 'string');
            });

        $this->assertDatabaseMissing('bookings', [
            'name' => $booking->customer
        ]);
    }
}
