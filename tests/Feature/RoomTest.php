<?php

namespace Tests\Feature;

use App\Models\Room;
use App\Models\Type;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class RoomTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * A basic feature test example.
     */
    public function test_getAll_rooms_success(): void
    {
        Room::factory()->create();
        $response = $this->getJson('/api/rooms');

        $response->assertStatus(200)
            ->assertJson(function (AssertableJson $json) {
                $json->hasAll(['message', 'data'])
                    ->has('data', 1, function (AssertableJson $json) {
                        $json->hasAll(['id', 'name', 'image', 'min_capacity', 'max_capacity', 'type'])
                            ->whereAllType([
                                'id' => 'integer',
                                'name' => 'string',
                                'image' => 'string',
                                'min_capacity' => 'integer',
                                'max_capacity' => 'integer',

                            ])
                            ->has('type', function (AssertableJson $json) {
                                $json->hasAll(['id', 'name'])
                                    ->whereAllType([
                                        'id' => 'integer',
                                        'name' => 'string',
                                    ]);
                            }

                            );
                    });
            });

    }

    public function test_getSingleRoomValid(): void
    {
        $room = Room::factory()->create();

        $response = $this->getJson('/api/rooms/1');

        $response->assertOk()
            ->assertJson(function (AssertableJson $json) {
                $json->hasAll(['message', 'data'])
                    ->has('data', function (AssertableJson $json) {
                        $json->hasAll(['id', 'name', 'rate', 'image', 'min_capacity', 'max_capacity', 'description', 'type'])
                            ->whereAllType([
                                'id' => 'integer',
                                'name' => 'string',
                                'rate' => 'integer',
                                'image' => 'string',
                                'min_capacity' => 'integer',
                                'max_capacity' => 'integer',
                                'description' => 'string',
                            ])
                            ->has('type', function (AssertableJson $json) {
                                $json->hasAll(['id', 'name'])
                                    ->whereAllType([
                                        'id' => 'integer',
                                        'name' => 'string',
                                    ]);
                            }

                            );
                    });
            });
    }

    public function test_getSingleRoomInvalid(): void
    {
        $response = $this->getJson('/api/rooms/100');

        $response->assertStatus(404)
            ->assertJson(function (AssertableJson $json) {
                $json->hasAll(['message'])
                    ->whereType('message', 'string');
            });
    }

    public function test_searchByTypeIdSuccess(): void
    {
        Room::factory()->count(1)->create();

        $response = $this->getJson('/api/rooms?type=1');

        $response->assertOk(200)
            ->assertJson(function (AssertableJson $json) {
                $json->hasAll(['data'])
                    ->has('data', 1, function (AssertableJson $json) {
                        $json->hasAll(['id', 'name', 'rate', 'image', 'min_capacity', 'max_capacity', 'description', 'type'])
                            ->whereAllType([
                                'id' => 'integer',
                                'name' => 'string',
                                'rate' => 'integer',
                                'image' => 'string',
                                'min_capacity' => 'integer',
                                'max_capacity' => 'integer',
                                'description' => 'string'
                            ])
                            ->has('type', function (AssertableJson $json) {
                                $json->hasAll(['id', 'name'])
                                    ->whereAllType([
                                        'id' => 'integer',
                                        'name' => 'string',
                                    ]);
                            }

                            );
                    });
            });
    }
}
