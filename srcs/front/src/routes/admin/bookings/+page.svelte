<script lang="ts">
  import { t } from '$lib/i18n';
  
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
  function getStatusClass(status) {
    switch (status) {
      case 'confirmed':
        return 'bg-green-100 text-green-800';
      case 'pending':
        return 'bg-yellow-100 text-yellow-800';
      case 'cancelled':
        return 'bg-red-100 text-red-800';
      default:
        return 'bg-gray-100 text-gray-800';
    }
  }
  
  // Función para obtener clase de color según el pago
  function getPaymentClass(payment) {
    switch (payment) {
      case 'paid':
        return 'bg-blue-100 text-blue-800';
      case 'pending':
        return 'bg-yellow-100 text-yellow-800';
      case 'refunded':
        return 'bg-purple-100 text-purple-800';
      default:
        return 'bg-gray-100 text-gray-800';
    }
  }
</script>

<div>
  <div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-900">{$t('bookings')}</h1>
    <a href="/admin/bookings/add" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-amber-600 hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500">
      <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
      </svg>
      {$t('addBooking')}
    </a>
  </div>
  
  <!-- Filtros y búsqueda -->
  <div class="bg-white p-5 rounded-xl shadow mb-6">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div>
        <label for="search" class="block text-sm font-medium text-gray-700">{$t('search')}</label>
        <div class="mt-1 relative rounded-md shadow-sm">
          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </div>
          <input 
            type="text" 
            name="search" 
            id="search" 
            bind:value={searchQuery}
            class="focus:ring-amber-500 focus:border-amber-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md" 
            placeholder={$t('searchBookings')}
          />
        </div>
      </div>
      
      <div>
        <label for="date" class="block text-sm font-medium text-gray-700">{$t('date')}</label>
        <select 
          id="date" 
          name="date" 
          bind:value={dateFilter}
          class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-amber-500 focus:border-amber-500 sm:text-sm rounded-md"
        >
          <option value="">{$t('allDates')}</option>
          {#each uniqueDates as date}
            <option value={date}>{date}</option>
          {/each}
        </select>
      </div>
      
      <div>
        <label for="status" class="block text-sm font-medium text-gray-700">{$t('status')}</label>
        <select 
          id="status" 
          name="status" 
          bind:value={statusFilter}
          class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-amber-500 focus:border-amber-500 sm:text-sm rounded-md"
        >
          <option value="">{$t('allStatuses')}</option>
          <option value="confirmed">{$t('confirmed')}</option>
          <option value="pending">{$t('pending')}</option>
          <option value="cancelled">{$t('cancelled')}</option>
        </select>
      </div>
    </div>
  </div>
  
  <!-- Tabla de reservas -->
  <div class="bg-white shadow rounded-xl overflow-hidden">
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              {$t('bookingInfo')}
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              {$t('movie')}
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              {$t('user')}
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              {$t('status')}
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              {$t('payment')}
            </th>
            <th scope="col" class="relative px-6 py-3">
              <span class="sr-only">{$t('actions')}</span>
            </th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          {#each filteredBookings as booking}
            <tr class="hover:bg-gray-50">
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900 font-medium">#{booking.id}</div>
                <div class="text-sm text-gray-500">{booking.date} | {booking.time}</div>
                <div class="text-sm text-gray-500">{$t('screen')} {booking.screen}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm font-medium text-gray-900">{booking.movie}</div>
                <div class="text-sm text-gray-500">{$t('seats')}: {booking.seats}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">{booking.user}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class={`px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${getStatusClass(booking.status)}`}>
                  {$t(booking.status)}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                  <span class={`px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${getPaymentClass(booking.payment)}`}>
                    {$t(booking.payment)}
                  </span>
                  <span class="ml-2 text-sm font-medium text-gray-900">${booking.price}</span>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <a href={`/admin/bookings/${booking.id}`} class="text-amber-600 hover:text-amber-900 mr-3">
                  {$t('view')}
                </a>
                <button class="text-red-600 hover:text-red-900">
                  {$t('delete')}
                </button>
              </td>
            </tr>
          {/each}
        </tbody>
      </table>
    </div>
    
    {#if filteredBookings.length === 0}
      <div class="px-6 py-8 text-center">
        <p class="text-gray-500">{$t('noBookingsFound')}</p>
      </div>
    {/if}
    
    <!-- Paginación (simplificada para este ejemplo) -->
    {#if filteredBookings.length > 0}
      <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-gray-700">
              {$t('showing')} <span class="font-medium">1</span> {$t('to')} <span class="font-medium">{filteredBookings.length}</span> {$t('of')} <span class="font-medium">{filteredBookings.length}</span> {$t('results')}
            </p>
          </div>
          <div>
            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
              <button class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                <span class="sr-only">{$t('previous')}</span>
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
              </button>
              <button aria-current="page" class="z-10 bg-amber-50 border-amber-500 text-amber-600 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                1
              </button>
              <button class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                <span class="sr-only">{$t('next')}</span>
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
              </button>
            </nav>
          </div>
        </div>
      </div>
    {/if}
  </div>
</div> 