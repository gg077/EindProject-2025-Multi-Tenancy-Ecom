<div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm">
    <div class="space-y-4">
        <!-- Zoekveld  -->
        <div>
            <input type="text"
                   id="search"
                   wire:model.live.debounce.300ms="search"
                   class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 text-sm"
                   placeholder="Search products...">
        </div>

        <!-- Filters: Categorie & Sortering -->
        <div class="grid grid-cols-2 gap-3">
            <!-- Category Filter -->
            <div>
                <select id="category"
                        wire:model.live="selectedCategory"
                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 text-sm">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Sort By -->
            <div>
                <select id="sortBy"
                        wire:model.live="sortBy"
                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 text-sm">
                    <option value="name_asc">Name (A-Z)</option>
                    <option value="name_desc">Name (Z-A)</option>
                    <option value="price_asc">Price (Low to High)</option>
                    <option value="price_desc">Price (High to Low)</option>
                    <option value="id_desc">Newest First</option>
                    <option value="id_asc">Oldest First</option>
                </select>
            </div>
        </div>
    </div>
</div>
