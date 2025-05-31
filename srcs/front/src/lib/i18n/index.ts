import { derived, writable } from 'svelte/store';
import { browser } from '$app/environment';

export type LanguageCode = 'es' | 'en';

type LanguageMeta = {
	name: string;
	flag: string;
	translations: TranslationDictionary;
};

// Importar idiomas
import es from './languages/es';
import en from './languages/en';

// Definir tipo para las traducciones
export type TranslationKey =
	// Navbar and common
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
	| 'unreadNotifications'
	| 'required'

	// About page
	| 'aboutTitle'
	| 'aboutSubtitle'
	| 'ourHistory'
	| 'historyText1'
	| 'historyText2'
	| 'ourMission'
	| 'missionText'
	| 'ourVision'
	| 'visionText'
	| 'leadDeveloper'
	| 'leadDeveloperBio'
	| 'collaborator'
	| 'collaboratorBio'
	| 'moviesInTheater'
	| 'satisfiedClients'
	| 'support247'
	| 'readyForKaizen'
	| 'viewMoviesButton'

	// Footer
	| 'about'
	| 'terms'
	| 'privacy'
	| 'footerTagline'
	| 'enterEmail'
	| 'subscribe'
	| 'quickLinks'
	| 'legal'
	| 'contactUs'
	| 'address'
	| 'phone'
	| 'email'
	| 'allRightsReserved'
	| 'paymentMethods'

	// Contact page
	| 'contact'
	| 'contactTitle'
	| 'contactSubtitle'
	| 'generalInfo'
	| 'generalInfoDesc'
	| 'technicalSupport'
	| 'technicalSupportDesc'
	| 'sendEmail'
	| 'createIssue'
	| 'quickContactForm'
	| 'yourName'
	| 'subject'
	| 'howCanWeHelp'
	| 'sending'
	| 'send'
	| 'thankYouMessage'
	| 'newMessage'
	| 'errorSendingMessage'

	// Cinema page
	| 'ourCinema'
	| 'cinemaSubtitle'
	| 'contactInfo'
	| 'viewOnMap'
	| 'phone'
	| 'openingHours'
	| 'aboutUs'
	| 'features'
	| 'services'
	| 'quickActions'
	| 'viewMovies'
	| 'buyTickets'
	| 'followUs'
	| 'ourRooms'
	| 'capacity'
	| 'viewNowShowing'

	// Login page
	| 'loginError'
	| 'signIn'
	| 'welcomeBack'
	| 'emailOrUsername'
	| 'email'
	| 'password'
	| 'emailPlaceholder'
	| 'passwordPlaceholder'
	| 'showPassword'
	| 'hidePassword'
	| 'dontHaveAccount'
	| 'signUpNow'

	// Register page
	| 'createAccount'
	| 'joinKaizenCinema'
	| 'fullName'
	| 'username'
	| 'confirmPassword'
	| 'birthdate'
	| 'optional'
	| 'registering'
	| 'alreadyHaveAccount'
	| 'fullNamePlaceholder'
	| 'usernamePlaceholder'
	| 'confirmPasswordPlaceholder'
	| 'registerError'
	| 'registerSuccess'

	// Categories
	| 'exploreCategories'
	| 'viewAll'
	| 'noCategories'
	| 'category_action'
	| 'category_comedy'
	| 'category_drama'

	// Error pages
	| 'pageNotFound'
	| 'pageNotFoundTitle'
	| 'accessDeniedTitle'
	| 'serverErrorTitle'
	| 'genericErrorTitle'
	| 'backToHome'
	| 'test'
	| 'accessDeniedTitle'
	| 'serverErrorTitle'
	| 'genericErrorTitle'
	| 'backToHome'
	| 'test'
	| 'emailAddress'
	| 'rememberMe'
	| 'signUpNow';

// Definir tipo para un diccionario de idioma
export type TranslationDictionary = Record<TranslationKey, string>;

// Definir tipo para todas las traducciones
export type Translations = {
	[lang: string]: TranslationDictionary;
};

// Definir los idiomas disponibles
export const languages = {
	es: { name: 'Español', flag: '🇪🇸', translations: es },
	en: { name: 'English', flag: '🇬🇧', translations: en }
};

// Crear store para el idioma actual
const createLanguageStore = () => {
	// Detectar idioma inicial (navegador o localStorage)
	const getInitialLanguage = () => {
		if (!browser) return 'es'; // Valor por defecto en SSR

		const savedLanguage = localStorage.getItem('language');
		if (savedLanguage && savedLanguage in languages && typeof savedLanguage === 'string')
			return savedLanguage as LanguageCode;

		// Detectar idioma del navegador
		const browserLanguage = navigator.language.split('-')[0];
		return browserLanguage in languages ? (browserLanguage as LanguageCode) : 'es';
	};

	const { subscribe, set, update } = writable(getInitialLanguage());

	return {
		subscribe,
		set: (language: LanguageCode) => {
			if (!languages[language]) return;

			if (browser) {
				localStorage.setItem('language', language);
			}

			set(language);
		},
		// Método para cambiar al siguiente idioma disponible
		toggle: () => {
			update((currentLang: LanguageCode): LanguageCode => {
				const langKeys = Object.keys(languages);
				const currentIndex = langKeys.indexOf(currentLang);
				const nextIndex = (currentIndex + 1) % langKeys.length;
				const nextLang = langKeys[nextIndex];

				if (browser) {
					localStorage.setItem('language', nextLang);
				}

				return nextLang as LanguageCode;
			});
		}
	};
};

// Crear store para el idioma
export const language = createLanguageStore();

// Crear store derivado para las traducciones
export const translations = derived(
	language,
	($language) => languages[$language]?.translations || {}
);

// IMPORTANTE: Crear un store para t que se pueda usar con $t en los componentes
export const t = derived(
	translations,
	($translations) =>
		(key: TranslationKey, defaultValue: string = key) =>
			$translations[key] || defaultValue
);

// Función para obtener traducciones (para uso en JS)
export function getTranslation(key: TranslationKey) {
	let translation;

	const unsubscribe = translations.subscribe((trans) => {
		translation = trans[key] || key;
	});

	unsubscribe();

	return translation;
}

// Función para obtener traducciones con formato
export function tf(key: TranslationKey, values: Record<string, string | number> = {}) {
	let translation = key as string;

	const unsubscribe = translations.subscribe((trans) => {
		translation = trans[key] || key;
	});

	unsubscribe();

	// Reemplazar placeholders en la traducción
	if (typeof translation === 'string' && Object.keys(values).length > 0) {
		Object.entries(values).forEach(([key, value]) => {
			translation = translation.replace(new RegExp(`{${key}}`, 'g'), String(value));
		});
	}

	return translation;
}

// Función para obtener traducciones con formato y pluralización
export function tp(
	key: TranslationKey,
	count: number,
	values: Record<string, string | number> = {}
) {
	// Determinar si usar singular o plural basado en count
	const actualKey = count === 1 ? key : `${key}_plural`;

	// Usar tf con el count incluido en los valores
	return tf(actualKey as TranslationKey, { count, ...values });
}

// Función para inicializar i18n en la aplicación
export function initI18n() {
	if (browser) {
		// Detectar cambios de idioma en la URL o localStorage
		const urlParams: URLSearchParams = new URLSearchParams(window.location.search);
		const urlLang: string | null = urlParams.get('lang');
		if (urlLang && (urlLang === 'es' || urlLang === 'en')) language.set(urlLang);
	}
}
