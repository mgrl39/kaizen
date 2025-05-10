import type { NavItem } from '$lib/types';
import { onMount } from 'svelte';
import { goto } from '$app/navigation';
import { API_URL } from '$lib/config';
import { currentLanguage, languages } from '$lib/i18n';
import { get } from 'svelte/store';

export function setupNavbar() {
	const navItems: NavItem[] = [
		{ url: '/cinemas', icon: 'building', text: 'Cines' },
		{ url: '/movies', icon: 'film', text: 'Películas' }
	];

	const userMenu: NavItem[] = [
		{ url: '/profile', icon: 'person', text: 'Mi Perfil' },
		{ url: '/bookings', icon: 'ticket', text: 'Mis Reservas' },
		{ divider: true, url: '', icon: '', text: '' },
		{ url: '#', icon: 'box-arrow-right', text: 'Cerrar Sesión', action: 'logout' }
	];

	let isAuthenticated: boolean = false;
	let userName: string = 'Usuario';
	let scrolled = false;
	let loadingProfile: boolean = true;

	// Verificar y establecer idioma predeterminado
	function setupDefaultLanguage() {
		if (!languages.includes(get(currentLanguage))) {
			// Si el idioma no es válido, usar español como predeterminado
			currentLanguage.set('es');
		}
	}

	async function fetchProfile() {
		const token = localStorage.getItem('token');
		if (!token) {
			isAuthenticated = false;
			loadingProfile = false;
			return;
		}
		try {
			const response = await fetch(`${API_URL}/profile`, {
				headers: {
					Authorization: `Bearer ${token}`,
					Accept: 'application/json'
				}
			});
			const data = await response.json();
			if (response.ok && data.success) {
				isAuthenticated = true;
				userName = data.user.name || data.user.username;
			} else {
				isAuthenticated = false;
				localStorage.removeItem('token');
			}
		} catch {
			isAuthenticated = false;
			localStorage.removeItem('token');
		} finally {
			loadingProfile = false;
		}
	}

	async function handleLogout(event: Event) {
		event.preventDefault();
		const token = localStorage.getItem('token');
		if (token) {
			try {
				await fetch(`${API_URL}/logout`, {
					method: 'POST',
					headers: {
						Authorization: `Bearer ${token}`,
						Accept: 'application/json'
					}
				});
			} catch (e) {
				// Ignorar errores de red
			}
			localStorage.removeItem('token');
			isAuthenticated = false;
			userName = 'Usuario';
			goto('/login');
		}
	}

	function setupScrollListener() {
		const handleScroll = () => {
			scrolled = window.scrollY > 20;
		};
		window.addEventListener('scroll', handleScroll);
		return () => {
			window.removeEventListener('scroll', handleScroll);
		};
	}

	return {
		navItems,
		userMenu,
		isAuthenticated,
		userName,
		scrolled,
		loadingProfile,
		fetchProfile,
		handleLogout,
		setupScrollListener,
		setupDefaultLanguage
	};
}
