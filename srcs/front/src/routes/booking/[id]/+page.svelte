# Booking Page

<script lang="ts">
    import { onMount } from 'svelte';
    import { API_URL } from '$lib/config';
    import { fade, fly } from 'svelte/transition';

    export let data;

    // Estado
    let loading = true;
    let error = null;
    let selectedSeats = [];
    let step = 'seats'; // 'seats' | 'payment' | 'confirmation'
    let seats = [];
    let paymentMethod = 'card';
    let formData = {
        name: '',
        email: '',
        phone: '',
        cardNumber: '',
        expiryDate: '',
        cvv: ''
    };

    // Precios
    const SEAT_PRICE = data.function?.price || 10;
    const BOOKING_FEE = 0.50;

    // Función para formatear fecha
    function formatDate(dateString) {
        if (!dateString) return 'Fecha no disponible';
        try {
            const date = new Date(dateString);
            return new Intl.DateTimeFormat('es-ES', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            }).format(date);
        } catch (e) {
            console.error('Error formateando fecha:', e);
            return 'Fecha inválida';
        }
    }

    // Función para formatear hora
    function formatTime(timeString) {
        if (!timeString) return 'N/A';
        try {
            const [hours, minutes] = timeString.split(':');
            return `${hours}:${minutes}`;
        } catch (e) {
            return timeString;
        }
    }

    // Cargar asientos
    async function loadSeats() {
        try {
            loading = true;
            error = null;

            const response = await fetch(`${API_URL}/functions/${data.function.id}/seats`);
            const result = await response.json();

            if (response.ok && result.success) {
                seats = result.data;
            } else {
                error = result.message || 'Error al cargar los asientos';
            }
        } catch (e) {
            error = 'Error de conexión al cargar los asientos';
        } finally {
            loading = false;
        }
    }

    // Seleccionar/deseleccionar asiento
    function toggleSeat(seat) {
        if (seat.is_occupied) return;

        const index = selectedSeats.findIndex(s => s.id === seat.id);
        if (index === -1) {
            selectedSeats = [...selectedSeats, seat];
        } else {
            selectedSeats = selectedSeats.filter(s => s.id !== seat.id);
        }
    }

    // Calcular total
    function calculateTotal() {
        const subtotal = selectedSeats.length * SEAT_PRICE;
        const fees = selectedSeats.length * BOOKING_FEE;
        return {
            subtotal,
            fees,
            total: subtotal + fees
        };
    }

    // Validar formulario
    function validateForm() {
        if (!formData.name || !formData.email || !formData.phone) {
            return false;
        }

        if (paymentMethod === 'card') {
            if (!formData.cardNumber || !formData.expiryDate || !formData.cvv) {
                return false;
            }
        }

        return true;
    }

    // Procesar pago
    async function processPayment() {
        try {
            loading = true;
            error = null;

            const response = await fetch(`${API_URL}/bookings`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    function_id: data.function.id,
                    seats: selectedSeats.map(seat => seat.id),
                    customer: {
                        name: formData.name,
                        email: formData.email,
                        phone: formData.phone
                    },
                    payment: {
                        method: paymentMethod,
                        ...paymentMethod === 'card' && {
                            card_number: formData.cardNumber,
                            expiry_date: formData.expiryDate,
                            cvv: formData.cvv
                        }
                    }
                })
            });

            const result = await response.json();

            if (response.ok && result.success) {
                step = 'confirmation';
            } else {
                error = result.message || 'Error al procesar el pago';
            }
        } catch (e) {
            error = 'Error de conexión al procesar el pago';
        } finally {
            loading = false;
        }
    }

    onMount(() => {
        loadSeats();
    });
</script>

<div class="container py-4">
    <!-- Encabezado -->
    <div class="card bg-dark bg-opacity-50 border-0 backdrop-blur-md mb-4">
        <div class="card-body">
            <h1 class="h3 mb-3">Reserva de entradas</h1>
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-1">
                        <strong>{data.function.movie?.title}</strong>
                        {#if data.function.is_3d}
                            <span class="badge bg-info ms-2">3D</span>
                        {/if}
                    </p>
                    <p class="mb-1">
                        <i class="bi bi-calendar-date me-2"></i>
                        {formatDate(data.function.date)}
                    </p>
                    <p class="mb-0">
                        <i class="bi bi-clock me-2"></i>
                        {formatTime(data.function.time)}
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-1">
                        <i class="bi bi-building me-2"></i>
                        {data.function.room?.cinema?.name}
                    </p>
                    <p class="mb-0">
                        <i class="bi bi-door-open me-2"></i>
                        Sala {data.function.room?.name}
                    </p>
                </div>
            </div>
        </div>
    </div>

    {#if loading}
        <div class="text-center py-5" in:fade>
            <div class="spinner-border text-light" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
        </div>
    {:else if error}
        <div class="alert alert-danger" role="alert" in:fade>
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            {error}
        </div>
    {:else}
        <!-- Pasos -->
        <div class="steps d-flex justify-content-center mb-4">
            <div class="step {step === 'seats' ? 'active' : ''} {step === 'payment' || step === 'confirmation' ? 'completed' : ''}">
                <div class="step-icon">
                    <i class="bi bi-1-circle"></i>
                </div>
                <div class="step-label">Selección de asientos</div>
            </div>
            <div class="step {step === 'payment' ? 'active' : ''} {step === 'confirmation' ? 'completed' : ''}">
                <div class="step-icon">
                    <i class="bi bi-2-circle"></i>
                </div>
                <div class="step-label">Pago</div>
            </div>
            <div class="step {step === 'confirmation' ? 'active' : ''}">
                <div class="step-icon">
                    <i class="bi bi-3-circle"></i>
                </div>
                <div class="step-label">Confirmación</div>
            </div>
        </div>

        {#if step === 'seats'}
            <div class="row" in:fade>
                <!-- Sala de cine -->
                <div class="col-lg-8">
                    <div class="cinema-screen mb-4 text-center">
                        <div class="screen-label mb-2">PANTALLA</div>
                        <div class="screen"></div>
                    </div>

                    <div class="seats-container">
                        {#each seats as row, rowIndex}
                            <div class="seat-row">
                                <div class="row-label">{String.fromCharCode(65 + rowIndex)}</div>
                                {#each row as seat}
                                    <button 
                                        class="seat-btn {seat.is_occupied ? 'occupied' : ''} {selectedSeats.some(s => s.id === seat.id) ? 'selected' : ''}"
                                        disabled={seat.is_occupied}
                                        on:click={() => toggleSeat(seat)}
                                    >
                                        {seat.number}
                                    </button>
                                {/each}
                            </div>
                        {/each}
                    </div>

                    <div class="seat-legend mt-4">
                        <div class="d-flex justify-content-center gap-4">
                            <div class="legend-item">
                                <div class="seat-example"></div>
                                <span>Disponible</span>
                            </div>
                            <div class="legend-item">
                                <div class="seat-example selected"></div>
                                <span>Seleccionado</span>
                            </div>
                            <div class="legend-item">
                                <div class="seat-example occupied"></div>
                                <span>Ocupado</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Resumen -->
                <div class="col-lg-4">
                    <div class="card bg-dark bg-opacity-50 border-0">
                        <div class="card-body">
                            <h5 class="card-title mb-4">Resumen de la compra</h5>
                            
                            {#if selectedSeats.length > 0}
                                <div class="selected-seats mb-3">
                                    <p class="mb-2">Asientos seleccionados:</p>
                                    <div class="d-flex flex-wrap gap-2">
                                        {#each selectedSeats as seat}
                                            <span class="badge bg-primary">
                                                {String.fromCharCode(65 + Math.floor(seat.number / 10))}{seat.number % 10}
                                            </span>
                                        {/each}
                                    </div>
                                </div>

                                {#if true}
                                    {@const totals = calculateTotal()}
                                    <div class="totals">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Subtotal</span>
                                            <span>{new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR' }).format(totals.subtotal)}</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-3">
                                            <span>Gastos de gestión</span>
                                            <span>{new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR' }).format(totals.fees)}</span>
                                        </div>
                                        <div class="d-flex justify-content-between fw-bold">
                                            <span>Total</span>
                                            <span>{new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR' }).format(totals.total)}</span>
                                        </div>
                                    </div>
                                {/if}

                                <button 
                                    class="btn btn-primary w-100 mt-4" 
                                    on:click={() => step = 'payment'}
                                >
                                    Continuar al pago
                                </button>
                            {:else}
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle-fill me-2"></i>
                                    Selecciona al menos un asiento para continuar
                                </div>
                            {/if}
                        </div>
                    </div>
                </div>
            </div>
        {:else if step === 'payment'}
            <div class="row" in:fade>
                <div class="col-lg-8">
                    <div class="card bg-dark bg-opacity-50 border-0">
                        <div class="card-body">
                            <h5 class="card-title mb-4">Datos de contacto</h5>
                            
                            <div class="mb-4">
                                <label for="name" class="form-label">Nombre completo</label>
                                <input 
                                    type="text" 
                                    class="form-control bg-dark border-secondary text-white" 
                                    id="name"
                                    bind:value={formData.name}
                                    placeholder="Introduce tu nombre completo"
                                >
                            </div>

                            <div class="mb-4">
                                <label for="email" class="form-label">Email</label>
                                <input 
                                    type="email" 
                                    class="form-control bg-dark border-secondary text-white" 
                                    id="email"
                                    bind:value={formData.email}
                                    placeholder="tu@email.com"
                                >
                            </div>

                            <div class="mb-4">
                                <label for="phone" class="form-label">Teléfono</label>
                                <input 
                                    type="tel" 
                                    class="form-control bg-dark border-secondary text-white" 
                                    id="phone"
                                    bind:value={formData.phone}
                                    placeholder="+34 600 000 000"
                                >
                            </div>

                            <h5 class="card-title mb-4">Método de pago</h5>

                            <div class="payment-methods mb-4">
                                <div class="form-check mb-3">
                                    <input 
                                        class="form-check-input" 
                                        type="radio" 
                                        name="paymentMethod" 
                                        id="card"
                                        value="card"
                                        bind:group={paymentMethod}
                                    >
                                    <label class="form-check-label" for="card">
                                        <i class="bi bi-credit-card me-2"></i>
                                        Tarjeta de crédito/débito
                                    </label>
                                </div>

                                <div class="form-check">
                                    <input 
                                        class="form-check-input" 
                                        type="radio" 
                                        name="paymentMethod" 
                                        id="paypal"
                                        value="paypal"
                                        bind:group={paymentMethod}
                                    >
                                    <label class="form-check-label" for="paypal">
                                        <i class="bi bi-paypal me-2"></i>
                                        PayPal
                                    </label>
                                </div>
                            </div>

                            {#if paymentMethod === 'card'}
                                <div class="card-details">
                                    <div class="mb-4">
                                        <label for="cardNumber" class="form-label">Número de tarjeta</label>
                                        <input 
                                            type="text" 
                                            class="form-control bg-dark border-secondary text-white" 
                                            id="cardNumber"
                                            bind:value={formData.cardNumber}
                                            placeholder="1234 5678 9012 3456"
                                        >
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-4">
                                            <label for="expiryDate" class="form-label">Fecha de caducidad</label>
                                            <input 
                                                type="text" 
                                                class="form-control bg-dark border-secondary text-white" 
                                                id="expiryDate"
                                                bind:value={formData.expiryDate}
                                                placeholder="MM/YY"
                                            >
                                        </div>

                                        <div class="col-md-6 mb-4">
                                            <label for="cvv" class="form-label">CVV</label>
                                            <input 
                                                type="text" 
                                                class="form-control bg-dark border-secondary text-white" 
                                                id="cvv"
                                                bind:value={formData.cvv}
                                                placeholder="123"
                                            >
                                        </div>
                                    </div>
                                </div>
                            {/if}
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card bg-dark bg-opacity-50 border-0">
                        <div class="card-body">
                            <h5 class="card-title mb-4">Resumen de la compra</h5>
                            
                            <div class="selected-seats mb-3">
                                <p class="mb-2">Asientos seleccionados:</p>
                                <div class="d-flex flex-wrap gap-2">
                                    {#each selectedSeats as seat}
                                        <span class="badge bg-primary">
                                            {String.fromCharCode(65 + Math.floor(seat.number / 10))}{seat.number % 10}
                                        </span>
                                    {/each}
                                </div>
                            </div>

                            {#if true}
                                {@const totals = calculateTotal()}
                                <div class="totals">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Subtotal</span>
                                        <span>{new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR' }).format(totals.subtotal)}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-3">
                                        <span>Gastos de gestión</span>
                                        <span>{new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR' }).format(totals.fees)}</span>
                                    </div>
                                    <div class="d-flex justify-content-between fw-bold">
                                        <span>Total</span>
                                        <span>{new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR' }).format(totals.total)}</span>
                                    </div>
                                </div>
                            {/if}

                            <div class="d-grid gap-2 mt-4">
                                <button 
                                    class="btn btn-primary"
                                    disabled={!validateForm()}
                                    on:click={processPayment}
                                >
                                    Pagar y confirmar
                                </button>
                                <button 
                                    class="btn btn-outline-light"
                                    on:click={() => step = 'seats'}
                                >
                                    Volver a los asientos
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        {:else if step === 'confirmation'}
            <div class="card bg-dark bg-opacity-50 border-0" in:fade>
                <div class="card-body text-center py-5">
                    <i class="bi bi-check-circle-fill text-success display-1 mb-4"></i>
                    <h2 class="mb-4">¡Reserva confirmada!</h2>
                    <p class="mb-4">
                        Hemos enviado los detalles de tu reserva a {formData.email}
                    </p>
                    <a href="/" class="btn btn-primary">
                        Volver al inicio
                    </a>
                </div>
            </div>
        {/if}
    {/if}
</div>

<style>
    .backdrop-blur-md {
        backdrop-filter: blur(12px);
    }

    /* Estilos para los pasos */
    .steps {
        gap: 2rem;
    }

    .step {
        flex: 1;
        max-width: 200px;
        text-align: center;
        position: relative;
    }

    .step:not(:last-child)::after {
        content: '';
        position: absolute;
        top: 25px;
        right: -1rem;
        width: calc(100% - 50px);
        height: 2px;
        background-color: rgba(255, 255, 255, 0.2);
        transform: translateX(25px);
    }

    .step.completed:not(:last-child)::after {
        background-color: var(--bs-primary);
    }

    .step-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background-color: rgba(255, 255, 255, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
        font-size: 1.5rem;
        color: rgba(255, 255, 255, 0.5);
    }

    .step.active .step-icon {
        background-color: var(--bs-primary);
        color: white;
    }

    .step.completed .step-icon {
        background-color: var(--bs-success);
        color: white;
    }

    .step-label {
        font-size: 0.875rem;
        color: rgba(255, 255, 255, 0.7);
    }

    .step.active .step-label {
        color: white;
    }

    /* Estilos para la sala de cine */
    .cinema-screen {
        margin-bottom: 2rem;
    }

    .screen-label {
        font-size: 0.875rem;
        color: rgba(255, 255, 255, 0.7);
    }

    .screen {
        height: 10px;
        background: linear-gradient(to bottom, rgba(255, 255, 255, 0.5), rgba(255, 255, 255, 0.2));
        border-radius: 5px;
        margin: 0 auto;
        max-width: 80%;
    }

    .seats-container {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        align-items: center;
    }

    .seat-row {
        display: flex;
        gap: 0.5rem;
        align-items: center;
    }

    .row-label {
        width: 30px;
        text-align: center;
        font-size: 0.875rem;
        color: rgba(255, 255, 255, 0.7);
    }

    .seat-btn {
        width: 35px;
        height: 35px;
        border: 2px solid rgba(255, 255, 255, 0.2);
        background-color: rgba(255, 255, 255, 0.1);
        color: white;
        border-radius: 5px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.875rem;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .seat-btn:hover:not(:disabled) {
        border-color: var(--bs-primary);
        background-color: rgba(var(--bs-primary-rgb), 0.2);
    }

    .seat-btn.selected {
        border-color: var(--bs-primary);
        background-color: var(--bs-primary);
    }

    .seat-btn.occupied {
        border-color: rgba(255, 255, 255, 0.1);
        background-color: rgba(255, 255, 255, 0.05);
        cursor: not-allowed;
        color: rgba(255, 255, 255, 0.3);
    }

    /* Leyenda de asientos */
    .seat-legend {
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        padding-top: 1rem;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.875rem;
        color: rgba(255, 255, 255, 0.7);
    }

    .seat-example {
        width: 20px;
        height: 20px;
        border: 2px solid rgba(255, 255, 255, 0.2);
        background-color: rgba(255, 255, 255, 0.1);
        border-radius: 4px;
    }

    .seat-example.selected {
        border-color: var(--bs-primary);
        background-color: var(--bs-primary);
    }

    .seat-example.occupied {
        border-color: rgba(255, 255, 255, 0.1);
        background-color: rgba(255, 255, 255, 0.05);
    }
</style> 