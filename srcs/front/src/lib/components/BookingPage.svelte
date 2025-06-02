<!-- BookingPage.svelte -->
<script lang="ts">
    import { onMount } from 'svelte';
    import { goto } from '$app/navigation';
    import { API_URL } from '$lib/config';
    import SeatMap from './SeatMap.svelte';
    import ManualSeatSelection from './ManualSeatSelection.svelte';
    
    export let functionId: string;
    export let functionData: any;

    let selectedSeats: string[] = [];
    let selectionMethod: 'map' | 'manual' = 'map';
    let error = '';
    let isAuthenticated = false;
    
    async function verifyToken() {
        const token = localStorage.getItem('token');
        if (!token) {
            isAuthenticated = false;
            return;
        }

        try {
            const response = await fetch(`${API_URL}/v1/profile`, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();
            
            if (response.ok && data.success) {
                isAuthenticated = true;
            } else {
                isAuthenticated = false;
                localStorage.removeItem('token');
            }
        } catch (e) {
            console.error('Error verificando token:', e);
            isAuthenticated = false;
            localStorage.removeItem('token');
        }
    }
    
    // Verificar token y recuperar selección previa al montar el componente
    onMount(async () => {
        await verifyToken();
        
        const savedBooking = sessionStorage.getItem('bookingRedirect');
        if (savedBooking) {
            try {
                const { selectedSeats: savedSeats } = JSON.parse(savedBooking);
                if (savedSeats) {
                    selectedSeats = savedSeats;
                }
            } catch (e) {
                console.error('Error recuperando selección previa:', e);
            }
        }
    });
    
    async function handleBooking() {
        // Verificar token antes de intentar la reserva
        await verifyToken();
        
        if (!isAuthenticated) {
            // Guardar el estado actual
            sessionStorage.setItem('bookingRedirect', JSON.stringify({
                functionId,
                selectedSeats,
                returnUrl: window.location.pathname
            }));
            // Redirigir a login
            goto('/login');
            return;
        }

        if (selectedSeats.length === 0) {
            error = 'Debes seleccionar al menos un asiento';
            return;
        }

        try {
            const token = localStorage.getItem('token');
            const response = await fetch(`${API_URL}/v1/bookings`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    function_id: functionId,
                    seats: selectedSeats,
                    payment_method: 'card'
                })
            });

            const result = await response.json();

            if (!response.ok) {
                if (response.status === 401) {
                    isAuthenticated = false;
                    localStorage.removeItem('token');
                    // Guardar el estado actual
                    sessionStorage.setItem('bookingRedirect', JSON.stringify({
                        functionId,
                        selectedSeats,
                        returnUrl: window.location.pathname
                    }));
                    goto('/login');
                    return;
                }
                throw new Error(result.message || 'Error al realizar la reserva');
            }

            if (!result.success) {
                throw new Error(result.message || 'Error al realizar la reserva');
            }

            // Limpiar el estado de redirección solo si la reserva fue exitosa
            sessionStorage.removeItem('bookingRedirect');
            goto(`/tickets/${result.data.booking.id}`);
        } catch (e) {
            error = e.message;
            console.error('Error en la reserva:', e);
        }
    }

    function handleSeatsChange(event: CustomEvent) {
        selectedSeats = event.detail.seats;
        error = ''; // Limpiar error al cambiar la selección
    }
</script>

<div class="booking-container">
    <div class="movie-info">
        <h1>{functionData.movie.title}</h1>
        <p>Sala: {functionData.room.name}</p>
        <p>Fecha: {new Date(functionData.date).toLocaleDateString()}</p>
        <p>Hora: {functionData.time}</p>
        {#if functionData.room.is_3d}
            <span class="badge-3d">3D</span>
        {/if}
    </div>

    <div class="selection-method">
        <button 
            class:active={selectionMethod === 'map'} 
            on:click={() => selectionMethod = 'map'}
        >
            Selección visual
        </button>
        <button 
            class:active={selectionMethod === 'manual'} 
            on:click={() => selectionMethod = 'manual'}
        >
            Selección manual
        </button>
    </div>

    {#if selectionMethod === 'map'}
        <SeatMap
            rows={functionData.room.rows}
            seatsPerRow={functionData.room.seats_per_row}
            selectedSeats={selectedSeats}
            occupiedSeats={functionData.occupied_seats}
            on:seatsChange={handleSeatsChange}
        />
    {:else}
        <ManualSeatSelection
            rows={functionData.room.rows}
            seatsPerRow={functionData.room.seats_per_row}
            selectedSeats={selectedSeats}
            occupiedSeats={functionData.occupied_seats}
            on:seatsChange={handleSeatsChange}
        />
    {/if}

    {#if error}
        <div class="error-message">
            {error}
        </div>
    {/if}

    <div class="selected-seats">
        <h3>Asientos seleccionados:</h3>
        {#if selectedSeats.length === 0}
            <p>No has seleccionado ningún asiento</p>
        {:else}
            <ul>
                {#each selectedSeats as seat}
                    <li>
                        Fila {seat[0]} - Asiento {seat.slice(1)}
                        <button 
                            class="remove-seat"
                            on:click={() => {
                                selectedSeats = selectedSeats.filter(s => s !== seat);
                            }}
                        >
                            ×
                        </button>
                    </li>
                {/each}
            </ul>
        {/if}
    </div>

    <div class="booking-actions">
        <p class="total">
            Total: {(selectedSeats.length * functionData.room.price).toFixed(2)}€
        </p>
        <button 
            class="book-button"
            disabled={selectedSeats.length === 0}
            on:click={handleBooking}
        >
            {isAuthenticated ? 'Reservar entradas' : 'Iniciar sesión para reservar'}
        </button>
        {#if !isAuthenticated}
            <p class="login-notice">
                Debes iniciar sesión para completar la reserva
            </p>
        {/if}
    </div>
</div>

<style>
    .booking-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 2rem;
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .movie-info {
        margin-bottom: 2rem;
        padding: 1rem;
        background: #f8f9fa;
        border-radius: 4px;
        position: relative;
    }

    .movie-info h1 {
        margin: 0 0 1rem 0;
        color: #333;
    }

    .badge-3d {
        position: absolute;
        top: 1rem;
        right: 1rem;
        background: #007bff;
        color: white;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        font-size: 0.875rem;
    }

    .selection-method {
        display: flex;
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .selection-method button {
        padding: 0.5rem 1rem;
        border: 2px solid #4CAF50;
        background: white;
        color: #4CAF50;
        border-radius: 4px;
        cursor: pointer;
        transition: all 0.2s;
    }

    .selection-method button.active {
        background: #4CAF50;
        color: white;
    }

    .error-message {
        color: #dc3545;
        padding: 1rem;
        margin: 1rem 0;
        background: #f8d7da;
        border-radius: 4px;
        text-align: center;
    }

    .selected-seats {
        margin: 2rem 0;
        padding: 1rem;
        border: 1px solid #dee2e6;
        border-radius: 4px;
    }

    .selected-seats ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .selected-seats li {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.5rem;
        background: #f8f9fa;
        margin-bottom: 0.5rem;
        border-radius: 4px;
    }

    .remove-seat {
        background: none;
        border: none;
        color: #dc3545;
        font-size: 1.2rem;
        cursor: pointer;
        padding: 0 0.5rem;
    }

    .booking-actions {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        align-items: center;
        margin-top: 2rem;
        padding-top: 1rem;
        border-top: 1px solid #dee2e6;
    }

    .total {
        font-size: 1.2rem;
        font-weight: bold;
    }

    .book-button {
        padding: 0.75rem 2rem;
        background: #4CAF50;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 1.1rem;
        transition: background 0.2s;
        width: 100%;
        max-width: 300px;
    }

    .book-button:hover:not(:disabled) {
        background: #45a049;
    }

    .book-button:disabled {
        background: #cccccc;
        cursor: not-allowed;
    }

    .login-notice {
        color: #666;
        font-size: 0.9rem;
        text-align: center;
    }

    @media (max-width: 768px) {
        .booking-container {
            padding: 1rem;
        }

        .selection-method {
            flex-direction: column;
        }

        .selection-method button {
            width: 100%;
        }
    }
</style> 