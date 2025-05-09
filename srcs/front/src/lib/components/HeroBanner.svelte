<script lang="ts">
  import { t } from '$lib/i18n';
  
  // Props para personalizar el banner
  export let title: string = "";
  export let subtitle: string = "";
  export let imageUrl: string = "https://source.unsplash.com/1200x600/?cinema";
  export let overlayOpacity: string = "50"; // 0-100
  
  // Convertir la opacidad a un valor decimal para CSS
  $: overlayStyle = `rgba(0, 0, 0, ${parseInt(overlayOpacity) / 100})`;
</script>

<div class="relative w-full h-80 md:h-96 overflow-hidden">
  <!-- Imagen de fondo -->
  <img 
    src={imageUrl} 
    alt={title} 
    class="w-full h-full object-cover"
  />
  
  <!-- Overlay con gradiente -->
  <div class="absolute inset-0 bg-gradient-to-t from-black via-black/70 to-transparent" style="--tw-bg-opacity: {overlayStyle};"></div>
  
  <!-- Contenido del banner -->
  <div class="absolute inset-0 flex flex-col justify-center items-center text-center px-4">
    <h1 class="text-4xl md:text-5xl font-bold text-white mb-4 text-shadow-lg">{title}</h1>
    {#if subtitle}
      <p class="text-lg md:text-xl text-white/80 max-w-2xl">{subtitle}</p>
    {/if}
  </div>
</div>

<style>
  .text-shadow-lg {
    text-shadow: 0 0 20px rgba(139, 92, 246, 0.5);
  }
</style>