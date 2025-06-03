<script>
    import { onMount } from 'svelte';
    import { PUBLIC_API_URL } from '$env/static/public';

    let actors = [];
    let loading = true;
    let error = null;

    onMount(async () => {
        try {
            const response = await fetch(`${PUBLIC_API_URL}/api/v1/actors`);
            const data = await response.json();
            if (data.success) {
                actors = data.data;
            } else {
                error = data.message;
            }
        } catch (e) {
            error = 'Error al cargar los actores';
        } finally {
            loading = false;
        }
    });
</script>

<svelte:head>
    <title>Actores | Kaizen Cinema</title>
</svelte:head>

<div class="container mx-auto px-4 py-8">
    <h1 class="text-4xl font-bold mb-8">Actores</h1>

    {#if loading}
        <div class="flex justify-center items-center h-64">
            <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-primary"></div>
        </div>
    {:else if error}
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            {error}
        </div>
    {:else if actors.length === 0}
        <div class="text-center py-12">
            <p class="text-xl text-gray-600">No hay actores registrados</p>
        </div>
    {:else}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            {#each actors as actor}
                <a href="/actors/{actor.slug}" class="block">
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300">
                        <div class="aspect-w-2 aspect-h-3 relative">
                            {#if actor.photo_url}
                                <img src={actor.photo_url} alt={actor.name} class="object-cover w-full h-full">
                            {:else}
                                <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                    <svg class="w-20 h-20 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                            {/if}
                        </div>
                        <div class="p-4">
                            <h2 class="text-xl font-semibold text-gray-800 mb-2">{actor.name}</h2>
                            {#if actor.biography}
                                <p class="text-sm text-gray-600 line-clamp-2">{actor.biography}</p>
                            {/if}
                        </div>
                    </div>
                </a>
            {/each}
        </div>
    {/if}
</div>

<style>
    .aspect-w-2 {
        position: relative;
        padding-bottom: 150%;
    }
    .aspect-w-2 > * {
        position: absolute;
        height: 100%;
        width: 100%;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
    }
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style> 