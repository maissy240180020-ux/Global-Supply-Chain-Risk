<?php

use App\Models\User;
use App\Models\Country;

it('redirects guest users to login on compare page', function () {
    $response = $this->get('/compare');
    $response->assertRedirect('/login');
});

it('allows authenticated users to view the compare select screen', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/compare');
    
    $response->assertStatus(200)
        ->assertViewHas('countries');
});

it('performs country comparison between two countries', function () {
    $user = User::factory()->create();

    $countryA = Country::create([
        'country_code' => 'AA',
        'country_name' => 'Country Alpha',
        'risk_score' => 45.0,
        'flag' => 'https://flagcdn.com/w320/aa.png',
        'capital' => 'Alpha Capital',
        'currency' => 'ALF',
        'population' => 1000000,
        'temperature' => 25,
        'weather' => 'Cerah',
        'risk_level' => 'Low',
        'gdp' => 1000,
        'inflation' => 2.0,
    ]);

    $countryB = Country::create([
        'country_code' => 'BB',
        'country_name' => 'Country Beta',
        'risk_score' => 60.0,
        'flag' => 'https://flagcdn.com/w320/bb.png',
        'capital' => 'Beta Capital',
        'currency' => 'BET',
        'population' => 2000000,
        'temperature' => 15,
        'weather' => 'Hujan',
        'risk_level' => 'Medium',
        'gdp' => 2000,
        'inflation' => 3.5,
    ]);

    $response = $this->actingAs($user)->post('/compare', [
        'country1' => $countryA->id,
        'country2' => $countryB->id,
    ]);

    $response->assertStatus(200)
        ->assertViewHas('countries')
        ->assertViewHas('countryA')
        ->assertViewHas('countryB');

    $viewCountryA = $response->viewData('countryA');
    $viewCountryB = $response->viewData('countryB');

    expect($viewCountryA->id)->toBe($countryA->id);
    expect($viewCountryB->id)->toBe($countryB->id);
    expect(isset($viewCountryA->port_count))->toBeTrue();
    expect(isset($viewCountryB->port_count))->toBeTrue();
    
    // Clean up
    $countryA->delete();
    $countryB->delete();
});
