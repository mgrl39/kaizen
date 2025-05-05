import { API_URL } from '$lib/config';

/** @type {import('./$types').PageLoad} */
export function load({ url }) {
	const searchQuery = url.searchParams.get('search') || '';
	const locationFilter = url.searchParams.get('location') || '';

	// Devolvemos directamente los parámetros de búsqueda sin hacer fetch
	// El componente se encargará de hacer el fetch
	return {
		searchParams: {
			search: searchQuery,
			location: locationFilter
		},
		searchQuery,
		locationFilter
	};
}
