/**
 * Interfaces para los modelos de datos de Kaizen Cinema
 */

// Interfaz para la película
export interface Movie {
	id: number;
	title: string;
	photo_url: string;
	synopsis?: string;
	release_date?: string;
	rating?: number;
	genre?: string;
	director?: string;
	duration?: number;
}

// Interfaz para el cine
export interface Cinema {
	id: number;
	name: string;
	address: string;
	photo_url: string;
	screens: number;
	city?: string;
	description?: string;
	has_parking?: boolean;
	has_food?: boolean;
	is_premium?: boolean;
	features?: string[];
	phone?: string;
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
}

// Interfaz para el usuario
export interface User {
	id: number;
	name: string;
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
