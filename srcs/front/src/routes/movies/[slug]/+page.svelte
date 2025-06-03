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
      }
    } catch (e) {
      console.error('Error loading screenings:', e);
    }
  }

  function goBack() {
    history.back();
  }
  
  onMount(loadMovieData);
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
          {#each Object.entries(groupScreeningsByDate(screenings)) as [date, dateScreenings]}
            <div class="date-screenings">
              <h3>{formatDate(date)}</h3>
              <div class="screening-list">
                {#each dateScreenings as screening}
                  <a href={`/booking/${screening.id}`} class="screening-item">
                    <span class="time">
                      <i class="bi bi-clock"></i>
                      {formatTime(screening.time)}
                    </span>
                    <span class="room">{screening.room?.cinema?.name} · Sala {screening.room?.name}</span>
                    <span class="price">
                      {screening.price 
                        ? new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR' }).format(screening.price)
                        : 'N/A'
                      }
                    </span>
                    {#if screening.is_3d}
                      <span class="tag-3d">3D</span>
                    {/if}
                  </a>
                {/each}
              </div>
            </div>
          {/each}
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
    max-width: 1000px;
    margin: 0 auto;
    min-height: 100vh;
    background: #0a0a0a;
    color: #fff;
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
    background: rgba(255,255,255,0.03);
    padding: 1rem;
    border-radius: 4px;
    margin-bottom: 1.5rem;
    font-size: 0.95rem;
    line-height: 1.6;
    color: #ddd;
  }

  .screenings-section {
    background: rgba(255,255,255,0.03);
    border-radius: 4px;
    padding: 1rem;
  }

  .date-screenings {
    margin-bottom: 1.5rem;
  }

  .date-screenings:last-child {
    margin-bottom: 0;
  }

  h3 {
    font-size: 1rem;
    color: #aaa;
    margin: 0 0 0.75rem 0;
  }

  .screening-list {
    display: grid;
    gap: 0.5rem;
  }

  .screening-item {
    display: grid;
    grid-template-columns: auto 1fr auto auto;
    align-items: center;
    gap: 1rem;
    background: rgba(255,255,255,0.05);
    padding: 0.75rem;
    border-radius: 4px;
    text-decoration: none;
    color: #fff;
    transition: background-color 0.2s;
  }

  .screening-item:hover {
    background: rgba(255,255,255,0.1);
  }

  .time {
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 0.4rem;
  }

  .room {
    color: #aaa;
    font-size: 0.9rem;
  }

  .price {
    font-weight: 500;
    color: #4CAF50;
  }

  .tag-3d {
    background: #2196F3;
    padding: 0.2rem 0.5rem;
    border-radius: 4px;
    font-size: 0.8rem;
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
    }

    .poster {
      max-width: 200px;
      margin: 0 auto;
    }

    .screening-item {
      grid-template-columns: 1fr 1fr;
      gap: 0.5rem;
    }

    .cast-grid {
      grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    }
  }
</style>