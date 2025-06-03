<script>
    import { onMount } from 'svelte';
    import { page } from '$app/stores';
    import { PUBLIC_API_URL } from '$env/static/public';

    let actor = null;
    let loading = true;
    let error = null;

    $: slug = $page.params.slug;

    onMount(async () => {
        try {
            const response = await fetch(`${PUBLIC_API_URL}/api/v1/actors/${slug}`);
            const data = await response.json();
            if (data.success) {
                actor = data.data;
            } else {
                error = data.message;
            }
        } catch (e) {
            error = 'Error al cargar el actor';
        } finally {
            loading = false;
        }
    });
</script>

<svelte:head>
    <title>{actor ? `${actor.name} | Kaizen Cinema` : 'Actor | Kaizen Cinema'}</title>
</svelte:head>

<div class="container mx-auto px-4 py-8">
    {#if loading}
        <div class="flex justify-center items-center h-64">
            <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-primary"></div>
        </div>
    {:else if error}
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            {error}
        </div>
    {:else if actor}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Información del actor -->
            <div class="md:col-span-1">
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    {#if actor.photo_url}
                        <img src={actor.photo_url} alt={actor.name} class="w-full h-auto">
                    {:else}
                        <div class="w-full h-96 bg-gray-200 flex items-center justify-center">
                            <svg class="w-32 h-32 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                    {/if}
                    <div class="p-6">
                        <h1 class="text-3xl font-bold text-gray-800 mb-4">{actor.name}</h1>
                        {#if actor.biography}
                            <p class="text-gray-600">{actor.biography}</p>
                        {:else}
                            <p class="text-gray-500 italic">No hay biografía disponible</p>
                        {/if}
                    </div>
                </div>
            </div>

            <!-- Películas del actor -->
            <div class="md:col-span-2">
                <h2 class="text-2xl font-bold mb-6">Películas</h2>
                {#if actor.movies.length === 0}
                    <div class="bg-white rounded-lg shadow-md p-6 text-center">
                        <p class="text-gray-600">Este actor no tiene películas registradas</p>
                    </div>
                {:else}
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        {#each actor.movies as movie}
                            <a href="/movies/{movie.slug}" class="block">
                                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300">
                                    {#if movie.photo_url}
                                        <img src={movie.photo_url} alt={movie.title} class="w-full h-48 object-cover">
                                    {:else}
                                        <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                            <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z" />
                                            </svg>
                                        </div>
                                    {/if}
                                    <div class="p-4">
                                        <h3 class="text-lg font-semibold text-gray-800">{movie.title}</h3>
                                    </div>
                                </div>
                            </a>
                        {/each}
                    </div>
                {/if}
            </div>
        </div>
    {/if}
</div> 