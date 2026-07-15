@extends('layouts.app')

@section('title', 'Tambah Data Negara')

@section('content')
<div class="container-fluid">
    <h2 class="fw-bold mb-4">🌍 Tambah Data Negara</h2>

    <div class="card dashboard-card">
        <div class="card-body">
            
            <!-- SECTION AUTOFILL DARI API (Sesuai PDF) -->
            <div class="mb-4 p-3 rounded bg-light border">
                <label for="autofill_select" class="form-label fw-bold text-secondary">
                    ✨ Autofill dari API Negara Resmi (REST Countries):
                </label>
                <select id="autofill_select" class="form-select border-primary" style="font-weight: 500;">
                    <option value="">-- Pilih Negara untuk Isi Formulir Otomatis --</option>
                    @foreach($apiCountries as $item)
                        @php
                            $code = $item['cca2'] ?? '';
                            $name = $item['name']['common'] ?? 'Unknown';
                            $flag = $item['flags']['png'] ?? '';
                            $capital = $item['capital'][0] ?? '';
                            $currency = 'USD';
                            if (isset($item['currencies']) && is_array($item['currencies'])) {
                                $currency = array_key_first($item['currencies']) ?? 'USD';
                            }
                            $population = $item['population'] ?? 0;
                            $lat = $item['latlng'][0] ?? 0;
                            $lng = $item['latlng'][1] ?? 0;
                        @endphp
                        <option value="{{ $code }}" 
                                data-name="{{ $name }}"
                                data-flag="{{ $flag }}"
                                data-capital="{{ $capital }}"
                                data-currency="{{ $currency }}"
                                data-population="{{ $population }}"
                                data-lat="{{ $lat }}"
                                data-lng="{{ $lng }}">
                            {{ $name }} ({{ $code }})
                        </option>
                    @endforeach
                </select>
                <small class="text-muted mt-1 d-block">Memilih negara dari daftar di atas akan otomatis mengisi seluruh kolom formulir di bawah ini dengan data resmi dari REST Countries API.</small>
            </div>

            <form action="{{ route('countries.store') }}" method="POST">
                @csrf
                
                <!-- Hidden Flag Input -->
                <input type="hidden" name="flag" id="country_flag_input">

                <div class="row">
                    <!-- Nama Negara -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nama Negara</label>
                        <input type="text" name="country_name" id="country_name_input" class="form-control" required>
                    </div>

                    <!-- Kode Negara -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Kode Negara (CCA2)</label>
                        <input type="text" name="country_code" id="country_code_input" class="form-control" required>
                    </div>

                    <!-- Ibu Kota -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Ibu Kota</label>
                        <input type="text" name="capital" id="capital_input" class="form-control" required>
                    </div>

                    <!-- Mata Uang -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Mata Uang</label>
                        <input type="text" name="currency" id="currency_input" class="form-control" required>
                    </div>

                    <!-- GDP -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Produk Domestik Bruto (GDP) ($)</label>
                        <input type="number" step="0.01" name="gdp" id="gdp_input" class="form-control" value="0">
                    </div>

                    <!-- Inflasi -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Inflasi (%)</label>
                        <input type="number" step="0.01" name="inflation" id="inflation_input" class="form-control" value="0">
                    </div>

                    <!-- Populasi -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Populasi</label>
                        <input type="number" name="population" id="population_input" class="form-control" value="0">
                    </div>

                    <!-- Skor Risiko -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Skor Risiko (0 - 100)</label>
                        <input type="number" step="0.01" name="risk_score" id="risk_score_input" class="form-control" required value="25">
                    </div>

                    <!-- Tingkat Risiko -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tingkat Risiko</label>
                        <select name="risk_level" id="risk_level_input" class="form-select">
                            <option value="Low">Rendah (Low)</option>
                            <option value="Medium" selected>Sedang (Medium)</option>
                            <option value="High">Tinggi (High)</option>
                        </select>
                    </div>

                    <!-- Suhu -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Suhu (°C)</label>
                        <input type="number" step="0.01" name="temperature" id="temperature_input" class="form-control" value="25">
                    </div>

                    <!-- Cuaca -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Kondisi Cuaca</label>
                        <select name="weather" id="weather_input" class="form-select">
                            <option value="Cerah">Cerah</option>
                            <option value="Berawan">Berawan</option>
                            <option value="Hujan">Hujan</option>
                            <option value="Cerah Berawan">Cerah Berawan</option>
                        </select>
                    </div>

                    <!-- Latitude -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Latitude</label>
                        <input type="number" step="0.000001" name="latitude" id="latitude_input" class="form-control" value="0">
                    </div>

                    <!-- Longitude -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Longitude</label>
                        <input type="number" step="0.000001" name="longitude" id="longitude_input" class="form-control" value="0">
                    </div>
                </div>

                <div class="mt-4">
                    <button class="btn btn-primary">💾 Simpan Data</button>
                    <a href="{{ route('countries.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>

        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const select = document.getElementById('autofill_select');
    if (!select) return;

    select.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (!selectedOption || this.value === "") return;

        const name = selectedOption.getAttribute('data-name');
        const code = this.value;
        const flag = selectedOption.getAttribute('data-flag');
        const capital = selectedOption.getAttribute('data-capital');
        const currency = selectedOption.getAttribute('data-currency');
        const population = selectedOption.getAttribute('data-population');
        const lat = selectedOption.getAttribute('data-lat');
        const lng = selectedOption.getAttribute('data-lng');

        // Isi form otomatis
        document.getElementById('country_name_input').value = name;
        document.getElementById('country_code_input').value = code;
        document.getElementById('country_flag_input').value = flag;
        document.getElementById('capital_input').value = capital;
        document.getElementById('currency_input').value = currency;
        document.getElementById('population_input').value = population;
        document.getElementById('latitude_input').value = lat;
        document.getElementById('longitude_input').value = lng;

        // Auto calculate initial risk based on random or standard settings
        const initialScore = Math.floor(Math.random() * (70 - 15 + 1)) + 15;
        document.getElementById('risk_score_input').value = initialScore;
        
        const riskLevelSelect = document.getElementById('risk_level_input');
        if (initialScore > 60) {
            riskLevelSelect.value = 'High';
        } else if (initialScore > 35) {
            riskLevelSelect.value = 'Medium';
        } else {
            riskLevelSelect.value = 'Low';
        }
    });
});
</script>
@endpush
@endsection