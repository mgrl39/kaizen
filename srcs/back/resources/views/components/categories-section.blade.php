<!-- Categories Section -->
<div class="container mb-5">
    <div class="row g-4">
        @php
        $categories = [
            ['name' => 'Acción', 'icon' => 'bi-lightning-charge-fill', 'color' => 'danger'],
            ['name' => 'Comedia', 'icon' => 'bi-emoji-laughing-fill', 'color' => 'warning'],
            ['name' => 'Drama', 'icon' => 'bi-mask', 'color' => 'info'],
            ['name' => 'Ciencia Ficción', 'icon' => 'bi-rocket-takeoff-fill', 'color' => 'primary'],
            ['name' => 'Terror', 'icon' => 'bi-eyeglasses', 'color' => 'dark'],
            ['name' => 'Romance', 'icon' => 'bi-heart-fill', 'color' => 'danger'],
        ];
        @endphp

        @foreach ($categories as $index => $category)
            <div class="col-md-4 col-sm-6" data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
                <div class="card border-0 h-100">
                    <div class="card-body p-4 text-center">
                        <div class="rounded-circle mb-3 mx-auto bg-{{ $category['color'] }}-subtle p-3 d-inline-block">
                            <i class="bi {{ $category['icon'] }} fs-1 text-{{ $category['color'] }}"></i>
                        </div>
                        <h5 class="mb-2">{{ $category['name'] }}</h5>
                        <a href="/genre/{{ strtolower($category['name']) }}" class="btn btn-sm btn-outline-{{ $category['color'] }} mt-2">
                            Ver películas
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
