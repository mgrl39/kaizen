<script lang="ts">
  export let title: string;
  export let value: string;
  export let iconSvg: string;
  export let color: string = "bg-blue-500";
  export let link: string = "#";
</script>

<a href={link} class="block bg-white rounded-lg shadow hover:shadow-md transition-shadow">
  <div class="p-5">
    <div class="flex items-center">
      <div class="{color} text-white p-3 rounded-lg">
        {@html iconSvg}
      </div>
      <div class="ml-5">
        <h3 class="text-lg font-medium text-gray-700">{title}</h3>
        <p class="text-2xl font-semibold">{value}</p>
      </div>
    </div>
  </div>
</a> 