import { writable, derived } from 'svelte/store';
import es from './languages/es';
import en from './languages/en';

// Definir tipo para las traducciones
export type TranslationKey =
	| 'cinemas'
	| 'movies'
	| 'profile'
	| 'bookings'
	| 'notifications'
	| 'logout'
	| 'register'
	| 'login'
	| 'adminPanel'
	| 'loading'
	| 'unreadNotifications';

// Definir tipo para un diccionario de idioma
export type TranslationDictionary = Record<TranslationKey, string>;

// Definir tipo para todas las traducciones
export type Translations = {
	[lang: string]: TranslationDictionary;
};

// Lista de idiomas soportados
export const languages = ['es', 'en'];

// Store para el idioma actual
export const currentLanguage = writable<string>(
	// Intentar obtener el idioma del localStorage, o usar el idioma del navegador, o por defecto 'es'
	typeof window !== 'undefined'
		? localStorage.getItem('language') || navigator.language.split('-')[0] || 'es'
		: 'es'
);

// Cuando cambie el idioma, guardarlo en localStorage
currentLanguage.subscribe((lang) => {
	if (typeof window !== 'undefined') {
		localStorage.setItem('language', lang);
	}
});

// Diccionarios de traducciones - importados desde módulos separados
const translations: Translations = {
	en,
	es
};

// Función para acceder a las traducciones
export const t = derived(currentLanguage, ($currentLanguage) => {
	return (key: string): string => {
		// Verificamos que el diccionario del idioma actual existe
		if (!translations[$currentLanguage as keyof typeof translations]) {
			console.warn(`No translations found for language: ${$currentLanguage}`);
			return key;
		}

		// Buscamos la traducción
		const translation =
			translations[$currentLanguage as keyof typeof translations][key as TranslationKey];

		// Si no hay traducción, retornamos la clave
		if (!translation) {
			console.warn(`No translation found for key: ${key} in language: ${$currentLanguage}`);
			return key;
		}

		return translation;
	};
});
