import type { TranslationDictionary } from '../../index';

const en: TranslationDictionary = {
	// Navbar
	cinemas: 'Cinemas',
	movies: 'Movies',
	profile: 'My Profile',
	bookings: 'My Bookings',
	notifications: 'Notifications',
	logout: 'Logout',
	register: 'Register',
	login: 'Login',
	adminPanel: 'Admin Dashboard',
	loading: 'Loading...',
	unreadNotifications: 'unread notifications',

	// Home page
	featuredMoviesError: 'Failed to load featured movies:',
	category_action: 'Action',
	category_comedy: 'Comedy',
	category_drama: 'Drama',

	// FeaturedMovies component
	featuredMoviesTitle: 'Featured Movies',
	noFeaturedMovies: 'No featured movies available at this time.',

	// HeroBanner component
	heroBannerTitle: 'Kaizen Cinema',
	heroBannerSubtitle:
		'The best cinematic experience with the latest movies and most comfortable theaters.',
	viewMovies: 'View movies',
	exploreCinemas: 'Explore cinemas',

	// Register page
	createAccount: 'Create Account',
	registerSuccess: 'Registration successful! Redirecting to login...',
	username: 'Username',
	fullName: 'Full Name',
	email: 'Email',
	password: 'Password',
	confirmPassword: 'Confirm Password',
	birthdate: 'Birth Date (optional)',
	optional: 'optional',
	processing: 'Processing...',
	alreadyHaveAccount: 'Already have an account?',
	joinKaizenCinema: 'Join Kaizen Cinema',

	// Register validation errors
	usernameRequired: 'Username is required',
	usernameMinLength: 'Username must be at least 3 characters',
	nameRequired: 'Name is required',
	emailRequired: 'Email is required',
	emailInvalid: 'Email format is invalid',
	passwordRequired: 'Password is required',
	passwordMinLength: 'Password must be at least 8 characters',
	passwordsDontMatch: 'Passwords do not match',
	birthdateInvalid: 'Birth date must be earlier than today',
	formErrors: 'Please correct the errors in the form',
	registerError: 'Registration error',
	connectionError: 'Connection error with the server',
	unknownError: 'Unknown error during registration',
	showFilms: 'Show Films',

	// Login page
	welcomeBack: 'Welcome back to Kaizen Cinema',
	emailOrUsername: 'Email or username',
	emailPlaceholder: 'user@email.com',
	loggingIn: 'Logging in...',
	noAccount: "Don't have an account?"
};

export default en;
