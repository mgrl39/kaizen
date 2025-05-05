<script lang="ts">
  import { onMount } from 'svelte';
  import { page } from '$app/stores';
  import { goto } from '$app/navigation';
  import { API_URL } from '$lib/config';
  
  // ID del cine a editar
  const cinemaId = $page.params.id;
  const isEditing = cinemaId !== 'new';
  
  // Estado del formulario
  let loading = true;
  let submitting = false;
  let success = false;
  let error = null;
  
  // Datos del cine
  let formData = {
    name: '',
    location: '',
    address: '',
    phone: '',
    rooms_count: 1,
    status: 'active',
    has_3d: false,
    has_imax: false,
    has_vip: false,
    image_url: ''
  };
  
  // Lista de ciudades disponibles
  const cities = ['Madrid', 'Barcelona', 'Valencia', 'Sevilla', 'Bilbao', 'Zaragoza', 
                  'Málaga', 'Murcia', 'Alicante', 'Palma de Mallorca', 'Granada', 'Las Palmas',
                  'Oviedo', 'Santander', 'Pamplona', 'Toledo', 'Salamanca', 'León'];
  
  async function fetchCinema() {
    if (!isEditing) {
      loading = false;
      return;
    }
    
    loading = true;
    const token = localStorage.getItem('token');
    
    if (!token) {
      goto('/login');
      return;
    }
    
    try {
      // En un entorno real, esto sería una llamada a la API
      // const response = await fetch(`${API_URL}/admin/cinemas/${cinemaId}`, {
      //   headers: {
      //     'Authorization': `Bearer ${token}`,
      //     'Accept': 'application/json'
      //   }
      // });
      // const data = await response.json();
      
      // Simulamos datos para desarrollo
      setTimeout(() => {
        // Datos de ejemplo según el ID
        const mockCinemas = {
          1: {
            id: 1,
            name: 'Cines Kaizen Madrid',
            location: 'Madrid',
            address: 'Calle Gran Vía 123, Madrid',
            phone: '+34 914 123 456',
            rooms_count: 8,
            status: 'active',
            has_3d: true,
            has_imax: true,
            has_vip: true,
            image_url: 'https://source.unsplash.com/800x400/?cinema,madrid'
          },
          2: {
            id: 2,
            name: 'Cines Kaizen Barcelona',
            location: 'Barcelona',
            address: 'Avinguda Diagonal 456, Barcelona',
            phone: '+34 932 456 789',
            rooms_count: 6,
            status: 'active',
            has_3d: true,
            has_imax: true,
            has_vip: false,
            image_url: 'https://source.unsplash.com/800x400/?cinema,barcelona'
          }
        };
        
        const cinema = mockCinemas[cinemaId] || null;
        
        if (cinema) {
          formData = { ...cinema };
        } else {
          error = 'No se encontró el cine solicitado';
        }
        
        loading = false;
      }, 500);
      
    } catch (e) {
      error = 'Error al cargar la información del cine';
      loading = false;
    }
  }
  
  async function handleSubmit() {
    submitting = true;
    error = null;
    success = false;
    
    const token = localStorage.getItem('token');
    
    if (!token) {
      goto('/login');
      return;
    }
    
    try {
      // En un entorno real, esto sería una llamada a la API
      // const url = isEditing 
      //   ? `${API_URL}/admin/cinemas/${cinemaId}` 
      //   : `${API_URL}/admin/cinemas`;
      
      // const method = isEditing ? 'PUT' : 'POST';
      
      // const response = await fetch(url, {
      //   method,
      //   headers: {
      //     'Authorization': `Bearer ${token}`,
      //     'Content-Type': 'application/json',
      //     'Accept': 'application/json'
      //   },
      //   body: JSON.stringify(formData)
      // });
      
      // const data = await response.json();
      
      // Simulamos respuesta
      setTimeout(() => {
        success = true;
        submitting = false;
        
        // Redirigir después de 2 segundos
        setTimeout(() => {
          goto('/admin/cinemas');
        }, 2000);
      }, 1000);
      
    } catch (e) {
      error = 'Error al guardar los datos del cine';
      submitting = false;
    }
  }
  
  onMount(() => {
    fetchCinema();
  });
</script>

<div class="cinema-form-container">
  <div class="page-header d-flex justify-content-between align-items-center mb-4">
    <h1>{isEditing ? 'Editar Cine' : 'Nuevo Cine'}</h1>
    <a href="/admin/cinemas" class="btn btn-outline-secondary">
      <i class="bi bi-arrow-left me-2"></i>Volver
    </a>
  </div>
  
  {#if loading}
    <div class="text-center my-5">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Cargando...</span>
      </div>
    </div>
  {:else if error}
    <div class="alert alert-danger">{error}</div>
  {:else}
    <div class="card">
      <div class="card-body">
        {#if success}
          <div class="alert alert-success">
            <i class="bi bi-check-circle me-2"></i>
            {isEditing ? 'Cine actualizado correctamente' : 'Cine creado correctamente'}
          </div>
        {/if}
        
        <form on:submit|preventDefault={handleSubmit}>
          <div class="row g-4">
            <!-- Columna izquierda -->
            <div class="col-md-6">
              <div class="mb-3">
                <label for="name" class="form-label">Nombre del cine <span class="text-danger">*</span></label>
                <input 
                  type="text" 
                  id="name" 
                  class="form-control" 
                  bind:value={formData.name} 
                  required
                  disabled={submitting}
                >
              </div>
              
              <div class="mb-3">
                <label for="location" class="form-label">Ciudad <span class="text-danger">*</span></label>
                <select 
                  id="location" 
                  class="form-select" 
                  bind:value={formData.location} 
                  required
                  disabled={submitting}
                >
                  <option value="">Seleccionar ciudad</option>
                  {#each cities as city}
                    <option value={city}>{city}</option>
                  {/each}
                </select>
              </div>
              
              <div class="mb-3">
                <label for="address" class="form-label">Dirección <span class="text-danger">*</span></label>
                <input 
                  type="text" 
                  id="address" 
                  class="form-control" 
                  bind:value={formData.address} 
                  required
                  disabled={submitting}
                >
              </div>
              
              <div class="mb-3">
                <label for="phone" class="form-label">Teléfono <span class="text-danger">*</span></label>
                <input 
                  type="text" 
                  id="phone" 
                  class="form-control" 
                  bind:value={formData.phone} 
                  required
                  disabled={submitting}
                >
              </div>
              
              <div class="mb-3">
                <label for="rooms_count" class="form-label">Número de salas <span class="text-danger">*</span></label>
                <input 
                  type="number" 
                  id="rooms_count" 
                  class="form-control" 
                  bind:value={formData.rooms_count} 
                  min="1"
                  max="20"
                  required
                  disabled={submitting}
                >
              </div>
            </div>
            
            <!-- Columna derecha -->
            <div class="col-md-6">
              <div class="mb-3">
                <label for="status" class="form-label">Estado <span class="text-danger">*</span></label>
                <select 
                  id="status" 
                  class="form-select" 
                  bind:value={formData.status} 
                  required
                  disabled={submitting}
                >
                  <option value="active">Activo</option>
                  <option value="maintenance">En mantenimiento</option>
                  <option value="coming_soon">Próximamente</option>
                </select>
              </div>
              
              <div class="mb-3">
                <label for="image_url" class="form-label">URL de imagen</label>
                <input 
                  type="url" 
                  id="image_url" 
                  class="form-control" 
                  bind:value={formData.image_url} 
                  placeholder="https://..."
                  disabled={submitting}
                >
                
                {#if formData.image_url}
                  <div class="image-preview mt-2">
                    <img src={formData.image_url} alt="Preview" class="img-thumbnail">
                  </div>
                {/if}
              </div>
              
              <div class="mb-4">
                <label class="form-label">Características</label>
                <div class="d-flex flex-column gap-2">
                  <div class="form-check">
                    <input 
                      type="checkbox" 
                      id="has_3d" 
                      class="form-check-input" 
                      bind:checked={formData.has_3d} 
                      disabled={submitting}
                    >
                    <label for="has_3d" class="form-check-label">Proyección 3D</label>
                  </div>
                  
                  <div class="form-check">
                    <input 
                      type="checkbox" 
                      id="has_imax" 
                      class="form-check-input" 
                      bind:checked={formData.has_imax} 
                      disabled={submitting}
                    >
                    <label for="has_imax" class="form-check-label">Sala IMAX</label>
                  </div>
                  
                  <div class="form-check">
                    <input 
                      type="checkbox" 
                      id="has_vip" 
                      class="form-check-input" 
                      bind:checked={formData.has_vip} 
                      disabled={submitting}
                    >
                    <label for="has_vip" class="form-check-label">Salas VIP</label>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Botones de formulario -->
            <div class="col-12 d-flex justify-content-end gap-2">
              <a href="/admin/cinemas" class="btn btn-outline-secondary" tabindex="-1">Cancelar</a>
              <button type="submit" class="btn btn-primary" disabled={submitting}>
                {#if submitting}
                  <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                  Guardando...
                {:else}
                  <i class="bi bi-save me-2"></i>
                  {isEditing ? 'Actualizar cine' : 'Crear cine'}
                {/if}
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  {/if}
</div>

<style>
  .image-preview {
    max-width: 200px;
  }
  
  .image-preview img {
    width: 100%;
    height: auto;
    object-fit: cover;
  }
</style> 