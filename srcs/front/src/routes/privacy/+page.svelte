<script lang="ts">
  import { onMount } from 'svelte';
  import { theme } from '$lib/theme';
  import privacyData from './privacy.json';
  
  let currentTheme = 'light';
  $: currentTheme = $theme;
  
  onMount(() => {
    window.scrollTo(0, 0);
  });
</script>

<svelte:head>
  <title>{privacyData.title}</title>
  <meta name="description" content={privacyData.description} />
</svelte:head>

<div class="container mt-5 pt-5" data-bs-theme={currentTheme}>
  <div class="row justify-content-center">
    <div class="col-12 col-lg-10">
      <div class="card shadow-sm">
        <div class="card-body p-4 p-md-5">
          <h1 class="display-6 mb-4">Política de Privacidad</h1>
          
          <div class="alert alert-info">
            <p class="mb-0"><strong>Última actualización:</strong> {privacyData.lastUpdated}</p>
          </div>
          
          <p class="lead">
            En Kaizen Cinema, valoramos y respetamos tu privacidad. Esta Política de Privacidad describe cómo recopilamos, utilizamos y compartimos tu información personal cuando utilizas nuestros servicios.
          </p>
          
          <hr class="my-4" />
          
          {#each privacyData.sections as section}
            <section class="mb-5">
              <h2 class="h4">{section.title}</h2>
              <p>{section.content}</p>
              
              {#if section.subsections}
                {#each section.subsections as subsection}
                  <h3 class="h5 mt-4">{subsection.title}</h3>
                  <p>{subsection.content}</p>
                  
                  {#if subsection.items}
                    <ul>
                      {#each subsection.items as item}
                        <li>{item}</li>
                      {/each}
                    </ul>
                  {/if}
                {/each}
              {/if}
              
              {#if section.items}
                <ul>
                  {#each section.items as item}
                    {#if typeof item === 'string'}
                      <li>{item}</li>
                    {:else}
                      <li>
                        <strong>{item.title}:</strong> {item.content}
                      </li>
                    {/if}
                  {/each}
                </ul>
              {/if}
              
              {#if section.contact}
                {#if typeof section.contact === 'string'}
                  <p>{section.contact}</p>
                {:else}
                  <address>
                    <strong>{section.contact.company}</strong><br>
                    {section.contact.address}<br>
                    {section.contact.city}<br>
                    <a href="mailto:{section.contact.email}">{section.contact.email}</a><br>
                    <a href="tel:{section.contact.phone}">{section.contact.phone}</a>
                  </address>
                {/if}
              {/if}
            </section>
          {/each}
          
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

<style>
  /* Estilos específicos para el tema oscuro */
  :global([data-bs-theme="dark"]) .card {
    background-color: var(--bs-dark);
    border-color: var(--bs-gray-700);
  }
  
  :global([data-bs-theme="dark"]) .text-muted {
    color: var(--bs-gray-400) !important;
  }
</style>