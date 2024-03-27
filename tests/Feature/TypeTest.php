<?php

namespace Tests\Feature;

use App\Models\Type;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class TypeTest extends TestCase
{
    use DatabaseMigrations;

    public function test_getAll_types_success(): void
    {
        Type::factory()->create();
        $response = $this->getJson('/api/types');

        $response->assertStatus(200)
            ->assertJson(function (AssertableJson $json) {
                $json->hasAll(['message', 'data'])
                    ->has('data', 1, function (AssertableJson $json) {
                        $json->hasAll(['id', 'name'])
                            ->whereAllType([
                                'id' => 'integer',
                                'name' => 'string',
                            ]);
                    });
            });
    }
}
