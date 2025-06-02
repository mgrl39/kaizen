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
    
    // Definir los pasos del proceso
    const STEPS = {
        SEATS: 'seats',
        CONTACT: 'contact',
        PAYMENT: 'payment',
        SUMMARY: 'summary'
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

            // Redirigir a la página de confirmación
            if (result.data?.booking?.id) {
            goto(`/tickets/${result.data.booking.id}`);
            } else {
                throw new Error('No se recibió el ID de la reserva');
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

        <!-- Steps indicators -->
        <div class="row mb-4">
            <div class="col-3 text-center">
                <button class="btn btn-link p-0 {currentStep === STEPS.SEATS ? 'text-primary fw-bold' : 'text-muted'}"
                        on:click={() => currentStep === STEPS.SEATS}>
                    <i class="bi bi-1-circle{currentStep === STEPS.SEATS ? '-fill' : ''} d-block h4 mb-1"></i>
                    Asientos
                </button>
            </div>
            <div class="col-3 text-center">
                <button class="btn btn-link p-0 {currentStep === STEPS.CONTACT ? 'text-primary fw-bold' : 'text-muted'}"
                        on:click={() => goToStep(STEPS.CONTACT)}>
                    <i class="bi bi-2-circle{currentStep === STEPS.CONTACT ? '-fill' : ''} d-block h4 mb-1"></i>
                    Contacto
                </button>
            </div>
            <div class="col-3 text-center">
                <button class="btn btn-link p-0 {currentStep === STEPS.PAYMENT ? 'text-primary fw-bold' : 'text-muted'}"
                        on:click={() => goToStep(STEPS.PAYMENT)}>
                    <i class="bi bi-3-circle{currentStep === STEPS.PAYMENT ? '-fill' : ''} d-block h4 mb-1"></i>
                    Pago
                </button>
            </div>
            <div class="col-3 text-center">
                <button class="btn btn-link p-0 {currentStep === STEPS.SUMMARY ? 'text-primary fw-bold' : 'text-muted'}"
                        on:click={() => goToStep(STEPS.SUMMARY)}>
                    <i class="bi bi-4-circle{currentStep === STEPS.SUMMARY ? '-fill' : ''} d-block h4 mb-1"></i>
                    Resumen
                </button>
            </div>
        </div>

        <!-- Movie info -->
        <div class="card mb-4">
            <div class="card-body">
                <h4 class="card-title">{functionData.movie?.title || 'Sin título'}</h4>
                <div class="d-flex flex-wrap gap-3">
                    <span class="text-muted"><i class="bi bi-building me-1"></i> Sala: {functionData.room?.name || 'Sin sala'}</span>
                    <span class="text-muted"><i class="bi bi-calendar me-1"></i> {new Date(functionData.date).toLocaleDateString('es-ES')}</span>
                    <span class="text-muted"><i class="bi bi-clock me-1"></i> {formatTime(functionData.time)}</span>
                    {#if functionData.room?.is_3d}
                        <span class="badge bg-primary">3D</span>
    {/if}
                </div>
            </div>
        </div>

        <!-- Step content -->
        {#if currentStep === STEPS.SEATS}
            <div class="card">
                <div class="card-body">
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

                    <div class="mt-4">
                        <h6>Asientos seleccionados:</h6>
                        {#if !selectedSeats || selectedSeats.length === 0}
                            <p class="text-muted">No has seleccionado ningún asiento</p>
                        {:else}
                            <ul class="list-group">
                                {#each selectedSeats as seatId}
                                    <li class="list-group-item">
                                        {#if Array.isArray(seatsData)}
                                            {#each seatsData as row}
                                                {#each row as seat}
                                                    {#if seat?.id?.toString() === seatId}
                                                        Fila {seat.row} - Asiento {seat.number}
                                                    {/if}
                                                {/each}
                                            {/each}
                                        {/if}
                    </li>
                {/each}
            </ul>
        {/if}
    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button class="btn btn-primary" 
                                disabled={!selectedSeats || selectedSeats.length === 0}
                                on:click={() => goToStep(STEPS.CONTACT)}>
                            Continuar <i class="bi bi-arrow-right ms-2"></i>
                        </button>
                    </div>
                </div>
            </div>

        {:else if currentStep === STEPS.CONTACT}
            <div class="card">
                <div class="card-body">
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

                    <div class="d-flex justify-content-between mt-4">
                        <button class="btn btn-outline-secondary" 
                                on:click={() => goToStep(STEPS.SEATS)}>
                            <i class="bi bi-arrow-left me-2"></i> Volver
                        </button>
                        <button class="btn btn-primary" 
                                disabled={!buyer.name || !buyer.email || !buyer.phone}
                                on:click={() => goToStep(STEPS.PAYMENT)}>
                            Continuar <i class="bi bi-arrow-right ms-2"></i>
                        </button>
                    </div>
                </div>
            </div>

        {:else if currentStep === STEPS.PAYMENT}
            <div class="card">
                <div class="card-body">
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

                    <div class="d-flex justify-content-between mt-4">
                        <button class="btn btn-outline-secondary" 
                                on:click={() => goToStep(STEPS.CONTACT)}>
                            <i class="bi bi-arrow-left me-2"></i> Volver
                        </button>
                        <button class="btn btn-primary"
                                disabled={!cardInfo.number || !cardInfo.name || !cardInfo.expiry || !cardInfo.cvv}
                                on:click={() => goToStep(STEPS.SUMMARY)}>
                            Continuar <i class="bi bi-arrow-right ms-2"></i>
                        </button>
                    </div>
                </div>
            </div>

        {:else if currentStep === STEPS.SUMMARY}
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">Resumen de la Reserva</h5>

                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h6 class="card-subtitle mb-3 text-muted">Película</h6>
                                    <p class="mb-1">{functionData.movie?.title}</p>
                                    <p class="mb-1">Sala: {functionData.room?.name}</p>
                                    <p class="mb-1">Fecha: {new Date(functionData.date).toLocaleDateString('es-ES')}</p>
                                    <p class="mb-0">Hora: {formatTime(functionData.time)}</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h6 class="card-subtitle mb-3 text-muted">Asientos</h6>
                                    <ul class="list-unstyled mb-0">
                                        {#each selectedSeats as seatId}
                                            <li>
                                                {#if Array.isArray(seatsData)}
                                                    {#each seatsData as row}
                                                        {#each row as seat}
                                                            {#if seat?.id?.toString() === seatId}
                                                                Fila {seat.row} - Asiento {seat.number}
                                                            {/if}
                                                        {/each}
                                                    {/each}
                                                {/if}
                                            </li>
                                        {/each}
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h6 class="card-subtitle mb-3 text-muted">Datos de contacto</h6>
                                    <p class="mb-1"><strong>Nombre:</strong> {buyer.name}</p>
                                    <p class="mb-1"><strong>Email:</strong> {buyer.email}</p>
                                    <p class="mb-0"><strong>Teléfono:</strong> {buyer.phone}</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h6 class="card-subtitle mb-3 text-muted">Método de pago</h6>
                                    <p class="mb-0">Tarjeta terminada en {cardInfo.number.slice(-4)}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card bg-primary text-white mt-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">Total</h6>
                                <div class="h3 mb-0">
                                    {(selectedSeats.length * (functionData.room?.price || 8)).toFixed(2)}€
        </div>
        </div>
        </div>
    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <button class="btn btn-outline-secondary" 
                                on:click={() => goToStep(STEPS.PAYMENT)}>
                            <i class="bi bi-arrow-left me-2"></i> Volver
                        </button>
                        <button class="btn btn-primary btn-lg" 
                                disabled={isSubmitting}
                                on:click={handleBooking}>
            {#if isSubmitting}
                                <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                Procesando...
            {:else}
                                <i class="bi bi-check-circle me-2"></i> Confirmar Reserva
            {/if}
        </button>
    </div>
</div>
            </div>
        {/if}
    </div>
{/if}

<style>
    .payment-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    }
</style> 