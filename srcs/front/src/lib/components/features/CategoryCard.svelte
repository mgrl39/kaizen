<script lang="ts">
	import type { Category } from '$lib/types';
	import { t } from '$lib/i18n';

	export let category: Category;
</script>

<div class="category-card card h-100">
	<div class="card-body text-center">
		<div class="category-icon-wrapper mb-3">
			<i class={`bi bi-${category.icon} category-icon`}></i>
		</div>
		<h5 class="category-title">{category.name}</h5>
		<a
			href={`/genre/${category.name.toLowerCase()}`}
			class={`btn btn-sm category-btn gradient-${category.gradient}`}
		>
			{$t('showFilms')}
		</a>
	</div>
</div>

<style>
	.category-card {
		transition: transform 0.3s ease;
	}

	.category-icon-wrapper {
		width: 60px;
		height: 60px;
		margin: 0 auto;
		display: flex;
		align-items: center;
		justify-content: center;
		border-radius: 50%;
		background: rgba(255, 255, 255, 0.1);
	}

	.category-icon {
		font-size: 1.75rem;
	}

	.category-btn {
		border: none;
		padding: 0.5rem 1.5rem;
		border-radius: 20px;
		color: white;
		transition: all 0.3s ease;
	}

	.gradient-danger {
		background: linear-gradient(135deg, #ff4e50, #f9d423);
	}

	.gradient-warning {
		background: linear-gradient(135deg, #f6d365, #fda085);
	}

	.gradient-info {
		background: linear-gradient(135deg, #0093e9, #80d0c7);
	}
</style>
