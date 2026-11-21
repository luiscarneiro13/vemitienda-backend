{{-- Category Chips Navigation --}}
<nav class="cat-chips-wrap" x-show="payload.categories.length > 0 && hasAnyProductVisible()" aria-label="Categorías del menú">
    <template x-for="category in payload.categories" :key="category.id">
        <button type="button"
                class="cat-chip"
                x-show="hasCategoryVisible(category)"
                :class="{ active: activeCategory === category.id }"
                :aria-current="activeCategory === category.id ? 'true' : null"
                @click="scrollToCategory(category.id)">
            <span x-show="category.icon" x-text="category.icon" aria-hidden="true"></span>
            <span x-text="category.name"></span>
        </button>
    </template>
</nav>
