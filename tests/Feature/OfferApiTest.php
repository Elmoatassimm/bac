<?php

use App\Models\User;
use App\Models\Offer;

beforeEach(function () {
    // Create test data
    $this->user = User::factory()->create();
    $this->offers = Offer::factory()->count(3)->create([
        'user_id' => $this->user->id,
    ]);
});

test('can get all offers', function () {
    $response = $this->getJson('/api/v1/offers');

    $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'description',
                        'price',
                        'user' => [
                            'id',
                            'name',
                            'email',
                        ]
                    ]
                ],
                'message'
            ])
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonCount(3, 'data');
});

test('can get specific offer by id', function () {
    $offer = $this->offers->first();

    $response = $this->getJson("/api/v1/offers/{$offer->id}");

    $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'title',
                    'description',
                    'price',
                    'user' => [
                        'id',
                        'name',
                        'email',
                    ]
                ],
                'message'
            ])
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $offer->id,
                    'title' => $offer->title,
                ]
            ]);
});



test('returns 404 for nonexistent offer', function () {
    $response = $this->getJson('/api/v1/offers/999999');

    $response->assertStatus(404)
            ->assertJson([
                'success' => false,
            ]);
});

test('offers include user relationship', function () {
    $response = $this->getJson('/api/v1/offers');

    $response->assertStatus(200);

    $data = $response->json('data');
    expect($data)->not->toBeEmpty();
    expect($data[0])->toHaveKey('user');
    expect($data[0]['user']['name'])->toBe($this->user->name);
});
