<script lang="ts">
  import { t } from '$lib/i18n';
  
  // Datos del único cine en el sistema
  let name = 'Cineplex Central';
  let location = 'Downtown';
  let address = 'Calle Principal 123, 28001 Madrid';
  let description = 'El mejor cine de la ciudad con las mejores instalaciones y tecnología de última generación.';
  let screens = 8;
  let phone = '+34 912 345 678';
  let email = 'info@cineplexcentral.com';
  let features = ['IMAX', '3D', 'Dolby Atmos', 'VIP'];
  let status = 'active';
  
  // Lista de características disponibles
  const availableFeatures = [
    'IMAX', '3D', 'Dolby Atmos', 'VIP', '4DX', 'ScreenX', 'Premium', 'Bar', 'Restaurant'
  ];
  
  // Función para manejar el envío del formulario
  function handleSubmit() {
    // Simulación de envío - en un entorno real, esto enviaría los datos a la API
    console.log({
      name,
      location,
      address,
      description,
      screens,
      phone,
      email,
      features,
      status
    });
    
    // Aquí iría la lógica para enviar los datos a tu backend
    alert('Información del cine actualizada correctamente');
  }
  
  // Función para volver atrás
  function goBack() {
    window.history.back();
  }
</script>

<div>
  <div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-900">{$t('editCinema')}</h1>
    <a href="/admin/cinemas" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
      <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
      </svg>
      {$t('backToCinema')}
    </a>
  </div>
  
  <div class="bg-white shadow overflow-hidden rounded-xl">
    <form on:submit|preventDefault={handleSubmit} class="p-6 space-y-6">
      <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
        <!-- Nombre del cine -->
        <div class="sm:col-span-4">
          <label for="name" class="block text-sm font-medium text-gray-700">{$t('name')}</label>
          <div class="mt-1">
            <input 
              type="text" 
              name="name" 
              id="name" 
              bind:value={name}
              required
              class="shadow-sm focus:ring-emerald-500 focus:border-emerald-500 block w-full sm:text-sm border-gray-300 rounded-md" 
            />
          </div>
        </div>
        
        <!-- Ubicación -->
        <div class="sm:col-span-3">
          <label for="location" class="block text-sm font-medium text-gray-700">{$t('location')}</label>
          <div class="mt-1">
            <input 
              type="text" 
              name="location" 
              id="location" 
              bind:value={location}
              required
              class="shadow-sm focus:ring-emerald-500 focus:border-emerald-500 block w-full sm:text-sm border-gray-300 rounded-md" 
            />
          </div>
        </div>
        
        <!-- Número de salas -->
        <div class="sm:col-span-1">
          <label for="screens" class="block text-sm font-medium text-gray-700">{$t('screens')}</label>
          <div class="mt-1">
            <input 
              type="number" 
              name="screens" 
              id="screens" 
              bind:value={screens}
              min="1" 
              max="20"
              required
              class="shadow-sm focus:ring-emerald-500 focus:border-emerald-500 block w-full sm:text-sm border-gray-300 rounded-md" 
            />
          </div>
        </div>
        
        <!-- Estado -->
        <div class="sm:col-span-2">
          <label for="status" class="block text-sm font-medium text-gray-700">{$t('status')}</label>
          <div class="mt-1">
            <select 
              id="status" 
              name="status" 
              bind:value={status}
              class="shadow-sm focus:ring-emerald-500 focus:border-emerald-500 block w-full sm:text-sm border-gray-300 rounded-md"
            >
              <option value="active">{$t('active')}</option>
              <option value="inactive">{$t('inactive')}</option>
              <option value="maintenance">{$t('maintenance')}</option>
            </select>
          </div>
        </div>
        
        <!-- Dirección -->
        <div class="sm:col-span-6">
          <label for="address" class="block text-sm font-medium text-gray-700">{$t('address')}</label>
          <div class="mt-1">
            <input 
              type="text" 
              name="address" 
              id="address" 
              bind:value={address}
              required
              class="shadow-sm focus:ring-emerald-500 focus:border-emerald-500 block w-full sm:text-sm border-gray-300 rounded-md" 
            />
          </div>
        </div>
        
        <!-- Descripción -->
        <div class="sm:col-span-6">
          <label for="description" class="block text-sm font-medium text-gray-700">{$t('description')}</label>
          <div class="mt-1">
            <textarea 
              id="description" 
              name="description" 
              bind:value={description}
              rows="4" 
              class="shadow-sm focus:ring-emerald-500 focus:border-emerald-500 block w-full sm:text-sm border border-gray-300 rounded-md"
            ></textarea>
          </div>
          <p class="mt-2 text-sm text-gray-500">{$t('briefDescriptionOfCinema')}</p>
        </div>
        
        <!-- Teléfono -->
        <div class="sm:col-span-3">
          <label for="phone" class="block text-sm font-medium text-gray-700">{$t('phone')}</label>
          <div class="mt-1">
            <input 
              type="tel" 
              name="phone" 
              id="phone" 
              bind:value={phone}
              class="shadow-sm focus:ring-emerald-500 focus:border-emerald-500 block w-full sm:text-sm border-gray-300 rounded-md" 
            />
          </div>
        </div>
        
        <!-- Email -->
        <div class="sm:col-span-3">
          <label for="email" class="block text-sm font-medium text-gray-700">{$t('email')}</label>
          <div class="mt-1">
            <input 
              type="email" 
              name="email" 
              id="email" 
              bind:value={email}
              class="shadow-sm focus:ring-emerald-500 focus:border-emerald-500 block w-full sm:text-sm border-gray-300 rounded-md" 
            />
          </div>
        </div>
        
        <!-- Características -->
        <div class="sm:col-span-6">
          <label class="block text-sm font-medium text-gray-700">{$t('features')}</label>
          <div class="mt-2 space-y-2">
            <div class="flex flex-wrap gap-2">
              {#each availableFeatures as feature}
                <label class="inline-flex items-center">
                  <input 
                    type="checkbox" 
                    bind:group={features} 
                    value={feature} 
                    class="focus:ring-emerald-500 h-4 w-4 text-emerald-600 border-gray-300 rounded"
                  />
                  <span class="ml-2 text-sm text-gray-700">{feature}</span>
                </label>
              {/each}
            </div>
          </div>
        </div>
      </div>
      
      <div class="pt-5 border-t border-gray-200">
        <div class="flex justify-end">
          <button 
            type="button" 
            class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500"
            on:click={goBack}
          >
            {$t('cancel')}
          </button>
          <button 
            type="submit" 
            class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500"
          >
            {$t('save')}
          </button>
        </div>
      </div>
    </form>
  </div>
</div> 