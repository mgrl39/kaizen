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
    let perPage = 30; // Aumentamos para llenar mejor la pantalla

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

    // Función para obtener un patrón aleatorio pero consistente para cada actor
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

    // Función para obtener altura aleatoria pero consistente
    function getHeight(name) {
        const heights = ['h-1', 'h-2'];
        return heights[name.length % 2];
    }
</script>

<svelte:head>
    <title>Actores | Kaizen Cinema</title>
</svelte:head>

<div data-bs-theme={$theme}>
    <HeroBanner 
        title="Nuestros Actores"
        subtitle={total > 0 ? `Una colección de ${total} talentos extraordinarios` : 'Descubre nuestro elenco'}
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
                    <i class="bi bi-person-x"></i>
                </div>
                <p class="lead text-muted">
                    No hay actores registrados
                </p>
            </div>
        {:else}
            <div class="masonry-grid" transition:fade>
                {#each actors as actor, i}
                    <div 
                        class="masonry-item {getHeight(actor.name)}"
                        in:fly={{y: 20, delay: i * 50}}
                    >
                        <div class="actor-tile {getPattern(actor.name)}">
                            <div class="content">
                                <div class="initials">
                                    {getInitials(actor.name)}
                                </div>
                                <h3 class="name">
                                    {actor.name}
                                </h3>
                            </div>
                        </div>
                    </div>
                {/each}
            </div>

            {#if lastPage > 1}
                <div class="pagination-container mt-5">
                    <div class="pagination-wrapper">
                        <button 
                            class="nav-button {currentPage === 1 ? 'disabled' : ''}"
                            on:click={() => handlePageChange(currentPage - 1)}
                            disabled={currentPage === 1}
                        >
                            <i class="bi bi-chevron-left"></i>
                        </button>
                        
                        <div class="page-numbers">
                            <span class="current-page">{currentPage}</span>
                            <span class="separator">/</span>
                            <span class="total-pages">{lastPage}</span>
                        </div>

                        <button 
                            class="nav-button {currentPage === lastPage ? 'disabled' : ''}"
                            on:click={() => handlePageChange(currentPage + 1)}
                            disabled={currentPage === lastPage}
                        >
                            <i class="bi bi-chevron-right"></i>
                        </button>
                    </div>
                </div>
            {/if}
        {/if}
    </div>
</div>

<style>
    .cinema-background {
        background-image: url('https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80');
        background-size: cover;
        background-position: center;
        position: relative;
        padding: 6rem 0;
        margin-bottom: 3rem;
    }

    .overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(to bottom, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0.9) 100%);
        display: flex;
        align-items: center;
    }

    .text-shadow {
        text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
    }

    .text-shadow-lg {
        text-shadow: 3px 3px 6px rgba(0,0,0,0.5);
    }

    .masonry-grid {
        columns: 6 200px;
        column-gap: 1.5rem;
    }

    .masonry-item {
        break-inside: avoid;
        margin-bottom: 1.5rem;
    }

    .h-1 { height: 200px; }
    .h-2 { height: 250px; }

    .actor-tile {
        height: 100%;
        border-radius: 1rem;
        overflow: hidden;
        position: relative;
        transition: all 0.3s ease;
    }

    .actor-tile:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.2);
    }

    .content {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        padding: 2rem 1rem;
        background: linear-gradient(to top, rgba(0,0,0,0.8) 0%, rgba(0,0,0,0) 100%);
        color: white;
        text-align: center;
    }

    .initials {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
    }

    .name {
        font-size: 1rem;
        margin: 0;
        font-weight: 500;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.2);
    }

    .pattern-1 {
        background: linear-gradient(45deg, #FF3CAC 0%, #784BA0 50%, #2B86C5 100%);
    }

    .pattern-2 {
        background: linear-gradient(45deg, #FA8BFF 0%, #2BD2FF 50%, #2BFF88 100%);
    }

    .pattern-3 {
        background: linear-gradient(45deg, #FFF720 0%, #3CD500 100%);
    }

    .pattern-4 {
        background: linear-gradient(45deg, #FF3D6F 0%, #FFB49A 100%);
    }

    .pattern-5 {
        background: linear-gradient(45deg, #40C9FF 0%, #E81CFF 100%);
    }

    .pattern-6 {
        background: linear-gradient(45deg, #FF9A9E 0%, #FAD0C4 100%);
    }

    .pagination-container {
        display: flex;
        justify-content: center;
    }

    .pagination-wrapper {
        background: rgba(255,255,255,0.1);
        backdrop-filter: blur(10px);
        padding: 0.5rem;
        border-radius: 2rem;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .nav-button {
        background: none;
        border: none;
        color: white;
        font-size: 1.2rem;
        padding: 0.5rem 1rem;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .nav-button:hover:not(.disabled) {
        color: #40C9FF;
    }

    .nav-button.disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .page-numbers {
        color: white;
        font-size: 1.1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .current-page {
        font-weight: bold;
        color: #40C9FF;
    }

    .separator {
        opacity: 0.5;
    }

    :global(body) {
        background: #121212;
    }
</style> 