<script>
    import { onMount } from 'svelte';
    import { API_URL } from '$lib/config';

    let actors = [];
    let loading = true;
    let error = null;
    let currentPage = 1;
    let lastPage = 1;
    let total = 0;
    let perPage = 24;

    function handleImageError(event) {
        event.target.src = '/images/default-actor.png';
    }

    async function fetchActors(page = 1) {
        loading = true;
        try {
            const response = await fetch(`${API_URL}/actors?page=${page}&per_page=${perPage}`);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
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
</script>

<svelte:head>
    <title>Actores | Kaizen Cinema</title>
</svelte:head>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col">
            <h1 class="display-4 fw-bold">
                Actores 
                {#if total > 0}
                    <small class="fs-6 text-muted">({total} {total === 1 ? 'actor' : 'actores'})</small>
                {/if}
            </h1>
        </div>
    </div>

    {#if loading && actors.length === 0}
        <div class="row justify-content-center align-items-center" style="height: 300px;">
            <div class="col-auto">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
            </div>
        </div>
    {:else if error}
        <div class="row">
            <div class="col">
                <div class="alert alert-danger" role="alert">
                    {error}
                </div>
            </div>
        </div>
    {:else if actors.length === 0}
        <div class="row justify-content-center py-5">
            <div class="col-auto">
                <p class="fs-4 text-muted">No hay actores registrados</p>
            </div>
        </div>
    {:else}
        <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-6 row-cols-xl-8 g-3">
            {#each actors as actor}
                <div class="col">
                    <div class="card h-100 shadow-sm hover-shadow">
                        <div class="card-img-top position-relative" style="padding-bottom: 150%;">
                            {#if actor.photo_url}
                                <img 
                                    src={actor.photo_url} 
                                    alt={actor.name} 
                                    class="position-absolute top-0 start-0 w-100 h-100 object-fit-cover"
                                    loading="lazy"
                                    on:error={handleImageError}
                                />
                            {:else}
                                <div class="position-absolute top-0 start-0 w-100 h-100 bg-light d-flex align-items-center justify-content-center">
                                    <svg class="text-secondary" style="width: 2.5rem; height: 2.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                            {/if}
                        </div>
                        <div class="card-body p-2">
                            <h6 class="card-title mb-1 text-truncate">{actor.name}</h6>
                            {#if actor.movies_count !== undefined}
                                <p class="card-text mb-0">
                                    <small class="badge bg-primary">
                                        {actor.movies_count} película{actor.movies_count === 1 ? '' : 's'}
                                    </small>
                                </p>
                            {/if}
                        </div>
                        <a href="/actors/{actor.slug}" class="stretched-link"></a>
                    </div>
                </div>
            {/each}
        </div>

        <!-- Paginación -->
        {#if lastPage > 1}
            <div class="row mt-4">
                <div class="col d-flex justify-content-center">
                    <nav aria-label="Navegación de páginas">
                        <ul class="pagination">
                            <!-- Botón Anterior -->
                            <li class="page-item {currentPage === 1 ? 'disabled' : ''}">
                                <button 
                                    class="page-link" 
                                    on:click={() => handlePageChange(currentPage - 1)}
                                    disabled={currentPage === 1}
                                >
                                    Anterior
                                </button>
                            </li>
                            
                            <!-- Primera página -->
                            {#if currentPage > 2}
                                <li class="page-item">
                                    <button class="page-link" on:click={() => handlePageChange(1)}>1</button>
                                </li>
                                {#if currentPage > 3}
                                    <li class="page-item disabled">
                                        <span class="page-link">...</span>
                                    </li>
                                {/if}
                            {/if}
                            
                            <!-- Páginas alrededor de la actual -->
                            {#each Array.from({length: Math.min(3, lastPage)}, (_, i) => {
                                const page = Math.max(1, Math.min(currentPage - 1 + i, lastPage));
                                return page;
                            }) as page}
                                <li class="page-item {currentPage === page ? 'active' : ''}">
                                    <button 
                                        class="page-link" 
                                        on:click={() => handlePageChange(page)}
                                    >
                                        {page}
                                    </button>
                                </li>
                            {/each}
                            
                            <!-- Última página -->
                            {#if currentPage < lastPage - 1}
                                {#if currentPage < lastPage - 2}
                                    <li class="page-item disabled">
                                        <span class="page-link">...</span>
                                    </li>
                                {/if}
                                <li class="page-item">
                                    <button 
                                        class="page-link" 
                                        on:click={() => handlePageChange(lastPage)}
                                    >
                                        {lastPage}
                                    </button>
                                </li>
                            {/if}
                            
                            <!-- Botón Siguiente -->
                            <li class="page-item {currentPage === lastPage ? 'disabled' : ''}">
                                <button 
                                    class="page-link" 
                                    on:click={() => handlePageChange(currentPage + 1)}
                                    disabled={currentPage === lastPage}
                                >
                                    Siguiente
                                </button>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        {/if}
    {/if}
</div>

<style>
    .text-truncate-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .hover-shadow {
        transition: box-shadow 0.3s ease-in-out;
    }

    .hover-shadow:hover {
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15) !important;
    }

    .object-fit-cover {
        object-fit: cover;
    }

    :global(.page-link) {
        cursor: pointer;
    }

    .card {
        border: none;
    }

    .card-body {
        background-color: rgba(255, 255, 255, 0.9);
    }
</style> 