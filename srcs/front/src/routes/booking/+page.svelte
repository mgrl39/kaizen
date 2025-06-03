<!-- BookingPage.svelte -->
<script lang="ts">
    import { onMount } from 'svelte';
    import { goto } from '$app/navigation';
    import { API_URL } from '$lib/config';
    import SeatMap from '$lib/components/SeatMap.svelte';
    import type { Buyer } from '$lib/types';
    
    export let data;
    const functionId = data.functionId;

    let functionData: any = null;
    let selectedSeats: string[] = [];
    let error = '';
    let isSubmitting = false;
    let seatsData: any[] = [];
    let rows = 0;
    let seatsPerRow = 0;
    let loading = true;
    let success = false;
    let ticketUrl: string | null = null;
    
    let buyer: Buyer = {
        name: '',
        email: '',
        phone: ''
    };
    
    let occupiedSeats: string[] = [];
    
    // Estados del proceso de reserva
    const STEPS = {
        SEATS: 'seats',
        CONTACT: 'contact',
        PAYMENT: 'payment',
        CONFIRMATION: 'confirmation'
    };
    
    let currentStep = STEPS.SEATS;
    
    // Información de pago
    let paymentInfo = {
        number: '',
        name: '',
        expiry: '',
        cvv: ''
    };
    
    onMount(async () => {
        try {
            // Cargar datos de la función
            const functionResponse = await fetch(`${API_URL}/functions/${functionId}`);
            if (!functionResponse.ok) throw new Error('Error al cargar la función');
            const functionResult = await functionResponse.json();
            if (!functionResult.success) throw new Error(functionResult.message);
            functionData = functionResult.data;

            // Cargar datos de asientos
            const seatsResponse = await fetch(`${API_URL}/functions/${functionId}/seats`);
            if (!seatsResponse.ok) throw new Error('Error al cargar los asientos');
            const seatsResult = await seatsResponse.json();
            if (!seatsResult.success) throw new Error(seatsResult.message);
            
            seatsData = seatsResult.data;
            
            if (Array.isArray(seatsData) && seatsData.length > 0) {
                rows = seatsData.length;
                seatsPerRow = seatsData[0].length;
                occupiedSeats = seatsData.flat()
                    .filter(seat => seat?.is_occupied)
                    .map(seat => seat.id.toString());
            }

            // Cargar datos del usuario si está autenticado
            const token = localStorage.getItem('token');
            if (token) {
                const userResponse = await fetch(`${API_URL}/profile`, {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });
                const userData = await userResponse.json();
                if (userData.success) {
                    buyer = {
                        name: userData.user?.name || '',
                        email: userData.user?.email || '',
                        phone: userData.user?.phone || ''
                    };
                }
            }

            loading = false;
        } catch (e: any) {
            error = e.message;
            loading = false;
        }
    });
    
    async function handleBooking() {
        if (isSubmitting) return;
        isSubmitting = true;
        error = '';

        try {
            if (!selectedSeats.length) throw new Error('Selecciona al menos un asiento');
            if (!buyer.name || !buyer.email || !buyer.phone) {
                throw new Error('Completa todos los datos de contacto');
            }

            const token = localStorage.getItem('token');
            const headers: Record<string, string> = {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            };
            if (token) headers['Authorization'] = `Bearer ${token}`;

            const response = await fetch(`${API_URL}/bookings`, {
                method: 'POST',
                headers,
                body: JSON.stringify({
                    function_id: functionId,
                    seats: selectedSeats,
                    buyer
                })
            });

            const result = await response.json();

            if (!response.ok) {
                if (response.status === 401 && result.message?.toLowerCase().includes('invalid token')) {
                    goto('/login');
                    throw new Error('Inicia sesión para continuar');
                }
                throw new Error(result.message || 'Error al realizar la reserva');
            }

            if (result.success && result.data?.booking?.uuid) {
                success = true;
                ticketUrl = result.data.ticket.download_url;
                localStorage.setItem('lastBookingEmail', buyer.email);
                currentStep = STEPS.CONFIRMATION;
            }
        } catch (e: any) {
            error = e.message;
            success = false;
        } finally {
            isSubmitting = false;
        }
    }

    function handleSeatsChange(event: CustomEvent<{seats: string[]}>) {
        selectedSeats = event.detail?.seats || [];
        error = '';
    }

    function formatTime(time: string): string {
        return time || 'Hora no disponible';
    }

    function goToStep(step: string) {
        if (step === STEPS.CONTACT && !selectedSeats.length) {
            error = 'Selecciona al menos un asiento';
            return;
        }
        if (step === STEPS.PAYMENT && (!buyer.name || !buyer.email || !buyer.phone)) {
            error = 'Completa todos los datos de contacto';
            return;
        }
        currentStep = step;
        error = '';
    }
</script>

{#if loading}
    <div class="flex justify-center items-center min-h-[400px]">
        <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-primary"></div>
    </div>
{:else if error}
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
        <span class="block sm:inline">{error}</span>
    </div>
{:else if !functionData}
    <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative" role="alert">
        <span class="block sm:inline">No se encontraron datos de la función</span>
    </div>
{:else}
    <div class="container mx-auto px-4 py-8">
        <!-- Barra de progreso -->
        <div class="mb-8">
            <div class="h-2 bg-gray-200 rounded-full">
                <div class="h-full bg-primary rounded-full transition-all duration-300"
                     style="width: {currentStep === STEPS.SEATS ? '25%' : 
                                   currentStep === STEPS.CONTACT ? '50%' : 
                                   currentStep === STEPS.PAYMENT ? '75%' : '100%'}">
                </div>
            </div>
        </div>

        <!-- Información de la película -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-2xl font-bold mb-4">{functionData.movie?.title || 'Sin título'}</h2>
            <div class="flex flex-wrap gap-4 text-gray-600">
                <span class="flex items-center">
                    <i class="bi bi-building mr-2"></i> 
                    {functionData.room?.name || 'Sin sala'}
                </span>
                <span class="flex items-center">
                    <i class="bi bi-calendar mr-2"></i> 
                    {new Date(functionData.date).toLocaleDateString('es-ES')}
                </span>
                <span class="flex items-center">
                    <i class="bi bi-clock mr-2"></i> 
                    {formatTime(functionData.time)}
                </span>
                {#if functionData.is_3d}
                    <span class="bg-primary text-white px-2 py-1 rounded text-sm">3D</span>
                {/if}
            </div>
        </div>

        <!-- Resumen de la selección -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h3 class="text-xl font-semibold mb-4">Resumen de la reserva</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h4 class="font-medium mb-2">Asientos seleccionados:</h4>
                    {#if selectedSeats.length === 0}
                        <p class="text-gray-500">No has seleccionado asientos</p>
                    {:else}
                        <div class="flex flex-wrap gap-2">
                            {#each selectedSeats as seatId}
                                {#if Array.isArray(seatsData)}
                                    {#each seatsData as row}
                                        {#each row as seat}
                                            {#if seat?.id?.toString() === seatId}
                                                <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm">
                                                    Fila {seat.row} - Asiento {seat.number}
                                                </span>
                                            {/if}
                                        {/each}
                                    {/each}
                                {/if}
                            {/each}
                        </div>
                    {/if}
                </div>
                
                <div>
                    <h4 class="font-medium mb-2">Total:</h4>
                    <span class="text-2xl font-bold text-primary">
                        {(selectedSeats.length * (functionData.room?.price || 8)).toFixed(2)}€
                    </span>
                </div>
            </div>
        </div>

        <!-- Contenido según el paso actual -->
        <div class="bg-white rounded-lg shadow-md p-6">
            {#if currentStep === STEPS.SEATS}
                <h3 class="text-xl font-semibold mb-6">Selección de Asientos</h3>
                {#if Array.isArray(seatsData)}
                    <SeatMap {rows} {seatsPerRow} {selectedSeats} {occupiedSeats} {seatsData}
                        on:seatsChange={handleSeatsChange} />
                {/if}

            {:else if currentStep === STEPS.CONTACT}
                <h3 class="text-xl font-semibold mb-6">Datos de Contacto</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Nombre completo *
                        </label>
                        <input type="text" 
                               bind:value={buyer.name}
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary"
                               placeholder="Ej: Juan Pérez" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Email *
                        </label>
                        <input type="email" 
                               bind:value={buyer.email}
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary"
                               placeholder="Ej: juan@email.com" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Teléfono *
                        </label>
                        <input type="tel" 
                               bind:value={buyer.phone}
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary"
                               placeholder="Ej: 666555444" />
                    </div>
                </div>

            {:else if currentStep === STEPS.PAYMENT}
                <h3 class="text-xl font-semibold mb-6">Método de Pago</h3>
                <div class="max-w-md mx-auto">
                    <!-- Tarjeta visual -->
                    <div class="bg-gradient-to-r from-primary to-primary-dark text-white rounded-xl p-6 mb-6 shadow-lg">
                        <div class="text-xl mb-8">{paymentInfo.number || '•••• •••• •••• ••••'}</div>
                        <div class="flex justify-between">
                            <div class="uppercase text-sm">{paymentInfo.name || 'TITULAR'}</div>
                            <div class="text-sm">{paymentInfo.expiry || 'MM/YY'}</div>
                        </div>
                    </div>

                    <!-- Formulario de pago -->
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Número de tarjeta *
                            </label>
                            <input type="text"
                                   bind:value={paymentInfo.number}
                                   class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary"
                                   placeholder="1234 5678 9012 3456"
                                   maxlength="19"
                                   on:input={(e) => {
                                       e.target.value = e.target.value
                                           .replace(/\s/g, '')
                                           .replace(/(\d{4})/g, '$1 ')
                                           .trim();
                                   }} />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Titular de la tarjeta *
                            </label>
                            <input type="text"
                                   bind:value={paymentInfo.name}
                                   class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary"
                                   placeholder="NOMBRE APELLIDOS" />
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Caducidad *
                                </label>
                                <input type="text"
                                       bind:value={paymentInfo.expiry}
                                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary"
                                       placeholder="MM/YY"
                                       maxlength="5"
                                       on:input={(e) => {
                                           e.target.value = e.target.value
                                               .replace(/\D/g, '')
                                               .replace(/(\d{2})(\d)/, '$1/$2');
                                       }} />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    CVV *
                                </label>
                                <input type="text"
                                       bind:value={paymentInfo.cvv}
                                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary"
                                       placeholder="123"
                                       maxlength="3" />
                            </div>
                        </div>
                    </div>
                </div>

            {:else if currentStep === STEPS.CONFIRMATION}
                <div class="text-center">
                    <div class="text-5xl text-green-500 mb-4">
                        <i class="bi bi-check-circle"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-4">¡Reserva Confirmada!</h3>
                    
                    {#if ticketUrl}
                        <div class="max-w-xs mx-auto mb-8">
                            <img src={ticketUrl} alt="QR Code" class="w-full" />
                        </div>
                        <p class="text-gray-600 mb-6">
                            Muestra este código QR en la entrada del cine
                        </p>
                        <div class="space-y-4">
                            <a href={ticketUrl}
                               class="inline-block bg-primary text-white px-6 py-2 rounded-lg hover:bg-primary-dark transition-colors"
                               download>
                                <i class="bi bi-download mr-2"></i>
                                Descargar QR
                            </a>
                            <p class="text-sm text-gray-500">
                                <i class="bi bi-info-circle mr-2"></i>
                                También hemos enviado el código QR a tu email ({buyer.email})
                            </p>
                        </div>
                    {/if}
                </div>
            {/if}

            <!-- Navegación entre pasos -->
            <div class="flex justify-between mt-8">
                {#if currentStep !== STEPS.SEATS && currentStep !== STEPS.CONFIRMATION}
                    <button class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
                            on:click={() => currentStep = STEPS[Object.keys(STEPS)[Object.values(STEPS).indexOf(currentStep) - 1]]}>
                        <i class="bi bi-arrow-left mr-2"></i> Volver
                    </button>
                {:else}
                    <div></div>
                {/if}

                {#if currentStep !== STEPS.CONFIRMATION}
                    {#if currentStep === STEPS.PAYMENT}
                        <button class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors disabled:opacity-50"
                                disabled={isSubmitting}
                                on:click={handleBooking}>
                            {#if isSubmitting}
                                <span class="inline-block animate-spin mr-2">⌛</span>
                                Procesando...
                            {:else}
                                Confirmar y Pagar
                            {/if}
                        </button>
                    {:else}
                        <button class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors"
                                on:click={() => currentStep = STEPS[Object.keys(STEPS)[Object.values(STEPS).indexOf(currentStep) + 1]]}>
                            Continuar <i class="bi bi-arrow-right ml-2"></i>
                        </button>
                    {/if}
                {/if}
            </div>
        </div>
    </div>
{/if}

<style>
    /* Asegúrate de que estos estilos sean compatibles con tu configuración de Tailwind */
    :global(.bi) {
        line-height: 1;
    }
</style> 