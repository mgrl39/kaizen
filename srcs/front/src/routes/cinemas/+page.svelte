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

<style>
  .section-title {
    font-weight: 700;
    letter-spacing: 0.02em;
    color: var(--bs-primary, #0d6efd);
    margin-bottom: 2rem;
    position: relative;
    display: inline-block;
  }
  
  .section-title::after {
    content: '';
    position: absolute;
    left: 0;
    bottom: -8px;
    width: 60%;
    height: 3px;
    background: linear-gradient(to right, #6d28d9, transparent);
    border-radius: 2px;
  }
  
  .filters-container {
    background-color: rgba(33, 37, 41, 0.7);
    border-radius: 8px;
    padding: 1.5rem;
    margin-bottom: 2rem;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
  }
  
  .cinema-card {
    background-color: #212529;
    border-radius: 12px;
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    height: 100%;
    display: flex;
    flex-direction: column;
  }
  
  .cinema-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.3);
  }
  
  .cinema-image {
    position: relative;
    height: 180px;
    overflow: hidden;
  }
  
  .cinema-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
  }
  
  .cinema-card:hover .cinema-image img {
    transform: scale(1.05);
  }
  
  .cinema-location-badge {
    position: absolute;
    top: 12px;
    right: 12px;
    background: rgba(109, 40, 217, 0.9);
    color: white;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 500;
  }
  
  .cinema-content {
    padding: 1.5rem;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
  }
  
  .cinema-title {
    font-weight: 700;
    margin-bottom: 1rem;
    color: #f8f9fa;
  }
  
  .cinema-details {
    margin-bottom: 1rem;
  }
  
  .detail-item {
    display: flex;
    align-items: center;
    margin-bottom: 0.5rem;
    color: #adb5bd;
  }
  
  .detail-item i {
    width: 24px;
    color: #6d28d9;
    font-size: 1rem;
  }
  
  .cinema-features {
    margin-bottom: 1.5rem;
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
  }
  
  .feature-badge {
    background-color: rgba(255, 255, 255, 0.1);
    color: #f8f9fa;
    padding: 4px 12px;
    border-radius: 16px;
    font-size: 0.8rem;
    display: inline-flex;
    align-items: center;
  }
  
  .feature-badge i {
    margin-right: 4px;
  }
  
  .cinema-actions {
    margin-top: auto;
    display: flex;
    gap: 8px;
  }
  
  @media (max-width: 768px) {
    .cinema-actions {
      flex-direction: column;
    }
    
    .cinema-actions a {
      width: 100%;
      margin-bottom: 0.5rem;
    }
  }
</style>

<div class="decorative-blob blob-1"></div>
<div class="decorative-blob blob-2"></div>