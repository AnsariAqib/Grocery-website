<template>
  <footer class="fl-footer">
    <div class="container">
      <div class="fl-footer-grid">
        <!-- Col 1: Brand + Social Icons -->
        <div class="fl-footer-brand">
          <Link href="/" class="fl-footer-logo">
            <i class="fa-solid fa-leaf fl-logo-leaf"></i>
            <span class="fl-footer-wordmark">VIZORA FOODS</span>
          </Link>

          <p class="fl-footer-desc">
            Fresh groceries, daily essentials and farm-fresh produce, straight from trusted sources to your home.
          </p>

          <div class="fl-social-row">
            <a :href="site.social?.instagram || '#'" target="_blank" rel="noopener noreferrer" aria-label="Instagram">
              <i class="fa-brands fa-instagram"></i>
            </a>
            <a :href="site.social?.youtube || '/about'" aria-label="YouTube">
              <i class="fa-brands fa-youtube"></i>
            </a>
            <a :href="site.social?.facebook || '#'" target="_blank" rel="noopener noreferrer" aria-label="Facebook">
              <i class="fa-brands fa-facebook-f"></i>
            </a>
            <Link href="/contact" aria-label="WhatsApp">
              <i class="fa-brands fa-whatsapp"></i>
            </Link>
          </div>
        </div>

        <!-- Col 2: About Us -->
        <div class="fl-footer-col">
          <h4 class="fl-footer-heading">About Us</h4>
          <ul class="fl-footer-links">
            <li><Link href="/about">About VIZORA FOODS</Link></li>
            <li><Link href="/about">Our Farmers</Link></li>
            <li><Link href="/contact">Contact Us</Link></li>
            <li><Link href="/contact">Careers</Link></li>
          </ul>
        </div>

        <!-- Col 3: Shop -->
        <div class="fl-footer-col">
          <h4 class="fl-footer-heading">Shop</h4>
          <ul class="fl-footer-links">
            <li><Link href="/categories">All Categories</Link></li>
            <li><Link href="/products">Shop Products</Link></li>
            <li><Link href="/products">New Arrivals</Link></li>
            <li><Link href="/products">Best Sellers</Link></li>
          </ul>
        </div>

        <!-- Col 4: Help -->
        <div class="fl-footer-col">
          <h4 class="fl-footer-heading">Help</h4>
          <ul class="fl-footer-links">
            <li><Link href="/contact">Track Your Order</Link></li>
            <li><Link href="/contact">Returns &amp; Refunds</Link></li>
            <li><Link href="/stores">Cancellation Policy</Link></li>
            <li><Link href="/about">FAQ</Link></li>
          </ul>
        </div>
      </div>

      <!-- Bottom Legal Bar -->
      <div class="fl-footer-bottom">
        <p>&copy; {{ year }} VIZORA FOODS. All rights reserved.</p>
        <div class="fl-footer-legal">
          <Link href="/privacy-policy">Privacy Policy</Link>
          <span>|</span>
          <Link href="/about">Terms &amp; Conditions</Link>
          <span>|</span>
          <Link href="/about">About Us</Link>
          <span>|</span>
          <Link href="/contact">Return &amp; Refund Policy</Link>
        </div>
      </div>
    </div>
  </footer>
  <MobileBottomNav />
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { useSite } from '@/composables/useSite';
import MobileBottomNav from '@/Components/MobileBottomNav.vue';

const { site } = useSite();
const year = new Date().getFullYear();
</script>

<style scoped>
.fl-footer {
  background-color: #0F3A1D;
  color: #E8F5DF;
  padding: 52px 0 26px;
  font-family: 'Plus Jakarta Sans', var(--font-main), sans-serif;
}

.fl-footer-grid {
  display: grid;
  grid-template-columns: 1.4fr 1fr 1fr 1fr;
  gap: 32px;
  align-items: start;
}

.fl-footer-logo {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  text-decoration: none;
  margin-bottom: 14px;
}

.fl-logo-leaf {
  font-size: 24px;
  color: #65D138;
}

.fl-footer-wordmark {
  font-size: 26px;
  font-weight: 800;
  color: #FFFFFF;
  letter-spacing: -0.025em;
}

.fl-footer-desc {
  font-size: 13.5px;
  line-height: 1.6;
  color: rgba(255, 255, 255, 0.85);
  max-width: 285px;
  margin: 0 0 18px 0;
}

.fl-social-row {
  display: flex;
  align-items: center;
  gap: 10px;
}

.fl-social-row a {
  width: 34px;
  height: 34px;
  border-radius: 8px;
  border: 1px solid rgba(101, 209, 56, 0.35);
  background: rgba(255, 255, 255, 0.04);
  color: #FFFFFF;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 15px;
  text-decoration: none;
  transition: background-color 0.2s ease, border-color 0.2s ease;
}

.fl-social-row a:hover {
  background-color: #3F9C1E;
  border-color: #3F9C1E;
}

.fl-footer-col {
  border-left: 1px solid rgba(101, 209, 56, 0.28);
  padding-left: 34px;
  min-height: 150px;
}

.fl-footer-heading {
  font-size: 17px;
  font-weight: 700;
  color: #FFFFFF;
  margin: 0 0 16px 0;
}

.fl-footer-links {
  list-style: none;
  padding: 0;
  margin: 0;
  display: flex;
  flex-direction: column;
  gap: 11px;
}

.fl-footer-links a {
  color: rgba(255, 255, 255, 0.86);
  font-size: 14px;
  font-weight: 400;
  text-decoration: none;
  transition: color 0.15s ease;
}

.fl-footer-links a:hover {
  color: #65D138;
}

.fl-footer-bottom {
  margin-top: 40px;
  padding-top: 20px;
  border-top: 1px solid rgba(255, 255, 255, 0.24);
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  flex-wrap: wrap;
  font-size: 13px;
  color: rgba(255, 255, 255, 0.85);
}

.fl-footer-bottom p {
  margin: 0;
}

.fl-footer-legal {
  display: flex;
  align-items: center;
  gap: 12px;
  flex-wrap: wrap;
}

.fl-footer-legal a {
  color: rgba(255, 255, 255, 0.85);
  text-decoration: none;
  transition: color 0.15s ease;
}

.fl-footer-legal a:hover {
  color: #65D138;
}

.fl-footer-legal span {
  opacity: 0.45;
}

@media (max-width: 960px) {
  .fl-footer {
    padding-bottom: 92px;
  }
  .fl-footer-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
  .fl-footer-col {
    border-left: none;
    padding-left: 0;
    min-height: auto;
  }
}

@media (max-width: 600px) {
  .fl-footer-grid {
    grid-template-columns: 1fr;
    gap: 28px;
  }
  .fl-footer-bottom {
    flex-direction: column;
    align-items: flex-start;
  }
}
</style>
