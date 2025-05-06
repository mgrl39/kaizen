<script lang="ts">
	import type { Category } from '$lib/types';
	import { t } from '$lib/i18n';

	export let category: Category;
	
	// Función para obtener la clase de gradiente basada en el tipo
	const getGradientClass = (gradient: string) => {
		switch(gradient) {
			case 'danger':
				return 'bg-gradient-to-r from-[#ff4e50] to-[#f9d423]';
			case 'warning':
				return 'bg-gradient-to-r from-[#f6d365] to-[#fda085]';
			case 'info':
				return 'bg-gradient-to-r from-[#0093e9] to-[#80d0c7]';
			default:
				return 'bg-gradient-to-r from-blue-500 to-teal-400';
		}
	};
</script>

<div class="rounded-lg shadow-md p-4 h-full transition-transform duration-300 ease-in-out hover:transform hover:scale-105">
	<div class="text-center">
		<div class="w-[60px] h-[60px] mx-auto mb-3 flex items-center justify-center rounded-full bg-white bg-opacity-10">
			<!-- Nota: Deberías reemplazar esto con un enfoque de íconos compatible con Tailwind -->
			<i class={`bi bi-${category.icon} text-[1.75rem]`}></i>
		</div>
		<h5 class="text-lg font-medium mb-3">{category.name}</h5>
		<a
			href={`/genre/${category.name.toLowerCase()}`}
			class={`inline-block px-6 py-2 rounded-full text-white text-sm transition-all duration-300 ease-in-out ${getGradientClass(category.gradient)}`}
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
