<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $fillable = [

        // Informasi Negara
        'country_name',
        'country_code',
        'flag',
        'capital',

        // Ekonomi
        'currency',
        'gdp',
        'inflation',
        'population',

        // Risiko
        'risk_score',
        'risk_level',

        // Cuaca
        'temperature',
        'weather',

        // Koordinat
        'latitude',
        'longitude',

        // Favorit
        'is_favorite',

    ];

    /**
     * Get the users who favorited this country.
     */
    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'country_user');
    }
}