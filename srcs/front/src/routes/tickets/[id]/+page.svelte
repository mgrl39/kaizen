<script lang="ts">
    import { onMount } from 'svelte';
    import { API_URL } from '$lib/config';
    import DigitalTicket from '$lib/components/DigitalTicket.svelte';
    import { page } from '$app/stores';
    import { goto } from '$app/navigation';

    let booking: any = null;
    let error: string | null = null;
    let loading = true;
    let debugInfo: any = null;

    onMount(async () => {
        try {
            const token = localStorage.getItem('token');
            if (!token) {
                error = 'Debe iniciar sesión para ver esta entrada';
                goto('/login');
                return;
            }

            const bookingId = $page.params.id;
            if (!bookingId) {
                error = 'ID de reserva no válido';
                return;
            }

            console.log('Intentando cargar la reserva:', bookingId);
            console.log('URL de la API:', `${API_URL}/bookings/${bookingId}`);

            // Intentar cargar la reserva
            const response = await fetch(`${API_URL}/bookings/${bookingId}`, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            });
            
            // Guardar información de depuración
            debugInfo = {
                status: response.status,
                statusText: response.statusText,
                headers: Object.fromEntries(response.headers.entries())
            };

            console.log('Respuesta de la API:', debugInfo);
            
            if (response.status === 401) {
                // Token inválido, redirigir al login
                localStorage.removeItem('token');
                error = 'Sesión expirada. Por favor, inicie sesión nuevamente.';
                goto('/login');
                return;
            }
            
            if (!response.ok) {
                const errorData = await response.json().catch(() => null);
                const errorMessage = errorData?.message || `Error ${response.status}: ${response.statusText}`;
                throw new Error(errorMessage);
            }

            const data = await response.json();
            console.log('Datos recibidos:', data);

            if (!data.success) {
                throw new Error(data.message || 'Error al cargar la reserva');
            }

            booking = data.data;
            console.log('Datos de la reserva:', booking);

            // Verificar que la reserva tenga todos los datos necesarios
            if (!booking?.function?.movie?.title || !booking?.seats?.length) {
                throw new Error('Datos de la reserva incompletos');
            }

        } catch (e: any) {
            console.error('Error completo:', e);
            error = e.message || 'Error al cargar la reserva';
        } finally {
            loading = false;
        }
    });

    // Función para volver a la página anterior
    const goBack = () => {
        window.history.back();
    };
</script>

<svelte:head>
    <title>Entrada - Kaizen Cinema</title>
</svelte:head>

<div class="container mx-auto px-4 py-8">
    <button 
        class="mb-4 flex items-center text-gray-600 hover:text-primary transition-colors"
        on:click={goBack}
    >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
        </svg>
        Volver
    </button>

    <div class="max-w-3xl mx-auto">
        {#if loading}
            <div class="flex justify-center items-center min-h-[400px]">
                <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-primary"></div>
            </div>
        {:else if error}
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">Error:</strong>
                <span class="block sm:inline">{error}</span>
                
                {#if debugInfo}
                    <div class="mt-4 text-sm">
                        <details>
                            <summary class="cursor-pointer">Información de depuración</summary>
                            <pre class="mt-2 bg-red-50 p-2 rounded">
                                Status: {debugInfo.status} {debugInfo.statusText}
                                Headers: {JSON.stringify(debugInfo.headers, null, 2)}
                            </pre>
                        </details>
                    </div>
                {/if}
            </div>
        {:else if booking}
            <DigitalTicket {booking} />
        {:else}
            <div class="text-center py-8">
                <p class="text-gray-600">No se encontró la reserva</p>
            </div>
        {/if}
    </div>
</div>

<style>
    pre {
        white-space: pre-wrap;
        word-wrap: break-word;
    }

    .container {
        max-width: 1200px;
    }

    button {
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        transition: all 0.2s;
    }

    button:hover {
        opacity: 0.8;
    }
</style>