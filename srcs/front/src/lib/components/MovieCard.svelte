<script lang="ts">
  import type { Movie } from '$lib/types';
  import { t } from '$lib/i18n';
  
  export let movie: Movie;
  
  function truncateText(text: string, limit: number = 100): string {
    if (!text) return 'Sin descripción disponible';
    return text.length > limit ? text.substring(0, limit) + '...' : text;
  }

  function formatDuration(minutes) {
    const hours = Math.floor(minutes / 60);
    const mins = minutes % 60;
    return `${hours}h ${mins}m`;
  }
</script>

<div class="card h-100">
  <img src={movie.photo_url} class="card-img-top" alt={movie.title}>
  <div class="card-body">
    <h5 class="card-title">{movie.title}</h5>
    <div class="d-flex justify-content-between align-items-center mb-2">
      <span class="badge bg-primary">{movie.year}</span>
      <span class="badge bg-warning text-dark">
        <i class="bi bi-star-fill me-1"></i>
        {movie.rating.toFixed(1)}
      </span>
    </div>
    <p class="card-text">
      <small class="text-body-secondary">
        {movie.genres.join(', ')} • {formatDuration(movie.duration)}
      </small>
    </p>
  </div>
  <div class="card-footer bg-transparent border-top-0">
    <div class="d-grid">
      <a href={`/movies/${movie.id}`} class="btn btn-outline-primary">
        {$t('viewDetails')}
      </a>
    </div>
  </div>
</div>

<style>
  :global(.movie-card) {
    transition: transform 0.3s ease;
  }
  
  :global(.card-image-wrapper) {
    position: relative;
    overflow: hidden;
  }
  
  :global(.card-image-wrapper img) {
    height: 300px;
    object-fit: cover;
  }

  .card-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
  }

  .movie-card:hover .card-overlay {
    opacity: 1;
  }

  .movie-card:hover img {
    transform: scale(1.05);
  }

  .rating-badge {
    background: linear-gradient(135deg, var(--primary-color, #6d28d9), var(--accent-color, #8b5cf6));
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.875rem;
  }

  :global(html[data-bs-theme="dark"]) .card {
    background-color: var(--bs-dark);
    border-color: var(--bs-border-color);
  }
</style>