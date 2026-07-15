@extends('layouts.app')

@section('title', 'Dashboard Nilai Tukar Realtime')

@section('content')

<div class="container-fluid">

    <!-- Header & Base Dropdown -->
    <div class="mb-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h2 class="fw-bold mb-0">💱 Currency Intelligence Center</h2>
            <p class="text-muted mb-0">Pemantauan Nilai Tukar Mata Uang Global Terupdate & Konverter Konversi Logistik</p>
        </div>
        
        <!-- Pemilih Base Currency -->
        <div class="bg-white p-2 rounded shadow-sm border border-light d-flex align-items-center gap-2">
            <label for="base" class="fw-semibold text-secondary mb-0">Base Currency:</label>
            <form action="{{ route('nilai-tukar.index') }}" method="GET" id="baseSelectForm" class="m-0">
                <select name="base" id="base" class="form-select form-select-sm border-0 bg-light" style="font-weight: 500;" onchange="this.form.submit()">
                    @foreach($supported as $code)
                        <option value="{{ $code }}" {{ $base == $code ? 'selected' : '' }}>
                            {{ $currencyMeta[$code]['flag'] }} {{ $code }} - {{ $currencyMeta[$code]['name'] }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>

    @if($kurs)

    @php
        // Cari 4 mata uang populer yang bukan base currency untuk dipasang di kartu atas
        $popular = ['USD', 'EUR', 'JPY', 'GBP', 'IDR', 'SGD', 'CNY'];
        $cardCurrencies = [];
        foreach ($popular as $pop) {
            if ($pop !== $base && isset($kurs['rates'][$pop])) {
                $cardCurrencies[] = $pop;
            }
            if (count($cardCurrencies) == 4) break;
        }

        // Kustomisasi ikon, warna, dan background
        $styleMap = [
            'USD' => ['icon' => 'bi-currency-dollar', 'color' => '#059669', 'bg' => 'rgba(5, 150, 105, 0.12)'],
            'IDR' => ['icon' => 'bi-cash-stack', 'color' => '#10b981', 'bg' => 'rgba(16, 185, 129, 0.12)'],
            'EUR' => ['icon' => 'bi-currency-euro', 'color' => '#0284c7', 'bg' => 'rgba(2, 130, 199, 0.12)'],
            'JPY' => ['icon' => 'bi-currency-yen', 'color' => '#d97706', 'bg' => 'rgba(217, 119, 6, 0.12)'],
            'GBP' => ['icon' => 'bi-currency-pound', 'color' => '#7c3aed', 'bg' => 'rgba(124, 58, 237, 0.12)'],
            'SGD' => ['icon' => 'bi-cash', 'color' => '#06b6d4', 'bg' => 'rgba(6, 182, 212, 0.12)'],
            'CNY' => ['icon' => 'bi-currency-yen', 'color' => '#e11d48', 'bg' => 'rgba(225, 29, 72, 0.12)'],
        ];
    @endphp

    <!-- Top Rate Cards Row -->
    <div class="row g-3 mb-4">
        @foreach($cardCurrencies as $cardCurr)
            @php
                $rateVal = $kurs['rates'][$cardCurr];
                $style = $styleMap[$cardCurr] ?? ['icon' => 'bi-currency-exchange', 'color' => '#475569', 'bg' => 'rgba(71, 85, 105, 0.12)'];
                // Tentukan jumlah desimal berdasarkan nilai rate
                $decimals = $rateVal < 1.0 ? 4 : 2;
            @endphp
            <div class="col-xl-3 col-md-6 col-sm-12">
                <div class="card dashboard-card border-0 shadow-sm h-100" style="border-radius: 16px;">
                    <div class="card-body p-4 d-flex flex-column justify-content-between">
                        <div>
                            <!-- Header -->
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="text-uppercase tracking-wider fw-bold text-muted" style="font-size: 0.72rem; letter-spacing: 0.05em;">
                                    {{ $base }} ke {{ $cardCurr }}
                                </span>
                                <div class="d-flex align-items-center justify-content-center rounded-circle" 
                                     style="width: 38px; height: 38px; background-color: {{ $style['bg'] }}; color: {{ $style['color'] }};">
                                    <i class="{{ $style['icon'] }} fs-5"></i>
                                </div>
                            </div>
                            
                            <!-- Value -->
                            <div class="d-flex align-items-baseline mb-2">
                                <h3 class="fw-bold mb-0 text-dark" style="font-size: 1.8rem; line-height: 1;">
                                    {{ number_format($rateVal, $decimals) }}
                                </h3>
                                <span class="text-muted ms-1 fw-semibold" style="font-size: 0.85rem;">{{ $cardCurr }}</span>
                            </div>
                        </div>
                        
                        <!-- Footer -->
                        <div class="mt-2 pt-2 border-top border-light d-flex justify-content-between align-items-center" style="font-size: 0.72rem;">
                            <span class="text-muted fw-medium">{{ $currencyMeta[$cardCurr]['name'] ?? '' }}</span>
                            <span class="badge rounded-pill fw-semibold px-2 py-0.5 bg-success-subtle text-success">
                                REALTIME
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Main Content Section -->
    <div class="row g-4">
        
        <!-- Table Column -->
        <div class="col-lg-7 col-md-12">
            <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                <div class="card-header bg-white py-3 border-light d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-list-stars text-primary"></i> Daftar Nilai Tukar Lengkap (Base: {{ $base }})
                    </h5>
                    <!-- Search Input -->
                    <div class="position-relative" style="width: 200px;">
                        <input type="text" id="tableSearch" class="form-control form-control-sm ps-4" placeholder="Cari mata uang...">
                        <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-2 text-muted" style="font-size: 0.8rem;"></i>
                    </div>
                </div>
                
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 480px; overflow-y: auto;">
                        <table class="table table-hover align-middle mb-0" id="ratesTable">
                            <thead class="table-light sticky-top" style="z-index: 1;">
                                <tr>
                                    <th class="ps-4">Mata Uang</th>
                                    <th>Nama Lengkap</th>
                                    <th class="text-end">Nilai Kurs</th>
                                    <th class="text-end pe-4">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Base Currency Row (always 1.0000) -->
                                <tr class="table-active">
                                    <td class="ps-4 fw-semibold">
                                        <span class="me-2">{{ $currencyMeta[$base]['flag'] }}</span>{{ $base }}
                                    </td>
                                    <td class="text-muted fw-medium">{{ $currencyMeta[$base]['name'] }}</td>
                                    <td class="text-end fw-bold text-dark">1.0000</td>
                                    <td class="text-end pe-4">
                                        <span class="badge rounded-pill bg-secondary-subtle text-secondary px-2.5 py-1 fw-bold" style="font-size: 0.65rem;">ACUAN</span>
                                    </td>
                                </tr>
                                
                                @foreach($kurs['rates'] as $curr => $rate)
                                    @php
                                        $decimals = $rate < 1.0 ? 4 : 2;
                                    @endphp
                                    <tr>
                                        <td class="ps-4 fw-semibold">
                                            <span class="me-2">{{ $currencyMeta[$curr]['flag'] ?? '🌍' }}</span>{{ $curr }}
                                        </td>
                                        <td class="text-muted" style="font-size: 0.85rem;">{{ $currencyMeta[$curr]['name'] ?? 'Mata Uang Asing' }}</td>
                                        <td class="text-end fw-bold text-dark">{{ number_format($rate, $decimals) }}</td>
                                        <td class="text-end pe-4">
                                            <span class="badge rounded-pill bg-success-subtle text-success px-2.5 py-1 fw-semibold" style="font-size: 0.65rem;">REALTIME</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div class="card-footer bg-white border-top border-light py-3" style="font-size: 0.8rem;">
                    <div class="row">
                        <div class="col-sm-6 text-muted">
                            Data diperbarui per tanggal: <strong class="text-dark">{{ $kurs['date'] }}</strong>
                        </div>
                        <div class="col-sm-6 text-sm-end text-muted mt-1 mt-sm-0">
                            Sumber Data: <span class="badge bg-light text-dark border">Frankfurter API</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Calculator Column -->
        <div class="col-lg-5 col-md-12">
            <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                <div class="card-header bg-white py-3 border-light">
                    <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-calculator text-primary"></i> Kalkulator Konversi Realtime
                    </h5>
                </div>
                
                <div class="card-body p-4">
                    <!-- Calculator Form -->
                    <div class="mb-3">
                        <label for="calcAmount" class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">Jumlah Uang</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 fw-semibold text-muted" id="amountAddon">{{ $base }}</span>
                            <input type="number" id="calcAmount" class="form-control border-start-0" value="1000" min="1" step="any" style="font-weight: 500;">
                        </div>
                    </div>
                    
                    <div class="row g-3 mb-4">
                        <div class="col-5">
                            <label for="calcFrom" class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">Dari</label>
                            <select id="calcFrom" class="form-select fw-semibold" style="background-color: #f8fafc;">
                                @foreach($supported as $code)
                                    <option value="{{ $code }}" {{ $code == $base ? 'selected' : '' }}>
                                        {{ $code }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-2 d-flex align-items-end justify-content-center pb-2">
                            <button id="swapBtn" class="btn btn-outline-secondary rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                                <i class="bi bi-arrow-left-right"></i>
                            </button>
                        </div>
                        
                        <div class="col-5">
                            <label for="calcTo" class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">Ke</label>
                            <select id="calcTo" class="form-select fw-semibold" style="background-color: #f8fafc;">
                                @foreach($supported as $code)
                                    @php
                                        // Default target: IDR if base is USD, otherwise USD
                                        $isDefault = ($base == 'USD' && $code == 'IDR') || ($base != 'USD' && $code == 'USD');
                                    @endphp
                                    <option value="{{ $code }}" {{ $isDefault ? 'selected' : '' }}>
                                        {{ $code }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <!-- Result Box -->
                    <div class="p-4 bg-light rounded-4 text-center border border-light-subtle mb-3">
                        <span class="text-muted d-block mb-1" style="font-size: 0.8rem; font-weight: 500;">HASIL KONVERSI</span>
                        <h2 class="fw-bold mb-1 text-primary" id="calcResult" style="font-size: 2.2rem;">
                            Calculating...
                        </h2>
                        <span class="text-secondary fw-semibold" id="calcFormula" style="font-size: 0.78rem;">
                            -
                        </span>
                    </div>
                </div>
            </div>
        </div>

    </div>

    @else

    <div class="alert alert-danger shadow-sm border-0 d-flex align-items-center gap-3 p-4" style="border-radius: 16px;">
        <i class="bi bi-x-circle-fill fs-3"></i>
        <div>
            <h5 class="fw-bold mb-1">Gagal Mengambil Data Nilai Tukar</h5>
            <p class="mb-0">Tidak dapat terhubung ke API Keuangan. Harap periksa jaringan Anda atau coba lagi beberapa saat lagi.</p>
        </div>
    </div>

    @endif

</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Ambil data kurs dari PHP
    const rates = @json($kurs['rates'] ?? []);
    const baseCurrency = "{{ $kurs['base'] ?? 'USD' }}";
    
    // Inisialisasi nilai base currency sendiri ke rates map untuk memudahkan kalkulasi
    rates[baseCurrency] = 1.0;

    const calcAmount = document.getElementById('calcAmount');
    const calcFrom = document.getElementById('calcFrom');
    const calcTo = document.getElementById('calcTo');
    const calcResult = document.getElementById('calcResult');
    const calcFormula = document.getElementById('calcFormula');
    const amountAddon = document.getElementById('amountAddon');
    const swapBtn = document.getElementById('swapBtn');
    
    // Perbarui label addon saat "Dari" berganti
    calcFrom.addEventListener('change', function() {
        amountAddon.textContent = this.value;
        calculate();
    });

    calcTo.addEventListener('change', calculate);
    calcAmount.addEventListener('input', calculate);

    // Tukar mata uang (Swap)
    swapBtn.addEventListener('click', function() {
        const temp = calcFrom.value;
        calcFrom.value = calcTo.value;
        calcTo.value = temp;
        
        amountAddon.textContent = calcFrom.value;
        calculate();
    });

    function calculate() {
        const amount = parseFloat(calcAmount.value) || 0;
        const from = calcFrom.value;
        const to = calcTo.value;

        if (!rates[from] || !rates[to]) {
            calcResult.textContent = "Error";
            return;
        }

        // Kalkulasi nilai tukar silang (Cross rates)
        // Rate dalam database adalah relatif terhadap `baseCurrency`
        // 1 `from` = (1 / rates[from]) baseCurrency
        // 1 baseCurrency = rates[to] `to`
        // Maka 1 `from` = rates[to] / rates[from] `to`
        const conversionRate = rates[to] / rates[from];
        const result = amount * conversionRate;

        // Desimal dinamis berdasarkan hasil
        const decimals = result < 1.0 && result > 0 ? 4 : 2;
        const rateDecimals = conversionRate < 1.0 ? 4 : 2;
        const reverseRateDecimals = (1 / conversionRate) < 1.0 ? 4 : 2;

        // Tampilkan hasil
        calcResult.textContent = new Intl.NumberFormat('id-ID', {
            minimumFractionDigits: decimals,
            maximumFractionDigits: decimals
        }).format(result) + ' ' + to;

        // Tampilkan formula pendukung
        calcFormula.innerHTML = `1 ${from} = ${new Intl.NumberFormat('id-ID', {
            minimumFractionDigits: rateDecimals,
            maximumFractionDigits: rateDecimals
        }).format(conversionRate)} ${to} &nbsp;&bull;&nbsp; 1 ${to} = ${new Intl.NumberFormat('id-ID', {
            minimumFractionDigits: reverseRateDecimals,
            maximumFractionDigits: reverseRateDecimals
        }).format(1 / conversionRate)} ${from}`;
    }

    // Jalankan kalkulasi pertama kali
    calculate();

    // Fitur pencarian tabel nilai tukar
    const tableSearch = document.getElementById('tableSearch');
    const ratesTableRows = document.querySelectorAll('#ratesTable tbody tr');

    tableSearch.addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        
        ratesTableRows.forEach(row => {
            const cells = row.getElementsByTagName('td');
            if (cells.length >= 2) {
                const code = cells[0].textContent.toLowerCase();
                const name = cells[1].textContent.toLowerCase();
                
                if (code.includes(query) || name.includes(query)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            }
        });
    });
});
</script>
@endpush