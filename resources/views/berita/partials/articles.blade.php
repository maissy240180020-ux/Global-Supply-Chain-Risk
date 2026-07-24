@if(empty($articles))
    <div class="col-12 text-center py-5">
        <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" alt="No Data" style="width: 120px; opacity: 0.5;" class="mb-3">
        <h5 class="text-muted fw-bold">Tidak ada berita ditemukan</h5>
        <p class="text-muted small">Coba gunakan kata kunci atau kategori yang berbeda.</p>
    </div>
@else
    @foreach($articles as $article)
        @php
            $imageStr = !empty($article['image']) ? $article['image'] : 'https://via.placeholder.com/600x300?text=No+Image';
            $srcName = !empty($article['source']['name']) ? $article['source']['name'] : 'Sumber Tidak Diketahui';
            $displayCountry = (isset($country) && $country !== 'world') ? $country : 'Global';
        @endphp
        <div class="col-md-6 col-lg-4">
            <div class="card shadow-sm border-0 h-100" style="border-radius: 16px; overflow: hidden; transition: transform 0.2s;">
                <div class="position-relative">
                    <img src="{{ $imageStr }}" class="card-img-top" alt="News Image" style="height: 200px; object-fit: cover;">
                    <div class="position-absolute top-0 end-0 m-3">
                        @if($article['sentiment'] === 'Positif')
                            <span class="badge bg-success shadow-sm rounded-pill px-3 py-2"><i class="bi bi-emoji-smile me-1"></i> Positif</span>
                        @elseif($article['sentiment'] === 'Negatif')
                            <span class="badge bg-danger shadow-sm rounded-pill px-3 py-2"><i class="bi bi-emoji-frown me-1"></i> Negatif</span>
                        @else
                            <span class="badge bg-secondary shadow-sm rounded-pill px-3 py-2"><i class="bi bi-emoji-neutral me-1"></i> Netral</span>
                        @endif
                    </div>
                </div>
                <div class="card-body p-4 d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <span class="badge bg-light text-primary border fw-semibold"><i class="bi bi-globe me-1"></i> {{ $srcName }}</span>
                            <span class="badge bg-light text-secondary border fw-semibold ms-1"><i class="bi bi-geo-alt me-1"></i> {{ $displayCountry }}</span>
                        </div>
                        <small class="text-muted fw-semibold" style="font-size: 0.75rem;"><i class="bi bi-clock me-1"></i> {{ $article['published_formatted'] ?? '' }}</small>
                    </div>
                    <h5 class="fw-bold text-dark mb-3" style="line-height: 1.4;">{{ $article['title'] }}</h5>
                    <p class="text-muted small mb-4" style="display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                        {{ $article['description'] }}
                    </p>
                    <a href="{{ $article['url'] }}" target="_blank" class="btn btn-outline-primary mt-auto rounded-pill fw-bold w-100 shadow-sm" style="transition: all 0.3s;">
                        Baca Selengkapnya <i class="bi bi-box-arrow-up-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    @endforeach
@endif
