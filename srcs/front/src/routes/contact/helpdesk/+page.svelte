<script lang="ts">
  import { onMount } from 'svelte';
  import { theme } from '$lib/theme';
  import { t } from '$lib/i18n';

  // Page metadata
  $: pageTitle = `${$t('helpDesk')} | Kaizen Cinema`;
  $: pageDescription = $t('helpDeskDesc');

  // Theme reactivity
  $: currentTheme = $theme;

  onMount(() => {
    // Load chat widget
    const s1 = document.createElement("script");
    s1.async = true;
    s1.src = 'https://embed.tawk.to/682d391da55be4190a7c5bab/1iroae6sn';
    s1.charset = 'UTF-8';
    s1.setAttribute('crossorigin', '*');
    document.getElementsByTagName("script")[0].parentNode?.insertBefore(s1, null);
  });
</script>

<svelte:head>
  <title>{pageTitle}</title>
  <meta name="description" content={pageDescription} />
</svelte:head>

<div class="container" data-bs-theme={currentTheme}>
  <div class="row justify-content-center">
    <div class="col-lg-8">
      <div class="card shadow-sm mb-4">
        <div class="card-body">
          <h1 class="h3 mb-4">{$t('helpDesk')}</h1>
          
          <div class="alert alert-info mb-4">
            <h5 class="alert-heading"><i class="bi bi-info-circle me-2"></i>{$t('previousSystem')}</h5>
            <p class="mb-0">{$t('tawkExplanation')}</p>
          </div>

          <h2 class="h5 mb-3">{$t('supportOptions')}</h2>
          
          <div class="list-group">
            <a href="mailto:kaizencontact@doncom.me" class="list-group-item list-group-item-action">
              <div class="d-flex align-items-center">
                <i class="bi bi-envelope fs-4 me-3 text-primary"></i>
                <div>
                  <h3 class="h6 mb-1">{$t('emailSupport')}</h3>
                  <p class="small mb-0">{$t('emailSupportDesc')}</p>
                </div>
              </div>
            </a>

            <a href="https://github.com/mgrl39/kaizen/issues" class="list-group-item list-group-item-action">
              <div class="d-flex align-items-center">
                <i class="bi bi-github fs-4 me-3 text-dark"></i>
                <div>
                  <h3 class="h6 mb-1">{$t('githubIssues')}</h3>
                  <p class="small mb-0">{$t('githubIssuesDesc')}</p>
                </div>
              </div>
            </a>

            <div class="list-group-item">
              <div class="d-flex align-items-center">
                <i class="bi bi-clock fs-4 me-3 text-success"></i>
                <div>
                  <h3 class="h6 mb-1">{$t('supportHours')}</h3>
                  <p class="small mb-0">{$t('supportHoursDesc')}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="text-center">
        <a href="/contact" class="btn btn-outline-primary">
          <i class="bi bi-arrow-left me-2"></i>{$t('backToContact')}
        </a>
      </div>
    </div>
  </div>
</div>

<style>
  :global([data-bs-theme="dark"]) .card {
    background-color: var(--bs-dark);
    border-color: var(--bs-gray-700);
  }

  :global([data-bs-theme="dark"]) .list-group-item {
    background-color: var(--bs-dark);
    border-color: var(--bs-gray-700);
  }

  :global([data-bs-theme="dark"]) .list-group-item-action:hover {
    background-color: var(--bs-gray-800);
  }

  .list-group-item {
    padding: 1rem;
  }

  .bi {
    width: 1em;
    height: 1em;
    display: inline-block;
    vertical-align: -0.125em;
  }
</style> 