import { error } from '@sveltejs/kit';
import type { PageLoad } from './$types';
import { API_URL } from '$lib/config';

export const load: PageLoad = async ({ params, fetch }) => {
    try {
        const response = await fetch(`${API_URL}/functions/${params.id}`);
        const result = await response.json();

        if (!response.ok || !result.success) {
            throw error(404, {
                message: result.message || 'No se encontró la función solicitada'
            });
        }

        return {
            function: result.data
        };
    } catch (e) {
        throw error(500, {
            message: 'Error al cargar los datos de la función'
        });
    }
}; 