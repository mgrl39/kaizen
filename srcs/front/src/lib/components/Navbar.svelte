<script lang="ts">
  import type { NavItem } from '$lib/types';
  import { onMount } from 'svelte';
  
  const navItems: NavItem[] = [
    {url: '/cinemas', icon: 'building', text: 'Cines'},
    {url: '/movies', icon: 'film', text: 'Películas'}
  ];

  const userMenu: NavItem[] = [
    {url: '/profile', icon: 'person', text: 'Mi Perfil'},
    {url: '/bookings', icon: 'ticket', text: 'Mis Reservas'},
    {divider: true, url: '', icon: '', text: ''},
    {url: '/logout', icon: 'box-arrow-right', text: 'Cerrar Sesión'}
  ];

  let isAuthenticated: boolean = false;
  let userName: string = 'Usuario';
  
  let scrolled = false;
  
  onMount(() => {
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
        
        {#if isAuthenticated}
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
              <i class="bi bi-person-circle me-1"></i>{userName}
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
              {#each userMenu as item}
                {#if item.divider}
                  <li><hr class="dropdown-divider"></li>
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
      </ul>
    </div>
  </div>
</nav> 