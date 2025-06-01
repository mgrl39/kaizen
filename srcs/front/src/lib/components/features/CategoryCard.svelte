<script>
	import { t } from '$lib/i18n';

	export let category = {};
	
	// Valores predeterminados para evitar errores
	$: {
		category.name = category.name || "Categoría";
		category.icon = category.icon || "tag";
		category.id = category.id || 1;
		category.count = category.count || 0;
	}
	
	// Texto simple para el contador
	$: countText = category.count === 1 ? "1 película" : `${category.count} películas`;
</script>

<div class="card h-100 border-0 shadow-sm">
	{#if category.image}
		<div class="position-relative">
			<img 
				src={category.image} 
				alt={category.name} 
				class="card-img-top"
				style="height: 140px; object-fit: cover;"
			>
			<div class="position-absolute top-0 end-0 p-2">
				<span class="badge bg-dark bg-opacity-75">
					{countText}
				</span>
			</div>
		</div>
	{:else}
		<div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 140px;">
			<i class="bi bi-{category.icon} display-4 text-primary"></i>
		</div>
	{/if}
	
	<div class="card-body">
		<div class="d-flex align-items-center">
			<i class="bi bi-{category.icon} me-2 text-primary"></i>
			<h5 class="card-title mb-0">{category.name}</h5>
		</div>
	</div>
	
	<div class="card-footer bg-transparent border-top-0">
		<a href="/categories/{category.id}" class="btn btn-sm btn-outline-primary w-100">
			{$t('explore', 'Explorar')}
		</a>
	</div>
</div>

<style>
  .card {
    background-color: var(--bs-body-bg);
    border-color: var(--bs-border-color);
  }

  .card-img-top.bg-light {
    background-color: var(--bs-tertiary-bg) !important;
  }

  :global(html[data-bs-theme="dark"]) .card {
    background-color: var(--bs-dark);
    border-color: var(--bs-border-color);
  }

  :global(html[data-bs-theme="dark"]) .card-img-top.bg-light {
    background-color: var(--bs-tertiary-bg) !important;
  }
</style>
