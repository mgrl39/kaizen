<script lang="ts">
    import { onMount } from 'svelte';
    import { API_URL } from '$lib/config';
    
    let movies = [];
    let rooms = [];
    let schedules = [];
    let loading = true;
    let selectedMovie = null;
    let selectedRoom = null;
    let selectedDate = new Date().toISOString().split('T')[0];
    let filteredSchedules = [];
    
    onMount(async () => {
        try {
            const [moviesRes, roomsRes, schedulesRes] = await Promise.all([
                fetch(`${API_URL}/movies`),
                fetch(`${API_URL}/rooms`),
                fetch(`${API_URL}/functions`)
            ]);
            
            const [moviesData, roomsData, schedulesData] = await Promise.all([
                moviesRes.json(),
                roomsRes.json(),
                schedulesRes.json()
            ]);
            
            movies = moviesData.data;
            rooms = roomsData.data;
            schedules = schedulesData.data;
            
            updateFilteredSchedules();
        } catch (error) {
            console.error('Error loading data:', error);
        } finally {
            loading = false;
        }
    });
    
    function updateFilteredSchedules() {
        filteredSchedules = schedules.filter(schedule => {
            const matchesMovie = !selectedMovie || schedule.movie_id === selectedMovie;
            const matchesRoom = !selectedRoom || schedule.room_id === selectedRoom;
            const matchesDate = !selectedDate || schedule.date.startsWith(selectedDate);
            return matchesMovie && matchesRoom && matchesDate;
        });
    }
    
    $: {
        if (schedules.length) {
            updateFilteredSchedules();
        }
    }
</script>

<div class="container py-4">
    <h1 class="mb-4">Horarios de Películas</h1>
    
    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Película</label>
                    <select class="form-select" bind:value={selectedMovie}>
                        <option value={null}>Todas las películas</option>
                        {#each movies as movie}
                            <option value={movie.id}>{movie.title}</option>
                        {/each}
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Sala</label>
                    <select class="form-select" bind:value={selectedRoom}>
                        <option value={null}>Todas las salas</option>
                        {#each rooms as room}
                            <option value={room.id}>{room.name}</option>
                        {/each}
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Fecha</label>
                    <input type="date" class="form-control" bind:value={selectedDate}>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Resultados -->
    {#if loading}
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
        </div>
    {:else if filteredSchedules.length === 0}
        <div class="alert alert-info">
            No se encontraron funciones con los filtros seleccionados
        </div>
    {:else}
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            {#each filteredSchedules as schedule}
                <div class="col">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">{schedule.movie.title}</h5>
                            <p class="card-text">
                                <i class="bi bi-building me-2"></i>Sala: {schedule.room.name}<br>
                                <i class="bi bi-calendar me-2"></i>Fecha: {new Date(schedule.date).toLocaleDateString('es-ES')}<br>
                                <i class="bi bi-clock me-2"></i>Hora: {new Date(schedule.time).toLocaleTimeString('es-ES', {hour: '2-digit', minute: '2-digit'})}
                            </p>
                            <a href="/booking/{schedule.id}" class="btn btn-primary w-100">
                                <i class="bi bi-ticket-perforated me-2"></i>
                                Reservar
                            </a>
                        </div>
                    </div>
                </div>
            {/each}
        </div>
    {/if}
</div> 