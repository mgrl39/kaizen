<div class="bg-gray-100 dark:bg-gray-800/50 p-4 rounded-lg shadow-sm mb-6">
    <div class="flex flex-wrap gap-2">
        <button id="filter-all" class="px-4 py-2 rounded-md bg-blue-600 text-white font-medium text-sm focus:outline-none">
            Todos
        </button>
        <button id="filter-get" class="px-4 py-2 rounded-md bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-medium text-sm hover:bg-green-100 dark:hover:bg-green-900/20 hover:text-green-700 dark:hover:text-green-300 focus:outline-none transition-colors">
            <i class="fas fa-arrow-down mr-1 text-green-600 dark:text-green-400"></i> GET
        </button>
        <button id="filter-post" class="px-4 py-2 rounded-md bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-medium text-sm hover:bg-blue-100 dark:hover:bg-blue-900/20 hover:text-blue-700 dark:hover:text-blue-300 focus:outline-none transition-colors">
            <i class="fas fa-plus mr-1 text-blue-600 dark:text-blue-400"></i> POST
        </button>
        <button id="filter-put" class="px-4 py-2 rounded-md bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-medium text-sm hover:bg-amber-100 dark:hover:bg-amber-900/20 hover:text-amber-700 dark:hover:text-amber-300 focus:outline-none transition-colors">
            <i class="fas fa-edit mr-1 text-amber-600 dark:text-amber-400"></i> PUT/PATCH
        </button>
        <button id="filter-delete" class="px-4 py-2 rounded-md bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-medium text-sm hover:bg-red-100 dark:hover:bg-red-900/20 hover:text-red-700 dark:hover:text-red-300 focus:outline-none transition-colors">
            <i class="fas fa-trash mr-1 text-red-600 dark:text-red-400"></i> DELETE
        </button>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const filterAll = document.getElementById('filter-all');
        const filterGet = document.getElementById('filter-get');
        const filterPost = document.getElementById('filter-post');
        const filterPut = document.getElementById('filter-put');
        const filterDelete = document.getElementById('filter-delete');
        const routes = document.querySelectorAll('.route-item');
        
        const setActiveFilter = (button) => {
            [filterAll, filterGet, filterPost, filterPut, filterDelete].forEach(btn => {
                btn.classList.remove('bg-blue-600', 'text-white');
                btn.classList.add('bg-gray-200', 'dark:bg-gray-700', 'text-gray-700', 'dark:text-gray-300');
            });
            button.classList.remove('bg-gray-200', 'dark:bg-gray-700', 'text-gray-700', 'dark:text-gray-300');
            button.classList.add('bg-blue-600', 'text-white');
        };
        
        filterAll.addEventListener('click', function() {
            routes.forEach(route => route.classList.remove('hidden'));
            setActiveFilter(this);
        });
        
        filterGet.addEventListener('click', function() {
            routes.forEach(route => {
                if (route.classList.contains('method-get')) {
                    route.classList.remove('hidden');
                } else {
                    route.classList.add('hidden');
                }
            });
            setActiveFilter(this);
        });
        
        filterPost.addEventListener('click', function() {
            routes.forEach(route => {
                if (route.classList.contains('method-post')) {
                    route.classList.remove('hidden');
                } else {
                    route.classList.add('hidden');
                }
            });
            setActiveFilter(this);
        });
        
        filterPut.addEventListener('click', function() {
            routes.forEach(route => {
                if (route.classList.contains('method-put') || route.classList.contains('method-patch')) {
                    route.classList.remove('hidden');
                } else {
                    route.classList.add('hidden');
                }
            });
            setActiveFilter(this);
        });
        
        filterDelete.addEventListener('click', function() {
            routes.forEach(route => {
                if (route.classList.contains('method-delete')) {
                    route.classList.remove('hidden');
                } else {
                    route.classList.add('hidden');
                }
            });
            setActiveFilter(this);
        });
    });
</script> 