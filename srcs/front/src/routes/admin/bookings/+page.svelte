<script lang="ts">
  import { t } from '$lib/i18n';
  import { onMount } from 'svelte';
  
  // Datos de ejemplo para las reservas
  const bookings = [
    { 
      id: 1, 
      movie: "Dune: Part Two", 
      screen: 3, 
      date: "2023-07-18", 
      time: "18:30", 
      user: "juan.perez@example.com", 
      seats: "A12, A13", 
      status: "confirmed",
      payment: "paid",
      price: "24.00"
    },
    { 
      id: 2, 
      movie: "The Batman", 
      screen: 1, 
      date: "2023-07-19", 
      time: "20:15", 
      user: "maria.garcia@example.com", 
      seats: "B5, B6, B7", 
      status: "confirmed",
      payment: "paid",
      price: "36.00"
    },
    { 
      id: 3, 
      movie: "Interstellar", 
      screen: 2, 
      date: "2023-07-18", 
      time: "19:45", 
      user: "carlos.lopez@example.com", 
      seats: "C8", 
      status: "confirmed",
      payment: "paid",
      price: "12.00"
    },
    { 
      id: 4, 
      movie: "Pulp Fiction", 
      screen: 4, 
      date: "2023-07-20", 
      time: "21:00", 
      user: "ana.martinez@example.com", 
      seats: "D11, D12", 
      status: "pending",
      payment: "pending",
      price: "24.00"
    },
    { 
      id: 5, 
      movie: "Inception", 
      screen: 2, 
      date: "2023-07-21", 
      time: "17:30", 
      user: "pablo.diaz@example.com", 
      seats: "E7, E8, E9, E10", 
      status: "cancelled",
      payment: "refunded",
      price: "48.00"
    }
  ];
  
  // Estadísticas
  const totalBookings = bookings.length;
  const confirmedBookings = bookings.filter(b => b.status === 'confirmed').length;
  const totalRevenue = bookings
    .filter(b => b.payment === 'paid')
    .reduce((sum, booking) => sum + parseFloat(booking.price), 0)
    .toFixed(2);
  
  // Estado para filtros
  let statusFilter = "";
  let dateFilter = "";
  let searchQuery = "";
  
  // Filtrar bookings
  $: filteredBookings = bookings.filter(booking => {
    let matchesStatus = true;
    let matchesDate = true;
    let matchesSearch = true;
    
    if (statusFilter && booking.status !== statusFilter) {
      matchesStatus = false;
    }
    
    if (dateFilter && booking.date !== dateFilter) {
      matchesDate = false;
    }
    
    if (searchQuery) {
      const query = searchQuery.toLowerCase();
      matchesSearch = 
        booking.movie.toLowerCase().includes(query) ||
        booking.user.toLowerCase().includes(query) ||
        booking.seats.toLowerCase().includes(query);
    }
    
    return matchesStatus && matchesDate && matchesSearch;
  });
  
  // Fechas únicas para el filtro
  $: uniqueDates = [...new Set(bookings.map(b => b.date))].sort();
  
  // Función para obtener clase de color según el estado
  function getStatusBadgeClass(status) {
    switch (status) {
      case 'confirmed':
        return 'bg-success';
      case 'pending':
        return 'bg-warning';
      case 'cancelled':
        return 'bg-danger';
      default:
        return 'bg-secondary';
    }
  }
  
  // Función para obtener clase de color según el pago
  function getPaymentBadgeClass(payment) {
    switch (payment) {
      case 'paid':
        return 'bg-primary';
      case 'pending':
        return 'bg-warning';
      case 'refunded':
        return 'bg-info';
      default:
        return 'bg-secondary';
    }
  }
  
  function resetFilters() {
    statusFilter = "";
    dateFilter = "";
    searchQuery = "";
  }
  
  // Seleccionar/deseleccionar reservas
  let selectedBookings = new Set();
  let selectAll = false;
  
  $: {
    if (selectAll) {
      selectedBookings = new Set(filteredBookings.map(booking => booking.id));
    }
  }
  
  function toggleSelectAll() {
    selectAll = !selectAll;
    if (!selectAll) {
      selectedBookings = new Set();
    }
  }
  
  function toggleSelectBooking(id) {
    if (selectedBookings.has(id)) {
      selectedBookings.delete(id);
      selectAll = false;
    } else {
      selectedBookings.add(id);
      if (selectedBookings.size === filteredBookings.length) {
        selectAll = true;
      }
    }
    selectedBookings = selectedBookings; // Trigger reactivity
  }
  
  onMount(() => {
    // Initialize any Bootstrap components if needed
  });
</script>

<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3">{$t('bookings')}</h1>
    <div>
      {#if selectedBookings.size > 0}
        <div class="btn-group me-2">
          <button class="btn btn-sm btn-outline-primary">
            <i class="bi bi-printer me-1"></i> {$t('print')}
          </button>
          <button class="btn btn-sm btn-outline-danger">
            <i class="bi bi-trash me-1"></i> {$t('delete')}
          </button>
        </div>
      {/if}
      <a href="/admin/bookings/add" class="btn btn-warning">
        <i class="bi bi-plus-circle me-2"></i>
        {$t('addBooking')}
      </a>
    </div>
  </div>
  
  <!-- Dashboard Cards -->
  <div class="row g-4 mb-4">
    <div class="col-md-4">
      <div class="card border-primary h-100">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <div class="bg-light p-3 rounded me-3">
              <i class="bi bi-ticket-perforated-fill text-primary fs-4"></i>
            </div>
            <div>
              <h6 class="card-subtitle mb-1 text-muted">{$t('totalBookings')}</h6>
              <h2 class="card-title mb-0">{totalBookings}</h2>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card border-success h-100">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <div class="bg-light p-3 rounded me-3">
              <i class="bi bi-check-circle text-success fs-4"></i>
            </div>
            <div>
              <h6 class="card-subtitle mb-1 text-muted">{$t('confirmedBookings')}</h6>
              <h2 class="card-title mb-0">{confirmedBookings}</h2>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card border-info h-100">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <div class="bg-light p-3 rounded me-3">
              <i class="bi bi-cash-coin text-info fs-4"></i>
            </div>
            <div>
              <h6 class="card-subtitle mb-1 text-muted">{$t('revenue')}</h6>
              <h2 class="card-title mb-0">€{totalRevenue}</h2>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Filtros y búsqueda -->
  <div class="card mb-4">
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-5">
          <div class="input-group">
            <span class="input-group-text">
              <i class="bi bi-search"></i>
            </span>
            <input 
              type="text" 
              class="form-control" 
              placeholder={$t('searchBookings')}
              bind:value={searchQuery}
            />
          </div>
        </div>
        
        <div class="col-md-3">
          <select 
            class="form-select"
            bind:value={dateFilter}
          >
            <option value="">{$t('allDates')}</option>
            {#each uniqueDates as date}
              <option value={date}>{date}</option>
            {/each}
          </select>
        </div>
        
        <div class="col-md-3">
          <select 
            class="form-select"
            bind:value={statusFilter}
          >
            <option value="">{$t('allStatuses')}</option>
            <option value="confirmed">{$t('confirmed')}</option>
            <option value="pending">{$t('pending')}</option>
            <option value="cancelled">{$t('cancelled')}</option>
          </select>
        </div>
        
        <div class="col-md-1 d-flex align-items-center">
          <button class="btn btn-outline-secondary w-100" on:click={resetFilters}>
            <i class="bi bi-x-circle"></i>
          </button>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Tabla de reservas -->
  <div class="card">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead class="table-light">
            <tr>
              <th scope="col" width="40">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" id="selectAll" bind:checked={selectAll} on:click={toggleSelectAll}>
                </div>
              </th>
              <th scope="col">{$t('id')}</th>
              <th scope="col">{$t('movie')}</th>
              <th scope="col">{$t('dateTime')}</th>
              <th scope="col">{$t('user')}</th>
              <th scope="col">{$t('seats')}</th>
              <th scope="col">{$t('status')}</th>
              <th scope="col">{$t('payment')}</th>
              <th scope="col" class="text-end">{$t('actions')}</th>
            </tr>
          </thead>
          <tbody>
            {#each filteredBookings as booking}
              <tr class={selectedBookings.has(booking.id) ? 'table-active' : ''}>
                <td>
                  <div class="form-check">
                    <input 
                      class="form-check-input" 
                      type="checkbox" 
                      checked={selectedBookings.has(booking.id)}
                      on:click={() => toggleSelectBooking(booking.id)}
                    >
                  </div>
                </td>
                <td><strong>#{booking.id}</strong></td>
                <td>{booking.movie}</td>
                <td>
                  <div>{booking.date}</div>
                  <small class="text-muted">{booking.time} - {$t('screen')} {booking.screen}</small>
                </td>
                <td>{booking.user}</td>
                <td>{booking.seats}</td>
                <td>
                  <span class={`badge ${getStatusBadgeClass(booking.status)}`}>
                    {$t(booking.status)}
                  </span>
                </td>
                <td>
                  <div class="d-flex align-items-center">
                    <span class={`badge ${getPaymentBadgeClass(booking.payment)} me-2`}>
                      {$t(booking.payment)}
                    </span>
                    <strong>€{booking.price}</strong>
                  </div>
                </td>
                <td class="text-end">
                  <div class="btn-group">
                    <a href={`/admin/bookings/${booking.id}`} class="btn btn-sm btn-outline-secondary">
                      <i class="bi bi-eye"></i>
                    </a>
                    <a href={`/admin/bookings/${booking.id}/edit`} class="btn btn-sm btn-outline-primary">
                      <i class="bi bi-pencil"></i>
                    </a>
                    <button class="btn btn-sm btn-outline-danger">
                      <i class="bi bi-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
            {/each}
            
            {#if filteredBookings.length === 0}
              <tr>
                <td colspan="9" class="text-center py-4">
                  <i class="bi bi-search fs-4 mb-2 d-block text-muted"></i>
                  <p class="text-muted">{$t('noBookingsFound')}</p>
                </td>
              </tr>
            {/if}
          </tbody>
        </table>
      </div>
    </div>
    <div class="card-footer bg-white d-flex justify-content-between align-items-center">
      <div>
        {#if filteredBookings.length > 0}
          <small class="text-muted">
            {$t('showing')} {filteredBookings.length} {$t('of')} {bookings.length} {$t('bookings')}
          </small>
        {/if}
      </div>
      <nav aria-label="Booking pagination">
        <ul class="pagination pagination-sm mb-0">
          <li class="page-item disabled"><a class="page-link" href="#">&laquo;</a></li>
          <li class="page-item active"><a class="page-link" href="#">1</a></li>
          <li class="page-item"><a class="page-link" href="#">2</a></li>
          <li class="page-item"><a class="page-link" href="#">3</a></li>
          <li class="page-item"><a class="page-link" href="#">&raquo;</a></li>
        </ul>
      </nav>
    </div>
  </div>
</div> 