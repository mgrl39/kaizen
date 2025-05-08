<script lang="ts">
  import { t } from '$lib/i18n';
  
  // Un solo cine en el sistema
  const cinema = {
    id: 1,
    name: 'Cineplex Central',
    location: 'Downtown',
    screens: 8,
    status: 'active',
    description: 'El mejor cine de la ciudad con las mejores instalaciones y tecnología de última generación.',
    phone: '+34 912 345 678',
    email: 'info@cineplexcentral.com',
    address: 'Calle Principal 123, 28001 Madrid'
  };
</script>

<div>
  <div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-900">{$t('cinema')}</h1>
    <a href="/admin/cinemas/edit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
      <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
      </svg>
      {$t('editCinema')}
    </a>
  </div>
  
  <!-- Información del cine -->
  <div class="bg-white rounded-xl shadow overflow-hidden">
    <div class="p-6">
      <div class="mb-6">
        <h2 class="text-xl font-bold text-gray-900 mb-3">{cinema.name}</h2>
        <p class="text-gray-600">{cinema.description}</p>
      </div>
      
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="space-y-4">
          <div>
            <h3 class="text-sm font-medium text-gray-500 uppercase">{$t('location')}</h3>
            <p class="text-gray-900 mt-1">{cinema.location}</p>
          </div>
          
          <div>
            <h3 class="text-sm font-medium text-gray-500 uppercase">{$t('address')}</h3>
            <p class="text-gray-900 mt-1">{cinema.address}</p>
          </div>
          
          <div>
            <h3 class="text-sm font-medium text-gray-500 uppercase">{$t('contact')}</h3>
            <p class="text-gray-900 mt-1">{cinema.phone}</p>
            <p class="text-gray-900">{cinema.email}</p>
          </div>
        </div>
        
        <div class="space-y-4">
          <div>
            <h3 class="text-sm font-medium text-gray-500 uppercase">{$t('screens')}</h3>
            <p class="text-3xl font-bold text-gray-900 mt-1">{cinema.screens}</p>
          </div>
          
          <div>
            <h3 class="text-sm font-medium text-gray-500 uppercase">{$t('status')}</h3>
            <span class="mt-1 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
              {$t(cinema.status)}
            </span>
          </div>
          
          <div>
            <h3 class="text-sm font-medium text-gray-500 uppercase">{$t('features')}</h3>
            <div class="mt-2 flex flex-wrap gap-2">
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-sm font-medium bg-blue-100 text-blue-800">
                IMAX
              </span>
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-sm font-medium bg-blue-100 text-blue-800">
                3D
              </span>
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-sm font-medium bg-blue-100 text-blue-800">
                Dolby Atmos
              </span>
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-sm font-medium bg-blue-100 text-blue-800">
                VIP
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Salas del cine -->
  <div class="mt-8">
    <h2 class="text-xl font-bold text-gray-900 mb-4">{$t('screens')}</h2>
    
    <div class="bg-white rounded-xl shadow">
      <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">{$t('screensList')}</h3>
      </div>
      <ul class="divide-y divide-gray-200">
        {#each Array(cinema.screens) as _, i}
          <li class="px-4 py-4 sm:px-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-gray-900">Sala {i+1}</p>
                <p class="text-sm text-gray-500">
                  {i < 2 ? 'IMAX' : i < 4 ? '3D' : 'Standard'} | 
                  {Math.floor(Math.random() * 50) + 100} asientos
                </p>
              </div>
              <div>
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                  {$t('active')}
                </span>
              </div>
            </div>
          </li>
        {/each}
      </ul>
    </div>
  </div>
</div> 