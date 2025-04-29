/**
 * Interfaces para los modelos de datos de Kaizen Cinema
 */

// Interfaz para la película
export interface Movie {
	id: number;
	title: string;
	description?: string;
	year?: number;
	genre?: string;
	rating?: number;
	photo_url?: string;
	synopsis: string;
	duration: number;
	release_date: string;
	slug: string;
	created_at: string;
	updated_at: string;
}

// Interfaz para el cine
export interface Cinema {
	id: number;
	name: string;
	location: string;
	address?: string;
	phone?: string;
	image_url?: string;
	screens: number;
	city?: string;
	description?: string;
	has_parking?: boolean;
	has_food?: boolean;
	is_premium?: boolean;
	features?: string[];
}

// Interfaz para categoría
export interface Category {
	name: string;
	icon: string;
	color: string;
	gradient: string;
}

// Interfaz para el elemento de navegación
export interface NavItem {
	url: string;
	icon: string;
	text: string;
	divider?: boolean;
	action?: string;
}

// Interfaz para el usuario
export interface User {
	id: number;
	name: string;
	username: string;
	email: string;
	role: string;
}

// Interfaces para las respuestas de API
export interface ApiResponse<T> {
	success: boolean;
	data?: T;
	message?: string;
	errors?: Record<string, string[]>;
}

// Interfaz para credenciales de login
export interface LoginCredentials {
	email: string;
	password: string;
	remember?: boolean;
}

// Interfaz para reserva
export interface Booking {
	id: number;
	movie_id: number;
	cinema_id: number;
	showtime_id: number;
	user_id: number;
	seats: string[];
	total_amount: number;
	status: string;
	created_at: string;
}
