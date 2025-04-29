import type { PageLoad } from './$types';

export const load: PageLoad = ({ params }) => {
	return {
		slug: params.slug
	};
};

// Asegurar que la página se renderiza del lado del cliente
export const ssr = false;
