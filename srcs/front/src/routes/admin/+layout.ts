import type { LayoutLoad } from './$types';

// PageLoad para rutas admin
export const load: LayoutLoad = async ({ url }) => {
	return {
		currentPath: url.pathname
	};
};
