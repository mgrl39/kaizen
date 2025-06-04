<script lang="ts">
  import { onMount } from 'svelte';
  import { theme } from '$lib/theme';
  import { t } from '$lib/i18n';
  import HeroBanner from '$lib/components/HeroBanner.svelte';
  import { page } from '$app/stores';
  import { API_URL } from '$lib/config';
  import { goto } from '$app/navigation';

  let selectedSeats: number[] = [];
  let totalPrice = 0;
  let loading = true;
  let error = null;
  let functionData: any = null;
  let seatLayout: any[] = [];
  let isSubmitting = false;

  async function loadFunctionData() {
    try {
      const functionId = $page.params.id;
      const response = await fetch(`${API_URL}/functions/${functionId}`);
      const result = await response.json();
      
      if (!response.ok || !result.success) {
        throw new Error(result.message || 'Error cargando datos de la función');
      }

      functionData = result.data;
      
      // Generar cuadrícula de asientos basada en las dimensiones de la sala
      if (functionData.room) {
        const { rows, seats_per_row } = functionData.room;
        seatLayout = Array(rows).fill(null).map((_, rowIndex) => 
          Array(seats_per_row).fill(null).map((_, seatIndex) => {
            const seatNumber = (rowIndex * seats_per_row) + seatIndex + 1;
            return {
              id: seatNumber,
              row: rowIndex + 1,
              number: seatIndex + 1,
              status: 'available'
            };
          })
        );

        // Cargar estado real de los asientos
        const seatsResponse = await fetch(`${API_URL}/functions/${functionId}/seats`);
        const seatsResult = await seatsResponse.json();
        
        if (seatsResponse.ok && seatsResult.success && Array.isArray(seatsResult.data)) {
          // Marcar asientos ocupados
          seatsResult.data.forEach((occupiedSeat: any) => {
            const row = Math.floor((occupiedSeat - 1) / seats_per_row);
            const col = (occupiedSeat - 1) % seats_per_row;
            if (seatLayout[row] && seatLayout[row][col]) {
              seatLayout[row][col].status = 'occupied';
            }
          });
        }
      }

      loading = false;
    } catch (e: any) {
      error = e.message;
      loading = false;
    }
  }

  async function handleBooking() {
    if (selectedSeats.length === 0 || isSubmitting) return;
    
    isSubmitting = true;
    error = null;

    try {
      const token = localStorage.getItem('token');
      if (!token) {
        // Si no hay token, redirigir al login
        const currentUrl = window.location.pathname + window.location.search;
        goto(`/login?redirect=${encodeURIComponent(currentUrl)}`);
        return;
      }

      const response = await fetch(`${API_URL}/bookings`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${token}`,
          'Accept': 'application/json'
        },
        body: JSON.stringify({
          function_id: functionData.id,
          seats: selectedSeats,
          buyer: {
            name: '', // Se obtiene del token
            email: '', // Se obtiene del token
            phone: '' // Se obtiene del token
          }
        })
      });

      const result = await response.json();

      if (!response.ok || !result.success) {
        if (response.status === 401) {
          // Token inválido, redirigir al login
          localStorage.removeItem('token');
          goto('/login');
          return;
        }
        throw new Error(result.message || 'Error al realizar la reserva');
      }

      // Redirigir a la página de confirmación con el UUID de la reserva
      if (result.data?.booking?.uuid) {
        goto(`/booking/confirmation/${result.data.booking.uuid}`);
      } else {
        throw new Error('No se recibió el identificador de la reserva');
      }

    } catch (e: any) {
      error = e.message;
      console.error('Error en la reserva:', e);
    } finally {
      isSubmitting = false;
    }
  }

  function toggleSeat(seat: any) {
    if (seat.status !== 'available') return;
    
    const index = selectedSeats.findIndex(s => s === seat.id);
    if (index === -1) {
      selectedSeats = [...selectedSeats, seat.id];
    } else {
      selectedSeats = selectedSeats.filter(s => s !== seat.id);
    }
    
    // Calcular precio total (si está disponible)
    const price = functionData?.price || 8.50; // Precio por defecto si no está definido
    totalPrice = selectedSeats.length * price;
  }

  function isSeatSelected(seat: any) {
    return selectedSeats.includes(seat.id);
  }

  function getSeatStatus(seat: any) {
    if (isSeatSelected(seat)) return 'selected';
    return seat.status;
  }

  function formatDate(dateString: string) {
    return new Date(dateString).toLocaleDateString('es-ES', {
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    });
  }

  function formatTime(timeString: string) {
    return timeString.substring(0, 5); // Formato HH:mm
  }

  function getSeatLabel(seat: any) {
    return `F${seat.row}A${seat.number}`;
  }

  onMount(() => {
    loadFunctionData();
  });
</script>

<div class="booking-page" data-bs-theme={$theme}>
  {#if functionData}
    <HeroBanner 
      title={functionData.movie.title}
      subtitle={`${formatDate(functionData.date)} - ${formatTime(functionData.time)}`}
      imageUrl="/images/banners/booking-hero.jpg"
      overlayOpacity="70"
    />
  {/if}

  <div class="container py-5">
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
    {:else if functionData}
      <div class="booking-content">
        <!-- Información de la película y sesión -->
        <div class="info-card">
          <div class="info-header">
            <i class="bi bi-info-circle text-primary"></i>
            <h3>Detalles de la sesión</h3>
          </div>
          <div class="info-body">
            <div class="info-item">
              <i class="bi bi-film"></i>
              <div>
                <span class="label">Película</span>
                <span class="value">{functionData.movie.title}</span>
              </div>
            </div>
            <div class="info-item">
              <i class="bi bi-calendar"></i>
              <div>
                <span class="label">Fecha</span>
                <span class="value">{formatDate(functionData.date)}</span>
              </div>
            </div>
            <div class="info-item">
              <i class="bi bi-clock"></i>
              <div>
                <span class="label">Hora</span>
                <span class="value">{formatTime(functionData.time)}</span>
              </div>
            </div>
            <div class="info-item">
              <i class="bi bi-door-open"></i>
              <div>
                <span class="label">Sala</span>
                <span class="value">{functionData.room.name}</span>
              </div>
            </div>
            {#if functionData.is_3d}
              <div class="info-item">
                <i class="bi bi-badge-3d"></i>
                <div>
                  <span class="label">Formato</span>
                  <span class="value">3D</span>
                </div>
              </div>
            {/if}
          </div>
        </div>

        <!-- Selector de asientos -->
        <div class="seats-section">
          <div class="screen">Pantalla</div>
          
          <div class="seats-container">
            {#each seatLayout as row, rowIndex}
              <div class="seat-row">
                <div class="row-label">{rowIndex + 1}</div>
                {#each row as seat}
                  <button 
                    class="seat {getSeatStatus(seat)}"
                    disabled={seat.status === 'occupied'}
                    on:click={() => toggleSeat(seat)}
                  >
                    <span class="seat-number">{seat.number}</span>
                  </button>
                {/each}
              </div>
            {/each}
          </div>

          <div class="seats-legend">
            <div class="legend-item">
              <div class="seat-demo available"></div>
              <span>Disponible</span>
            </div>
            <div class="legend-item">
              <div class="seat-demo selected"></div>
              <span>Seleccionado</span>
            </div>
            <div class="legend-item">
              <div class="seat-demo occupied"></div>
              <span>Ocupado</span>
            </div>
          </div>
        </div>

        <!-- Resumen y botón de reserva -->
        <div class="booking-summary">
          <div class="summary-content">
            <div class="selected-seats">
              <h4>Asientos seleccionados</h4>
              {#if selectedSeats.length > 0}
                <div class="seats-list">
                  {#each selectedSeats as seatId}
                    {#each seatLayout as row}
                      {#each row as seat}
                        {#if seat.id === seatId}
                          <span class="selected-seat-tag">
                            {getSeatLabel(seat)}
                          </span>
                        {/if}
                      {/each}
                    {/each}
                  {/each}
                </div>
              {:else}
                <p class="no-seats">No has seleccionado ningún asiento</p>
              {/if}
            </div>
            
            <div class="price-summary">
              <div class="price-row">
                <span>Precio por entrada</span>
                <span>{new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR' }).format(functionData.price || 8.50)}</span>
              </div>
              <div class="price-row">
                <span>Cantidad</span>
                <span>{selectedSeats.length}</span>
              </div>
              <div class="price-row total">
                <span>Total</span>
                <span>{new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR' }).format(totalPrice)}</span>
              </div>
            </div>
          </div>

          <button 
            class="book-button" 
            disabled={selectedSeats.length === 0 || isSubmitting}
            on:click={handleBooking}
          >
            {#if isSubmitting}
              <div class="spinner-border spinner-border-sm me-2" role="status">
                <span class="visually-hidden">Procesando...</span>
              </div>
              Procesando...
            {:else}
              <i class="bi bi-check2-circle me-2"></i>
              Confirmar reserva
            {/if}
          </button>
        </div>
      </div>
    {/if}
  </div>
</div>

<style>
  .booking-page {
    width: 100%;
    min-height: 100vh;
    background: var(--app-bg);
    color: var(--bs-body-color);
  }

  .booking-content {
    display: grid;
    grid-template-columns: 300px 1fr 300px;
    gap: 2rem;
    align-items: start;
  }

  /* Info Card Styles */
  .info-card {
    background: var(--app-card-bg);
    border-radius: 1rem;
    border: 1px solid var(--app-border);
    overflow: hidden;
    backdrop-filter: blur(10px);
  }

  .info-header {
    padding: 1.25rem;
    border-bottom: 1px solid var(--app-border);
    display: flex;
    align-items: center;
    gap: 0.75rem;
  }

  .info-header h3 {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 600;
  }

  .info-body {
    padding: 1.25rem;
  }

  .info-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 0.75rem 0;
    border-bottom: 1px solid var(--app-border);
  }

  .info-item:last-child {
    border-bottom: none;
    padding-bottom: 0;
  }

  .info-item i {
    font-size: 1.2rem;
    color: var(--primary-color);
  }

  .info-item div {
    display: flex;
    flex-direction: column;
  }

  .info-item .label {
    font-size: 0.85rem;
    opacity: 0.7;
  }

  .info-item .value {
    font-weight: 500;
  }

  /* Seats Section Styles */
  .seats-section {
    background: var(--app-card-bg);
    border-radius: 1rem;
    padding: 2rem;
    border: 1px solid var(--app-border);
    backdrop-filter: blur(10px);
  }

  .screen {
    background: linear-gradient(to bottom, var(--primary-color), transparent);
    color: white;
    padding: 1rem;
    text-align: center;
    border-radius: 0.5rem;
    margin-bottom: 2rem;
    font-weight: 500;
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
    font-weight: 500;
    opacity: 0.7;
  }

  .seat {
    width: 35px;
    height: 35px;
    border: none;
    border-radius: 0.5rem;
    display: grid;
    place-items: center;
    cursor: pointer;
    transition: all 0.2s ease;
    background: var(--app-card-bg);
    border: 1px solid var(--app-border);
  }

  .seat:hover:not(:disabled) {
    transform: translateY(-2px);
  }

  .seat.available:hover {
    border-color: var(--primary-color);
  }

  .seat.selected {
    background: var(--primary-color);
    color: white;
    border-color: transparent;
  }

  .seat.occupied {
    background: var(--app-border);
    cursor: not-allowed;
    opacity: 0.5;
  }

  .seat-number {
    font-size: 0.8rem;
    font-weight: 500;
  }

  /* Legend Styles */
  .seats-legend {
    display: flex;
    justify-content: center;
    gap: 2rem;
    margin-top: 2rem;
    padding-top: 1rem;
    border-top: 1px solid var(--app-border);
  }

  .legend-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.9rem;
  }

  .seat-demo {
    width: 20px;
    height: 20px;
    border-radius: 0.25rem;
  }

  .seat-demo.available {
    background: var(--app-card-bg);
    border: 1px solid var(--app-border);
  }

  .seat-demo.selected {
    background: var(--primary-color);
  }

  .seat-demo.occupied {
    background: var(--app-border);
    opacity: 0.5;
  }

  /* Booking Summary Styles */
  .booking-summary {
    background: var(--app-card-bg);
    border-radius: 1rem;
    border: 1px solid var(--app-border);
    overflow: hidden;
    backdrop-filter: blur(10px);
  }

  .summary-content {
    padding: 1.25rem;
  }

  .selected-seats h4 {
    font-size: 1rem;
    margin-bottom: 1rem;
  }

  .seats-list {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-bottom: 1.5rem;
  }

  .selected-seat-tag {
    background: var(--primary-color);
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 1rem;
    font-size: 0.9rem;
    font-weight: 500;
  }

  .no-seats {
    font-size: 0.9rem;
    opacity: 0.7;
    margin-bottom: 1.5rem;
  }

  .price-summary {
    border-top: 1px solid var(--app-border);
    padding-top: 1rem;
  }

  .price-row {
    display: flex;
    justify-content: space-between;
    padding: 0.5rem 0;
    font-size: 0.9rem;
  }

  .price-row.total {
    border-top: 1px solid var(--app-border);
    margin-top: 0.5rem;
    padding-top: 1rem;
    font-weight: 600;
    font-size: 1.1rem;
  }

  .book-button {
    width: 100%;
    padding: 1rem;
    background: var(--primary-color);
    color: white;
    border: none;
    font-weight: 500;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    transition: all 0.2s ease;
    cursor: pointer;
  }

  .book-button:hover:not(:disabled) {
    background: var(--primary-hover);
  }

  .book-button:disabled {
    opacity: 0.7;
    cursor: not-allowed;
  }

  @media (max-width: 1200px) {
    .booking-content {
      grid-template-columns: 1fr;
      gap: 1.5rem;
    }

    .info-card {
      order: 2;
    }

    .seats-section {
      order: 1;
    }

    .booking-summary {
      order: 3;
    }
  }

  @media (max-width: 768px) {
    .seats-container {
      overflow-x: auto;
      padding-bottom: 1rem;
    }

    .seat {
      width: 30px;
      height: 30px;
    }

    .seats-legend {
      flex-wrap: wrap;
      gap: 1rem;
    }
  }
</style> 