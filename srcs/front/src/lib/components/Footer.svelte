<script>
  const links = [
    { href: "/", text: "Inicio" },
    { href: "/cinemas", text: "Cines" },
    { href: "/movies", text: "Películas" }
  ];
</script>

<footer class="py-4 footer">
  <div class="container">
    <div class="row g-3 text-center text-md-start">
      <div class="col-md-6" data-aos="fade-right" data-aos-duration="800">
        <a href="/" class="text-decoration-none text-light">
          <i class="bi bi-film me-2"></i>Kaizen
        </a>
        <span class="ms-2 text-muted">MIT License</span>
      </div>

      <div class="col-md-6 text-md-end" data-aos="fade-left" data-aos-duration="800">
        <nav class="d-flex justify-content-center justify-content-md-end gap-3">
          {#each links as link}
            <a href={link.href} class="text-decoration-none text-muted">{link.text}</a>
          {/each}
        </nav>
      </div>
    </div>
  </div>
</footer>