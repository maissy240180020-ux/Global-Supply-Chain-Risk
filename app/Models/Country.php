<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $fillable = [

        // Informasi Negara
        'country_name',
        'country_code',
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

        // Koordinat Peta
        'latitude',
        'longitude',

    ];
}