import { writable } from 'svelte/store';

// Verificar el tema guardado o la preferencia del sistema
function getInitialTheme() {
	if (typeof window === 'undefined') return 'light';

	// Verificar si hay un tema guardado en localStorage
	const savedTheme = localStorage.getItem('theme');
	if (savedTheme) {
		return savedTheme;
	}

	// Verificar la preferencia del sistema
	if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
		return 'dark';
	}

	// Por defecto, usar tema claro
	return 'light';
}

// Crear un store para el tema
export const theme = writable(getInitialTheme());

// Función para inicializar el tema
export function initTheme() {
	if (typeof window !== 'undefined') {
		theme.subscribe((value) => {
			document.documentElement.setAttribute('data-bs-theme', value);
			localStorage.setItem('theme', value);
		});
	}
}

// Función para cambiar el tema
export function toggleTheme() {
	theme.update((currentTheme) => {
		return currentTheme === 'light' ? 'dark' : 'light';
	});
}
