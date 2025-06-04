<script>
    import { onMount } from 'svelte';
    import { API_URL } from '$lib/config';
    import { fade, fly } from 'svelte/transition';
    import { theme } from '$lib/theme';
    import MovieCard from '$lib/components/MovieCard.svelte';
    import HeroBanner from '$lib/components/HeroBanner.svelte';
    import { t } from '$lib/i18n';

    export let data;
    const { slug } = data;

    let actor = null;
    let movies = [];
    let loading = true;
    let error = null;

    async function fetchActorData() {
        loading = true;
        try {
            // Fetch actor details
            const actorResponse = await fetch(`${API_URL}/actors/${slug}`);
            if (!actorResponse.ok) throw new Error(`HTTP error! status: ${actorResponse.status}`);
            const actorData = await actorResponse.json();
            
            if (actorData.success) {
                actor = actorData.data;
                
                // Fetch actor's movies
                const moviesResponse = await fetch(`${API_URL}/actors/${slug}/movies`);
                if (!moviesResponse.ok) throw new Error(`HTTP error! status: ${moviesResponse.status}`);
                const moviesData = await moviesResponse.json();
                
                if (moviesData.success) {
                    movies = moviesData.data.movies;
                } else {
                    throw new Error(moviesData.message);
                }
            } else {
                throw new Error(actorData.message);
            }
        } catch (e) {
            console.error('Error fetching actor data:', e);
            error = e.message || 'Error al cargar los datos del actor. Por favor, inténtalo de nuevo más tarde.';
        } finally {
            loading = false;
        }
    }

    onMount(() => {
        fetchActorData();
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
    <title>{actor ? `${actor.name} | Actores` : 'Cargando...'} | Kaizen Cinema</title>
</svelte:head>

<div class="actor-detail" data-bs-theme={$theme}>
    {#if loading}
        <div class="min-vh-50 d-flex align-items-center justify-content-center py-5" in:fade>
            <div class="spinner-border text-primary me-3" role="status"></div>
            <span>Cargando información del actor...</span>
        </div>
    {:else if error}
        <div class="min-vh-50 d-flex align-items-center justify-content-center py-5" in:fade>
            <div class="card border-danger w-100 max-w-md">
                <div class="card-header bg-danger bg-opacity-25 text-danger">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    Error
                </div>
                <div class="card-body text-center">
                    <p class="mb-4">{error}</p>
                    <a href="/actors" class="btn btn-outline-primary">
                        <i class="bi bi-arrow-left me-2"></i>
                        Volver a actores
                    </a>
                </div>
            </div>
        </div>
    {:else if actor}
        <HeroBanner 
            title={actor.name}
            subtitle={$t('bannerActorSubtitle', { count: actor.movies?.length || 0 })}
            imageUrl="https://source.unsplash.com/random/1920x1080/?cinema,theater"
            overlayOpacity="60"
        />

        <div class="container py-5">
            <div class="row g-4">
                <div class="col-md-3">
                    <div class="actor-profile-card {getPattern(actor.name)}" in:fade>
                        <div class="card-body text-center py-4">
                            <div class="actor-initials mb-3">
                                {getInitials(actor.name)}
                            </div>
                            <h1 class="card-title h4 text-white mb-0">
                                {actor.name}
                            </h1>
                            {#if actor.biography}
                                <p class="text-white-50 mt-3 mb-0">
                                    {actor.biography}
                                </p>
                            {/if}
                        </div>
                    </div>
                </div>

                <div class="col-md-9">
                    <div class="movies-section" in:fade={{ delay: 200 }}>
                        <h2 class="h4 mb-4">Películas en las que participa</h2>
                        {#if !movies || movies.length === 0}
                            <div class="text-center py-5 text-muted" in:fade>
                                <i class="bi bi-camera-reels display-1 mb-3"></i>
                                <p class="lead">No hay películas registradas para este actor</p>
                            </div>
                        {:else}
                            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
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
        </div>
    {/if}
</div>

<style>
    /* Layout */
    .min-vh-50 {
        min-height: 50vh;
    }

    .max-w-md {
        max-width: 500px;
    }

    /* Actor Profile Card */
    .actor-profile-card {
        background: linear-gradient(135deg, rgba(147, 51, 234, 0.1) 0%, rgba(79, 70, 229, 0.1) 100%);
        border-radius: 1rem;
        border: 1px solid rgba(255, 255, 255, 0.1);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        backdrop-filter: blur(10px);
        transition: all 0.3s ease;
    }

    .actor-profile-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }

    /* Actor Initials */
    .actor-initials {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--bs-primary) 0%, var(--bs-purple) 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        font-weight: bold;
        color: white;
        margin: 0 auto;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    /* Pattern Variations */
    .pattern-1 { background: linear-gradient(135deg, rgba(147, 51, 234, 0.1) 0%, rgba(79, 70, 229, 0.1) 100%); }
    .pattern-2 { background: linear-gradient(135deg, rgba(236, 72, 153, 0.1) 0%, rgba(147, 51, 234, 0.1) 100%); }
    .pattern-3 { background: linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(147, 51, 234, 0.1) 100%); }
    .pattern-4 { background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, rgba(59, 130, 246, 0.1) 100%); }
    .pattern-5 { background: linear-gradient(135deg, rgba(245, 158, 11, 0.1) 0%, rgba(236, 72, 153, 0.1) 100%); }
    .pattern-6 { background: linear-gradient(135deg, rgba(239, 68, 68, 0.1) 0%, rgba(245, 158, 11, 0.1) 100%); }

    /* Movies Section */
    .movies-section {
        position: relative;
        z-index: 1;
    }

    /* Global Styles */
    :global(.actor-detail) {
        min-height: 100vh;
        background: var(--bs-dark);
        color: var(--bs-light);
    }

    :global(.actor-detail .card) {
        background: rgba(33, 37, 41, 0.95);
        backdrop-filter: blur(10px);
    }

    :global(.actor-detail .btn) {
        transition: all 0.3s ease;
    }

    :global(.actor-detail .btn:hover) {
        transform: translateY(-2px);
    }
</style> 