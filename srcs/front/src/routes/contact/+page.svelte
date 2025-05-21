<script>
  import { onMount } from 'svelte';
  
  // Variables para metadatos de la página
  const pageTitle = "Contacto | Kaizen Cinema";
  const pageDescription = "Contacta con el equipo de Kaizen Cinema para información general o reportar problemas técnicos.";
  
  // Estado del formulario
  let formSubmitted = false;
  let formError = false;
  let loading = false;
  
  // Opciones de contacto
  const contactOptions = [
    { 
      id: 'info', 
      title: 'Información General', 
      description: 'Para consultas sobre nuestros servicios, colaboraciones o información general.',
      icon: 'info-circle',
      email: 'kaizencontact@doncom.me',
      color: 'primary'
    },
    { 
      id: 'technical', 
      title: 'Soporte Técnico', 
      description: 'Para reportar problemas técnicos, bugs o sugerir mejoras en la plataforma.',
      icon: 'code-slash',
      url: 'https://github.com/mgrl39/kaizen/issues/new?template=BLANK_ISSUE',
      color: 'success'
    }
  ];
  
  // Función para manejar el envío del formulario
  async function handleSubmit(event) {
    loading = true;
    formError = false;
    
    try {
      const form = event.target;
      const formData = new FormData(form);
      
      const response = await fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
          'Accept': 'application/json'
        }
      });
      
      if (response.ok) {
        formSubmitted = true;
        form.reset();
      } else {
        formError = true;
      }
    } catch (error) {
      console.error('Error al enviar formulario:', error);
      formError = true;
    } finally {
      loading = false;
    }
  }
  
  onMount(() => {
    // Scroll to top on page load
    window.scrollTo(0, 0);
    
    // Código de Tawk.to
    var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
    (function(){
      var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
      s1.async=true;
      s1.src='https://embed.tawk.to/682d391da55be4190a7c5bab/1iroae6sn';
      s1.charset='UTF-8';
      s1.setAttribute('crossorigin','*');
      s0.parentNode.insertBefore(s1,s0);
    })();
  });
</script>

<svelte:head>
  <title>{pageTitle}</title>
  <meta name="description" content={pageDescription} />
</svelte:head>

<div class="container mt-5 pt-5">
  <div class="row justify-content-center">
    <div class="col-12 col-lg-10">
      <!-- Encabezado -->
      <section class="text-center mb-5">
        <h1 class="display-5 fw-bold mb-4">Contacto</h1>
        <p class="lead">
          Estamos aquí para ayudarte. Elige la opción que mejor se adapte a tu consulta.
        </p>
      </section>
      
      <!-- Opciones de contacto -->
      <section class="mb-5">
        <div class="row">
          {#each contactOptions as option}
            <div class="col-md-6 mb-4">
              <div class="card h-100 shadow-sm border-{option.color} border-top-0 border-end-0 border-bottom-0 border-3">
                <div class="card-body">
                  <div class="d-flex align-items-center mb-3">
                    <div class="bg-{option.color} bg-opacity-10 p-3 rounded-circle me-3">
                      <i class="bi bi-{option.icon} text-{option.color} fs-4"></i>
                    </div>
                    <h2 class="h4 mb-0">{option.title}</h2>
                  </div>
                  <p>{option.description}</p>
                  
                  {#if option.email}
                    <a href="mailto:{option.email}" class="btn btn-{option.color}">
                      <i class="bi bi-envelope me-1"></i>
                      Enviar email
                    </a>
                    <div class="mt-2">
                      <small class="text-muted">
                        <i class="bi bi-envelope-at me-1"></i>
                        {option.email}
                      </small>
                    </div>
                  {:else if option.url}
                    <a href={option.url} target="_blank" rel="noopener noreferrer" class="btn btn-{option.color}">
                      <i class="bi bi-github me-1"></i>
                      Crear issue en GitHub
                    </a>
                    <div class="mt-2">
                      <small class="text-muted">
                        <i class="bi bi-link-45deg me-1"></i>
                        <a href={option.url} target="_blank" rel="noopener noreferrer" class="text-muted">
                          {option.url}
                        </a>
                      </small>
                    </div>
                  {/if}
                </div>
              </div>
            </div>
          {/each}
        </div>
      </section>
      
      <!-- Formulario de contacto rápido -->
      <section class="mb-5">
        <div class="card shadow-sm">
          <div class="card-body p-4">
            <h2 class="h4 mb-4">Formulario de contacto rápido</h2>
            
            {#if formSubmitted}
              <div class="alert alert-success">
                <i class="bi bi-check-circle me-2"></i>
                ¡Gracias por contactarnos! Hemos recibido tu mensaje y te responderemos lo antes posible.
              </div>
              <button class="btn btn-outline-primary" on:click={() => formSubmitted = false}>
                <i class="bi bi-arrow-repeat me-1"></i>
                Enviar otro mensaje
              </button>
            {:else}
              {#if formError}
                <div class="alert alert-danger">
                  <i class="bi bi-exclamation-triangle me-2"></i>
                  Ha ocurrido un error al enviar tu mensaje. Por favor, inténtalo de nuevo.
                </div>
              {/if}
              
              <form 
                action="https://formspree.io/f/xqaqjozd"
                method="POST"
                on:submit|preventDefault={handleSubmit}
              >
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">Nombre</label>
                    <input 
                      type="text" 
                      class="form-control" 
                      id="name" 
                      name="name"
                      required
                      placeholder="Tu nombre"
                    />
                  </div>
                  <div class="col-md-6 mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input 
                      type="email" 
                      class="form-control" 
                      id="email" 
                      name="email"
                      required
                      placeholder="tu@email.com"
                    />
                  </div>
                </div>
                
                <div class="mb-3">
                  <label for="subject" class="form-label">Asunto</label>
                  <input 
                    type="text" 
                    class="form-control" 
                    id="subject" 
                    name="subject"
                    required
                    placeholder="Asunto de tu mensaje"
                  />
                </div>
                
                <div class="mb-3">
                  <label for="message" class="form-label">Mensaje</label>
                  <textarea 
                    class="form-control" 
                    id="message" 
                    name="message"
                    rows="5" 
                    required
                    placeholder="¿En qué podemos ayudarte?"
                  ></textarea>
                </div>
                
                <button 
                  type="submit" 
                  class="btn btn-primary" 
                  disabled={loading}
                >
                  {#if loading}
                    <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                    Enviando...
                  {:else}
                    <i class="bi bi-send me-1"></i>
                    Enviar mensaje
                  {/if}
                </button>
              </form>
            {/if}
          </div>
        </div>
      </section>
      
      <!-- Información adicional -->
      <section class="mb-5">
        <div class="row">
          <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm">
              <div class="card-body text-center">
                <div class="mb-3">
                  <i class="bi bi-geo-alt text-primary display-5"></i>
                </div>
                <h3 class="h5">Dirección</h3>
                <p class="mb-0">
                  Calle Innovación, 42<br>
                  28001 Madrid, España
                </p>
              </div>
            </div>
          </div>
          
          <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm">
              <div class="card-body text-center">
                <div class="mb-3">
                  <i class="bi bi-telephone text-primary display-5"></i>
                </div>
                <h3 class="h5">Teléfono</h3>
                <p class="mb-0">
                  <a href="tel:+34911234567" class="text-decoration-none">+34 911 234 567</a><br>
                  Lun-Vie: 9:00 - 18:00
                </p>
              </div>
            </div>
          </div>
          
          <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm">
              <div class="card-body text-center">
                <div class="mb-3">
                  <i class="bi bi-globe text-primary display-5"></i>
                </div>
                <h3 class="h5">Redes Sociales</h3>
                <div class="d-flex justify-content-center gap-3">
                  <a href="https://twitter.com/kaizencinema" target="_blank" rel="noopener noreferrer" class="text-decoration-none">
                    <i class="bi bi-twitter fs-4"></i>
                  </a>
                  <a href="https://facebook.com/kaizencinema" target="_blank" rel="noopener noreferrer" class="text-decoration-none">
                    <i class="bi bi-facebook fs-4"></i>
                  </a>
                  <a href="https://instagram.com/kaizencinema" target="_blank" rel="noopener noreferrer" class="text-decoration-none">
                    <i class="bi bi-instagram fs-4"></i>
                  </a>
                  <a href="https://github.com/mgrl39/kaizen" target="_blank" rel="noopener noreferrer" class="text-decoration-none">
                    <i class="bi bi-github fs-4"></i>
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
      
      <!-- Mapa -->
      <section class="mb-5">
        <div class="card shadow-sm">
          <div class="card-body p-0">
            <div class="ratio ratio-21x9">
              <iframe 
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d12143.354760973505!2d-3.703355649999999!3d40.416729000000004!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xd42287d6da3df9d%3A0x408f7fea601cb8fe!2sMadrid%2C%20Spain!5e0!3m2!1sen!2sus!4v1653669571594!5m2!1sen!2sus" 
                width="600" 
                height="450" 
                style="border:0;" 
                allowfullscreen="" 
                loading="lazy" 
                referrerpolicy="no-referrer-when-downgrade"
                title="Ubicación de Kaizen Cinema"
              ></iframe>
            </div>
          </div>
        </div>
      </section>
      
      <!-- Enlaces a páginas legales -->
      <div class="d-flex justify-content-between mt-5 pt-4 border-top">
        <a href="/terms" class="btn btn-outline-secondary">
          <i class="bi bi-file-text me-1"></i>
          Términos de Servicio
        </a>
        <a href="/privacy-policy" class="btn btn-outline-secondary">
          <i class="bi bi-shield-lock me-1"></i>
          Política de Privacidad
        </a>
      </div>
    </div>
  </div>
</div>
