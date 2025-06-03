<script>
    import { onMount } from 'svelte';
    import { API_URL } from '$lib/config';
    import { fade } from 'svelte/transition';

    let actors = [];
    let loading = true;
    let error = null;
    let currentPage = 1;
    let lastPage = 1;
    let total = 0;
    let perPage = 50;
    let searchTerm = '';
    let searchTimeout;

    async function fetchActors(page = 1, search = '') {
        loading = true;
        try {
            const response = await fetch(`${API_URL}/actors?page=${page}&per_page=${perPage}&search=${encodeURIComponent(search)}`);
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
            fetchActors(newPage, searchTerm);
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    }

    function handleSearch() {
        if (searchTimeout) clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            fetchActors(1, searchTerm);
        }, 300);
    }

    $: {
        handleSearch();
    }

    onMount(() => {
        fetchActors();
    });

    // Función para obtener iniciales del nombre
    function getInitials(name) {
        return name
            .split(' ')
            .map(word => word[0])
            .join('')
            .toUpperCase();
    }

    // Función para obtener color basado en el nombre
    function getColorClass(name) {
        const colors = ['bg-primary', 'bg-success', 'bg-info', 'bg-warning', 'bg-danger', 'bg-secondary'];
        const index = name.length % colors.length;
        return colors[index];
    }
</script>

<svelte:head>
    <title>Actores | Kaizen Cinema</title>
</svelte:head>

<div class="container py-4">
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h1 class="display-5 fw-bold mb-0">
                Actores 
                {#if total > 0}
                    <small class="text-muted fs-6">({total})</small>
                {/if}
            </h1>
        </div>
        <div class="col-md-6">
            <div class="input-group input-group-lg">
                <span class="input-group-text border-0 bg-light">
                    <i class="bi bi-search"></i>
                </span>
                <input 
                    type="text" 
                    class="form-control form-control-lg border-0 bg-light"
                    placeholder="Buscar actor..." 
                    bind:value={searchTerm}
                >
            </div>
        </div>
    </div>

    {#if loading && actors.length === 0}
        <div class="d-flex justify-content-center py-5" transition:fade>
            <div class="spinner-border text-primary" role="status">
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
                {searchTerm ? 'No se encontraron actores que coincidan con la búsqueda' : 'No hay actores registrados'}
            </p>
        </div>
    {:else}
        <div class="row g-4" transition:fade>
            {#each actors as actor}
                <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                    <div class="card h-100 border-0 shadow-sm hover-card">
                        <div class="card-body p-3 text-center">
                            <div class="avatar-circle mb-3 mx-auto {getColorClass(actor.name)}">
                                {getInitials(actor.name)}
                            </div>
                            <h6 class="card-title mb-0 text-truncate">
                                {actor.name}
                            </h6>
                        </div>
                    </div>
                </div>
            {/each}
        </div>

        {#if lastPage > 1}
            <div class="row mt-4">
                <div class="col d-flex justify-content-center">
                    <nav aria-label="Navegación de páginas">
                        <ul class="pagination pagination-lg">
                            <li class="page-item {currentPage === 1 ? 'disabled' : ''}">
                                <button 
                                    class="page-link border-0" 
                                    on:click={() => handlePageChange(currentPage - 1)}
                                    disabled={currentPage === 1}
                                >
                                    <i class="bi bi-chevron-left"></i>
                                </button>
                            </li>
                            
                            {#each Array.from({length: lastPage}, (_, i) => i + 1) as page}
                                {#if page === 1 || page === lastPage || (page >= currentPage - 1 && page <= currentPage + 1)}
                                    <li class="page-item {currentPage === page ? 'active' : ''}">
                                        <button 
                                            class="page-link border-0" 
                                            on:click={() => handlePageChange(page)}
                                        >
                                            {page}
                                        </button>
                                    </li>
                                {:else if page === currentPage - 2 || page === currentPage + 2}
                                    <li class="page-item disabled">
                                        <span class="page-link border-0">...</span>
                                    </li>
                                {/if}
                            {/each}
                            
                            <li class="page-item {currentPage === lastPage ? 'disabled' : ''}">
                                <button 
                                    class="page-link border-0" 
                                    on:click={() => handlePageChange(currentPage + 1)}
                                    disabled={currentPage === lastPage}
                                >
                                    <i class="bi bi-chevron-right"></i>
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
    .avatar-circle {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        font-weight: bold;
    }

    .hover-card {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }

    .hover-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15) !important;
    }

    :global(.page-link) {
        cursor: pointer;
        border-radius: 50% !important;
        margin: 0 2px;
        width: 45px;
        height: 45px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    :global(.pagination .active .page-link) {
        background-color: var(--bs-primary);
        color: white;
    }

    .input-group-text, .form-control {
        box-shadow: 0 2px 5px rgba(0,0,0,.05);
    }

    .form-control:focus {
        background-color: white !important;
        box-shadow: 0 2px 10px rgba(0,0,0,.1);
    }
</style> 