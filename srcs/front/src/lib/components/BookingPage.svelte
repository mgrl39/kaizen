<!-- BookingPage.svelte -->
<script lang="ts">
    import { onMount } from 'svelte';
    import { goto } from '$app/navigation';
    import { API_URL } from '$lib/config';
    import SeatMap from './SeatMap.svelte';
    import ManualSeatSelection from './ManualSeatSelection.svelte';
    import type { Buyer } from '$lib/types';
    
    export let functionId: string;

    let functionData: any = null;
    let selectedSeats: string[] = [];
    let selectionMethod: 'map' | 'manual' = 'map';
    let error = '';
    let isSubmitting = false;
    let rawSeatsData: any = null;
    let rawFunctionData: any = null;
    
    let buyer: Buyer = {
        name: '',
        email: '',
        phone: ''
    };
    
    let occupiedSeats: string[] = [];
    let seatsData: any[] = [];
    let rows = 0;
    let seatsPerRow = 0;
    let loading = true;
    let success = false;
    let ticketUrl: string | null = null;
    
    // Definir los pasos del proceso
    const STEPS = {
        SEATS: 'seats',
        CONTACT: 'contact',
        PAYMENT: 'payment',
        SUMMARY: 'summary',
        CONFIRMATION: 'confirmation'
    };
    
    let currentStep = STEPS.SEATS;
    
    // Añadir estado para la tarjeta
    let cardInfo = {
        number: '',
        name: '',
        expiry: '',
        cvv: ''
    };
    
    // Cargar datos de la función y del usuario
    onMount(async () => {
        try {
            // Cargar datos de la función
            const functionResponse = await fetch(`${API_URL}/functions/${functionId}`);
            if (!functionResponse.ok) {
                throw new Error('Error cargando datos de la función');
            }
            const functionResult = await functionResponse.json();
            if (!functionResult.success) {
                throw new Error(functionResult.message || 'Error cargando datos de la función');
            }
            functionData = functionResult.data;

            // Cargar datos de asientos
            const seatsResponse = await fetch(`${API_URL}/functions/${functionId}/seats`);
            if (!seatsResponse.ok) {
                throw new Error('Error cargando datos de asientos');
            }
            const seatsResult = await seatsResponse.json();
            if (!seatsResult.success) {
                throw new Error(seatsResult.message || 'Error cargando datos de asientos');
            }
            
            // Procesar los datos de asientos
            seatsData = seatsResult.data;
            
            // Calcular filas y asientos por fila
            if (Array.isArray(seatsData) && seatsData.length > 0) {
                rows = seatsData.length; // Número de filas
                seatsPerRow = seatsData[0].length; // Asientos por fila
                
                // Extraer asientos ocupados
                occupiedSeats = seatsData.flat()
                    .filter(seat => seat && seat.is_occupied)
                    .map(seat => seat.id.toString());
            }

            // Cargar datos del usuario si está logueado
        const token = localStorage.getItem('token');
        if (token) {
            try {
                    const userResponse = await fetch(`${API_URL}/profile`, {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });

                    const userData = await userResponse.json();
                    if (userResponse.ok && userData.success) {
                        buyer = {
                            name: userData.user?.name || '',
                            email: userData.user?.email || '',
                            phone: userData.user?.phone || ''
                        };
                    } else {
                        // Si hay error, solo loguearlo pero mantener el token
                        console.warn('Error al cargar datos del usuario:', userData.message);
                    }
                } catch (e) {
                    // En caso de error de red, solo loguearlo pero mantener el token
                    console.warn('Error de conexión al cargar datos del usuario:', e);
                }
            }

            loading = false;
        } catch (e: any) {
            console.error('Error en la carga:', e);
            error = e.message;
            loading = false;
        }
    });
    
    async function handleBooking() {
        if (isSubmitting) return;
        isSubmitting = true;
        error = '';

        try {
            if (!Array.isArray(selectedSeats) || selectedSeats.length === 0) {
                throw new Error('Debes seleccionar al menos un asiento');
            }

            if (!buyer.name || !buyer.email || !buyer.phone) {
                throw new Error('Por favor, completa todos los datos de contacto');
            }

            const token = localStorage.getItem('token');
            const headers: Record<string, string> = {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            };
            
            if (token) {
                headers['Authorization'] = `Bearer ${token}`;
            }

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
                    // Solo en este caso específico redirigimos al login, pero sin borrar el token
                    goto('/login');
                    throw new Error('Por favor, inicia sesión para continuar');
                }
                throw new Error(result.message || 'Error al realizar la reserva');
            }

            if (!result.success) {
                throw new Error(result.message || 'Error al realizar la reserva');
            }

            if (result.success && result.data?.booking?.uuid) {
                // En lugar de redirigir inmediatamente, mostrar el ticket
                success = true;
                ticketUrl = result.data.ticket.download_url;
                
                // Opcional: guardar el email y el código del ticket en localStorage
                localStorage.setItem('lastBookingEmail', buyer.email);
                
                // Mostrar el paso de confirmación con el enlace del ticket
                currentStep = STEPS.CONFIRMATION;
            }
        } catch (e: any) {
            console.error('Error en la reserva:', e);
            error = e.message;
            success = false;
        } finally {
            isSubmitting = false;
        }
    }

    function handleSeatsChange(event: CustomEvent<{seats: string[]}>) {
        if (event.detail && Array.isArray(event.detail.seats)) {
        selectedSeats = event.detail.seats;
            error = '';
        }
    }

    function formatTime(isoString: string): string {
        try {
            const date = new Date(isoString);
            return date.toLocaleTimeString('es-ES', {
                hour: '2-digit',
                minute: '2-digit'
            });
        } catch (e) {
            return 'Hora no disponible';
        }
    }

    function goToStep(step: string) {
        if (step === STEPS.CONTACT && (!selectedSeats || selectedSeats.length === 0)) {
            error = 'Debes seleccionar al menos un asiento';
            return;
        }
        if (step === STEPS.PAYMENT && (!buyer.name || !buyer.email || !buyer.phone)) {
            error = 'Debes completar todos los datos de contacto';
            return;
        }
        currentStep = step;
        error = '';
    }

    function getPreviousStep(currentStep) {
        switch (currentStep) {
            case STEPS.CONTACT: return STEPS.SEATS;
            case STEPS.PAYMENT: return STEPS.CONTACT;
            case STEPS.SUMMARY: return STEPS.PAYMENT;
            default: return STEPS.SEATS;
        }
    }

    function getNextStep(currentStep) {
        switch (currentStep) {
            case STEPS.SEATS: return STEPS.CONTACT;
            case STEPS.CONTACT: return STEPS.PAYMENT;
            case STEPS.PAYMENT: return STEPS.SUMMARY;
            default: return STEPS.SUMMARY;
        }
    }

    function canProceed(step) {
        switch (step) {
            case STEPS.SEATS:
                return selectedSeats.length > 0;
            case STEPS.CONTACT:
                return buyer.name && buyer.email && buyer.phone;
            case STEPS.PAYMENT:
                return cardInfo.number && cardInfo.name && cardInfo.expiry && cardInfo.cvv;
            default:
                return true;
        }
    }
</script>

{#if loading}
    <div class="d-flex justify-content-center py-5">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Cargando...</span>
        </div>
    </div>
{:else if error}
    <div class="alert alert-danger" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i>
        {error}
    </div>
{:else if !functionData}
    <div class="alert alert-warning" role="alert">
        <i class="bi bi-exclamation-circle me-2"></i>
        No se encontraron datos de la función
    </div>
    {:else}
    <div class="container py-4">
        <!-- Progress bar usando Bootstrap -->
        <div class="progress mb-4" style="height: 4px;">
            <div class="progress-bar bg-primary" role="progressbar" 
                 style="width: {currentStep === STEPS.SEATS ? '25%' : 
                              currentStep === STEPS.CONTACT ? '50%' : 
                              currentStep === STEPS.PAYMENT ? '75%' : '100%'}" 
                 aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
            </div>
        </div>

        <!-- Movie info siempre visible -->
        <div class="card mb-4">
            <div class="card-body">
                <h4 class="card-title">{functionData.movie?.title || 'Sin título'}</h4>
                <div class="d-flex flex-wrap gap-3">
                    <span class="text-muted">
                        <i class="bi bi-building me-1"></i> 
                        Sala: {functionData.room?.name || 'Sin sala'}
                    </span>
                    <span class="text-muted">
                        <i class="bi bi-calendar me-1"></i> 
                        {new Date(functionData.date).toLocaleDateString('es-ES')}
                    </span>
                    <span class="text-muted">
                        <i class="bi bi-clock me-1"></i> 
                        {formatTime(functionData.time)}
                    </span>
                    {#if functionData.room?.is_3d}
                        <span class="badge bg-primary">3D</span>
                    {/if}
                </div>
            </div>
        </div>

        <!-- Resumen de la selección actual -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="mb-3">Resumen de la reserva</h5>
                <div class="row">
                    <!-- Asientos seleccionados -->
                    <div class="col-md-6">
                        <h6>Asientos seleccionados:</h6>
                        {#if selectedSeats.length === 0}
                            <p class="text-muted">No has seleccionado asientos</p>
                        {:else}
                            <ul class="list-unstyled">
                                {#each selectedSeats as seatId}
                                    <li>
                                        {#if Array.isArray(seatsData)}
                                            {#each seatsData as row}
                                                {#each row as seat}
                                                    {#if seat?.id?.toString() === seatId}
                                                        <span class="badge bg-success me-2">
                                                            Fila {seat.row} - Asiento {seat.number}
                                                        </span>
                                                    {/if}
                                                {/each}
                                            {/each}
                                        {/if}
                                    </li>
                                {/each}
                            </ul>
                        {/if}
                    </div>
                    
                    <!-- Precio total -->
                    <div class="col-md-6">
                        <h6>Total:</h6>
                        <h4 class="text-primary">
                            {(selectedSeats.length * (functionData.room?.price || 8)).toFixed(2)}€
                        </h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contenido del paso actual -->
        <div class="card">
            <div class="card-body">
                {#if currentStep === STEPS.SEATS}
                    <h5 class="card-title mb-4">Selección de Asientos</h5>
                    
                    <div class="btn-group mb-4 w-100">
                        <button class="btn {selectionMethod === 'map' ? 'btn-primary' : 'btn-outline-primary'}"
                                on:click={() => selectionMethod = 'map'}>
                            <i class="bi bi-grid me-2"></i> Selección visual
                        </button>
                        <button class="btn {selectionMethod === 'manual' ? 'btn-primary' : 'btn-outline-primary'}"
                                on:click={() => selectionMethod = 'manual'}>
                            <i class="bi bi-list me-2"></i> Selección manual
                        </button>
                    </div>

                    {#if selectionMethod === 'map' && Array.isArray(seatsData)}
                        <SeatMap {rows} {seatsPerRow} {selectedSeats} {occupiedSeats} {seatsData}
                            on:seatsChange={handleSeatsChange} />
                    {:else if selectionMethod === 'manual' && functionData.room}
                        <ManualSeatSelection rows={functionData.room.rows || 0}
                            seatsPerRow={functionData.room.seats_per_row || 0} {selectedSeats} {occupiedSeats}
                            on:seatsChange={handleSeatsChange} />
                    {/if}
                {:else if currentStep === STEPS.CONTACT}
                    <h5 class="card-title mb-4">Datos de Contacto</h5>
                    
                    <form class="row g-3">
                        <div class="col-12">
                            <label for="name" class="form-label">Nombre completo *</label>
                            <input type="text" class="form-control" id="name" 
                                   bind:value={buyer.name} required
                                   placeholder="Ej: Juan Pérez" />
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email *</label>
                            <input type="email" class="form-control" id="email" 
                                   bind:value={buyer.email} required
                                   placeholder="Ej: juan@email.com" />
                        </div>
                        <div class="col-md-6">
                            <label for="phone" class="form-label">Teléfono *</label>
                            <input type="tel" class="form-control" id="phone" 
                                   bind:value={buyer.phone} required
                                   placeholder="Ej: 666555444" />
                        </div>
                    </form>
                {:else if currentStep === STEPS.PAYMENT}
                    <h5 class="card-title mb-4">Método de Pago</h5>

                    <div class="card bg-primary text-white mb-4 payment-card">
                        <div class="card-body">
                            <div class="h5 mb-4">{cardInfo.number || '•••• •••• •••• ••••'}</div>
                            <div class="d-flex justify-content-between">
                                <div class="small text-uppercase">{cardInfo.name || 'TITULAR DE LA TARJETA'}</div>
                                <div class="small">{cardInfo.expiry || 'MM/YY'}</div>
                            </div>
                        </div>
                    </div>

                    <form class="row g-3">
                        <div class="col-12">
                            <label for="card-number" class="form-label">Número de tarjeta *</label>
                            <input type="text" class="form-control" id="card-number"
                                   bind:value={cardInfo.number}
                                   placeholder="1234 5678 9012 3456"
                                   maxlength="19"
                                   on:input={(e) => {
                                       e.target.value = e.target.value
                                           .replace(/\s/g, '')
                                           .replace(/(\d{4})/g, '$1 ')
                                           .trim();
                                   }} />
                        </div>
                        <div class="col-12">
                            <label for="card-name" class="form-label">Titular de la tarjeta *</label>
                            <input type="text" class="form-control" id="card-name"
                                   bind:value={cardInfo.name}
                                   placeholder="NOMBRE APELLIDOS" />
                        </div>
                        <div class="col-8">
                            <label for="card-expiry" class="form-label">Fecha de caducidad *</label>
                            <input type="text" class="form-control" id="card-expiry"
                                   bind:value={cardInfo.expiry}
                                   placeholder="MM/YY"
                                   maxlength="5"
                                   on:input={(e) => {
                                       e.target.value = e.target.value
                                           .replace(/\D/g, '')
                                           .replace(/(\d{2})(\d)/, '$1/$2');
                                   }} />
                        </div>
                        <div class="col-4">
                            <label for="card-cvv" class="form-label">CVV *</label>
                            <input type="text" class="form-control" id="card-cvv"
                                   bind:value={cardInfo.cvv}
                                   placeholder="123"
                                   maxlength="3" />
                        </div>
                    </form>
                {:else if currentStep === STEPS.SUMMARY}
                    <h5 class="card-title mb-4">Confirmar Reserva</h5>
                    <!-- Botón de confirmación final -->
                    <button 
                        class="btn btn-primary btn-lg w-100" 
                        disabled={isSubmitting}
                        on:click={handleBooking}
                    >
                        {#if isSubmitting}
                            <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                            Procesando...
                        {:else}
                            Confirmar y Pagar
                        {/if}
                    </button>
                {:else if currentStep === STEPS.CONFIRMATION}
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title mb-4">¡Reserva Confirmada!</h5>
                            
                            <div class="alert alert-success">
                                <i class="bi bi-check-circle me-2"></i>
                                Tu reserva se ha completado con éxito
                            </div>

                            {#if ticketUrl}
                                <div class="mt-4">
                                    <p>Puedes descargar tu entrada ahora:</p>
                                    <a href={ticketUrl} 
                                       class="btn btn-primary"
                                       target="_blank">
                                        <i class="bi bi-download me-2"></i>
                                        Descargar Entrada
                                    </a>
                                </div>
                                
                                <div class="mt-4">
                                    <p class="text-muted">
                                        <i class="bi bi-info-circle me-2"></i>
                                        También hemos enviado un enlace a tu email ({buyer.email}) 
                                        para que puedas descargar tu entrada más tarde.
                                    </p>
                                </div>
                            {/if}

                            <div class="mt-4 d-flex gap-3">
                                <a href="/" class="btn btn-outline-secondary">
                                    <i class="bi bi-house me-2"></i>
                                    Volver al inicio
                                </a>
                                <a href="/movies" class="btn btn-primary">
                                    <i class="bi bi-film me-2"></i>
                                    Ver más películas
                                </a>
                            </div>
                        </div>
                    </div>
                {/if}

                <!-- Navegación entre pasos -->
                <div class="d-flex justify-content-between mt-4">
                    {#if currentStep !== STEPS.SEATS}
                        <button class="btn btn-outline-secondary" 
                                on:click={() => goToStep(getPreviousStep(currentStep))}>
                            <i class="bi bi-arrow-left me-2"></i> Volver
                        </button>
                    {:else}
                        <div></div>
                    {/if}

                    {#if currentStep !== STEPS.SUMMARY}
                        <button class="btn btn-primary" 
                                disabled={!canProceed(currentStep)}
                                on:click={() => goToStep(getNextStep(currentStep))}>
                            Continuar <i class="bi bi-arrow-right ms-2"></i>
                        </button>
                    {/if}
                </div>
            </div>
        </div>
    </div>
{/if}

<style>
    .payment-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    }
</style> 