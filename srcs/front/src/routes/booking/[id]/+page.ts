import { error } from '@sveltejs/kit';
import type { PageLoad } from './$types';
import { API_URL } from '$lib/config';

export const load: PageLoad = async ({ params, fetch }) => {
    try {
        // Obtener datos de la función
        const response = await fetch(`${API_URL}/functions/${params.id}`, {
            method: 'GET',
            credentials: 'include',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });

        if (!response.ok) {
            if (response.status === 404) {
                throw error(404, {
                    message: 'No se encontró la función solicitada'
                });
            }
            throw error(response.status, {
                message: 'Error al cargar los datos de la función'
            });
        }

        const result = await response.json();

        if (!result.success) {
            throw error(400, {
                message: result.message || 'Error al cargar los datos de la función'
            });
        }

        // Verificar que los datos necesarios estén presentes
        if (!result.data || !result.data.movie || !result.data.room) {
            throw error(500, {
                message: 'Los datos de la función están incompletos'
            });
        }

        return {
            functionId: params.id,
            functionData: result.data
        };
    } catch (e: any) {
        console.error('Error loading booking page:', e);
        
        // Si ya es un error de SvelteKit, lo propagamos
        if (e?.status) throw e;
        
        // Si es otro tipo de error, devolvemos 500
        throw error(500, {
            message: 'Error al cargar los datos de la función'
        });
    }
}; 