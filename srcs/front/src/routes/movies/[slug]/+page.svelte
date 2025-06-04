<script lang="ts">
  import { onMount } from 'svelte';
  import { API_URL } from '$lib/config';
  import { fade } from 'svelte/transition';
  import SharePopup from '$lib/components/SharePopup.svelte';
  
  export let data;
  
  const DEFAULT_IMAGE_BASE64 = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=';
  
  function getImageUrl(photoUrl) {
    if (!photoUrl) return DEFAULT_IMAGE_BASE64;
    return `/images/movies/${photoUrl}`;
  }
  
  let movie = null;
  let loading = true;
  let error = null;
  let screenings = [];
  let showSharePopup = false;
  let selectedDate = null;
  
  function formatDate(dateString) {
    if (!dateString) return 'N/A';
    try {
      return new Intl.DateTimeFormat('es-ES', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      }).format(new Date(dateString));
    } catch (e) {
      return 'N/A';
    }
  }

  function formatShortDate(dateString) {
    if (!dateString) return 'N/A';
    try {
      return new Intl.DateTimeFormat('es-ES', {
        weekday: 'short',
        day: 'numeric',
        month: 'short'
      }).format(new Date(dateString));
    } catch (e) {
      return 'N/A';
    }
  }
  
  function formatTime(timeString) {
    if (!timeString) return 'N/A';
    try {
      const [hours, minutes] = timeString.split(':');
      return `${hours}:${minutes}`;
    } catch (e) {
      return timeString;
    }
  }
  
  function formatDuration(minutes) {
    if (!minutes) return 'N/A';
    const hours = Math.floor(minutes / 60);
    const mins = minutes % 60;
    return `${hours}h ${mins}m`;
  }
  
  function groupScreeningsByDate(screenings) {
    return screenings.reduce((groups, screening) => {
      const date = screening.date;
      if (!groups[date]) groups[date] = [];
      groups[date].push(screening);
      return groups;
    }, {});
  }
  
  async function loadMovieData() {
    try {
      loading = true;
      error = null;
      
      const response = await fetch(`${API_URL}/movies/${data.slug}`, {
        headers: { 'Accept': 'application/json' }
      });
      
      const result = await response.json();
      
      if (response.ok && result.success) {
        movie = result.data;
        if (movie.id) loadScreenings(movie.id);
      } else {
        error = result.message || 'Error al cargar la película';
      }
    } catch (e) {
      error = 'Error de conexión';
    } finally {
      loading = false;
    }
  }
  
  async function loadScreenings(movieId) {
    try {
      const response = await fetch(`${API_URL}/movies/${movieId}/screenings`, {
        headers: { 'Accept': 'application/json' }
      });
      
      const result = await response.json();
      if (response.ok && result.success) {
        screenings = result.data;
        const dates = Object.keys(groupScreeningsByDate(screenings));
        if (dates.length > 0) {
          selectedDate = dates[0];
        }
      }
    } catch (e) {
      console.error('Error loading screenings:', e);
    }
  }

  function goBack() {
    history.back();
  }
  
  onMount(loadMovieData);

  $: groupedScreenings = groupScreeningsByDate(screenings);
  $: availableDates = Object.keys(groupedScreenings);
</script>

<div class="movie-page">
  {#if loading}
    <div class="center-content" in:fade>
      <div class="spinner" />
    </div>
  {:else if error}
    <div class="center-content error" in:fade>
      <p>{error}</p>
      <button on:click={goBack}>Volver</button>
    </div>
  {:else if movie}
    <div class="movie-content">
      <div class="top-bar">
        <button class="icon-btn" on:click={goBack}>
          <i class="bi bi-arrow-left"></i>
        </button>
        {#if movie.rating}
          <span class="rating">{movie.rating}</span>
        {/if}
      </div>

      <div class="movie-grid">
        <img 
          src={getImageUrl(movie.photo_url)}
          alt={movie.title}
          class="poster"
          on:error={(e) => e.target.src = DEFAULT_IMAGE_BASE64}
        />
        
        <div class="info-panel">
          <h1>{movie.title}</h1>
          
          <div class="meta">
            {#if movie.release_date}
              <span class="meta-item">
                <i class="bi bi-calendar3"></i>
                {formatDate(movie.release_date)}
              </span>
            {/if}
            {#if movie.duration}
              <span class="meta-item">
                <i class="bi bi-clock"></i>
                {formatDuration(movie.duration)}
              </span>
            {/if}
          </div>

          {#if movie.genres?.length}
            <div class="genres">
              {#each movie.genres as genre}
                <span class="tag">{genre.name}</span>
              {/each}
            </div>
          {/if}

          {#if movie.directors}
            <div class="director">
              <i class="bi bi-camera-reels"></i>
              <span>{movie.directors}</span>
            </div>
          {/if}

          {#if movie.actors?.length}
            <div class="cast">
              <div class="cast-grid">
                {#each movie.actors as actor}
                  <a href="/actors/{actor.slug}" class="actor">
                    <span class="actor-name">{actor.name}</span>
                  </a>
                {/each}
              </div>
            </div>
          {/if}
        </div>
      </div>

      <div class="synopsis-section">
        <p>{movie.synopsis}</p>
      </div>

      {#if screenings.length > 0}
        <div class="screenings-section">
          <div class="dates-nav">
            {#each availableDates as date}
              <button 
                class="date-btn {selectedDate === date ? 'active' : ''}"
                on:click={() => selectedDate = date}
              >
                <span class="date-short">{formatShortDate(date)}</span>
                <span class="screening-count">{groupedScreenings[date].length} sesiones</span>
              </button>
            {/each}
          </div>

          {#if selectedDate && groupedScreenings[selectedDate]}
            <div class="screenings-grid">
              {#each groupedScreenings[selectedDate] as screening}
                <a 
                  href={`/booking/${screening.id}`} 
                  class="screening-card"
                >
                  <div class="screening-header">
                    <span class="time">
                      <i class="bi bi-clock"></i>
                      {formatTime(screening.time)}
                    </span>
                    {#if screening.is_3d}
                      <span class="tag-3d">3D</span>
                    {/if}
                  </div>

                  <div class="screening-info">
                    <div class="cinema-info">
                      <span class="cinema-name">{screening.room?.cinema?.name}</span>
                      <span class="room-name">{screening.room?.name}</span>
                      <span class="location">{screening.room?.cinema?.location}</span>
                    </div>

                    <div class="price-tag">
                      {screening.price 
                        ? new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR' }).format(screening.price)
                        : 'N/A'
                      }
                    </div>
                  </div>

                  <button class="book-btn">
                    <i class="bi bi-ticket-perforated"></i>
                    Reservar
                  </button>
                </a>
              {/each}
            </div>
          {/if}
        </div>
      {:else}
        <p class="no-screenings">No hay sesiones disponibles</p>
      {/if}
    </div>
  {:else}
    <div class="center-content error" in:fade>
      <p>Película no encontrada</p>
      <button on:click={goBack}>Volver</button>
    </div>
  {/if}
</div>

<SharePopup 
  bind:show={showSharePopup}
  title={movie?.title || ''}
  url={window.location.href}
/>

<style>
  .movie-page {
    width: 100%;
    margin: 0 auto;
    min-height: 100vh;
    background: var(--app-bg);
    color: var(--bs-body-color);
  }

  .center-content {
    display: grid;
    place-items: center;
    min-height: 50vh;
  }

  .spinner {
    width: 2rem;
    height: 2rem;
    border: 2px solid #fff;
    border-top-color: transparent;
    border-radius: 50%;
    animation: spin 1s linear infinite;
  }

  @keyframes spin {
    to { transform: rotate(360deg); }
  }

  .movie-content {
    max-width: 1200px;
    margin: 0 auto;
    padding: 1rem;
  }

  .top-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
  }

  .icon-btn {
    background: none;
    border: none;
    color: #fff;
    padding: 0.5rem;
    cursor: pointer;
    opacity: 0.8;
    transition: opacity 0.2s;
  }

  .icon-btn:hover {
    opacity: 1;
  }

  .rating {
    background: #ffd700;
    color: #000;
    padding: 0.25rem 0.75rem;
    border-radius: 4px;
    font-size: 0.8rem;
    font-weight: 500;
  }

  .movie-grid {
    display: grid;
    grid-template-columns: minmax(100px, 180px) 1fr;
    gap: 1.5rem;
    margin-bottom: 1.5rem;
    background: var(--app-card-bg);
    border-radius: 1rem;
    padding: 1.5rem;
    border: 1px solid var(--app-border);
    backdrop-filter: blur(10px);
  }

  .poster {
    width: 100%;
    aspect-ratio: 2/3;
    object-fit: cover;
    border-radius: 4px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.3);
  }

  .info-panel {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
  }

  h1 {
    font-size: 1.75rem;
    font-weight: 600;
    margin: 0;
    line-height: 1.2;
  }

  .meta {
    display: flex;
    gap: 1rem;
    font-size: 0.9rem;
    color: #aaa;
  }

  .meta-item {
    display: flex;
    align-items: center;
    gap: 0.4rem;
  }

  .genres {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
  }

  .tag {
    background: rgba(255,255,255,0.1);
    padding: 0.2rem 0.6rem;
    border-radius: 100px;
    font-size: 0.8rem;
  }

  .director {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #aaa;
    font-size: 0.9rem;
  }

  .cast-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
    gap: 0.5rem;
  }

  .actor {
    background: rgba(255,255,255,0.05);
    padding: 0.5rem;
    border-radius: 4px;
    text-decoration: none;
    color: #fff;
    transition: background-color 0.2s;
  }

  .actor:hover {
    background: rgba(255,255,255,0.1);
  }

  .actor-name {
    font-size: 0.85rem;
    display: block;
    text-align: center;
  }

  .synopsis-section {
    background: var(--app-card-bg);
    padding: 1.5rem;
    border-radius: 1rem;
    margin-bottom: 1.5rem;
    border: 1px solid var(--app-border);
    backdrop-filter: blur(10px);
  }

  .screenings-section {
    background: var(--app-card-bg);
    border-radius: 1rem;
    padding: 1.5rem;
    border: 1px solid var(--app-border);
    backdrop-filter: blur(10px);
  }

  .dates-nav {
    display: flex;
    gap: 0.5rem;
    overflow-x: auto;
    padding-bottom: 1rem;
    margin-bottom: 1rem;
    border-bottom: 1px solid var(--app-border);
  }

  .date-btn {
    background: none;
    border: 1px solid var(--app-border);
    border-radius: 0.75rem;
    padding: 0.5rem 1rem;
    color: var(--bs-body-color);
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    flex-direction: column;
    align-items: center;
    min-width: 100px;
    opacity: 0.7;
  }

  .date-btn:hover {
    opacity: 1;
    transform: translateY(-2px);
  }

  .date-btn.active {
    background: var(--primary-color);
    color: white;
    border-color: transparent;
    opacity: 1;
  }

  .date-short {
    font-weight: 500;
    text-transform: capitalize;
  }

  .screening-count {
    font-size: 0.8rem;
    opacity: 0.7;
  }

  .screenings-grid {
    display: grid;
    gap: 1rem;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  }

  .screening-card {
    background: var(--app-card-bg);
    border: 1px solid var(--app-border);
    border-radius: 1rem;
    padding: 1rem;
    text-decoration: none;
    color: var(--bs-body-color);
    display: flex;
    flex-direction: column;
    gap: 1rem;
    transition: all 0.2s ease;
  }

  .screening-card:hover {
    transform: translateY(-2px);
    border-color: var(--primary-color);
  }

  .screening-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .time {
    font-size: 1.2rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.4rem;
  }

  .tag-3d {
    background: #2196F3;
    padding: 0.2rem 0.5rem;
    border-radius: 4px;
    font-size: 0.8rem;
  }

  .screening-info {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
  }

  .cinema-info {
    display: flex;
    flex-direction: column;
    gap: 0.2rem;
  }

  .cinema-name {
    font-weight: 500;
  }

  .room-name {
    font-size: 0.9rem;
    color: #aaa;
  }

  .location {
    font-size: 0.85rem;
    color: #888;
  }

  .price-tag {
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--primary-color);
    margin-top: 0.5rem;
  }

  .book-btn {
    background: var(--primary-color);
    color: white;
    border: none;
    border-radius: 0.75rem;
    padding: 0.75rem;
    font-weight: 500;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    transition: all 0.2s ease;
  }

  .book-btn:hover {
    background: var(--primary-hover);
    transform: translateY(-2px);
  }

  .no-screenings {
    text-align: center;
    color: #aaa;
    padding: 1rem;
  }

  .error {
    color: #ff4444;
  }

  @media (max-width: 640px) {
    .movie-grid {
      grid-template-columns: 1fr;
      padding: 1rem;
    }

    .poster {
      max-width: 200px;
      margin: 0 auto;
    }

    .screenings-grid {
      grid-template-columns: 1fr;
    }

    .dates-nav {
      margin: -1rem -1rem 1rem -1rem;
      padding: 1rem;
      background: var(--app-card-bg);
      border-bottom: 1px solid var(--app-border);
    }

    .date-btn {
      min-width: 90px;
      padding: 0.4rem 0.75rem;
    }
  }
</style>