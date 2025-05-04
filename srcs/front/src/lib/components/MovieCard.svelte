<script lang="ts">
  import type { Movie } from '$lib/types';
  
  export let movie: Movie;
  
  function truncateText(text: string, limit: number = 100): string {
    if (!text) return 'Sin descripción disponible';
    return text.length > limit ? text.substring(0, limit) + '...' : text;
  }
</script>

<div class="movie-card card h-100">
  <div class="card-image-wrapper">
    <img src={movie.photo_url} class="card-img-top" alt={movie.title}>
    <div class="card-overlay">
      <a href={`/movies/${movie.id}`} class="btn btn-primary btn-sm">
        <i class="bi bi-ticket me-1"></i>Ver detalles
      </a>
    </div>
  </div>
  <div class="card-body">
    <div class="d-flex justify-content-between align-items-start mb-2">
      <h5 class="card-title mb-0">{movie.title}</h5>
      <span class="rating-badge">
        <i class="bi bi-star-fill me-1"></i>{movie.rating}
      </span>
    </div>
    <p class="card-text text-muted">
      {truncateText(movie.synopsis || '', 100)}
    </p>
  </div>
</div>

<style>
  .movie-card {
    transition: transform 0.3s ease;
  }

  .card-image-wrapper {
    position: relative;
    overflow: hidden;
    border-radius: 16px 16px 0 0;
  }

  .card-image-wrapper img {
    height: 300px;
    object-fit: cover;
    transition: transform 0.3s ease;
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
    background: linear-gradient(135deg, #6d28d9, #8b5cf6);
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.875rem;
  }
</style>