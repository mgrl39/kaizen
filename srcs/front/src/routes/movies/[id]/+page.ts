import type { PageLoad } from './$types';

// Esta función no realiza carga de datos del lado del servidor,
// pero configura los parámetros de la página para que estén disponibles
export const load: PageLoad = ({ params }) => {
	return {
		id: params.id
	};
};

// Asegurar que la página se renderiza del lado del cliente
export const ssr = false;
