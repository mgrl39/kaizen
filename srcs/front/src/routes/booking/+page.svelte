<script lang="ts">
  import { onMount } from 'svelte';
  import { theme } from '$lib/theme';
  import { t } from '$lib/i18n';
  import HeroBanner from '$lib/components/HeroBanner.svelte';

  let selectedSeats = [];
  let totalPrice = 0;
  let loading = true;
  let error = null;

  // Datos de ejemplo (reemplazar con datos reales de la API)
  const screening = {
    movie: {
      title: "Ejemplo de Película",
      duration: 120,
      rating: "PG-13"
    },
    time: "20:30",
    date: "2024-03-20",
    room: {
      name: "Sala 1",
      layout: {
        rows: 8,
        seats_per_row: 12
      }
    },
    price: 9.99
  };

  const seatLayout = Array(screening.room.layout.rows).fill(null).map((_, rowIndex) => 
    Array(screening.room.layout.seats_per_row).fill(null).map((_, seatIndex) => ({
      id: `${String.fromCharCode(65 + rowIndex)}${seatIndex + 1}`,
      status: Math.random() > 0.3 ? 'available' : 'occupied'
    }))
  );

  function toggleSeat(seat) {
    if (seat.status !== 'available') return;
    
    const index = selectedSeats.findIndex(s => s.id === seat.id);
    if (index === -1) {
      selectedSeats = [...selectedSeats, seat];
    } else {
      selectedSeats = selectedSeats.filter(s => s.id !== seat.id);
    }
    
    totalPrice = selectedSeats.length * screening.price;
  }

  function isSeatSelected(seat) {
    return selectedSeats.some(s => s.id === seat.id);
  }

  function getSeatStatus(seat) {
    if (isSeatSelected(seat)) return 'selected';
    return seat.status;
  }

  function handleBooking() {
    if (selectedSeats.length === 0) {
      alert('Por favor, selecciona al menos un asiento');
      return;
    }
    // Implementar lógica de reserva
  }

  onMount(() => {
    loading = false;
  });
</script>

<div class="booking-page" data-bs-theme={$theme}>
  <HeroBanner 
    title={screening.movie.title}
    subtitle={`${screening.date} - ${screening.time}`}
    imageUrl="/images/banners/booking-hero.jpg"
    overlayOpacity="70"
  />

  <div class="container py-5">
    {#if loading}
      <div class="d-flex justify-content-center py-5">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Cargando...</span>
        </div>
      </div>
    {:else if error}
      <div class="alert alert-danger" role="alert">
        {error}
      </div>
    {:else}
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
                <span class="value">{screening.movie.title}</span>
              </div>
            </div>
            <div class="info-item">
              <i class="bi bi-calendar"></i>
              <div>
                <span class="label">Fecha</span>
                <span class="value">{screening.date}</span>
              </div>
            </div>
            <div class="info-item">
              <i class="bi bi-clock"></i>
              <div>
                <span class="label">Hora</span>
                <span class="value">{screening.time}</span>
              </div>
            </div>
            <div class="info-item">
              <i class="bi bi-door-open"></i>
              <div>
                <span class="label">Sala</span>
                <span class="value">{screening.room.name}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Selector de asientos -->
        <div class="seats-section">
          <div class="screen">Pantalla</div>
          
          <div class="seats-container">
            {#each seatLayout as row, rowIndex}
              <div class="seat-row">
                <div class="row-label">{String.fromCharCode(65 + rowIndex)}</div>
                {#each row as seat}
                  <button 
                    class="seat {getSeatStatus(seat)}"
                    disabled={seat.status === 'occupied'}
                    on:click={() => toggleSeat(seat)}
                  >
                    <span class="seat-number">{seat.id.slice(1)}</span>
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
                  {#each selectedSeats as seat}
                    <span class="selected-seat-tag">{seat.id}</span>
                  {/each}
                </div>
              {:else}
                <p class="no-seats">No has seleccionado ningún asiento</p>
              {/if}
            </div>
            
            <div class="price-summary">
              <div class="price-row">
                <span>Precio por entrada</span>
                <span>{new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR' }).format(screening.price)}</span>
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
            disabled={selectedSeats.length === 0}
            on:click={handleBooking}
          >
            <i class="bi bi-check2-circle"></i>
            Confirmar reserva
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