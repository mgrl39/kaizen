<script lang="ts">
  import { onMount } from 'svelte';
  import { theme } from '$lib/theme';
  import { t } from '$lib/i18n';
  import HeroBanner from '$lib/components/HeroBanner.svelte';
  import { page } from '$app/stores';
  import { API_URL } from '$lib/config';
  import { goto } from '$app/navigation';

  let selectedSeats: string[] = [];
  let totalPrice = 0;
  let loading = true;
  let error = null;
  let functionData: any = null;
  let seatLayout: any[][] = [];
  let isSubmitting = false;

  // Datos del comprador
  let buyer = {
    name: '',
    email: '',
    phone: ''
  };

  // Datos de la tarjeta
  let cardInfo = {
    number: '',
    name: '',
    expiry: '',
    cvv: ''
  };

  // Estado del formulario
  let showPaymentForm = false;
  let formErrors = {
    buyer: {
      name: '',
      email: '',
      phone: ''
    },
    card: {
      number: '',
      name: '',
      expiry: '',
      cvv: ''
    }
  };

  async function loadFunctionData() {
    try {
      const functionId = $page.params.id;
      const response = await fetch(`${API_URL}/functions/${functionId}`);
      const result = await response.json();
      
      if (!response.ok || !result.success) {
        throw new Error(result.message || 'Error cargando datos de la función');
      }

      functionData = result.data;
      
      // Cargar estado real de los asientos
      const seatsResponse = await fetch(`${API_URL}/functions/${functionId}/seats`);
      const seatsResult = await seatsResponse.json();
      
      if (!seatsResponse.ok || !seatsResult.success) {
        throw new Error(seatsResult.message || 'Error cargando datos de asientos');
      }

      // Obtener las dimensiones reales de la sala
      const roomRows = functionData.room.rows;
      const roomSeatsPerRow = functionData.room.seats_per_row;

      // Crear una matriz vacía con las dimensiones correctas
      seatLayout = Array(roomRows).fill(null).map(() => Array(roomSeatsPerRow).fill(null));

      // Distribuir los asientos en la matriz según las dimensiones de la sala
      const flatSeats = seatsResult.data.flat();
      let seatIndex = 0;

      for (let row = 0; row < roomRows; row++) {
        for (let col = 0; col < roomSeatsPerRow; col++) {
          const seat = flatSeats[seatIndex];
          if (seat) {
            seatLayout[row][col] = {
              ...seat,
              row: row, // Usar el índice de fila actual
              number: col + 1 // Números de asiento del 1 al 8 en cada fila
            };
          }
          seatIndex++;
        }
      }

      // Debug para verificar la estructura de datos
      console.log('Room dimensions:', { rows: roomRows, seatsPerRow: roomSeatsPerRow });
      console.log('Organized seat layout:', seatLayout);

      loading = false;
    } catch (e: any) {
      error = e.message;
      loading = false;
    }
  }

  async function handleBooking() {
    if (selectedSeats.length === 0 || isSubmitting) return;
    
    // Si no se ha mostrado el formulario, mostrarlo
    if (!showPaymentForm) {
      showPaymentForm = true;
      return;
    }

    // Validar datos del comprador
    let hasErrors = false;
    if (!buyer.name.trim()) {
      formErrors.buyer.name = 'El nombre es obligatorio';
      hasErrors = true;
    }
    if (!buyer.email.trim() || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(buyer.email)) {
      formErrors.buyer.email = 'El email no es válido';
      hasErrors = true;
    }
    if (!buyer.phone.trim()) {
      formErrors.buyer.phone = 'El teléfono es obligatorio';
      hasErrors = true;
    }

    // Validar datos de la tarjeta
    if (!cardInfo.number.trim() || cardInfo.number.replace(/\s/g, '').length !== 16) {
      formErrors.card.number = 'El número de tarjeta no es válido';
      hasErrors = true;
    }
    if (!cardInfo.name.trim()) {
      formErrors.card.name = 'El nombre del titular es obligatorio';
      hasErrors = true;
    }
    if (!cardInfo.expiry.trim() || !/^\d{2}\/\d{2}$/.test(cardInfo.expiry)) {
      formErrors.card.expiry = 'La fecha de caducidad no es válida';
      hasErrors = true;
    }
    if (!cardInfo.cvv.trim() || !/^\d{3}$/.test(cardInfo.cvv)) {
      formErrors.card.cvv = 'El CVV no es válido';
      hasErrors = true;
    }

    if (hasErrors) {
      return;
    }
    
    isSubmitting = true;
    error = null;

    try {
      const response = await fetch(`${API_URL}/bookings`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
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

      if (!response.ok || !result.success) {
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
    if (!seat || seat.is_occupied) return;
    
    const seatId = seat.id.toString();
    if (selectedSeats.includes(seatId)) {
      // Deseleccionar asiento
      selectedSeats = selectedSeats.filter(id => id !== seatId);
    } else {
      // Seleccionar nuevo asiento
      selectedSeats = [...selectedSeats, seatId];
    }
    
    // Calcular precio total usando el precio de la función o de la sala
    const price = functionData?.price || functionData?.room?.price || 8.50;
    totalPrice = selectedSeats.length * (functionData?.is_3d ? price + 2 : price);
  }

  function isSeatSelected(seatId: number): boolean {
    return selectedSeats.includes(seatId.toString());
  }

  function getSeatStatus(seat: any) {
    if (isSeatSelected(seat.id)) return 'selected';
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
    return `${seat.row}${seat.number}`; // Ya tenemos la letra de fila directamente
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
            {#if Array.isArray(seatLayout) && seatLayout.length > 0}
              {#each seatLayout as row, rowIndex}
                <div class="seat-row">
                  <div class="row-label">{String.fromCharCode(65 + rowIndex)}</div>
                  {#each row as seat, seatIndex}
                    {#if seat}
                      <button 
                        class="seat {seat.is_occupied ? 'occupied' : isSeatSelected(seat.id) ? 'selected' : 'available'}"
                        disabled={seat.is_occupied}
                        on:click={() => toggleSeat(seat)}
                        title="Fila {String.fromCharCode(65 + rowIndex)} Asiento {seat.number}"
                      >
                        <span class="seat-number">{seat.number}</span>
                      </button>
                    {:else}
                      <button class="seat unavailable" disabled>
                        <span class="seat-number">-</span>
                      </button>
                    {/if}
                  {/each}
                </div>
              {/each}
            {:else}
              <div class="alert alert-warning">
                No se encontraron asientos disponibles
              </div>
            {/if}
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

            {#if showPaymentForm}
              <!-- Formulario de datos del comprador -->
              <div class="payment-form mt-4">
                <h5 class="mb-3">Datos del comprador</h5>
                <div class="mb-3">
                  <label for="name" class="form-label">Nombre completo *</label>
                  <input 
                    type="text" 
                    class="form-control" 
                    class:is-invalid={formErrors.buyer.name}
                    id="name" 
                    bind:value={buyer.name}
                    placeholder="Ej: Juan Pérez"
                  />
                  {#if formErrors.buyer.name}
                    <div class="invalid-feedback">{formErrors.buyer.name}</div>
                  {/if}
                </div>
                <div class="mb-3">
                  <label for="email" class="form-label">Email *</label>
                  <input 
                    type="email" 
                    class="form-control"
                    class:is-invalid={formErrors.buyer.email}
                    id="email" 
                    bind:value={buyer.email}
                    placeholder="Ej: juan@email.com"
                  />
                  {#if formErrors.buyer.email}
                    <div class="invalid-feedback">{formErrors.buyer.email}</div>
                  {/if}
                </div>
                <div class="mb-3">
                  <label for="phone" class="form-label">Teléfono *</label>
                  <input 
                    type="tel" 
                    class="form-control"
                    class:is-invalid={formErrors.buyer.phone}
                    id="phone" 
                    bind:value={buyer.phone}
                    placeholder="Ej: 666555444"
                  />
                  {#if formErrors.buyer.phone}
                    <div class="invalid-feedback">{formErrors.buyer.phone}</div>
                  {/if}
                </div>

                <!-- Formulario de tarjeta -->
                <h5 class="mb-3 mt-4">Datos de pago</h5>
                <div class="card bg-primary text-white mb-4 payment-card">
                  <div class="card-body">
                    <div class="h5 mb-4">{cardInfo.number || '•••• •••• •••• ••••'}</div>
                    <div class="d-flex justify-content-between">
                      <div class="small text-uppercase">{cardInfo.name || 'TITULAR DE LA TARJETA'}</div>
                      <div class="small">{cardInfo.expiry || 'MM/YY'}</div>
                    </div>
                  </div>
                </div>

                <div class="mb-3">
                  <label for="card-number" class="form-label">Número de tarjeta *</label>
                  <input 
                    type="text" 
                    class="form-control"
                    class:is-invalid={formErrors.card.number}
                    id="card-number" 
                    bind:value={cardInfo.number}
                    placeholder="1234 5678 9012 3456"
                    maxlength="19"
                    on:input={(e) => {
                      e.target.value = e.target.value
                        .replace(/\s/g, '')
                        .replace(/(\d{4})/g, '$1 ')
                        .trim();
                    }}
                  />
                  {#if formErrors.card.number}
                    <div class="invalid-feedback">{formErrors.card.number}</div>
                  {/if}
                </div>
                <div class="mb-3">
                  <label for="card-name" class="form-label">Titular de la tarjeta *</label>
                  <input 
                    type="text" 
                    class="form-control"
                    class:is-invalid={formErrors.card.name}
                    id="card-name" 
                    bind:value={cardInfo.name}
                    placeholder="NOMBRE APELLIDOS"
                  />
                  {#if formErrors.card.name}
                    <div class="invalid-feedback">{formErrors.card.name}</div>
                  {/if}
                </div>
                <div class="row">
                  <div class="col-8">
                    <div class="mb-3">
                      <label for="card-expiry" class="form-label">Fecha de caducidad *</label>
                      <input 
                        type="text" 
                        class="form-control"
                        class:is-invalid={formErrors.card.expiry}
                        id="card-expiry" 
                        bind:value={cardInfo.expiry}
                        placeholder="MM/YY"
                        maxlength="5"
                        on:input={(e) => {
                          e.target.value = e.target.value
                            .replace(/\D/g, '')
                            .replace(/(\d{2})(\d)/, '$1/$2');
                        }}
                      />
                      {#if formErrors.card.expiry}
                        <div class="invalid-feedback">{formErrors.card.expiry}</div>
                      {/if}
                    </div>
                  </div>
                  <div class="col-4">
                    <div class="mb-3">
                      <label for="card-cvv" class="form-label">CVV *</label>
                      <input 
                        type="text" 
                        class="form-control"
                        class:is-invalid={formErrors.card.cvv}
                        id="card-cvv" 
                        bind:value={cardInfo.cvv}
                        placeholder="123"
                        maxlength="3"
                      />
                      {#if formErrors.card.cvv}
                        <div class="invalid-feedback">{formErrors.card.cvv}</div>
                      {/if}
                    </div>
                  </div>
                </div>
              </div>
            {/if}
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
              {showPaymentForm ? 'Confirmar y pagar' : 'Continuar'}
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
    max-width: 100%;
    overflow-x: auto;
    padding: 1rem;
  }

  .seat-row {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    min-height: 40px;
    white-space: nowrap;
  }

  .row-label {
    width: 30px;
    text-align: center;
    font-weight: bold;
  }

  .seat {
    width: 40px;
    height: 40px;
    border: 1px solid var(--app-border);
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    background: var(--app-card-bg);
    transition: all 0.2s ease;
  }

  .seat:not(:disabled):hover {
    transform: translateY(-2px);
    border-color: var(--primary-color);
  }

  .seat.selected {
    background: var(--primary-color);
    color: white;
    border-color: var(--primary-color);
  }

  .seat.occupied {
    background: var(--danger);
    color: white;
    cursor: not-allowed;
    opacity: 0.7;
  }

  .seat-number {
    font-size: 0.8rem;
    font-weight: 500;
  }

  .seat.unavailable {
    background: var(--app-border);
    opacity: 0.3;
    cursor: not-allowed;
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

  .payment-form {
    border-top: 1px solid var(--app-border);
    padding-top: 1.5rem;
  }

  .payment-card {
    background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
    border: none;
    border-radius: 1rem;
  }

  .form-control {
    background: var(--app-bg);
    border-color: var(--app-border);
    color: var(--bs-body-color);
  }

  .form-control:focus {
    background: var(--app-bg);
    border-color: var(--primary-color);
    color: var(--bs-body-color);
    box-shadow: 0 0 0 0.25rem rgba(var(--primary-rgb), 0.25);
  }

  .form-label {
    font-size: 0.9rem;
    font-weight: 500;
  }

  .invalid-feedback {
    font-size: 0.8rem;
  }
</style> 