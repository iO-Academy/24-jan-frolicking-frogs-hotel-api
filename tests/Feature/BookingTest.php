<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Room;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class BookingTest extends TestCase
{
    use DatabaseMigrations;

    public function test_getAllBookingsSuccess(): void
    {
        Booking::factory()->create();

        $response = $this->getJson('/api/bookings');
        $response->assertStatus(200)
            ->assertJson(function (AssertableJson $json) {
                $json->hasAll(['message', 'data'])
                    ->has('data', 1, function (AssertableJson $json){
                        $json->hasAll(['id', 'customer', 'start', 'end', 'rooms'])
                            ->whereAllType([
                                'id' => 'integer',
                                'customer' => 'string',
                                'start' => 'string',
                                'end' => 'string',

                            ]);
                    });
            });
    }

}
