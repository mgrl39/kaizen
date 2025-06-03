<script>
    import { onMount } from 'svelte';
    import { API_URL } from '$lib/config';
    import { fade, fly } from 'svelte/transition';
    import { theme } from '$lib/theme';
    import MovieCard from '$lib/components/MovieCard.svelte';
    import HeroBanner from '$lib/components/HeroBanner.svelte';

    export let data;
    const { slug } = data;

    let director = null;
    let movies = [];
    let loading = true;
    let error = null;

    async function fetchDirectorData() {
        loading = true;
        try {
            // Fetch director details
            const directorResponse = await fetch(`${API_URL}/directors/${slug}`);
            if (!directorResponse.ok) throw new Error(`HTTP error! status: ${directorResponse.status}`);
            const directorData = await directorResponse.json();
            
            if (directorData.success) {
                director = directorData.data;
                
                // Fetch director's movies
                const moviesResponse = await fetch(`${API_URL}/directors/${slug}/movies`);
                if (!moviesResponse.ok) throw new Error(`HTTP error! status: ${moviesResponse.status}`);
                const moviesData = await moviesResponse.json();
                
                if (moviesData.success) {
                    movies = moviesData.data;
                } else {
                    throw new Error(moviesData.message);
                }
            } else {
                throw new Error(directorData.message);
            }
        } catch (e) {
            console.error('Error fetching director data:', e);
            error = 'Error al cargar los datos del director. Por favor, inténtalo de nuevo más tarde.';
        } finally {
            loading = false;
        }
    }

    onMount(() => {
        fetchDirectorData();
    });

    function getInitials(name) {
        return name.split(' ').map(word => word[0]).join('').toUpperCase();
    }

    function getPattern(name) {
        const patterns = [
            'pattern-1',
            'pattern-2',
            'pattern-3',
            'pattern-4',
            'pattern-5',
            'pattern-6'
        ];
        return patterns[name.length % patterns.length];
    }
</script>

<svelte:head>
    <title>{director ? `${director.name} | Directores` : 'Cargando...'} | Kaizen Cinema</title>
</svelte:head>

<div data-bs-theme={$theme}>
    {#if loading && !director}
        <div class="d-flex justify-content-center py-5" transition:fade>
            <div class="spinner-grow text-light" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
        </div>
    {:else if error}
        <div class="alert alert-danger m-4" role="alert" transition:fade>
            {error}
        </div>
    {:else if director}
        <HeroBanner 
            title={director.name}
            subtitle={`${director.movies_count} ${director.movies_count === 1 ? 'película' : 'películas'}`}
            imageUrl="/images/director-detail-hero.jpg"
            overlayOpacity="60"
        />

        <div class="container py-5">
            <div class="row g-4">
                <div class="col-md-3">
                    <div class="card bg-dark director-card {getPattern(director.name)}" transition:fade>
                        <div class="card-body text-center py-4">
                            <div class="director-initials mb-3">
                                {getInitials(director.name)}
                            </div>
                            <h1 class="card-title h4 text-white mb-0">
                                {director.name}
                            </h1>
                        </div>
                    </div>
                </div>

                <div class="col-md-9">
                    <h2 class="h4 mb-4">Películas dirigidas</h2>
                    {#if movies.length === 0}
                        <div class="text-center py-5 text-muted" transition:fade>
                            <i class="bi bi-camera-reels display-1 mb-3"></i>
                            <p class="lead">No hay películas registradas para este director</p>
                        </div>
                    {:else}
                        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4" transition:fade>
                            {#each movies as movie, i}
                                <div class="col" in:fly={{y: 20, delay: i * 50}}>
                                    <MovieCard {movie} />
                                </div>
                            {/each}
                        </div>
                    {/if}
                </div>
            </div>
        </div>
    {/if}
</div>

<style>
    .director-card {
        border: none;
    }

    .director-initials {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background: rgba(255,255,255,0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3rem;
        font-weight: bold;
        color: white;
        margin: 0 auto;
    }

    .pattern-1 { background: linear-gradient(45deg, #FF3CAC 0%, #784BA0 50%, #2B86C5 100%); }
    .pattern-2 { background: linear-gradient(45deg, #FA8BFF 0%, #2BD2FF 50%, #2BFF88 100%); }
    .pattern-3 { background: linear-gradient(45deg, #FFF720 0%, #3CD500 100%); }
    .pattern-4 { background: linear-gradient(45deg, #FF3D6F 0%, #FFB49A 100%); }
    .pattern-5 { background: linear-gradient(45deg, #40C9FF 0%, #E81CFF 100%); }
    .pattern-6 { background: linear-gradient(45deg, #FF9A9E 0%, #FAD0C4 100%); }
</style> 