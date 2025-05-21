<script>
  import { onMount } from 'svelte';
  
  // Variables para metadatos de la página
  const pageTitle = "Política de Privacidad | Kaizen Cinema";
  const pageDescription = "Política de privacidad de Kaizen Cinema. Conoce cómo recopilamos, utilizamos y protegemos tus datos personales.";
  
  // Fecha de última actualización
  const lastUpdated = "15 de agosto de 2023";
  
  onMount(() => {
    // Scroll to top on page load
    window.scrollTo(0, 0);
  });
</script>

<svelte:head>
  <title>{pageTitle}</title>
  <meta name="description" content={pageDescription} />
</svelte:head>

<div class="container mt-5 pt-5">
  <div class="row justify-content-center">
    <div class="col-12 col-lg-10">
      <div class="card shadow-sm">
        <div class="card-body p-4 p-md-5">
          <h1 class="display-6 mb-4">Política de Privacidad</h1>
          
          <div class="alert alert-info">
            <p class="mb-0"><strong>Última actualización:</strong> {lastUpdated}</p>
          </div>
          
          <p class="lead">
            En Kaizen Cinema, valoramos y respetamos tu privacidad. Esta Política de Privacidad describe cómo recopilamos, utilizamos y compartimos tu información personal cuando utilizas nuestros servicios.
          </p>
          
          <hr class="my-4" />
          
          <section class="mb-5">
            <h2 class="h4">1. Información que recopilamos</h2>
            <p>Podemos recopilar los siguientes tipos de información:</p>
            
            <h3 class="h5 mt-4">1.1 Información personal</h3>
            <p>
              Cuando te registras en nuestra plataforma, reservas entradas o interactúas con nuestros servicios, podemos recopilar:
            </p>
            <ul>
              <li>Nombre y apellidos</li>
              <li>Dirección de correo electrónico</li>
              <li>Número de teléfono</li>
              <li>Dirección de facturación</li>
              <li>Información de pago (procesada de forma segura por nuestros proveedores de pago)</li>
              <li>Preferencias de películas y cines</li>
            </ul>
            
            <h3 class="h5 mt-4">1.2 Información de uso</h3>
            <p>
              Recopilamos automáticamente cierta información sobre cómo utilizas nuestra plataforma:
            </p>
            <ul>
              <li>Dirección IP</li>
              <li>Tipo de dispositivo y navegador</li>
              <li>Páginas visitadas y tiempo de permanencia</li>
              <li>Películas visualizadas o buscadas</li>
              <li>Ubicación aproximada (si lo permites)</li>
              <li>Referentes y clics en enlaces</li>
            </ul>
            
            <h3 class="h5 mt-4">1.3 Cookies y tecnologías similares</h3>
            <p>
              Utilizamos cookies y tecnologías similares para mejorar tu experiencia, recordar tus preferencias y analizar el tráfico. Puedes gestionar tus preferencias de cookies a través de la configuración de tu navegador.
            </p>
          </section>
          
          <section class="mb-5">
            <h2 class="h4">2. Cómo utilizamos tu información</h2>
            <p>Utilizamos la información recopilada para:</p>
            <ul>
              <li>Proporcionar, mantener y mejorar nuestros servicios</li>
              <li>Procesar tus reservas y pagos</li>
              <li>Enviarte confirmaciones, actualizaciones y alertas relacionadas con el servicio</li>
              <li>Personalizar tu experiencia y ofrecerte contenido relevante</li>
              <li>Analizar tendencias y comportamientos de uso para mejorar nuestra plataforma</li>
              <li>Detectar, investigar y prevenir actividades fraudulentas o no autorizadas</li>
              <li>Cumplir con obligaciones legales</li>
            </ul>
          </section>
          
          <section class="mb-5">
            <h2 class="h4">3. Compartición de información</h2>
            <p>
              No vendemos tu información personal. Podemos compartir tu información en las siguientes circunstancias:
            </p>
            <ul>
              <li><strong>Proveedores de servicios:</strong> Compartimos información con empresas que nos ayudan a operar, como procesadores de pago, servicios de análisis y atención al cliente.</li>
              <li><strong>Socios comerciales:</strong> Podemos compartir información con cines asociados para facilitar tus reservas.</li>
              <li><strong>Cumplimiento legal:</strong> Podemos divulgar información si creemos que es necesario para cumplir con la ley o proteger nuestros derechos.</li>
              <li><strong>Con tu consentimiento:</strong> Compartiremos tu información personal para cualquier otro propósito con tu consentimiento explícito.</li>
            </ul>
          </section>
          
          <section class="mb-5">
            <h2 class="h4">4. Seguridad de datos</h2>
            <p>
              Implementamos medidas de seguridad técnicas y organizativas diseñadas para proteger tu información personal. Sin embargo, ningún sistema es completamente seguro, y no podemos garantizar la seguridad absoluta de tu información.
            </p>
          </section>
          
          <section class="mb-5">
            <h2 class="h4">5. Tus derechos</h2>
            <p>Dependiendo de tu ubicación, puedes tener derecho a:</p>
            <ul>
              <li>Acceder a tu información personal</li>
              <li>Corregir datos inexactos</li>
              <li>Eliminar tu información personal</li>
              <li>Oponerte al procesamiento de tus datos</li>
              <li>Restringir el procesamiento de tus datos</li>
              <li>Portabilidad de datos</li>
              <li>Retirar el consentimiento en cualquier momento</li>
            </ul>
            <p>
              Para ejercer estos derechos, contáctanos a través de <a href="mailto:privacy@kaizencinema.com">privacy@kaizencinema.com</a>.
            </p>
          </section>
          
          <section class="mb-5">
            <h2 class="h4">6. Retención de datos</h2>
            <p>
              Conservamos tu información personal mientras mantengas una cuenta activa o según sea necesario para proporcionarte servicios. También podemos retener información para cumplir con obligaciones legales, resolver disputas y hacer cumplir nuestros acuerdos.
            </p>
          </section>
          
          <section class="mb-5">
            <h2 class="h4">7. Menores</h2>
            <p>
              Nuestros servicios no están dirigidos a personas menores de 16 años. No recopilamos a sabiendas información personal de menores de 16 años. Si eres padre o tutor y crees que tu hijo nos ha proporcionado información personal, contáctanos para que podamos tomar las medidas adecuadas.
            </p>
          </section>
          
          <section class="mb-5">
            <h2 class="h4">8. Cambios en esta política</h2>
            <p>
              Podemos actualizar esta Política de Privacidad periódicamente. Te notificaremos cualquier cambio material publicando la nueva política en esta página y, si los cambios son significativos, te enviaremos una notificación.
            </p>
          </section>
          
          <section class="mb-5">
            <h2 class="h4">9. Contacto</h2>
            <p>
              Si tienes preguntas o inquietudes sobre esta Política de Privacidad o nuestras prácticas de datos, contáctanos en:
            </p>
            <address>
              <strong>Kaizen Cinema</strong><br>
              Calle Innovación, 42<br>
              28001 Madrid, España<br>
              <a href="mailto:privacy@kaizencinema.com">privacy@kaizencinema.com</a><br>
              <a href="tel:+34911234567">+34 911 234 567</a>
            </address>
          </section>
          
          <div class="d-flex justify-content-between mt-5 pt-4 border-top">
            <a href="/terms" class="btn btn-outline-primary">
              <i class="bi bi-file-text me-1"></i>
              Términos de Servicio
            </a>
            <a href="/about" class="btn btn-outline-secondary">
              <i class="bi bi-info-circle me-1"></i>
              Acerca de Nosotros
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>