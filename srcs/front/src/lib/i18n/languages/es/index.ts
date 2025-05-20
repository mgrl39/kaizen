import type { TranslationDictionary } from '../../index';

const es: TranslationDictionary = {
	// Navbar
	cinemas: 'Cines',
	movies: 'Películas',
	profile: 'Mi Perfil',
	bookings: 'Mis Reservas',
	notifications: 'Notificaciones',
	logout: 'Cerrar Sesión',
	register: 'Registrarse',
	login: 'Iniciar Sesión',
	adminPanel: 'Panel Admin',
	loading: 'Cargando...',
	unreadNotifications: 'notificaciones sin leer',

	// Página principal
	featuredMoviesError: 'No se pudieron cargar las películas destacadas:',
	category_action: 'Acción',
	category_comedy: 'Comedia',
	category_drama: 'Drama',

	// Componente FeaturedMovies
	featuredMoviesTitle: 'Películas Destacadas',
	noFeaturedMovies: 'No hay películas destacadas disponibles en este momento.',

	// Componente HeroBanner
	heroBannerTitle: 'Kaizen Cinema',
	heroBannerSubtitle:
		'La mejor experiencia cinematográfica con las últimas películas y los cines más confortables.',
	viewMovies: 'Ver películas',
	exploreCinemas: 'Explorar cines',

	// Register page
	createAccount: 'Crear cuenta',
	registerSuccess: '¡Registro exitoso! Redireccionando al inicio de sesión...',
	username: 'Nombre de usuario',
	fullName: 'Nombre completo',
	email: 'Email',
	password: 'Contraseña',
	confirmPassword: 'Confirmar contraseña',
	birthdate: 'Fecha de nacimiento (opcional)',
	optional: 'opcional',
	processing: 'Procesando...',
	alreadyHaveAccount: '¿Ya tienes cuenta?',
	joinKaizenCinema: 'Únete a Kaizen Cinema',

	// Register validation errors
	usernameRequired: 'El nombre de usuario es obligatorio',
	usernameMinLength: 'El nombre de usuario debe tener al menos 3 caracteres',
	nameRequired: 'El nombre es obligatorio',
	emailRequired: 'El email es obligatorio',
	emailInvalid: 'El formato del email no es válido',
	passwordRequired: 'La contraseña es obligatoria',
	passwordMinLength: 'La contraseña debe tener al menos 8 caracteres',
	passwordsDontMatch: 'Las contraseñas no coinciden',
	birthdateInvalid: 'La fecha de nacimiento debe ser anterior a hoy',
	formErrors: 'Por favor corrige los errores en el formulario',
	registerError: 'Error en el registro',
	connectionError: 'Error de conexión con el servidor',
	unknownError: 'Error desconocido durante el registro',
	showFilms: 'Ver peliculas',

	// Login page
	welcomeBack: 'Bienvenido de nuevo a Kaizen Cinema',
	emailOrUsername: 'Email o nombre de usuario',
	emailPlaceholder: 'usuario@email.com',
	loggingIn: 'Iniciando sesión...',
	noAccount: '¿No tienes una cuenta?',

	// Páginas de error
	pageNotFound: 'La página que buscas no existe o ha sido movida.',
	pageNotFoundTitle: 'Página no encontrada',
	accessDeniedTitle: 'Acceso denegado',
	serverErrorTitle: 'Error del servidor',
	genericErrorTitle: 'Ha ocurrido un error',
	backToHome: 'Volver al inicio'
};

export default es;
