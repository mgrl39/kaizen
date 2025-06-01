import { writable } from 'svelte/store';
import { browser } from '$app/environment';

// Create theme store
export const theme = writable('light');

// Get initial theme - can be used server-side
export function getInitialTheme() {
	if (!browser) return 'light';
	
	const savedTheme = localStorage.getItem('theme');
	if (savedTheme) return savedTheme;
	
	return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
}

// Initialize theme system
export function initTheme() {
	if (browser) {
		const currentTheme = getInitialTheme();
		applyTheme(currentTheme);

		// Listen for system theme changes
		window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
			if (!localStorage.getItem('theme')) {
				applyTheme(e.matches ? 'dark' : 'light');
			}
		});
	}
}

// Apply theme
export function applyTheme(newTheme) {
	if (browser) {
		theme.set(newTheme);
		localStorage.setItem('theme', newTheme);
		document.documentElement.setAttribute('data-bs-theme', newTheme);
		document.documentElement.classList.remove('theme-dark', 'theme-light');
		document.documentElement.classList.add(`theme-${newTheme}`);
	}
}

// Toggle theme
export function toggleTheme() {
	theme.update((currentTheme) => {
		const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
		applyTheme(newTheme);
		return newTheme;
	});
}

// Script to be inlined in HTML head
export const themeScript = `
	(function() {
		function getTheme() {
			const savedTheme = localStorage.getItem('theme');
			if (savedTheme) return savedTheme;
			return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
		}
		
		const theme = getTheme();
		document.documentElement.setAttribute('data-bs-theme', theme);
		document.documentElement.classList.add('theme-' + theme);
	})();
`;
