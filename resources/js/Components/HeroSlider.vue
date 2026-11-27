<template>
  <section class="hero-section">
    <div class="container">
      <div class="hero-banner-card">
        <!-- Left Copy -->
        <div class="hero-content">
          <h1 class="hero-title">
            Good Food.<br>
            <span class="hero-title-highlight">Delivered Fresh.</span>
          </h1>
          <p class="hero-subtext">
            Handpicked organic groceries and farm-fresh<br>
            produce delivered to your doorstep.
          </p>

          <div class="hero-cta-group">
            <Link href="/products" class="hero-btn-primary">
              Shop Fresh Now <i class="fa-solid fa-arrow-right"></i>
            </Link>
            <div class="hero-delivery-pill">
              <i class="fa-solid fa-truck-fast hero-delivery-icon"></i>
              <div class="hero-delivery-text">
                <strong>Fast Delivery</strong>
                <span>At Your Doorstep</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Right Transparent PNG -->
        <div class="hero-visual">
          <img src="/assets/hero-farmlokal-crate.png" alt="VIZORA FOODS Fresh Produce Crate" class="hero-png-img">
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
  slides: {
    type: Array,
    default: () => []
  }
});
</script>

<style scoped>
/* Hero Action Row (Arrows + CTA) */
.hero-action-row {
  display: flex;
  align-items: center;
  gap: 24px;
  margin-top: 32px;
}

/* Slide arrows sit on the extreme left and right edges of the banner rather
   than beside the CTAs, so they read as banner controls and leave the copy
   block uncluttered. */
.hero-edge-btn {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  z-index: 6;
  width: 46px;
  height: 46px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.14);
  border: 1px solid rgba(255, 255, 255, 0.35);
  -webkit-backdrop-filter: blur(8px);
  backdrop-filter: blur(8px);
  color: #FFFFFF;
  font-size: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: background-color 0.3s cubic-bezier(0.4, 0, 0.2, 1),
              border-color 0.3s cubic-bezier(0.4, 0, 0.2, 1),
              box-shadow 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.hero-edge-btn.prev { left: 18px; }
.hero-edge-btn.next { right: 18px; }

.hero-edge-btn:hover {
  background-color: #E4652E;
  border-color: #E4652E;
  box-shadow: 0 8px 22px rgba(228, 101, 46, 0.35);
}

.hero-edge-btn:focus-visible {
  outline: 2px solid #FFFFFF;
  outline-offset: 2px;
}

/* Pagination at Bottom Center */
.hero-pagination-bar {
  position: absolute;
  bottom: 24px;
  left: 50%;
  transform: translateX(-50%);
  z-index: 10;
}

.hero-dashes-track {
  display: flex;
  align-items: center;
  gap: 8px;
}

.hero-dash-pill {
  width: 24px;
  height: 4px;
  border-radius: 2px;
  background-color: rgba(20, 83, 45, 0.22);
  cursor: pointer;
  transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
}

.hero-dash-pill:hover {
  background-color: rgba(20, 83, 45, 0.5);
}

.hero-dash-pill.active {
  width: 36px;
  background: #14532D;
  box-shadow: none;
}

@media (max-width: 768px) {
  .hero-action-row {
    flex-direction: column;
    align-items: flex-start;
    gap: 16px;
  }

  /* Smaller and tighter to the edge — a phone banner has no room to spare */
  .hero-edge-btn {
    width: 38px;
    height: 38px;
    font-size: 12px;
    top: auto;
    bottom: 18px;
    transform: none;
  }

  .hero-edge-btn.prev { left: 12px; }
  .hero-edge-btn.next { right: 12px; }
}
</style>
