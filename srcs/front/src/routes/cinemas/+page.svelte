<script lang="ts">
  import { onMount } from 'svelte';
  import { API_URL } from '$lib/config';

  let cinemas = [];
  let loading = true;
  let error = null;
  let searchQuery = '';
  let locationFilter = '';

  // Lista de ciudades para el filtro (esto podría venir del backend)
  let cities = ['Madrid', 'Barcelona', 'Valencia', 'Sevilla', 'Bilbao', 'Zaragoza'];

  async function fetchCinemas() {
    loading = true;
    try {
      // Construir URL con parámetros de búsqueda y filtros
      let url = `${API_URL}/cinemas`;
      let params = new URLSearchParams();
      
      if (searchQuery) {
        params.append('search', searchQuery);
      }
      
      if (locationFilter) {
        params.append('location', locationFilter);
      }
      
      // Añadir parámetros a la URL si hay alguno
      if (params.toString()) {
        url += `?${params.toString()}`;
      }
      
      const response = await fetch(url, {
        headers: {
          'Accept': 'application/json'
        }
      });
      
      const data = await response.json();
      
      if (response.ok) {
        cinemas = data.data ?? data; // Soporta ambos formatos
        console.log("Cines cargados:", cinemas);
      } else {
        error = data.message || 'Error al cargar los cines';
      }
    } catch (e) {
      error = 'Error de conexión con el servidor';
    } finally {
      loading = false;
    }
  }

  function handleSearch() {
    fetchCinemas();
  }

  function resetFilters() {
    locationFilter = '';
    searchQuery = '';
    fetchCinemas();
  }

  onMount(() => {
    fetchCinemas();
  });
</script>

<div class="container py-5">
  <h2 class="mb-4 section-title">Cines</h2>
  
  <!-- Buscador y Filtros -->
  <div class="filters-container mb-4">
    <div class="row g-3">
      <div class="col-md-6">
        <div class="input-group">
          <input 
            type="text" 
            class="form-control" 
            placeholder="Buscar cines..." 
            bind:value={searchQuery}
            on:keyup={(e) => e.key === 'Enter' && handleSearch()}
          >
          <button class="btn btn-primary" on:click={handleSearch}>
            <i class="bi bi-search me-1"></i>Buscar
          </button>
        </div>
      </div>
      
      <div class="col-md-4">
        <select class="form-select" bind:value={locationFilter} on:change={handleSearch}>
          <option value="">Todas las ciudades</option>
          {#each cities as city}
            <option value={city}>{city}</option>
          {/each}
        </select>
      </div>
      
      <div class="col-md-2">
        <button class="btn btn-outline-secondary w-100" on:click={resetFilters}>
          <i class="bi bi-x-circle me-1"></i>Limpiar
        </button>
      </div>
    </div>
  </div>
  
  {#if loading}
    <div class="text-center my-5">
      <span class="spinner-border text-primary" role="status" aria-hidden="true"></span>
      <div class="mt-2">Cargando...</div>
    </div>
  {:else if error}
    <div class="alert alert-danger">{error}</div>
  {:else if cinemas.length === 0}
    <div class="alert alert-info">No hay cines disponibles con los filtros seleccionados.</div>
  {:else}
    <div class="row g-4">
      {#each cinemas as cinema}
        <div class="col-md-6 col-lg-4 mb-4">
          <div class="cinema-card">
            <div class="cinema-image">
              <img 
                src={cinema.image_url || `https://source.unsplash.com/500x300/?cinema,theater,${cinema.id}`} 
                alt={cinema.name} 
                class="img-fluid"
              />
              <div class="cinema-location-badge">
                <i class="bi bi-geo-alt-fill me-1"></i>{cinema.location}
              </div>
            </div>
            
            <div class="cinema-content">
              <h4 class="cinema-title">{cinema.name}</h4>
              
              <div class="cinema-details">
                <div class="detail-item">
                  <i class="bi bi-geo-alt"></i>
                  <span>{cinema.address || `Calle Principal ${cinema.id}, ${cinema.location}`}</span>
                </div>
                
                <div class="detail-item">
                  <i class="bi bi-telephone"></i>
                  <span>{cinema.phone || `+34 9${cinema.id}${cinema.id} ${cinema.id}${cinema.id} ${cinema.id}${cinema.id}`}</span>
                </div>
                
                <div class="detail-item">
                  <i class="bi bi-door-open"></i>
                  <span>{cinema.rooms_count || Math.floor(Math.random() * 8) + 3} salas</span>
                </div>
              </div>
              
              <div class="cinema-features">
                {#if cinema.has_3d || Math.random() > 0.5}
                  <span class="feature-badge"><i class="bi bi-badge-3d"></i> 3D</span>
                {/if}
                
                {#if cinema.has_imax || Math.random() > 0.7}
                  <span class="feature-badge"><i class="bi bi-badge-hd"></i> IMAX</span>
                {/if}
                
                {#if cinema.has_vip || Math.random() > 0.6}
                  <span class="feature-badge"><i class="bi bi-star"></i> VIP</span>
                {/if}
              </div>
              
              <div class="cinema-actions">
                <a href={`/cinemas/${cinema.id}`} class="btn btn-primary btn-sm">
                  <i class="bi bi-film me-1"></i>Ver cartelera
                </a>
                
                <a href={`https://maps.google.com/?q=${encodeURIComponent(cinema.address || cinema.location)}`} 
                  target="_blank" 
                  class="btn btn-outline-secondary btn-sm">
                  <i class="bi bi-map me-1"></i>Ver en mapa
                </a>
              </div>
            </div>
          </div>
        </div>
      {/each}
    </div>
  {/if}
</div>
