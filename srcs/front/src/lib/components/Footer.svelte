<script lang="ts">
  const links = [
    { href: "/", text: "Inicio" },
    { href: "/cinemas", text: "Cines" },
    { href: "/movies", text: "Películas" }
  ];
</script>

<footer class="py-6 bg-gray-900 border-t border-gray-800">
  <div class="container mx-auto px-4">
    <div class="flex flex-col md:flex-row items-center justify-between">
      <div class="flex items-center mb-4 md:mb-0">
        <a href="/" class="text-white flex items-center">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h18M3 16h18" />
          </svg>
          <span class="font-medium">Kaizen</span>
        </a>
        <span class="ml-3 text-gray-400 text-sm">MIT License</span>
      </div>

      <nav class="flex items-center space-x-6">
        {#each links as link}
          <a href={link.href} class="text-gray-400 hover:text-white transition-colors duration-200">{link.text}</a>
        {/each}
      </nav>
    </div>
  </div>
</footer>