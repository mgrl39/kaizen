<script lang="ts">
  import { onMount } from 'svelte';
  import { API_URL } from '$lib/config';
  import { goto } from '$app/navigation';
  import { t } from '$lib/i18n';
  import HeroBanner from '$lib/components/HeroBanner.svelte';

  let loading = true;
  let error: string | null = null;
  let success: string | null = null;
  let user: { username: string; name: string; email: string } | null = null;
  let editing = false;
  let formData = {
    username: '',
    name: '',
    email: '',
    current_password: '',
    new_password: '',
    new_password_confirmation: '',
    password: ''
  };

  // Datos de usuario (simulados)
  const userData = {
    id: 1,
    name: 'María García',
    email: 'maria.garcia@example.com',
    avatar: 'https://source.unsplash.com/random/300x300/?portrait',
    memberSince: '2023-04-15',
    loyaltyPoints: 350,
    preferences: {
      genres: ['Acción', 'Ciencia Ficción', 'Comedia'],
      notifications: true,
      newsletter: true
    }
  };
  
  // Historial de reservas (simulado)
  const bookings = [
    {
      id: 'B12345',
      date: '2023-10-15',
      movie: 'Dune: Parte Dos',
      cinema: 'Kaizen Cinema Madrid',
      room: 'Sala 2 (IMAX)',
      seats: ['F5', 'F6'],
      time: '19:30',
      status: 'completed',
      total: 24.90
    },
    {
      id: 'B12346',
      date: '2023-09-28',
      movie: 'Oppenheimer',
      cinema: 'Kaizen Cinema Madrid',
      room: 'Sala 1 (3D)',
      seats: ['D7', 'D8', 'D9'],
      time: '18:00',
      status: 'completed',
      total: 32.70
    },
    {
      id: 'B12347',
      date: '2023-11-05',
      movie: 'Gladiator II',
      cinema: 'Kaizen Cinema Madrid',
      room: 'Sala VIP',
      seats: ['C4', 'C5'],
      time: '20:15',
      status: 'upcoming',
      total: 28.50
    }
  ];
  
  // Películas favoritas (simuladas)
  const favoriteMovies = [
    {
      id: 101,
      title: 'Interstellar',
      poster_url: 'https://source.unsplash.com/random/300x450/?space',
      release_year: 2014
    },
    {
      id: 102,
      title: 'El Padrino',
      poster_url: 'https://source.unsplash.com/random/300x450/?mafia',
      release_year: 1972
    },
    {
      id: 103,
      title: 'Parásitos',
      poster_url: 'https://source.unsplash.com/random/300x450/?house',
      release_year: 2019
    },
    {
      id: 104,
      title: 'El Señor de los Anillos',
      poster_url: 'https://source.unsplash.com/random/300x450/?fantasy',
      release_year: 2001
    }
  ];
  
  // Función para formatear fechas
  function formatDate(dateStr: string): string {
    const date = new Date(dateStr);
    return new Intl.DateTimeFormat('es-ES', { 
      day: '2-digit', 
      month: '2-digit', 
      year: 'numeric' 
    }).format(date);
  }
  
  // Función para formatear precios
  function formatPrice(price: number): string {
    return new Intl.NumberFormat('es-ES', {
      style: 'currency',
      currency: 'EUR'
    }).format(price);
  }
  
  // Estado para pestañas
  let activeTab = 'profile'; // 'profile', 'bookings', 'favorites'

  async function fetchProfile() {
    const token = localStorage.getItem('token');
    if (!token) {
      goto('/login');
      return;
    }
    try {
      const response = await fetch(`${API_URL}/profile`, {
        headers: {
          'Authorization': `Bearer ${token}`,
          'Accept': 'application/json'
        }
      });
      const data = await response.json();
      if (response.ok && data.success) {
        user = data.user;
        formData.username = data.user.username;
        formData.name = data.user.name;
        formData.email = data.user.email;
      } else {
        error = data.message || 'No autorizado';
        localStorage.removeItem('token');
        goto('/login');
      }
    } catch (e) {
      error = 'Error de conexión con el servidor';
      localStorage.removeItem('token');
      goto('/login');
    } finally {
      loading = false;
    }
  }

  async function updateProfile() {
    const token = localStorage.getItem('token');
    if (!token) return;

    try {
      const response = await fetch(`${API_URL}/profile`, {
        method: 'PUT',
        headers: {
          'Authorization': `Bearer ${token}`,
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        body: JSON.stringify({
          username: formData.username,
          name: formData.name,
          email: formData.email
        })
      });

      const data = await response.json();
      if (response.ok && data.success) {
        user = data.user;
        success = data.message;
        editing = false;
      } else {
        error = data.message || 'Error al actualizar el perfil';
      }
    } catch (e) {
      error = 'Error de conexión con el servidor';
    }
  }

  async function changePassword() {
    const token = localStorage.getItem('token');
    if (!token) return;

    try {
      const response = await fetch(`${API_URL}/profile/password`, {
        method: 'PUT',
        headers: {
          'Authorization': `Bearer ${token}`,
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        body: JSON.stringify({
          current_password: formData.current_password,
          password: formData.new_password,
          password_confirmation: formData.new_password_confirmation
        })
      });

      const data = await response.json();
      if (response.ok && data.success) {
        success = data.message;
        formData.current_password = '';
        formData.new_password = '';
        formData.new_password_confirmation = '';
      } else {
        error = data.message || 'Error al cambiar la contraseña';
      }
    } catch (e) {
      error = 'Error de conexión con el servidor';
    }
  }

  onMount(() => {
    fetchProfile();
  });
</script>

<!-- Hero Banner para la página de perfil -->
<HeroBanner 
  title="Mi Perfil"
  subtitle="Gestiona tu información personal, reservas y preferencias"
  imageUrl="https://source.unsplash.com/random/1920x1080/?cinema,profile,user"
  overlayOpacity="70"
/>

<div class="container mx-auto px-4 py-8">
  <!-- Pestañas de navegación -->
  <div class="flex border-b border-white/10 mb-6 overflow-x-auto">
    <button 
      class="px-6 py-3 font-medium text-sm {activeTab === 'profile' ? 'text-purple-400 border-b-2 border-purple-400' : 'text-gray-400 hover:text-white'}"
      on:click={() => activeTab = 'profile'}
    >
      <i class="bi bi-person mr-2"></i>
      Información Personal
    </button>
    <button 
      class="px-6 py-3 font-medium text-sm {activeTab === 'bookings' ? 'text-purple-400 border-b-2 border-purple-400' : 'text-gray-400 hover:text-white'}"
      on:click={() => activeTab = 'bookings'}
    >
      <i class="bi bi-ticket-perforated mr-2"></i>
      Mis Reservas
    </button>
    <button 
      class="px-6 py-3 font-medium text-sm {activeTab === 'favorites' ? 'text-purple-400 border-b-2 border-purple-400' : 'text-gray-400 hover:text-white'}"
      on:click={() => activeTab = 'favorites'}
    >
      <i class="bi bi-heart mr-2"></i>
      Películas Favoritas
    </button>
  </div>
  
  <!-- Contenido de pestañas -->
  {#if activeTab === 'profile'}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
      <!-- Columna izquierda: Información básica y avatar -->
      <div class="bg-card border border-white/10 rounded-lg p-6">
        <div class="flex flex-col items-center mb-6">
          <div class="w-32 h-32 rounded-full overflow-hidden mb-4 border-2 border-purple-500/50">
            <img src={userData.avatar} alt={userData.name} class="w-full h-full object-cover" />
          </div>
          <h3 class="text-xl font-bold">{userData.name}</h3>
          <p class="text-gray-400">{userData.email}</p>
          <div class="mt-2 flex items-center text-sm text-purple-400">
            <i class="bi bi-star-fill mr-1"></i>
            <span>{userData.loyaltyPoints} puntos de fidelidad</span>
          </div>
        </div>
        
        <div class="border-t border-white/10 pt-4">
          <p class="text-gray-400 text-sm flex items-center">
            <i class="bi bi-calendar3 mr-2"></i>
            Miembro desde {formatDate(userData.memberSince)}
          </p>
        </div>
      </div>
      
      <!-- Columna central: Información detallada -->
      <div class="bg-card border border-white/10 rounded-lg p-6 md:col-span-2">
        <h3 class="text-xl font-bold mb-4 pb-2 border-b border-white/10">Información Personal</h3>
        
        <form on:submit|preventDefault={updateProfile} class="space-y-4">
          <div>
            <label for="name" class="block text-sm font-medium text-gray-300 mb-1">Nombre completo</label>
            <input 
              type="text" 
              id="name" 
              bind:value={formData.name}
              class="w-full bg-white/5 border border-white/10 rounded-md px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-purple-500"
            />
          </div>
          
          <div>
            <label for="email" class="block text-sm font-medium text-gray-300 mb-1">Correo electrónico</label>
            <input 
              type="email" 
              id="email" 
              bind:value={formData.email}
              class="w-full bg-white/5 border border-white/10 rounded-md px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-purple-500"
            />
          </div>
          
          <div>
            <label for="password" class="block text-sm font-medium text-gray-300 mb-1">Contraseña</label>
            <input 
              type="password" 
              id="password" 
              bind:value={formData.password}
              class="w-full bg-white/5 border border-white/10 rounded-md px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-purple-500"
            />
          </div>
          
          <h4 class="text-lg font-bold mt-6 mb-3">Preferencias</h4>
          
          <div>
            <label for="genres" class="block text-sm font-medium text-gray-300 mb-2">Géneros favoritos</label>
            <div class="flex flex-wrap gap-2">
              {#each userData.preferences.genres as genre}
                <span class="bg-purple-900/40 border border-purple-500/30 text-purple-200 text-xs py-1 px-2 rounded-md">
                  {genre}
                </span>
              {/each}
              <button 
                class="bg-white/10 text-white text-xs py-1 px-2 rounded-md hover:bg-white/20"
                aria-label="Añadir género favorito"
              >
                <i class="bi bi-plus"></i>
              </button>
            </div>
          </div>
          
          <div class="flex items-center">
            <input 
              type="checkbox" 
              id="notifications" 
              checked={userData.preferences.notifications} 
              class="w-4 h-4 text-purple-600 bg-white/5 border-white/10 rounded focus:ring-purple-500"
            />
            <label for="notifications" class="ml-2 text-sm text-gray-300">
              Recibir notificaciones sobre nuevos estrenos
            </label>
          </div>
          
          <div class="flex items-center">
            <input 
              type="checkbox" 
              id="newsletter" 
              checked={userData.preferences.newsletter} 
              class="w-4 h-4 text-purple-600 bg-white/5 border-white/10 rounded focus:ring-purple-500"
            />
            <label for="newsletter" class="ml-2 text-sm text-gray-300">
              Suscribirse al boletín de noticias
            </label>
          </div>
          
          <div class="pt-4 flex justify-end">
            <button 
              type="submit" 
              class="bg-purple-700 hover:bg-purple-600 text-white py-2 px-6 rounded-md transition-colors"
            >
              Guardar cambios
            </button>
          </div>
        </form>
      </div>
    </div>
  {:else if activeTab === 'bookings'}
    <div class="bg-card border border-white/10 rounded-lg p-6">
      <h3 class="text-xl font-bold mb-6">Mis Reservas</h3>
      
      {#if bookings.length === 0}
        <div class="bg-blue-900/20 border border-blue-500/30 text-blue-200 p-4 rounded-md">
          <p>No tienes reservas realizadas aún.</p>
        </div>
      {:else}
        <div class="space-y-6">
          {#each bookings as booking}
            <div class="border border-white/10 rounded-lg overflow-hidden">
              <div class="bg-white/5 px-4 py-3 flex items-center justify-between">
                <div>
                  <span class="text-sm font-medium text-gray-300">Reserva #{booking.id}</span>
                  <span class="mx-2 text-gray-500">•</span>
                  <span class="text-sm text-gray-400">{formatDate(booking.date)}</span>
                </div>
                <div>
                  {#if booking.status === 'upcoming'}
                    <span class="bg-green-900/40 border border-green-500/30 text-green-200 text-xs py-1 px-2 rounded-md">
                      Próxima
                    </span>
                  {:else if booking.status === 'completed'}
                    <span class="bg-blue-900/40 border border-blue-500/30 text-blue-200 text-xs py-1 px-2 rounded-md">
                      Completada
                    </span>
                  {:else}
                    <span class="bg-amber-900/40 border border-amber-500/30 text-amber-200 text-xs py-1 px-2 rounded-md">
                      {booking.status}
                    </span>
                  {/if}
                </div>
              </div>
              
              <div class="p-4">
                <h4 class="text-lg font-bold mb-2">{booking.movie}</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div class="space-y-2">
                    <p class="text-sm text-gray-300 flex items-start">
                      <i class="bi bi-building mr-2 mt-1 text-purple-400"></i>
                      <span>{booking.cinema}</span>
                    </p>
                    <p class="text-sm text-gray-300 flex items-start">
                      <i class="bi bi-door-open mr-2 mt-1 text-purple-400"></i>
                      <span>{booking.room}</span>
                    </p>
                    <p class="text-sm text-gray-300 flex items-start">
                      <i class="bi bi-calendar-event mr-2 mt-1 text-purple-400"></i>
                      <span>{formatDate(booking.date)} - {booking.time}</span>
                    </p>
                  </div>
                  
                  <div class="space-y-2">
                    <p class="text-sm text-gray-300 flex items-start">
                      <i class="bi bi-ticket-detailed mr-2 mt-1 text-purple-400"></i>
                      <span>Asientos: {booking.seats.join(', ')}</span>
                    </p>
                    <p class="text-sm text-gray-300 flex items-start">
                      <i class="bi bi-cash mr-2 mt-1 text-purple-400"></i>
                      <span>Total: {formatPrice(booking.total)}</span>
                    </p>
                  </div>
                </div>
                
                <div class="mt-4 flex justify-end space-x-3">
                  {#if booking.status === 'upcoming'}
                    <button class="bg-red-700 hover:bg-red-600 text-white py-1 px-4 text-sm rounded-md transition-colors">
                      Cancelar
                    </button>
                  {/if}
                  <button class="bg-purple-700 hover:bg-purple-600 text-white py-1 px-4 text-sm rounded-md transition-colors">
                    Ver detalles
                  </button>
                </div>
              </div>
            </div>
          {/each}
        </div>
      {/if}
    </div>
  {:else if activeTab === 'favorites'}
    <div class="bg-card border border-white/10 rounded-lg p-6">
      <h3 class="text-xl font-bold mb-6">Películas Favoritas</h3>
      
      {#if favoriteMovies.length === 0}
        <div class="bg-blue-900/20 border border-blue-500/30 text-blue-200 p-4 rounded-md">
          <p>No has añadido películas a favoritos aún.</p>
        </div>
      {:else}
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
          {#each favoriteMovies as movie}
            <div class="bg-white/5 border border-white/10 rounded-lg overflow-hidden transition-transform hover:scale-105">
              <a href={`/movies/${movie.id}`} class="block">
                <div class="h-64 overflow-hidden relative">
                  <img 
                    src={movie.poster_url} 
                    alt={movie.title} 
                    class="w-full h-full object-cover"
                  />
                  <button 
                    class="absolute top-2 right-2 bg-black/50 hover:bg-black/70 text-white p-2 rounded-full transition-colors"
                    aria-label="Remove from favorites"
                  >
                    <i class="bi bi-heart-fill text-red-500"></i>
                  </button>
                </div>
                <div class="p-4">
                  <h4 class="text-lg font-bold mb-1">{movie.title}</h4>
                  <p class="text-gray-400 text-sm">{movie.release_year}</p>
                </div>
              </a>
            </div>
          {/each}
        </div>
      {/if}
    </div>
  {/if}
</div>