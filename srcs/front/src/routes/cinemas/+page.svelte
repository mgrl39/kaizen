<script lang="ts">
  import { t } from '$lib/i18n';
  import HeroBanner from '$lib/components/HeroBanner.svelte';

  // Datos hardcodeados del cine
  const cinema = {
    id: 1,
    name: 'Kaizen Cinema',
    location: 'Madrid',
    address: 'Calle Gran Vía 123, Madrid',
    phone: '+34 912 345 678',
    email: 'info@kaizencinema.com',
    description: 'Bienvenidos a Kaizen Cinema, el lugar perfecto para disfrutar de las mejores películas con la mejor calidad de imagen y sonido. Nuestras modernas instalaciones están diseñadas para ofrecerte la mejor experiencia cinematográfica.',
    image_url: 'https://source.unsplash.com/random/1920x1080/?cinema,theater,auditorium',
    has_3d: true,
    has_imax: true,
    has_vip: true,
    rooms_count: 5,
    opening_hours: '10:00 - 00:00',
    features: ['Parking gratuito', 'Cafetería', 'Zona infantil', 'Acceso para discapacitados']
  };
  
  // Información de salas del cine
  const rooms = [
    { id: 1, name: 'Sala 1', capacity: 120, features: ['3D', 'Dolby Atmos'] },
    { id: 2, name: 'Sala 2', capacity: 150, features: ['IMAX', 'Dolby Atmos'] },
    { id: 3, name: 'Sala 3', capacity: 80, features: ['VIP'] },
    { id: 4, name: 'Sala 4', capacity: 100, features: [] },
    { id: 5, name: 'Sala 5', capacity: 90, features: ['3D'] }
  ];
</script>

<!-- Hero Banner con imagen específica para la página de cines -->
<HeroBanner 
  title="Nuestro Cine"
  subtitle="Descubre el mejor lugar para disfrutar del séptimo arte"
  imageUrl={cinema.image_url}
  overlayOpacity="60"
/>

<div class="container mx-auto px-4 py-8">
  <!-- Información principal y características -->
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-10">
    <!-- Columna izquierda: Información de contacto -->
    <div class="bg-card border border-white/10 rounded-lg p-6">
      <h3 class="text-xl font-bold mb-4 pb-2 border-b border-white/10">Información de contacto</h3>
      
      <div class="space-y-4 text-gray-300">
        <div class="flex items-start">
          <i class="bi bi-geo-alt text-purple-400 mr-3 mt-1"></i>
          <div>
            <p class="font-medium text-white">Dirección</p>
            <p>{cinema.address}</p>
            <a 
              href={`https://maps.google.com/?q=${encodeURIComponent(cinema.address)}`} 
              target="_blank" 
              rel="noopener noreferrer"
              class="text-purple-400 hover:text-purple-300 text-sm inline-flex items-center mt-1"
            >
              <i class="bi bi-map mr-1"></i>Ver en mapa
            </a>
          </div>
        </div>
        
        <div class="flex items-start">
          <i class="bi bi-telephone text-purple-400 mr-3 mt-1"></i>
          <div>
            <p class="font-medium text-white">Teléfono</p>
            <p>{cinema.phone}</p>
          </div>
        </div>
        
        <div class="flex items-start">
          <i class="bi bi-envelope text-purple-400 mr-3 mt-1"></i>
          <div>
            <p class="font-medium text-white">Email</p>
            <p>{cinema.email}</p>
          </div>
        </div>
        
        <div class="flex items-start">
          <i class="bi bi-clock text-purple-400 mr-3 mt-1"></i>
          <div>
            <p class="font-medium text-white">Horario</p>
            <p>{cinema.opening_hours}</p>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Columna central: Descripción y características -->
    <div class="bg-card border border-white/10 rounded-lg p-6">
      <h3 class="text-xl font-bold mb-4 pb-2 border-b border-white/10">Sobre nosotros</h3>
      
      <p class="text-gray-300 mb-6">{cinema.description}</p>
      
      <h4 class="font-bold text-white mb-3">Características</h4>
      <div class="flex flex-wrap gap-2 mb-4">
        {#if cinema.has_3d}
          <span class="bg-purple-900/40 border border-purple-500/30 text-purple-200 text-xs py-1 px-2 rounded-md">
            <i class="bi bi-badge-3d mr-1"></i>3D
          </span>
        {/if}
        
        {#if cinema.has_imax}
          <span class="bg-blue-900/40 border border-blue-500/30 text-blue-200 text-xs py-1 px-2 rounded-md">
            <i class="bi bi-badge-hd mr-1"></i>IMAX
          </span>
        {/if}
        
        {#if cinema.has_vip}
          <span class="bg-amber-900/40 border border-amber-500/30 text-amber-200 text-xs py-1 px-2 rounded-md">
            <i class="bi bi-star mr-1"></i>Salas VIP
          </span>
        {/if}
      </div>
      
      <h4 class="font-bold text-white mb-3">Servicios</h4>
      <ul class="grid grid-cols-2 gap-2">
        {#each cinema.features as feature}
          <li class="flex items-center text-gray-300">
            <i class="bi bi-check-circle text-green-400 mr-2"></i>
            {feature}
          </li>
        {/each}
      </ul>
    </div>
    
    <!-- Columna derecha: Botones de acción -->
    <div class="bg-card border border-white/10 rounded-lg p-6">
      <h3 class="text-xl font-bold mb-4 pb-2 border-b border-white/10">Acciones rápidas</h3>
      
      <div class="space-y-4">
        <a 
          href="/movies" 
          class="bg-purple-800 hover:bg-purple-700 text-white py-3 px-4 rounded-md flex items-center justify-center transition-colors w-full"
        >
          <i class="bi bi-film mr-2 text-lg"></i>
          Ver cartelera
        </a>
        
        <a 
          href="/bookings/new" 
          class="bg-gradient-to-r from-purple-700 to-indigo-700 hover:from-purple-600 hover:to-indigo-600 text-white py-3 px-4 rounded-md flex items-center justify-center transition-colors w-full"
        >
          <i class="bi bi-ticket-perforated mr-2 text-lg"></i>
          Comprar entradas
        </a>
        
        <a 
          href="/contact" 
          class="bg-transparent border border-white/20 text-white py-3 px-4 rounded-md flex items-center justify-center hover:bg-white/10 transition-colors w-full"
        >
          <i class="bi bi-chat-dots mr-2 text-lg"></i>
          Contactar
        </a>
      </div>
      
      <div class="mt-6">
        <h4 class="font-bold text-white mb-3">Síguenos</h4>
        <div class="flex space-x-3">
          <a href="#" class="bg-white/10 hover:bg-white/20 p-2 rounded-full transition-colors">
            <i class="bi bi-facebook text-lg"></i>
          </a>
          <a href="#" class="bg-white/10 hover:bg-white/20 p-2 rounded-full transition-colors">
            <i class="bi bi-instagram text-lg"></i>
          </a>
          <a href="#" class="bg-white/10 hover:bg-white/20 p-2 rounded-full transition-colors">
            <i class="bi bi-twitter text-lg"></i>
          </a>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Sección de salas -->
  <div class="mb-10">
    <h3 class="text-2xl font-bold mb-6">Nuestras salas</h3>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      {#each rooms as room}
        <div class="bg-card border border-white/10 rounded-lg overflow-hidden">
          <div class="p-5">
            <h4 class="text-lg font-bold mb-2">{room.name}</h4>
            <p class="text-gray-300 mb-3">Capacidad: {room.capacity} personas</p>
            
            {#if room.features.length > 0}
              <div class="flex flex-wrap gap-2">
                {#each room.features as feature}
                  <span class="bg-white/10 text-white text-xs py-1 px-2 rounded-md">
                    {feature}
                  </span>
                {/each}
              </div>
            {/if}
          </div>
        </div>
      {/each}
    </div>
  </div>
  
  <!-- Botón para ver cartelera -->
  <div class="text-center">
    <a 
      href="/movies" 
      class="inline-flex items-center justify-center bg-purple-800 hover:bg-purple-700 text-white py-3 px-6 rounded-md transition-colors"
    >
      <i class="bi bi-film mr-2"></i>
      Ver películas en cartelera
    </a>
  </div>
</div>