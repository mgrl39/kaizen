<script>
  import { onMount } from 'svelte';
  
  // Variables para metadatos de la página
  const pageTitle = "Sobre Nosotros | Kaizen Cinema";
  const pageDescription = "Conoce más sobre Kaizen Cinema, nuestra historia, misión y el equipo detrás de tu experiencia cinematográfica favorita.";
  
  // Datos del equipo
  const team = [
    {
      name: "María García",
      role: "CEO & Fundadora",
      image: "https://i.pravatar.cc/300?img=1",
      bio: "Apasionada del cine y la innovación, con más de 15 años de experiencia en la industria."
    },
    {
      name: "Carlos Rodríguez",
      role: "CTO",
      image: "https://i.pravatar.cc/300?img=2",
      bio: "Experto en tecnología y desarrollo, liderando la transformación digital de Kaizen."
    },
    {
      name: "Ana Martínez",
      role: "Directora de Marketing",
      image: "https://i.pravatar.cc/300?img=3",
      bio: "Estratega creativa con un ojo para las tendencias y la experiencia del usuario."
    }
  ];
  
  // Estadísticas de la empresa
  const stats = [
    { number: "50+", label: "Películas en Cartelera" },
    { number: "10", label: "Cines" },
    { number: "100K+", label: "Clientes Satisfechos" },
    { number: "24/7", label: "Soporte" }
  ];
  
  onMount(() => {
    window.scrollTo(0, 0);
  });
</script>

<svelte:head>
  <title>{pageTitle}</title>
  <meta name="description" content={pageDescription} />
</svelte:head>

<div class="container mt-5 pt-5">
  <!-- Hero Section -->
  <section class="text-center mb-5">
    <h1 class="display-4 fw-bold mb-4">Sobre Kaizen Cinema</h1>
    <p class="lead text-muted">
      Transformando la experiencia cinematográfica desde 2024
    </p>
  </section>
  
  <!-- Historia y Misión -->
  <section class="row align-items-center mb-5 py-5">
    <div class="col-md-6 mb-4 mb-md-0">
      <h2 class="h3 mb-4">Nuestra Historia</h2>
      <p class="text-muted">
        Kaizen Cinema nació de la pasión por el cine y la innovación. Fundada en 2024, 
        nos propusimos revolucionar la forma en que las personas experimentan el cine, 
        combinando tecnología de vanguardia con el encanto tradicional de las salas de cine.
      </p>
      <p class="text-muted">
        Nuestro nombre, "Kaizen", refleja nuestra filosofía de mejora continua, 
        aplicando este principio japonés a cada aspecto de nuestra operación.
      </p>
    </div>
    <div class="col-md-6">
      <img 
        src="https://images.unsplash.com/photo-1536440136628-849c177e76a1?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" 
        alt="Nuestro cine" 
        class="img-fluid rounded shadow"
      />
    </div>
  </section>
  
  <!-- Misión y Visión -->
  <section class="bg-light py-5 mb-5">
    <div class="container">
      <div class="row">
        <div class="col-md-6 mb-4 mb-md-0">
          <div class="card h-100 border-0 shadow-sm">
            <div class="card-body p-4">
              <h3 class="h4 mb-3">
                <i class="bi bi-bullseye text-primary me-2"></i>
                Nuestra Misión
              </h3>
              <p class="text-muted mb-0">
                Proporcionar experiencias cinematográficas excepcionales que conecten 
                a las personas con el arte del cine, utilizando tecnología innovadora 
                y un servicio de primera clase.
              </p>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="card h-100 border-0 shadow-sm">
            <div class="card-body p-4">
              <h3 class="h4 mb-3">
                <i class="bi bi-eye text-primary me-2"></i>
                Nuestra Visión
              </h3>
              <p class="text-muted mb-0">
                Ser el líder en innovación cinematográfica, creando espacios donde 
                la tecnología y la tradición se unen para ofrecer experiencias 
                memorables a nuestros clientes.
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  
  <!-- Estadísticas -->
  <section class="mb-5">
    <div class="row text-center">
      {#each stats as stat}
        <div class="col-6 col-md-3 mb-4">
          <div class="p-3">
            <h3 class="h2 fw-bold text-primary mb-2">{stat.number}</h3>
            <p class="text-muted mb-0">{stat.label}</p>
          </div>
        </div>
      {/each}
    </div>
  </section>
  
  <!-- Equipo -->
  <section class="mb-5">
    <h2 class="h3 text-center mb-4">Nuestro Equipo</h2>
    <div class="row">
      {#each team as member}
        <div class="col-md-4 mb-4">
          <div class="card h-100 border-0 shadow-sm">
            <img 
              src={member.image} 
              alt={member.name}
              class="card-img-top"
              style="height: 300px; object-fit: cover;"
            />
            <div class="card-body text-center">
              <h3 class="h5 mb-1">{member.name}</h3>
              <p class="text-primary mb-3">{member.role}</p>
              <p class="text-muted mb-0">{member.bio}</p>
            </div>
          </div>
        </div>
      {/each}
    </div>
  </section>
  
  <!-- Valores -->
  <section class="mb-5">
    <h2 class="h3 text-center mb-4">Nuestros Valores</h2>
    <div class="row">
      <div class="col-md-3 mb-4">
        <div class="text-center">
          <i class="bi bi-heart-fill text-primary display-4 mb-3"></i>
          <h3 class="h5">Pasión</h3>
          <p class="text-muted">Amamos el cine y todo lo que representa</p>
        </div>
      </div>
      <div class="col-md-3 mb-4">
        <div class="text-center">
          <i class="bi bi-lightning-fill text-primary display-4 mb-3"></i>
          <h3 class="h5">Innovación</h3>
          <p class="text-muted">Buscamos constantemente mejorar</p>
        </div>
      </div>
      <div class="col-md-3 mb-4">
        <div class="text-center">
          <i class="bi bi-people-fill text-primary display-4 mb-3"></i>
          <h3 class="h5">Comunidad</h3>
          <p class="text-muted">Construimos experiencias juntos</p>
        </div>
      </div>
      <div class="col-md-3 mb-4">
        <div class="text-center">
          <i class="bi bi-star-fill text-primary display-4 mb-3"></i>
          <h3 class="h5">Excelencia</h3>
          <p class="text-muted">Nos esforzamos por la perfección</p>
        </div>
      </div>
    </div>
  </section>
  
  <!-- CTA -->
  <section class="text-center mb-5">
    <div class="card border-0 shadow-sm">
      <div class="card-body p-5">
        <h2 class="h3 mb-4">¿Listo para vivir la experiencia Kaizen?</h2>
        <a href="/movies" class="btn btn-primary btn-lg">
          <i class="bi bi-film me-2"></i>
          Ver Películas
        </a>
      </div>
    </div>
  </section>
</div>