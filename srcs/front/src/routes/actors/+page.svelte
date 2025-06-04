<script>
    import { onMount } from 'svelte';
    import { API_URL } from '$lib/config';
    import { fade, fly } from 'svelte/transition';
    import { theme } from '$lib/theme';
    import HeroBanner from '$lib/components/HeroBanner.svelte';

    let actors = [];
    let loading = true;
    let error = null;
    let currentPage = 1;
    let lastPage = 1;
    let total = 0;
    let perPage = 24;

    async function fetchActors(page = 1) {
        loading = true;
        try {
            const response = await fetch(`${API_URL}/actors?page=${page}&per_page=${perPage}`);
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            const data = await response.json();
            if (data.success) {
                actors = data.data;
                currentPage = data.meta.current_page;
                lastPage = data.meta.last_page;
                total = data.meta.total;
            } else {
                throw new Error(data.message);
            }
        } catch (e) {
            console.error('Error fetching actors:', e);
            error = 'Error al cargar los actores. Por favor, inténtalo de nuevo más tarde.';
        } finally {
            loading = false;
        }
    }

    function handlePageChange(newPage) {
        if (newPage >= 1 && newPage <= lastPage && newPage !== currentPage) {
            fetchActors(newPage);
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    }

    onMount(() => {
        fetchActors();
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
    <title>Actores | Kaizen Cinema</title>
</svelte:head>

<div data-bs-theme={$theme}>
    <HeroBanner 
        title="Nuestros Actores"
        subtitle={total > 0 ? `${total} actores y actrices` : 'Descubre nuestros actores'}
        imageUrl="/images/actors-hero.jpg"
        overlayOpacity="60"
    />

    <div class="container py-5">
        {#if loading && actors.length === 0}
            <div class="d-flex justify-content-center py-5" transition:fade>
                <div class="spinner-grow text-light" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
            </div>
        {:else if error}
            <div class="alert alert-danger" role="alert" transition:fade>
                {error}
            </div>
        {:else if actors.length === 0}
            <div class="text-center py-5" transition:fade>
                <div class="display-1 text-muted mb-4">
                    <i class="bi bi-people"></i>
                </div>
                <p class="lead text-muted">
                    No hay actores registrados
                </p>
            </div>
        {:else}
            <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-4" transition:fade>
                {#each actors as actor, i}
                    <div class="col" in:fly={{y: 20, delay: i * 50}}>
                        <a 
                            href="/actors/{actor.slug}" 
                            class="text-decoration-none"
                        >
                            <div class="card bg-dark actor-card {getPattern(actor.name)}">
                                <div class="card-body text-center py-4">
                                    <div class="actor-initials mb-3">
                                        {getInitials(actor.name)}
                                    </div>
                                    <h3 class="card-title h5 text-white mb-2">
                                        {actor.name}
                                    </h3>
                                </div>
                            </div>
                        </a>
                    </div>
                {/each}
            </div>

            {#if lastPage > 1}
                <nav class="mt-5" aria-label="Navegación de páginas">
                    <ul class="pagination justify-content-center">
                        <li class="page-item {currentPage === 1 ? 'disabled' : ''}">
                            <button 
                                class="page-link" 
                                on:click={() => handlePageChange(currentPage - 1)}
                            >
                                <i class="bi bi-chevron-left"></i>
                            </button>
                        </li>
                        
                        {#each Array(lastPage) as _, i}
                            <li class="page-item {currentPage === i + 1 ? 'active' : ''}">
                                <button 
                                    class="page-link"
                                    on:click={() => handlePageChange(i + 1)}
                                >
                                    {i + 1}
                                </button>
                            </li>
                        {/each}
                        
                        <li class="page-item {currentPage === lastPage ? 'disabled' : ''}">
                            <button 
                                class="page-link"
                                on:click={() => handlePageChange(currentPage + 1)}
                            >
                                <i class="bi bi-chevron-right"></i>
                            </button>
                        </li>
                    </ul>
                </nav>
            {/if}
        {/if}
    </div>
</div>

<style>
    .actor-card {
        transition: all 0.3s ease;
        border: none;
        height: 100%;
    }

    .actor-card:hover {
        transform: translateY(-5px);
    }

    .actor-initials {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: rgba(255,255,255,0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
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