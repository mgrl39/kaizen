<script lang="ts">
  import { onMount } from 'svelte';
  import type { Cinema } from '$lib/types';
  
  let cinemas: Cinema[] = [];
  let loading: boolean = true;
  let error: string | null = null;
  
  onMount(async () => {
    try {
      const response = await fetch('http://localhost:8000/api/v1/cinemas');
      
      if (!response.ok) {
        throw new Error(`API respondió con estado: ${response.status}`);
      }
      
      const data = await response.json();
      
      if (data && Array.isArray(data)) {
        cinemas = data;
      } else if (data && data.data && Array.isArray(data.data)) {
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
      <h1 class="mb-4">Nuestros Cines</h1>
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
          <div class="card h-100">
            <div class="position-relative">
              <img src={cinema.photo_url || '/img/default-cinema.jpg'} 
                  class="card-img-top" alt={cinema.name}>
              <div class="position-absolute bottom-0 start-0 p-2">
                <span class="badge bg-dark">
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
                <div>
                  {#if cinema.has_parking}
                    <i class="bi bi-p-square-fill me-2" title="Parking disponible"></i>
                  {/if}
                  {#if cinema.has_food}
                    <i class="bi bi-cup-hot-fill me-2" title="Servicio de comida"></i>
                  {/if}
                  {#if cinema.is_premium}
                    <i class="bi bi-star-fill" title="Cine premium"></i>
                  {/if}
                </div>
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

<div class="decorative-blob blob-1"></div>
<div class="decorative-blob blob-2"></div>