import { writable } from 'svelte/store';
import { browser } from '$app/environment';

// Crear store para el tema
export const theme = writable('light');

// Función para obtener el tema inicial
export function initTheme() {
	if (browser) {
		// Verificar preferencia guardada
		const savedTheme = localStorage.getItem('theme');

		if (savedTheme) {
			applyTheme(savedTheme);
		} else {
			// Verificar preferencia del sistema
			const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
			applyTheme(prefersDark ? 'dark' : 'light');
		}

		// Escuchar cambios en las preferencias del sistema
		window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
			if (!localStorage.getItem('theme')) {
				applyTheme(e.matches ? 'dark' : 'light');
			}
		});
	}
}

// Función para aplicar el tema
export function applyTheme(newTheme) {
	if (browser) {
		// Actualizar el store
		theme.set(newTheme);

		// Guardar en localStorage
		localStorage.setItem('theme', newTheme);

		// Aplicar al documento HTML
		document.documentElement.setAttribute('data-bs-theme', newTheme);

		// Aplicar clases específicas del tema
		if (newTheme === 'dark') {
			document.body.classList.add('theme-dark');
			document.body.classList.remove('theme-light');
		} else {
			document.body.classList.add('theme-light');
			document.body.classList.remove('theme-dark');
		}
	}
}

// Función para alternar el tema
export function toggleTheme() {
	theme.update((currentTheme) => {
		const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
		applyTheme(newTheme);
		return newTheme;
	});
}
