<script>
  import { t } from '$lib/i18n';
  import { theme } from '$lib/theme';
  import Navbar from '$lib/components/Navbar.svelte';
  import Footer from '$lib/components/Footer.svelte';
</script>

<Navbar />

<div class="container py-5" data-bs-theme={$theme}>
  <div class="row justify-content-center">
    <div class="col-lg-10">
      <!-- Encabezado -->
      <div class="text-center mb-5">
        <h1 class="display-5 fw-bold">{$t('termsAndConditions', 'Términos y Condiciones')}</h1>
        <p class="text-muted">Última actualización: 20 de mayo de 2025</p>
      </div>
      
      <!-- Contenido principal -->
      <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4 p-lg-5">
          <section class="mb-5">
            <h2 class="h4 mb-3">1. {$t('introduction', 'Introducción')}</h2>
            <p>Bienvenido a Kaizen Cinema. Estos Términos y Condiciones rigen el uso de nuestro sitio web y servicios. Al acceder a nuestro sitio web o utilizar nuestros servicios, usted acepta estos términos en su totalidad. Si no está de acuerdo con estos términos, no utilice nuestro sitio web o servicios.</p>
            <p>Kaizen Cinema se reserva el derecho de modificar estos términos en cualquier momento. Las modificaciones entrarán en vigor inmediatamente después de su publicación en el sitio web. Su uso continuado del sitio web después de cualquier modificación constituye su aceptación de los términos modificados.</p>
          </section>
          
          <section class="mb-5">
            <h2 class="h4 mb-3">2. {$t('definitions', 'Definiciones')}</h2>
            <ul class="list-group list-group-flush">
              <li class="list-group-item bg-transparent ps-0">
                <strong>"Kaizen Cinema"</strong>: Se refiere a nuestra empresa, sitio web, aplicación y servicios relacionados.
              </li>
              <li class="list-group-item bg-transparent ps-0">
                <strong>"Usuario"</strong>: Cualquier persona que acceda o utilice nuestro sitio web o servicios.
              </li>
              <li class="list-group-item bg-transparent ps-0">
                <strong>"Contenido"</strong>: Incluye texto, gráficos, logotipos, iconos, imágenes, clips de audio, descargas digitales, recopilaciones de datos y software.
              </li>
              <li class="list-group-item bg-transparent ps-0">
                <strong>"Servicios"</strong>: Todos los servicios ofrecidos por Kaizen Cinema, incluyendo reservas de entradas, información de películas y cines.
              </li>
            </ul>
          </section>
          
          <section class="mb-5">
            <h2 class="h4 mb-3">3. {$t('accountRegistration', 'Registro de cuenta')}</h2>
            <p>Para acceder a ciertos servicios, puede ser necesario crear una cuenta. Al registrarse, usted acepta proporcionar información precisa, actual y completa. Es su responsabilidad mantener la confidencialidad de su contraseña y cuenta.</p>
            <p>Usted es responsable de todas las actividades que ocurran bajo su cuenta. Kaizen Cinema no será responsable por ninguna pérdida o daño derivado de su incumplimiento de esta obligación de seguridad.</p>
            <div class="alert alert-info">
              <i class="bi bi-info-circle me-2"></i>
              Recomendamos utilizar contraseñas seguras y no compartir los detalles de su cuenta con terceros.
            </div>
          </section>
          
          <section class="mb-5">
            <h2 class="h4 mb-3">4. {$t('ticketPurchaseAndRefunds', 'Compra de entradas y reembolsos')}</h2>
            <p>Al comprar entradas a través de Kaizen Cinema, usted está sujeto a las siguientes condiciones:</p>
            <ol>
              <li>Las entradas no son reembolsables ni transferibles, excepto en casos específicos detallados en nuestra política de reembolsos.</li>
              <li>Kaizen Cinema se reserva el derecho de rechazar la entrada si se sospecha que la entrada ha sido obtenida fraudulentamente.</li>
              <li>Los precios de las entradas están sujetos a cambios sin previo aviso.</li>
              <li>Es responsabilidad del usuario verificar los detalles de su reserva antes de confirmar la compra.</li>
            </ol>
            <p>Para solicitar un reembolso en circunstancias excepcionales, póngase en contacto con nuestro servicio de atención al cliente dentro de las 24 horas posteriores a la compra.</p>
          </section>
          
          <section class="mb-5">
            <h2 class="h4 mb-3">5. {$t('intellectualProperty', 'Propiedad intelectual')}</h2>
            <p>Todo el contenido incluido en o disponible a través de Kaizen Cinema, incluyendo pero no limitado a texto, gráficos, logotipos, iconos, imágenes, clips de audio, descargas digitales y software, es propiedad de Kaizen Cinema o sus proveedores de contenido y está protegido por leyes internacionales de derechos de autor, marcas registradas, patentes, secretos comerciales y otras leyes de propiedad intelectual.</p>
            <p>No está permitido extraer sistemáticamente ni reutilizar partes del contenido de Kaizen Cinema sin nuestro consentimiento expreso por escrito. En particular, no está permitido utilizar minería de datos, robots o herramientas similares de recolección y extracción de datos para extraer cualquier parte sustancial del sitio para su reutilización.</p>
          </section>
          
          <section class="mb-5">
            <h2 class="h4 mb-3">6. {$t('privacyPolicy', 'Política de privacidad')}</h2>
            <p>Nuestra política de privacidad, que establece cómo utilizamos su información, forma parte de estos Términos y Condiciones. Al utilizar nuestro sitio web, usted acepta el procesamiento de sus datos de acuerdo con nuestra política de privacidad.</p>
            <p>Para más información sobre cómo recopilamos, usamos y protegemos sus datos personales, consulte nuestra <a href="/privacy" class="text-decoration-none">Política de Privacidad</a>.</p>
          </section>
          
          <section class="mb-5">
            <h2 class="h4 mb-3">7. {$t('limitationOfLiability', 'Limitación de responsabilidad')}</h2>
            <p>Kaizen Cinema no será responsable por ningún daño directo, indirecto, incidental, especial, consecuente o punitivo, incluyendo pero no limitado a, pérdida de beneficios, datos, uso, buena voluntad u otras pérdidas intangibles, resultantes de:</p>
            <ul>
              <li>El uso o la imposibilidad de usar el sitio o los servicios.</li>
              <li>Cualquier cambio en el sitio o servicios.</li>
              <li>Acceso no autorizado o alteración de sus transmisiones o datos.</li>
              <li>Declaraciones o conductas de terceros en el sitio.</li>
              <li>Cualquier otra cuestión relacionada con el sitio o los servicios.</li>
            </ul>
          </section>
          
          <section class="mb-5">
            <h2 class="h4 mb-3">8. {$t('governingLaw', 'Ley aplicable')}</h2>
            <p>Estos Términos y Condiciones se regirán e interpretarán de acuerdo con las leyes de España, sin dar efecto a ningún principio de conflictos de leyes.</p>
            <p>Cualquier disputa relacionada con estos términos será sujeta a la jurisdicción exclusiva de los tribunales de Madrid, España.</p>
          </section>
          
          <section>
            <h2 class="h4 mb-3">9. {$t('contactUs', 'Contáctenos')}</h2>
            <p>Si tiene alguna pregunta sobre estos Términos y Condiciones, póngase en contacto con nosotros:</p>
            <ul>
              <li>Por correo electrónico: legal@kaizencinema.com</li>
              <li>Por teléfono: +34 912 345 678</li>
              <li>Por correo postal: Kaizen Cinema, Calle Principal 123, 28001 Madrid, España</li>
            </ul>
          </section>
        </div>
      </div>
      
      <!-- Aceptación de términos -->
      <div class="card border-0 bg-light mb-4">
        <div class="card-body p-4 text-center">
          <h3 class="h5 mb-3">{$t('acceptanceOfTerms', '¿Acepta estos términos y condiciones?')}</h3>
          <p class="mb-4">Al utilizar nuestro sitio web y servicios, usted acepta estos términos y condiciones en su totalidad.</p>
          <div class="d-flex justify-content-center gap-3">
            <a href="/" class="btn btn-primary">
              <i class="bi bi-check-circle me-2"></i>
              {$t('acceptAndContinue', 'Aceptar y continuar')}
            </a>
            <a href="javascript:history.back()" class="btn btn-outline-secondary">
              <i class="bi bi-arrow-left me-2"></i>
              {$t('goBack', 'Volver atrás')}
            </a>
          </div>
        </div>
      </div>
      
      <!-- Información adicional -->
      <div class="text-center">
        <p class="text-muted small">
          <i class="bi bi-shield-check me-1"></i>
          {$t('termsFooter', 'Estos términos están diseñados para proteger tanto a nuestros usuarios como a nuestra empresa.')}
        </p>
        <p class="text-muted small">
          <a href="/privacy" class="text-decoration-none">Política de Privacidad</a> • 
          <a href="/contact" class="text-decoration-none">Contacto</a> • 
          <a href="/faq" class="text-decoration-none">Preguntas Frecuentes</a>
        </p>
      </div>
    </div>
  </div>
</div>

<Footer /> 