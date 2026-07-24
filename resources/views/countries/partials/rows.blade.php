@forelse($countries as $index => $country)
<tr id="country-row-{{ $country->id }}" class="table-row-hover">
    <td class="text-center">
        <button class="btn btn-link p-0 toggle-favorite-btn" data-country-id="{{ $country->id }}" style="text-decoration: none; border: none; background: none;">
            <i class="bi {{ (isset($userFavorites) && in_array($country->id, $userFavorites)) ? 'bi-star-fill text-warning' : 'bi-star text-muted' }} fs-5 favorite-star-icon"></i>
        </button>
    </td>

    <td>{{ $countries->firstItem() + $index }}</td>

    <td>
        @if($country->flag)
            <img src="{{ $country->flag }}" width="45" class="border rounded shadow-sm">
        @endif
    </td>

    <td>
        <strong>{{ $country->country_name }}</strong>
    </td>

    <td>
        @php
            $badge = 'bg-success';
            if ($country->risk_level == 'Medium') {
                $badge = 'bg-warning text-dark';
            }
            if ($country->risk_level == 'High') {
                $badge = 'bg-danger';
            }
        @endphp
        <span class="badge {{ $badge }}">
            {{ number_format($country->risk_score, 0) }}
        </span>
    </td>

    <td>{{ $country->currency }}</td>

    <td>
        <strong>{{ $country->temperature }}°C</strong>
        <br>
        <small class="text-muted">{{ $country->weather }}</small>
    </td>

    <td class="text-center">
        <a href="{{ route('countries.show', $country->id) }}" class="btn btn-info text-white shadow-sm" style="background-color: #0dcaf0; border-color: #0dcaf0; font-size: 0.88rem; font-weight: 500; border-radius: 8px;">
            <i class="bi bi-eye me-1"></i> Lihat Detail
        </a>
    </td>
</tr>
@empty
<tr>
    <td colspan="8" class="text-center py-5">
        <i class="bi bi-database fs-1 text-secondary"></i>
        <br><br>
        Belum ada data negara.
    </td>
</tr>
@endforelse
