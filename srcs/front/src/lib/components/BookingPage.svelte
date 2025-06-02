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
</script>

{#if loading}
    <div class="loading">Cargando datos...</div>
{:else if error}
    <div class="error">{error}</div>
{:else if !functionData}
    <div class="error">No se encontraron datos de la función</div>
{:else}
    <div class="booking-container">
        <div class="movie-info">
            <h1>{functionData.movie?.title || 'Sin título'}</h1>
            <p>Sala: {functionData.room?.name || 'Sin sala'}</p>
            <p>Fecha: {new Date(functionData.date).toLocaleDateString('es-ES')}</p>
            <p>Hora: {formatTime(functionData.time)}</p>
            {#if functionData.room?.is_3d}
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

        {#if selectionMethod === 'map' && Array.isArray(seatsData)}
            <SeatMap
                {rows}
                {seatsPerRow}
                {selectedSeats}
                {occupiedSeats}
                {seatsData}
                on:seatsChange={handleSeatsChange}
            />
        {:else if selectionMethod === 'manual' && functionData.room}
            <ManualSeatSelection
                rows={functionData.room.rows || 0}
                seatsPerRow={functionData.room.seats_per_row || 0}
                {selectedSeats}
                {occupiedSeats}
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
            {#if !Array.isArray(selectedSeats) || selectedSeats.length === 0}
                <p>No has seleccionado ningún asiento</p>
            {:else}
                <ul>
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
                            <button 
                                class="remove-seat"
                                on:click={() => {
                                    selectedSeats = selectedSeats.filter(id => id !== seatId);
                                    handleSeatsChange({ detail: { seats: selectedSeats } } as CustomEvent<{seats: string[]}>);
                                }}
                            >
                                ×
                            </button>
                        </li>
                    {/each}
                </ul>
            {/if}
        </div>

        <div class="buyer-form">
            <h3>Datos de contacto</h3>
            <div class="form-group">
                <label for="name">Nombre completo *</label>
                <input
                    type="text"
                    id="name"
                    class="form-control"
                    bind:value={buyer.name}
                    required
                />
            </div>
            <div class="form-group">
                <label for="email">Email *</label>
                <input
                    type="email"
                    id="email"
                    class="form-control"
                    bind:value={buyer.email}
                    required
                />
            </div>
            <div class="form-group">
                <label for="phone">Teléfono *</label>
                <input
                    type="tel"
                    id="phone"
                    class="form-control"
                    bind:value={buyer.phone}
                    required
                />
            </div>
        </div>

        <div class="booking-actions">
            <p class="total">
                Total: {((Array.isArray(selectedSeats) ? selectedSeats.length : 0) * (functionData.room?.price || 8)).toFixed(2)}€
            </p>
            <button 
                class="book-button"
                disabled={!Array.isArray(selectedSeats) || selectedSeats.length === 0 || isSubmitting}
                on:click={handleBooking}
            >
                {#if isSubmitting}
                    Procesando...
                {:else}
                    Confirmar reserva ({selectedSeats.length} {selectedSeats.length === 1 ? 'asiento' : 'asientos'})
                {/if}
            </button>
        </div>
    </div>
{/if}

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

    .buyer-form {
        margin: 2rem 0;
        padding: 1rem;
        border: 1px solid #dee2e6;
        border-radius: 4px;
    }

    .form-group {
        margin-bottom: 1rem;
    }

    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        color: #333;
    }

    .form-control {
        width: 100%;
        padding: 0.5rem;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        font-size: 1rem;
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

    .loading {
        text-align: center;
        padding: 2rem;
    }

    .error {
        color: #dc3545;
        text-align: center;
        padding: 1rem;
        margin: 1rem;
        background: #f8d7da;
        border-radius: 4px;
    }

    .debug-info {
        padding: 20px;
        background: #f8f9fa;
        border-radius: 4px;
        margin: 20px;
    }
    
    .debug-info pre {
        background: #fff;
        padding: 10px;
        border-radius: 4px;
        overflow-x: auto;
        white-space: pre-wrap;
        word-wrap: break-word;
    }
    
    .debug-info h2 {
        color: #333;
        margin-bottom: 20px;
    }
    
    .debug-info h3 {
        color: #666;
        margin: 15px 0 10px 0;
    }
</style> 