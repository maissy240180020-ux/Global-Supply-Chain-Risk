<?php

use App\Models\User;
use App\Models\Country;
use Illuminate\Support\Facades\DB;

it('redirects guest users to login on visualization page', function () {
    $response = $this->get('/visualisasi');
    $response->assertRedirect('/login');
});

it('allows authenticated users to view the visualization dashboard', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/visualisasi');
    
    $response->assertStatus(200)
        ->assertViewHas('countries')
        ->assertViewHas('riskCounts')
        ->assertViewHas('topRisk')
        ->assertViewHas('topPorts')
        ->assertViewHas('correlationData')
        ->assertViewHas('globalAverages');

    $riskCounts = $response->viewData('riskCounts');
    expect($riskCounts)->toHaveKeys(['High', 'Medium', 'Low']);
    
    $globalAverages = $response->viewData('globalAverages');
    expect($globalAverages)->toHaveKeys(['risk_score', 'inflation', 'temperature', 'population', 'port_count']);
});
