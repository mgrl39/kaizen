<!-- BookingPage.svelte -->
<style>
    /* Critical styles */
    :global(body) {
        margin: 0;
        padding: 0;
        min-height: 100vh;
        background: var(--app-bg);
    }

    /* Base styles */
    .booking-page {
        width: 100%;
        min-height: 100vh;
        background: var(--app-bg);
        color: var(--bs-body-color);
        opacity: 0;
        animation: fadeIn 0.3s ease forwards;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    /* Loading screen */
    .loading-screen {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: var(--app-bg);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
    }

    /* ... resto de los estilos existentes ... */
</style>

<script lang="ts">
    // Import styles first
    import '$lib/styles/index.css';
    import 'bootstrap-icons/font/bootstrap-icons.css';
    
    import { onMount } from 'svelte';
    import { goto } from '$app/navigation';
    import { API_URL } from '$lib/config';
    import SeatMap from './SeatMap.svelte';
    import ManualSeatSelection from './ManualSeatSelection.svelte';
    import type { Buyer } from '$lib/types';
    
    export let functionId: string;

    let functionData: any = null;
    let selectedSeats: string[] = [];
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
            
            // Procesar los datos de asientos directamente desde el backend
            rawSeatsData = seatsResult.data;
            
            // Calcular filas y asientos por fila basado en los datos del backend
            if (Array.isArray(rawSeatsData) && rawSeatsData.length > 0) {
                rows = rawSeatsData.length; // Número de filas
                seatsPerRow = rawSeatsData[0].length; // Asientos por fila
                
                // Extraer asientos ocupados - convertir IDs a string
                occupiedSeats = rawSeatsData.flat()
                    .filter(seat => seat && seat.is_occupied)
                    .map(seat => seat.id.toString());

                // Pasar los datos sin procesar al componente SeatMap
                seatsData = rawSeatsData;
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
        
        // Validar que estemos en el paso correcto
        if (currentStep !== STEPS.SUMMARY) {
            error = 'Por favor, complete todos los pasos antes de confirmar la reserva';
            return;
        }

        isSubmitting = true;
        error = '';

        try {
            // Validación de asientos
            if (!Array.isArray(selectedSeats) || selectedSeats.length === 0) {
                throw new Error('Debes seleccionar al menos un asiento');
            }

            // Validación de datos del comprador
            if (!buyer.name.trim()) {
                throw new Error('El nombre es obligatorio');
            }
            if (!buyer.email.trim() || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(buyer.email)) {
                throw new Error('El email no es válido');
            }
            if (!buyer.phone.trim()) {
                throw new Error('El teléfono es obligatorio');
            }

            const headers = {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            };
            
            // Solo añadir el token si existe (opcional)
            const token = localStorage.getItem('token');
            if (token) {
                headers['Authorization'] = `Bearer ${token}`;
            }

            const response = await fetch(`${API_URL}/bookings`, {
                method: 'POST',
                headers,
                body: JSON.stringify({
                    function_id: functionData.id,
                    seats: selectedSeats,
                    buyer: {
                        name: buyer.name.trim(),
                        email: buyer.email.trim(),
                        phone: buyer.phone.trim()
                    }
                })
            });

            const result = await response.json();

            if (!response.ok) {
                if (result.errors) {
                    // Manejar errores de validación específicos
                    const errorMessages = [];
                    if (result.errors['buyer.name']) {
                        errorMessages.push('El nombre es obligatorio');
                    }
                    if (result.errors['buyer.email']) {
                        errorMessages.push('El email es obligatorio y debe ser válido');
                    }
                    if (result.errors['buyer.phone']) {
                        errorMessages.push('El teléfono es obligatorio');
                    }
                    if (result.errors['seats.0']) {
                        errorMessages.push('El asiento seleccionado no es válido');
                    }
                    throw new Error(errorMessages.join('. '));
                }
                throw new Error(result.message || 'Error al realizar la reserva');
            }

            if (!result.success) {
                throw new Error(result.message || 'Error al realizar la reserva');
            }

            if (result.success && result.data?.booking?.uuid) {
                success = true;
                ticketUrl = result.data.ticket_url || result.data.qr_url;
                currentStep = STEPS.CONFIRMATION;
            }
        } catch (e: any) {
            console.error('Error en la reserva:', e);
            error = e.message;
            success = false;
            // Si hay un error de validación, volver al paso de contacto
            if (e.message.includes('obligatorio') || e.message.includes('válido')) {
                currentStep = STEPS.CONTACT;
            }
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
        error = ''; // Limpiar errores anteriores
        
        // Validaciones según el paso al que queremos ir
        if (step === STEPS.CONTACT) {
            if (!selectedSeats || selectedSeats.length === 0) {
                error = 'Debes seleccionar al menos un asiento';
                return;
            }
        }
        else if (step === STEPS.PAYMENT) {
            if (!buyer.name || !buyer.email || !buyer.phone) {
                error = 'Debes completar todos los datos de contacto';
                return;
            }
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(buyer.email)) {
                error = 'El email no es válido';
                return;
            }
        }
        else if (step === STEPS.SUMMARY) {
            if (!cardInfo.number || !cardInfo.name || !cardInfo.expiry || !cardInfo.cvv) {
                error = 'Debes completar todos los datos de pago';
                return;
            }
        }

        currentStep = step;
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
                return buyer.name.trim() && 
                       buyer.email.trim() && 
                       /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(buyer.email) &&
                       buyer.phone.trim();
            case STEPS.PAYMENT:
                return cardInfo.number && cardInfo.name && cardInfo.expiry && cardInfo.cvv;
            default:
                return true;
        }
    }
</script>

{#if loading}
    <div class="loading-screen">
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
    <div class="booking-page">
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
                    
                    <!-- Mensaje de deprecación para selección manual -->
                    <div class="alert alert-warning mb-4">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <strong>Aviso:</strong> La selección manual de asientos ha sido descontinuada. 
                        Por favor, utiliza el mapa visual para seleccionar tus asientos.
                    </div>
                    <div class="btn-group mb-4 w-100">
                        <button class="btn btn-primary"
                                on:click={() => selectionMethod = 'map'}>
                            <i class="bi bi-grid me-2"></i> Selección visual
                        </button>
                        <button class="btn btn-outline-secondary text-decoration-line-through"
                                data-bs-toggle="tooltip"
                                data-bs-placement="bottom"
                                title="La selección manual ha sido descontinuada"
                                disabled>
                            <i class="bi bi-list me-2"></i> Selección manual (obsoleto)
                        </button>
                    </div>

                    {#if Array.isArray(seatsData)}
                        <SeatMap {rows} {seatsPerRow} {selectedSeats} {occupiedSeats} {seatsData}
                            on:seatsChange={handleSeatsChange} />
                    {/if}
                {:else if currentStep === STEPS.CONTACT}
                    <h5 class="card-title mb-4">Datos de Contacto</h5>
                    
                    <form class="row g-3" on:submit|preventDefault>
                        <div class="col-12">
                            <label for="name" class="form-label">
                                Nombre completo <span class="text-danger">*</span>
                            </label>
                            <input 
                                type="text" 
                                class="form-control {!buyer.name.trim() && 'is-invalid'}" 
                                id="name" 
                                bind:value={buyer.name} 
                                required
                                placeholder="Ej: Juan Pérez" 
                            />
                            {#if !buyer.name.trim()}
                                <div class="invalid-feedback">
                                    El nombre es obligatorio
                                </div>
                            {/if}
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">
                                Email <span class="text-danger">*</span>
                            </label>
                            <input 
                                type="email" 
                                class="form-control {(!buyer.email.trim() || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(buyer.email)) && 'is-invalid'}" 
                                id="email" 
                                bind:value={buyer.email} 
                                required
                                placeholder="Ej: juan@email.com" 
                            />
                            {#if !buyer.email.trim()}
                                <div class="invalid-feedback">
                                    El email es obligatorio
                                </div>
                            {:else if !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(buyer.email)}
                                <div class="invalid-feedback">
                                    El email no es válido
                                </div>
                            {/if}
                        </div>
                        <div class="col-md-6">
                            <label for="phone" class="form-label">
                                Teléfono <span class="text-danger">*</span>
                            </label>
                            <input 
                                type="tel" 
                                class="form-control {!buyer.phone.trim() && 'is-invalid'}" 
                                id="phone" 
                                bind:value={buyer.phone} 
                                required
                                placeholder="Ej: 666555444" 
                            />
                            {#if !buyer.phone.trim()}
                                <div class="invalid-feedback">
                                    El teléfono es obligatorio
                                </div>
                            {/if}
                        </div>
                        <div class="col-12">
                            <small class="text-muted">
                                <i class="bi bi-info-circle me-1"></i>
                                Los campos marcados con <span class="text-danger">*</span> son obligatorios
                            </small>
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
                    <h5 class="card-title mb-4">Resumen de la Reserva</h5>
                    
                    <!-- Resumen de asientos -->
                    <div class="mb-4">
                        <h6>Asientos seleccionados:</h6>
                        <div class="d-flex flex-wrap gap-2">
                            {#each selectedSeats as seatId}
                                {#if Array.isArray(seatsData)}
                                    {#each seatsData as row}
                                        {#each row as seat}
                                            {#if seat?.id?.toString() === seatId}
                                                <span class="badge bg-success">
                                                    Fila {seat.row} - Asiento {seat.number}
                                                </span>
                                            {/if}
                                        {/each}
                                    {/each}
                                {/if}
                            {/each}
                        </div>
                    </div>

                    <!-- Resumen de datos de contacto -->
                    <div class="mb-4">
                        <h6>Datos de contacto:</h6>
                        <ul class="list-unstyled">
                            <li><strong>Nombre:</strong> {buyer.name}</li>
                            <li><strong>Email:</strong> {buyer.email}</li>
                            <li><strong>Teléfono:</strong> {buyer.phone}</li>
                        </ul>
                    </div>

                    <!-- Resumen de pago -->
                    <div class="mb-4">
                        <h6>Datos de pago:</h6>
                        <ul class="list-unstyled">
                            <li><strong>Tarjeta:</strong> •••• {cardInfo.number.slice(-4)}</li>
                            <li><strong>Titular:</strong> {cardInfo.name}</li>
                        </ul>
                    </div>

                    <!-- Total a pagar -->
                    <div class="mb-4">
                        <h6>Total a pagar:</h6>
                        <h4 class="text-primary">
                            {(selectedSeats.length * (functionData.room?.price || 8)).toFixed(2)}€
                        </h4>
                    </div>

                    <!-- Botón de confirmación -->
                    <button 
                        class="btn btn-primary btn-lg w-100" 
                        disabled={isSubmitting}
                        on:click={handleBooking}
                    >
                        {#if isSubmitting}
                            <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                            Procesando pago...
                        {:else}
                            Confirmar y Pagar
                        {/if}
                    </button>
                {:else if currentStep === STEPS.CONFIRMATION}
                    <div class="card">
                        <div class="card-body text-center">
                            <h5 class="card-title mb-4">¡Reserva Confirmada!</h5>
                            
                            <div class="alert alert-success">
                                <i class="bi bi-check-circle me-2"></i>
                                Tu reserva se ha completado con éxito
                            </div>

                            {#if ticketUrl}
                                <div class="mt-4">
                                    <div class="qr-container mb-4">
                                        <img src={ticketUrl} alt="QR Code" class="img-fluid" style="max-width: 200px;" />
                                    </div>
                                    <p class="mb-3">Muestra este código QR en la entrada del cine</p>
                                    <a href={ticketUrl} 
                                       class="btn btn-primary mb-3"
                                       download>
                                        <i class="bi bi-download me-2"></i>
                                        Descargar QR
                                    </a>
                                </div>
                                
                                <div class="mt-4">
                                    <p class="text-muted">
                                        <i class="bi bi-info-circle me-2"></i>
                                        También hemos enviado el código QR a tu email ({buyer.email}) 
                                        para que puedas acceder a él más tarde.
                                    </p>
                                </div>

                                <div class="mt-4 d-flex justify-content-center gap-3">
                                    <a href="/" class="btn btn-outline-secondary">
                                        <i class="bi bi-house me-2"></i>
                                        Volver al inicio
                                    </a>
                                    <a href="/movies" class="btn btn-primary">
                                        <i class="bi bi-film me-2"></i>
                                        Ver más películas
                                    </a>
                                </div>
                            {/if}
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