<script lang="ts">
  import { onMount } from 'svelte';
  import { API_URL } from '$lib/config';

  let cinemas: Array<{ id: number; name: string; location: string }> = [];
  let loading = true;
  let error: string | null = null;

  async function fetchCinemas() {
    try {
      const response = await fetch(`${API_URL}/cinemas`, {
        headers: {
          'Accept': 'application/json'
        }
      });
      const data = await response.json();
      if (response.ok) {
        cinemas = data.data ?? data; // Soporta ambos formatos
      } else {
        error = data.message || 'Error al cargar los cines';
      }
    } catch (e) {
      error = 'Error de conexión con el servidor';
    } finally {
      loading = false;
    }
  }

  onMount(() => {
    fetchCinemas();
  });
</script>

<div class="container py-5">
  <h2 class="mb-4 section-title">Cines</h2>
  {#if loading}
    <div class="text-center my-5">
      <span class="spinner-border text-primary" role="status" aria-hidden="true"></span>
      <div class="mt-2">Cargando...</div>
    </div>
  {:else if error}
    <div class="alert alert-danger">{error}</div>
  {:else if cinemas.length === 0}
    <div class="alert alert-info">No hay cines disponibles.</div>
  {:else}
    <div class="row">
      {#each cinemas as cinema}
        <div class="col-md-6 col-lg-4 mb-4">
          <div class="card h-100 shadow-sm">
            <div class="card-body">
              <h5 class="card-title">{cinema.name}</h5>
              <p class="card-text text-muted">{cinema.location}</p>
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
  }
</style>

<div class="decorative-blob blob-1"></div>
<div class="decorative-blob blob-2"></div>