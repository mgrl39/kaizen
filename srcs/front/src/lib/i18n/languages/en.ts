import type { TranslationDictionary } from '../index';

const en: TranslationDictionary = {
	// Navbar and common
	cinemas: 'Our Cinema',
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
	required: 'required',
	emailAddress: 'Email Address',
	rememberMe: 'Remember me',
	guest: 'Guest',
	home: 'Home',
	toggleTheme: 'Toggle theme',
	selectLanguage: 'Select language',
	close: 'Close',
	cancel: 'Cancel',
	continue: 'Continue',
	adminAccessTitle: 'Admin Panel Access',
	adminAccessWarning: 'You are about to access the admin panel. This area is restricted to authorized personnel.',
	continueQuestion: 'Do you want to continue?',
	actors: 'Actors',

	// Login page
	loginError: 'An error occurred. Please try again.',
	signIn: 'Sign In',
	welcomeBack: 'Welcome back to Kaizen Cinema',
	emailOrUsername: 'Email or username',
	email: 'Email',
	password: 'Password',
	emailPlaceholder: 'user@email.com',
	passwordPlaceholder: '••••••••',
	showPassword: 'Show password',
	hidePassword: 'Hide password',
	dontHaveAccount: "Don't have an account?",
	signUpNow: 'Sign up now',

	// Register page
	createAccount: 'Create Account',
	joinKaizenCinema: 'Join Kaizen Cinema',
	fullName: 'Full Name',
	username: 'Username',
	confirmPassword: 'Confirm Password',
	birthdate: 'Birth Date',
	optional: 'optional',
	registering: 'Registering...',
	alreadyHaveAccount: 'Already have an account?',
	fullNamePlaceholder: 'E.g. John Smith',
	usernamePlaceholder: 'E.g. johnsmith',
	confirmPasswordPlaceholder: '••••••••',
	registerError: 'Registration could not be completed. Please try again.',
	registerSuccess: 'Registration successful. Redirecting...',

	// Categories
	exploreCategories: 'Explore categories',
	viewAll: 'View all',
	noCategories: 'No categories available',
	category_action: 'Action',
	category_comedy: 'Comedy',
	category_drama: 'Drama',

	// Featured Movies
	featuredMoviesTitle: 'Featured Movies',
	discoverMovies: 'Discover the best movies of the moment',
	noFeaturedMovies: 'No featured movies available',
	checkLater: 'Check back later for new movies',
	showFilms: 'View movies',
	browseAllMovies: 'Browse all movies',

	// Error pages
	pageNotFound: "The page you are looking for doesn't exist or has been moved.",
	pageNotFoundTitle: 'Page not found',
	accessDeniedTitle: 'Access denied',
	serverErrorTitle: 'Server error',
	genericErrorTitle: 'An error occurred',
	backToHome: 'Back to home',
	test: 'Test',

	// Cinema page
	ourCinema: 'Our Cinema',
	cinemaSubtitle: 'Discover the best place to enjoy the seventh art',
	contactInfo: 'Contact Information',
	address: 'Address',
	viewOnMap: 'View on map',
	phone: 'Phone',
	openingHours: 'Opening Hours',
	aboutUs: 'About Us',
	features: 'Features',
	services: 'Services',
	quickActions: 'Quick Actions',
	viewMovies: 'View Movies',
	buyTickets: 'Buy Tickets',
	contact: 'Contact',
	followUs: 'Follow Us',
	ourRooms: 'Our Rooms',
	capacity: 'Capacity',
	viewNowShowing: 'View Now Showing Movies',

	// Contact page
	contactTitle: 'Contact',
	contactSubtitle: 'We are here to help.',
	generalInfo: 'General Information',
	generalInfoDesc: 'For inquiries about our services, collaborations, or general information.',
	technicalSupport: 'Technical Support',
	technicalSupportDesc: 'To report technical issues, bugs, or suggest improvements to the platform.',
	sendEmail: 'Send email',
	createIssue: 'Create issue',
	quickContactForm: 'Quick Contact Form',
	yourName: 'Your name',
	subject: 'Subject',
	howCanWeHelp: 'How can we help you?',
	sending: 'Sending...',
	send: 'Send',
	thankYouMessage: 'Thank you! We will respond soon.',
	newMessage: 'New message',
	errorSendingMessage: 'Error sending message.',

	// Footer
	about: 'About Us',
	terms: 'Terms & Conditions',
	privacy: 'Privacy Policy',
	footerTagline: 'Your destination for the best cinematic experiences',
	enterEmail: 'Enter your email',
	subscribe: 'Subscribe',
	quickLinks: 'Quick Links',
	legal: 'Legal',
	contactUs: 'Contact Us',
	allRightsReserved: 'All rights reserved',
	paymentMethods: 'Payment Methods',

	// New translations
	experienceTheBest: 'Experience the best cinema with quality',
	and: 'and',

	// About page
	aboutTitle: 'About Kaizen Cinema',
	aboutSubtitle: 'Transforming the cinema experience since 2024',
	ourHistory: 'Our History',
	historyText1: 'Kaizen Cinema was born from a passion for cinema and innovation. Founded in 2024, we set out to revolutionize the way people experience cinema, combining cutting-edge technology with the traditional charm of movie theaters.',
	historyText2: 'Our name, "Kaizen", reflects our philosophy of continuous improvement, applying this Japanese principle to every aspect of our operation.',
	ourMission: 'Our Mission',
	missionText: 'To provide exceptional cinematic experiences that connect people with the art of cinema, using innovative technology and first-class service.',
	ourVision: 'Our Vision',
	visionText: 'To be the leader in cinema innovation, creating spaces where technology and tradition come together to offer memorable experiences to our customers.',
	leadDeveloper: 'Lead Developer',
	leadDeveloperBio: 'Full Stack Developer and main creator of Kaizen Cinema',
	collaborator: 'Collaborating Developer',
	collaboratorBio: 'Developer and key collaborator in the Kaizen Cinema project',
	moviesInTheater: 'Movies in Theater',
	satisfiedClients: 'Satisfied Clients',
	support247: '24/7 Support',
	readyForKaizen: 'Ready for the Kaizen experience?',
	viewMoviesButton: 'View Movies',

	// Helpdesk
	helpDesk: 'Help Desk',
	helpDeskDesc: 'Direct support and personalized assistance for all your inquiries.',
	previousSystem: 'Live Chat System',
	tawkExplanation: 'We use Tawk.to to provide real-time support. An agent will be available to help you during our business hours.',
	supportOptions: 'Support Options',
	emailSupport: 'Email Support',
	emailSupportDesc: 'Send us an email and we will respond within 24 hours.',
	githubIssues: 'Report on GitHub',
	githubIssuesDesc: 'Report technical issues or suggest improvements directly on our repository.',
	supportHours: 'Support Hours',
	supportHoursDesc: 'Monday to Friday: 9:00 AM - 6:00 PM (GMT+1)',
	backToContact: 'Back to Contact',
	getHelp: 'Get Help',
	chatWithSupport: 'Support Chat',
	chatWithSupportDesc: 'Need assistance? Our expert team is available 24/7 to address all your questions. Whether it\'s about bookings, movies, technical issues, or any other inquiry - we\'re here to help you in real-time. Click the chat button and connect instantly with our team!',
	chatMinimized: 'Chat minimized',
	chatMaximized: 'Chat maximized',

	// Home page
	heroSubtitle: 'Experience the magic of cinema on the best screen',
	heroCta: 'View Movies',
	servicesTitle: 'Our Services',
	servicesProjection4kTitle: '4K Projection',
	servicesProjection4kDesc: 'The best image quality',
	servicesDolbyAtmosTitle: 'Dolby Atmos',
	servicesDolbyAtmosDesc: 'Premium surround sound',
	servicesPremiumBarTitle: 'Premium Bar',
	servicesPremiumBarDesc: 'Quality snacks and drinks',
	servicesFreeParking: 'Free Parking',
	servicesFreeDesc: '3h with your movie ticket',

	// Services
	services: {
		title: 'Our Services',
		projection4k: {
			title: '4K Projection',
			desc: 'The best image quality'
		},
		dolbyAtmos: {
			title: 'Dolby Atmos',
			desc: 'Premium surround sound'
		},
		premiumBar: {
			title: 'Premium Bar',
			desc: 'Quality snacks and drinks'
		},
		freeParking: {
			title: 'Free Parking',
			desc: '3h with your movie ticket'
		}
	},

	// Banner titles and subtitles
	bannerMoviesTitle: 'Now Showing',
	bannerMoviesSubtitle: 'Enjoy our collection of {total} movies',
	bannerActorSubtitle: '{count} {count, plural, one {movie} other {movies}}',
	bannerCinemaTitle: 'Our Cinema',
	bannerCinemaSubtitle: 'Discover the best place to enjoy the seventh art',

	// Cinema features
	freeParking: 'Free Parking',
	cafeteria: 'Cafeteria',
	snackBar: 'Snack Bar',
	gameZone: 'Game Zone',
	accessibleFacilities: 'Accessible Facilities',
	errorLoadingMovies: 'Error loading movies:',
	unexpectedApiResponse: 'Unexpected API response format',
	previous: 'Previous',
	next: 'Next',

	// Room features
	surroundSound: 'Surround Sound',
	comfortableSeats: 'Comfortable Seats',
	imaxScreen: 'Giant IMAX Screen',
	dolbyAtmosSound: 'Dolby Atmos Sound',
	immersiveExperience: 'Immersive Experience',
	luxurySeats: 'Luxury Reclining Seats',
	personalizedService: 'Personalized Service',
	exclusiveMenu: 'Exclusive Menu',
	advanced3d: 'Advanced 3D Technology',
	premium3dGlasses: 'Premium 3D Glasses',
	compatible3d: '3D Compatible',
	imaxCertified: 'IMAX Certified',
	vipExperience: 'VIP Experience',
};

export default en;
