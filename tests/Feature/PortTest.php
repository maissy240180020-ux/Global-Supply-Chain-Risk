<?php

use App\Models\User;
use App\Models\Country;
use Illuminate\Support\Facades\DB;

it('redirects guest users to login', function () {
    $response = $this->get('/pelabuhan');
    $response->assertRedirect('/login');
});

it('allows authenticated users to view the port dashboard', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/pelabuhan');
    $response->assertStatus(200);
});

it('performs real-time global search on Nominatim', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/pelabuhan/search-global?q=Rotterdam');
    $response->assertStatus(200);
    
    $data = $response->json();
    expect(is_array($data))->toBeTrue();
});

it('stores a global port to the database', function () {
    $user = User::factory()->create();

    // Clean up if it exists
    DB::table('ports')->where('port_name', 'Test Global Port')->delete();

    $response = $this->actingAs($user)->postJson('/pelabuhan/store-global', [
        'port_name' => 'Test Global Port',
        'country_code' => 'ZZ',
        'latitude' => 51.9244,
        'longitude' => 4.4777,
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
        ]);

    $this->assertDatabaseHas('ports', [
        'port_name' => 'Test Global Port',
        'country_code' => 'ZZ',
    ]);
});
