<script lang="ts">
  import { onMount } from 'svelte';
  import type { Cinema } from '$lib/types';
  
  // Estado para cines
  let cinemas: Cinema[] = [];
  let loading: boolean = true;
  let error: string | null = null;
  
  onMount(async () => {
    try {
      // Fetch data from the API endpoint
      const response = await fetch('http://localhost:8000/api/v1/cinemas');
      
      if (!response.ok) {
        throw new Error(`API respondió con estado: ${response.status}`);
      }
      
      const data = await response.json();
      
      // Check if the response has the expected structure
      if (data && Array.isArray(data)) {
        cinemas = data;
      } else if (data && data.data && Array.isArray(data.data)) {
        // Handle case where data is wrapped in a 'data' property
        cinemas = data.data;
      } else {
        throw new Error('Formato de respuesta API inesperado');
      }
      
      loading = false;
    } catch (err) {
      console.error('Error cargando cines:', err);
      error = "No se pudieron cargar los cines: " + (err instanceof Error ? err.message : String(err));
      loading = false;
    }
  });
</script>

<div class="container mt-4">
  <div class="row">
    <div class="col-12">
      <h1 class="section-title mb-4">Nuestros Cines</h1>
    </div>
  </div>
  
  {#if loading}
    <div class="d-flex justify-content-center my-5">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Cargando...</span>
      </div>
    </div>
  {:else if error}
    <div class="col-12 text-center">
      <div class="alert alert-danger">
        {error}
      </div>
    </div>
  {:else if cinemas.length === 0}
    <div class="col-12 text-center">
      <p>No hay cines disponibles</p>
    </div>
  {:else}
    <div class="row g-4">
      {#each cinemas as cinema}
        <div class="col-md-6 col-lg-4">
          <div class="card cinema-card h-100">
            <div class="cinema-img-container">
              <img src={cinema.photo_url || '/img/default-cinema.jpg'} 
                  class="card-img-top" alt={cinema.name}>
              <div class="cinema-overlay">
                <span class="location-badge">
                  <i class="bi bi-geo-alt-fill me-1"></i>{cinema.city || 'Ciudad no disponible'}
                </span>
              </div>
            </div>
            <div class="card-body">
              <h5 class="card-title">{cinema.name}</h5>
              <p class="card-text text-muted small mb-3">
                <i class="bi bi-map me-1"></i>{cinema.address || 'Dirección no disponible'}
              </p>
              <p class="card-text">{cinema.description || 'Sin descripción disponible'}</p>
              <div class="d-flex justify-content-between align-items-center mt-3">
                <span class="facilities">
                  {#if cinema.has_parking}
                    <i class="bi bi-p-square-fill me-2" title="Parking disponible"></i>
                  {/if}
                  {#if cinema.has_food}
                    <i class="bi bi-cup-hot-fill me-2" title="Servicio de comida"></i>
                  {/if}
                  {#if cinema.is_premium}
                    <i class="bi bi-star-fill" title="Cine premium"></i>
                  {/if}
                </span>
                <a href={`/cinemas/${cinema.id}`} class="btn btn-primary btn-sm">
                  <i class="bi bi-info-circle me-1"></i>Ver detalles
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
  .cinema-card {
    transition: var(--transition-base);
    overflow: hidden;
  }
  
  .cinema-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.15);
  }
  
  .cinema-img-container {
    position: relative;
    overflow: hidden;
  }
  
  .card-img-top {
    height: 200px;
    object-fit: cover;
    transition: var(--transition-base);
  }
  
  .cinema-card:hover .card-img-top {
    transform: scale(1.05);
  }
  
  .cinema-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    padding: 15px;
    background: linear-gradient(to top, rgba(0,0,0,0.7), transparent);
  }
  
  .location-badge {
    display: inline-block;
    background-color: var(--primary-color);
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 2rem;
    font-size: 0.8rem;
  }
  
  .facilities {
    color: var(--text-muted);
    font-size: 1.2rem;
  }
  
  .facilities i {
    transition: var(--transition-base);
  }
  
  .facilities i:hover {
    color: var(--primary-color);
  }
</style> 

<div class="decorative-blob blob-1"></div>
<div class="decorative-blob blob-2"></div> 