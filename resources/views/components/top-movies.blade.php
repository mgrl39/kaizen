<!-- Top Movies -->
<div class="container mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0">
            <i class="fas fa-star text-warning me-2"></i>Top Películas
        </h2>
        <a href="/movies" class="btn btn-outline-light btn-floating">
            <i class="fas fa-arrow-right"></i>
        </a>
    </div>
    
    <div class="row">
        <div class="col-12">
            <div class="position-relative" data-aos="fade-up">
                <div class="swiper-container">
                    <div class="swiper-wrapper">
                        @php
                        $movies = [
                            ['id' => 1, 'title' => 'Dune', 'image' => 'https://image.tmdb.org/t/p/w500/jYEW5xZkZk2WTrdbMGAPFuBqbDc.jpg', 'rating' => 8.1],
                            ['id' => 2, 'title' => 'Oppenheimer', 'image' => 'https://image.tmdb.org/t/p/w500/8Gxv8gSFCU0XGDykEGv7zR1n2ua.jpg', 'rating' => 8.4],
                            ['id' => 3, 'title' => 'Barbie', 'image' => 'https://image.tmdb.org/t/p/w500/iuFNMS8U5cb6xfzi8Qzsk3e3hkS.jpg', 'rating' => 7.2],
                            ['id' => 4, 'title' => 'Aquaman', 'image' => 'https://image.tmdb.org/t/p/w500/xLPffWMhMj1l50ND3KchMjYoKmE.jpg', 'rating' => 6.9],
                            ['id' => 5, 'title' => 'The Marvels', 'image' => 'https://image.tmdb.org/t/p/w500/Ag3D9qXjhJ2FUkrlJ0Cv1pgxqYQ.jpg', 'rating' => 6.3],
                        ];
                        @endphp
                        
                        @foreach($movies as $movie)
                            <div class="swiper-slide" style="width: 220px;">
                                <div class="card border-0 h-100">
                                    <div class="position-relative">
                                        <img src="{{ $movie['image'] }}" class="card-img-top rounded-3" alt="{{ $movie['title'] }}">
                                        <div class="position-absolute top-0 end-0 m-2">
                                            <span class="badge bg-dark">
                                                <i class="bi bi-star-fill text-warning me-1"></i>
                                                {{ $movie['rating'] }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="card-body p-3">
                                        <h6 class="card-title mb-1">{{ $movie['title'] }}</h6>
                                        <a href="/movies/{{ $movie['id'] }}" class="btn btn-sm btn-primary w-100 mt-2">
                                            <i class="bi bi-ticket-perforated me-1"></i> Reservar
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
            </div>
        </div>
    </div>
</div> 