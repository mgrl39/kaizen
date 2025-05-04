<script lang="ts">
  export let title = "Kaizen Cinema";
  export let subtitle = "La mejor experiencia cinematográfica con las últimas películas y los cines más confortables.";
</script>

<div class="hero-banner mb-5">
  <div class="hero-content">
    <div class="row align-items-center">
      <div class="col-md-6 px-0">
        <h1 class="display-4 fw-bold glow-text">{title}</h1>
        <p class="lead my-4">{subtitle}</p>
        <div class="d-flex gap-3">
          <a href="/movies" class="btn btn-primary glow-effect">
            <i class="bi bi-film me-2"></i>Ver películas
          </a>
          <a href="/cinemas" class="btn btn-outline-light hover-glow">
            <i class="bi bi-building me-2"></i>Explorar cines
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
  .hero-banner {
    position: relative;
    padding: 6rem 0;
    background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), 
                url('https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');
    background-size: cover;
    background-position: center;
    border-radius: 16px;
    overflow: hidden;
  }

  .hero-content {
    position: relative;
    z-index: 2;
    padding: 0 2rem;
  }

  .glow-text {
    text-shadow: 0 0 20px rgba(139, 92, 246, 0.5);
  }
</style>