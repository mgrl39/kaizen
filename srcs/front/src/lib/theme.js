import { writable } from 'svelte/store';

// Verificar el tema guardado o la preferencia del sistema
function getInitialTheme() {
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
export const theme = writable('light'); // Valor inicial por defecto

// Función para inicializar el tema
export function initTheme() {
	if (typeof window !== 'undefined') {
		const initialTheme = getInitialTheme();
		theme.set(initialTheme);
		applyTheme(initialTheme);
	}
}

// Función para cambiar el tema
export function toggleTheme() {
	theme.update((currentTheme) => {
		const newTheme = currentTheme === 'light' ? 'dark' : 'light';
		applyTheme(newTheme);
		return newTheme;
	});
}

// Función para aplicar el tema
function applyTheme(themeName) {
	if (typeof window !== 'undefined') {
		document.documentElement.setAttribute('data-bs-theme', themeName);
		localStorage.setItem('theme', themeName);
	}
}
