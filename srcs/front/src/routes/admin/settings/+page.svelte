<script lang="ts">
  import { t } from '$lib/i18n';
  
  // Configuraciones de ejemplo (en un entorno real se cargarían desde una API)
  let settings = {
    // Configuración general
    siteName: 'Cineplex Central',
    siteDescription: 'Tu cine de confianza',
    contactEmail: 'info@cineplexcentral.com',
    contactPhone: '+34 912 345 678',
    
    // Configuración de reservas
    allowOnlineBooking: true,
    advanceBookingDays: 7,
    maxTicketsPerBooking: 10,
    bookingFee: 0.50,
    
    // Configuración de notificaciones
    sendEmailConfirmations: true,
    sendSmsReminders: false,
    reminderHoursBefore: 24,
    
    // Configuración de precios
    standardTicketPrice: 12,
    childrenTicketPrice: 8,
    seniorTicketPrice: 9,
    discountTuesday: true
  };
  
  // Función para guardar configuración
  function saveSettings() {
    // En un entorno real, aquí harías una llamada a la API
    console.log('Guardando configuración:', settings);
    alert('Configuración guardada correctamente');
  }
</script>

<div>
  <div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">{$t('settings')}</h1>
  </div>
  
  <form on:submit|preventDefault={saveSettings} class="space-y-6">
    <!-- Configuración general -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
      <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
        <h3 class="text-lg font-medium text-gray-900">{$t('generalSettings')}</h3>
      </div>
      <div class="p-6 space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label for="site-name" class="block text-sm font-medium text-gray-700">{$t('siteName')}</label>
            <input 
              type="text" 
              id="site-name" 
              bind:value={settings.siteName}
              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
            />
          </div>
          
          <div>
            <label for="site-description" class="block text-sm font-medium text-gray-700">{$t('siteDescription')}</label>
            <input 
              type="text" 
              id="site-description" 
              bind:value={settings.siteDescription}
              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
            />
          </div>
          
          <div>
            <label for="contact-email" class="block text-sm font-medium text-gray-700">{$t('contactEmail')}</label>
            <input 
              type="email" 
              id="contact-email" 
              bind:value={settings.contactEmail}
              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
            />
          </div>
          
          <div>
            <label for="contact-phone" class="block text-sm font-medium text-gray-700">{$t('contactPhone')}</label>
            <input 
              type="tel" 
              id="contact-phone" 
              bind:value={settings.contactPhone}
              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
            />
          </div>
        </div>
      </div>
    </div>
    
    <!-- Configuración de precios -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
      <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
        <h3 class="text-lg font-medium text-gray-900">{$t('pricingSettings')}</h3>
      </div>
      <div class="p-6 space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label for="standard-price" class="block text-sm font-medium text-gray-700">{$t('standardTicketPrice')}</label>
            <div class="mt-1 relative rounded-md shadow-sm">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <span class="text-gray-500 sm:text-sm">$</span>
              </div>
              <input 
                type="number" 
                id="standard-price" 
                bind:value={settings.standardTicketPrice}
                class="mt-1 block w-full pl-7 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                min="0"
                step="0.01"
              />
            </div>
          </div>
          
          <div>
            <label for="children-price" class="block text-sm font-medium text-gray-700">{$t('childrenTicketPrice')}</label>
            <div class="mt-1 relative rounded-md shadow-sm">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <span class="text-gray-500 sm:text-sm">$</span>
              </div>
              <input 
                type="number" 
                id="children-price" 
                bind:value={settings.childrenTicketPrice}
                class="mt-1 block w-full pl-7 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                min="0"
                step="0.01"
              />
            </div>
          </div>
          
          <div>
            <label for="senior-price" class="block text-sm font-medium text-gray-700">{$t('seniorTicketPrice')}</label>
            <div class="mt-1 relative rounded-md shadow-sm">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <span class="text-gray-500 sm:text-sm">$</span>
              </div>
              <input 
                type="number" 
                id="senior-price" 
                bind:value={settings.seniorTicketPrice}
                class="mt-1 block w-full pl-7 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                min="0"
                step="0.01"
              />
            </div>
          </div>
          
          <div class="flex items-center pt-4">
            <input 
              type="checkbox" 
              id="discount-tuesday" 
              bind:checked={settings.discountTuesday}
              class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
            />
            <label for="discount-tuesday" class="ml-2 block text-sm text-gray-700">
              {$t('discountTuesday')}
            </label>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Configuración de reservas -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
      <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
        <h3 class="text-lg font-medium text-gray-900">{$t('bookingSettings')}</h3>
      </div>
      <div class="p-6 space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="flex items-center">
            <input 
              type="checkbox" 
              id="allow-online-booking" 
              bind:checked={settings.allowOnlineBooking}
              class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
            />
            <label for="allow-online-booking" class="ml-2 block text-sm text-gray-700">
              {$t('allowOnlineBooking')}
            </label>
          </div>
          
          <div>
            <label for="advance-booking-days" class="block text-sm font-medium text-gray-700">{$t('advanceBookingDays')}</label>
            <input 
              type="number" 
              id="advance-booking-days" 
              bind:value={settings.advanceBookingDays}
              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
              min="1"
              max="30"
            />
          </div>
          
          <div>
            <label for="max-tickets" class="block text-sm font-medium text-gray-700">{$t('maxTicketsPerBooking')}</label>
            <input 
              type="number" 
              id="max-tickets" 
              bind:value={settings.maxTicketsPerBooking}
              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
              min="1"
              max="20"
            />
          </div>
          
          <div>
            <label for="booking-fee" class="block text-sm font-medium text-gray-700">{$t('bookingFee')}</label>
            <div class="mt-1 relative rounded-md shadow-sm">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <span class="text-gray-500 sm:text-sm">$</span>
              </div>
              <input 
                type="number" 
                id="booking-fee" 
                bind:value={settings.bookingFee}
                class="mt-1 block w-full pl-7 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                min="0"
                step="0.01"
              />
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Botones de acción -->
    <div class="flex justify-end mt-6">
      <button
        type="button"
        class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
      >
        {$t('cancel')}
      </button>
      <button
        type="submit"
        class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
      >
        {$t('saveSettings')}
      </button>
    </div>
  </form>
</div> 