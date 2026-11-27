<template>
  <section class="fl-cat-strip">
    <div class="container">
      <div class="fl-cat-mobile-head">
        <span>CATEGORIES</span>
        <Link href="/categories">See All <i class="fa-solid fa-chevron-right"></i></Link>
      </div>
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

      <!-- Mobile/Tablet 4-Item Trust Strip -->
      <div class="fl-mobile-trust-bar">
        <div class="fl-trust-item">
          <i class="fa-solid fa-truck-fast"></i>
          <span>Fresh &amp;<br>Hygienic</span>
        </div>
        <div class="fl-trust-item">
          <i class="fa-regular fa-circle-check"></i>
          <span>Quality<br>Assured</span>
        </div>
        <div class="fl-trust-item">
          <i class="fa-solid fa-hand-holding-dollar"></i>
          <span>Fair<br>Prices</span>
        </div>
        <div class="fl-trust-item">
          <i class="fa-solid fa-seedling"></i>
          <span>Direct from<br>Farmers</span>
        </div>
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
  { label: 'Dal &<br>Pulses', image: '/assets/cat-1.jpg', href: '/products?category=Dal' },
  { label: 'Masala &<br>Spices', image: '/assets/cat-2.jpg', href: '/products?category=Spices' },
  { label: 'Oils &<br>Ghee', image: '/assets/cat-3.jpg', href: '/products?category=Oils' },
  { label: 'Dry<br>Fruits', image: '/assets/cat-4.jpg', href: '/products?category=Dry+Fruits' },
  { label: 'Tea &<br>Coffee', image: '/assets/cat-5.jpg', href: '/products?category=Beverages' },
  { label: 'Sauces &<br>Spreads', image: '/assets/cat-6.jpg', href: '/products?category=Sauces' },
  { label: 'Snacks', image: '/assets/cat-7.jpg', href: '/products?category=Snacks' },
  { label: 'Pickles', image: '/assets/cat-8.jpg', href: '/products?category=Pickles' },
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
  width: 86px;
  height: 86px;
  border-radius: 50%;
  background: #F3F8EF;
  border: 1.5px solid #DCECD3;
  box-shadow: 0 4px 12px rgba(20, 83, 45, 0.07);
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0;
  margin-bottom: 10px;
}

.fl-cat-thumb img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: 50%;
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
  border-radius: 50%;
  background: #EFF7EA;
  border: 1.5px solid #DCECD3;
  box-shadow: 0 4px 12px rgba(20, 83, 45, 0.07);
  display: flex;
  align-items: center;
  justify-content: center;
  color: #14532D;
  font-size: 30px;
  margin-bottom: 10px;
}

.fl-cat-mobile-head,
.fl-mobile-trust-bar {
  display: none;
}

@media (max-width: 960px) {
  .fl-mobile-trust-bar {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 8px;
    background: #F1F9EB;
    border: 1px solid #DCECD3;
    border-radius: 12px;
    padding: 10px 8px;
    margin-top: 12px;
  }
  .fl-trust-item {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    font-size: 10.5px;
    font-weight: 700;
    line-height: 1.2;
    color: #14532D;
    text-align: left;
  }
  .fl-trust-item i {
    font-size: 14px;
    color: #3F9C1E;
    flex-shrink: 0;
  }
}

@media (max-width: 1024px) {
  .fl-cat-strip-grid {
    grid-template-columns: repeat(5, minmax(0, 1fr));
    gap: 12px;
  }
  .fl-cat-thumb,
  .fl-cat-grid-icon {
    width: 76px;
    height: 76px;
  }
  .fl-cat-label {
    font-size: 13px;
  }
}

@media (max-width: 640px) {
  .fl-cat-strip {
    padding: 12px 0 10px;
  }
  .fl-cat-mobile-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 10px;
  }
  .fl-cat-mobile-head span {
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 0.08em;
    color: #4B5563;
  }
  .fl-cat-mobile-head a {
    font-size: 12px;
    font-weight: 700;
    color: #3F9C1E;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 4px;
  }
  .fl-cat-strip-grid {
    display: flex;
    overflow-x: auto;
    gap: 12px;
    padding-bottom: 4px;
    scrollbar-width: none;
    -webkit-overflow-scrolling: touch;
  }
  .fl-cat-strip-grid::-webkit-scrollbar {
    display: none;
  }
  .fl-cat-tile {
    flex: 0 0 74px;
    padding: 0;
  }
  .fl-cat-thumb {
    width: 66px;
    height: 66px;
    border-radius: 50%;
    padding: 0;
    margin-bottom: 6px;
  }
  .fl-cat-grid-icon {
    width: 66px;
    height: 66px;
    border-radius: 50%;
    font-size: 22px;
    margin-bottom: 6px;
  }
  .fl-cat-label {
    font-size: 11.5px;
    line-height: 1.2;
  }
}
</style>
