<template>
  <div class="fl-mobile-dock">
    <!-- Floating Cart Summary Bar (only when cart has items) -->
    <Link v-if="totalCount > 0" href="/cart" class="fl-dock-cart-bar">
      <div class="fl-dock-cart-left">
        <span class="fl-dock-cart-count">{{ totalCount }}</span>
        <span class="fl-dock-cart-text">
          <strong>₹{{ subtotal }} total</strong> &bull; {{ totalCount }} {{ totalCount === 1 ? 'Item' : 'Items' }}
        </span>
      </div>
      <span class="fl-dock-cart-cta">
        VIEW CART <i class="fa-solid fa-chevron-right"></i>
      </span>
    </Link>

    <!-- Dark Green Bottom App Navigation -->
    <nav class="fl-bottom-nav" aria-label="Mobile Navigation">
      <Link href="/" class="fl-bnav-item" :class="{ active: currentPath === '/' }">
        <i class="fa-solid fa-house"></i>
        <span>Home</span>
      </Link>
      <Link href="/categories" class="fl-bnav-item" :class="{ active: currentPath.startsWith('/categories') }">
        <i class="fa-solid fa-border-all"></i>
        <span>Categories</span>
      </Link>
      <Link href="/promotions" class="fl-bnav-item" :class="{ active: currentPath.startsWith('/promotions') }">
        <i class="fa-solid fa-tag"></i>
        <span>Offers</span>
      </Link>
      <Link href="/cart" class="fl-bnav-item fl-bnav-cart" :class="{ active: currentPath.startsWith('/cart') }">
        <span class="fl-bnav-icon-wrap">
          <i class="fa-solid fa-cart-shopping"></i>
          <span v-if="totalCount > 0" class="fl-bnav-badge">{{ totalCount }}</span>
        </span>
        <span>Cart</span>
      </Link>
      <Link href="/about" class="fl-bnav-item" :class="{ active: currentPath.startsWith('/about') }">
        <i class="fa-regular fa-user"></i>
        <span>Account</span>
      </Link>
    </nav>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { useCart } from '@/composables/useCart';

const page = usePage();
const { totalCount, subtotal } = useCart();

const currentPath = computed(() => {
  const url = String(page.url || '/');
  return url.split('?')[0] || '/';
});
</script>

<style scoped>
.fl-mobile-dock {
  display: none;
}

@media (max-width: 960px) {
  .fl-mobile-dock {
    display: block;
    position: fixed;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 950;
    font-family: 'Plus Jakarta Sans', var(--font-main), sans-serif;
  }

  .fl-dock-cart-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #1B6B38;
    color: #FFFFFF;
    padding: 8px 16px;
    text-decoration: none;
    border-top: 1px solid rgba(255, 255, 255, 0.14);
  }

  .fl-dock-cart-left {
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .fl-dock-cart-count {
    width: 22px;
    height: 22px;
    border-radius: 6px;
    background: rgba(255, 255, 255, 0.2);
    color: #FFFFFF;
    font-size: 11.5px;
    font-weight: 800;
    display: inline-flex;
    align-items: center;
    justify-content: center;
  }

  .fl-dock-cart-text {
    font-size: 12.5px;
    color: #ECFDF3;
  }

  .fl-dock-cart-cta {
    background: #FFFFFF;
    color: #14532D;
    font-size: 11px;
    font-weight: 800;
    padding: 5px 11px;
    border-radius: 9999px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    letter-spacing: 0.02em;
  }

  .fl-bottom-nav {
    display: grid;
    grid-template-columns: repeat(5, minmax(0, 1fr));
    background: #0F3A1D;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    height: 60px;
    padding-bottom: env(safe-area-inset-bottom, 0px);
    box-shadow: 0 -4px 16px rgba(15, 46, 26, 0.2);
  }

  .fl-bnav-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 3px;
    text-decoration: none;
    color: rgba(255, 255, 255, 0.72);
    font-size: 10.5px;
    font-weight: 600;
    transition: color 0.15s ease;
  }

  .fl-bnav-item i {
    font-size: 17px;
  }

  .fl-bnav-icon-wrap {
    position: relative;
    display: inline-flex;
    align-items: center;
    justify-content: center;
  }

  .fl-bnav-badge {
    position: absolute;
    top: -6px;
    right: -10px;
    background: #F97316;
    color: #FFFFFF;
    font-size: 9.5px;
    font-weight: 800;
    min-width: 16px;
    height: 16px;
    border-radius: 9999px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0 4px;
  }

  .fl-bnav-item.active {
    color: #65D138;
    font-weight: 700;
  }
}
</style>
