<template>
  <section class="fl-cat-strip">
    <div class="container">
      <div class="fl-cat-strip-grid">
        <Link
          v-for="item in stripCategories"
          :key="item.label"
          :href="item.href"
          class="fl-cat-tile"
        >
          <div class="fl-cat-thumb">
            <img :src="item.image" :alt="item.label" loading="lazy">
          </div>
          <span class="fl-cat-label" v-html="item.label"></span>
        </Link>

        <Link href="/categories" class="fl-cat-tile fl-cat-view-all">
          <div class="fl-cat-grid-icon">
            <i class="fa-solid fa-border-all"></i>
          </div>
          <span class="fl-cat-label">View All<br>Categories</span>
        </Link>
      </div>
    </div>
  </section>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
  stats: {
    type: Array,
    default: () => []
  }
});

const stripCategories = [
  { label: 'Dal &<br>Pulses', image: '/assets/cat-1.png', href: '/products?category=Dal' },
  { label: 'Masala &<br>Spices', image: '/assets/cat-2.png', href: '/products?category=Spices' },
  { label: 'Oils &<br>Ghee', image: '/assets/cat-3.png', href: '/products?category=Oils' },
  { label: 'Dry<br>Fruits', image: '/assets/cat-4.png', href: '/products?category=Dry+Fruits' },
  { label: 'Tea &<br>Coffee', image: '/assets/cat-5.png', href: '/products?category=Beverages' },
  { label: 'Sauces &<br>Spreads', image: '/assets/cat-6.png', href: '/products?category=Sauces' },
  { label: 'Snacks', image: '/assets/cat-7.png', href: '/products?category=Snacks' },
  { label: 'Pickles', image: '/assets/cat-8.png', href: '/products?category=Pickles' },
];
</script>

<style scoped>
.fl-cat-strip {
  padding: 18px 0 18px;
  background: #FFFFFF;
}

.fl-cat-strip-grid {
  display: grid;
  grid-template-columns: repeat(9, minmax(0, 1fr));
  gap: 14px;
}

.fl-cat-tile {
  background: transparent;
  border: none;
  border-radius: 0;
  padding: 6px 4px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: flex-start;
  text-align: center;
  text-decoration: none;
  transition: transform 0.2s ease;
}

.fl-cat-tile:hover {
  transform: translateY(-3px);
}

.fl-cat-tile:hover .fl-cat-label {
  color: #14532D;
}

.fl-cat-thumb {
  width: 108px;
  height: 86px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 10px;
}

.fl-cat-thumb img {
  max-width: 100%;
  max-height: 100%;
  object-fit: contain;
}

.fl-cat-label {
  font-family: 'Plus Jakarta Sans', var(--font-main), sans-serif;
  font-size: 14.5px;
  font-weight: 700;
  line-height: 1.28;
  color: #111827;
  transition: color 0.2s ease;
}

.fl-cat-view-all {
  background: transparent;
  border: none;
  justify-content: flex-start;
}

.fl-cat-grid-icon {
  width: 86px;
  height: 86px;
  border-radius: 18px;
  background: #EFF7EA;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #14532D;
  font-size: 32px;
  margin-bottom: 10px;
}

@media (max-width: 1024px) {
  .fl-cat-strip-grid {
    grid-template-columns: repeat(5, minmax(0, 1fr));
  }
}

@media (max-width: 640px) {
  .fl-cat-strip-grid {
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 10px;
  }
}
</style>
