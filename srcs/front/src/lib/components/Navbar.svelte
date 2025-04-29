<script lang="ts">
  import type { NavItem } from '$lib/types';
  import { onMount } from 'svelte';
  import { goto } from '$app/navigation';
  import { API_URL } from '$lib/config';

  const navItems: NavItem[] = [
    {url: '/cinemas', icon: 'building', text: 'Cines'},
    {url: '/movies', icon: 'film', text: 'Películas'}
  ];

  const userMenu: NavItem[] = [
    {url: '/profile', icon: 'person', text: 'Mi Perfil'},
    {url: '/bookings', icon: 'ticket', text: 'Mis Reservas'},
    {divider: true, url: '', icon: '', text: ''},
    {url: 'javascript:void(0)', icon: 'box-arrow-right', text: 'Cerrar Sesión', action: 'logout'}
  ];

  let isAuthenticated: boolean = false;
  let userName: string = 'Usuario';
  let scrolled = false;
  let loadingProfile: boolean = true;

  async function fetchProfile() {
    const token = localStorage.getItem('token');
    if (!token) {
      isAuthenticated = false;
      loadingProfile = false;
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
        isAuthenticated = true;
        userName = data.user.name || data.user.username;
      } else {
        isAuthenticated = false;
        localStorage.removeItem('token');
      }
    } catch {
      isAuthenticated = false;
      localStorage.removeItem('token');
    } finally {
      loadingProfile = false;
    }
  }

  async function handleLogout(event: Event) {
    event.preventDefault();
    const token = localStorage.getItem('token');
    if (token) {
      try {
        await fetch(`${API_URL}/logout`, {
          method: 'POST',
          headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json'
          }
        });
      } catch (e) {
        // Ignorar errores de red
      }
      localStorage.removeItem('token');
      isAuthenticated = false;
      userName = 'Usuario';
      goto('/login');
    }
  }

  onMount(() => {
    fetchProfile();

    const handleScroll = () => {
      scrolled = window.scrollY > 20;
    };
    window.addEventListener('scroll', handleScroll);
    return () => {
      window.removeEventListener('scroll', handleScroll);
    };
  });
</script>

<nav class="navbar navbar-expand-md navbar-dark fixed-top {scrolled ? 'scrolled' : ''}">
  <div class="container">
    <a href="/" class="navbar-brand d-flex align-items-center">
      <span class="ms-2">Kaizen</span>
    </a>

    <div class="collapse navbar-collapse" id="navbarContent">
      <ul class="navbar-nav ms-auto align-items-center">
        {#each navItems as item}
          <li class="nav-item">
            <a href={item.url} class="nav-link">
              <i class="bi bi-{item.icon} me-1"></i>{item.text}
            </a>
          </li>
        {/each}
        
        {#if loadingProfile}
          <li class="nav-item ms-2">
            <span class="navbar-text">
              <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
              Cargando...
            </span>
          </li>
        {:else}
          {#if isAuthenticated}
            <li class="nav-item dropdown">
              <button 
                class="nav-link dropdown-toggle btn btn-link p-0 border-0 text-white" 
                data-bs-toggle="dropdown"
                aria-expanded="false"
                type="button"
              >
                <i class="bi bi-person-circle me-1"></i>{userName}
              </button>
              <ul class="dropdown-menu dropdown-menu-end">
                {#each userMenu as item}
                  {#if item.divider}
                    <li><hr class="dropdown-divider"></li>
                  {:else if item.action === 'logout'}
                    <li>
                      <button class="dropdown-item" on:click={handleLogout}>
                        <i class="bi bi-{item.icon} me-1"></i>{item.text}
                      </button>
                    </li>
                  {:else}
                    <li>
                      <a class="dropdown-item" href={item.url}>
                        <i class="bi bi-{item.icon} me-1"></i>{item.text}
                      </a>
                    </li>
                  {/if}
                {/each}
              </ul>
            </li>
          {:else}
            <li class="nav-item ms-2">
              <a href="/login" class="btn btn-primary">
                <i class="bi bi-box-arrow-in-right me-1"></i>Acceder
              </a>
            </li>
          {/if}
        {/if}
      </ul>
    </div>
  </div>
</nav> 