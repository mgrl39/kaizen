<script lang="ts">
  import { t } from '$lib/i18n';
  import { theme } from '$lib/theme';
  
  const currentYear = new Date().getFullYear();
</script>

<footer class="footer mt-auto py-3 bg-body-tertiary" data-bs-theme={$theme}>
  <div class="container">
    <div class="row">
      <div class="col-md-6">
        <h5>Kaizen Cinema</h5>
        <p class="text-body-secondary">{$t('footerTagline')}</p>
      </div>
      <div class="col-md-3">
        <h5>{$t('links')}</h5>
        <ul class="nav flex-column">
          <li class="nav-item"><a href="/about" class="nav-link p-0 text-body-secondary">{$t('about')}</a></li>
          <li class="nav-item"><a href="/contact" class="nav-link p-0 text-body-secondary">{$t('contact')}</a></li>
          <li class="nav-item"><a href="/terms" class="nav-link p-0 text-body-secondary">{$t('terms')}</a></li>
        </ul>
      </div>
      <div class="col-md-3">
        <h5>{$t('social')}</h5>
        <ul class="list-unstyled d-flex">
          <li class="ms-3"><a class="text-body-secondary" href="#"><i class="bi bi-twitter"></i></a></li>
          <li class="ms-3"><a class="text-body-secondary" href="#"><i class="bi bi-instagram"></i></a></li>
          <li class="ms-3"><a class="text-body-secondary" href="#"><i class="bi bi-facebook"></i></a></li>
        </ul>
      </div>
    </div>
    <div class="d-flex justify-content-between pt-4 mt-4 border-top">
      <p>© {currentYear} Kaizen Cinema. {$t('allRightsReserved')}</p>
    </div>
  </div>
</footer>