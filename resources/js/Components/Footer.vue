<template>
  <footer class="fl-footer">
    <div class="container">
      <div class="fl-footer-grid">
        <!-- Col 1: Brand + 4 Trust Icons + Follow Us -->
        <div class="fl-footer-brand">
          <Link href="/" class="fl-footer-logo">
            <i class="fa-solid fa-leaf fl-logo-leaf"></i>
            <span class="fl-footer-wordmark">FarmLokal</span>
          </Link>

          <p class="fl-footer-desc">
            Fresh groceries, daily essentials and farm-fresh produce, straight from trusted sources to your home.
          </p>

          <div class="fl-trust-row">
            <div class="fl-trust-item">
              <i class="fa-solid fa-truck-fast"></i>
              <span>Fast<br>Delivery</span>
            </div>
            <div class="fl-trust-item">
              <i class="fa-solid fa-award"></i>
              <span>Quality<br>Assured</span>
            </div>
            <div class="fl-trust-item">
              <i class="fa-solid fa-seedling"></i>
              <span>Farm<br>Fresh</span>
            </div>
            <div class="fl-trust-item">
              <i class="fa-solid fa-shield-halved"></i>
              <span>Secure<br>Shopping</span>
            </div>
          </div>

          <div class="fl-follow-row">
            <span class="fl-follow-label">Follow Us</span>
            <div class="fl-follow-icons">
              <a :href="site.social?.instagram || '#'" target="_blank" rel="noopener noreferrer" aria-label="Instagram">
                <i class="fa-brands fa-instagram"></i>
              </a>
              <a href="#" aria-label="YouTube">
                <i class="fa-brands fa-youtube"></i>
              </a>
              <a :href="site.social?.facebook || '#'" target="_blank" rel="noopener noreferrer" aria-label="Facebook">
                <i class="fa-brands fa-facebook-f"></i>
              </a>
              <a href="#" aria-label="WhatsApp">
                <i class="fa-brands fa-whatsapp"></i>
              </a>
            </div>
          </div>
        </div>

        <!-- Col 2: Shop -->
        <div class="fl-footer-col">
          <h4 class="fl-footer-heading">Shop</h4>
          <ul class="fl-footer-links">
            <li><Link href="/categories">All Categories</Link></li>
            <li><Link href="/promotions">Deals &amp; Offers</Link></li>
            <li><Link href="/products">New Arrivals</Link></li>
            <li><Link href="/products">Bestsellers</Link></li>
            <li><Link href="/about">By Farmers</Link></li>
          </ul>
        </div>

        <!-- Col 3: Help -->
        <div class="fl-footer-col">
          <h4 class="fl-footer-heading">Help</h4>
          <ul class="fl-footer-links">
            <li><Link href="/contact">Track Order</Link></li>
            <li><Link href="/contact">Returns &amp; Refunds</Link></li>
            <li><Link href="/stores">Shipping &amp; Delivery</Link></li>
            <li><Link href="/about">FAQs</Link></li>
            <li><Link href="/contact">Contact Support</Link></li>
          </ul>
        </div>

        <!-- Col 4: Company -->
        <div class="fl-footer-col">
          <h4 class="fl-footer-heading">Company</h4>
          <ul class="fl-footer-links">
            <li><Link href="/about">About FarmLokal</Link></li>
            <li><Link href="/about">Our Farmers</Link></li>
            <li><Link href="/contact">Careers</Link></li>
            <li><Link href="/about">Privacy Policy</Link></li>
            <li><Link href="/about">Terms &amp; Conditions</Link></li>
          </ul>
        </div>

        <!-- Col 5: Stay Updated -->
        <div class="fl-footer-col fl-newsletter-col">
          <h4 class="fl-footer-heading">Stay Updated</h4>
          <p class="fl-newsletter-desc">
            Get the latest offers, new products and farm-fresh updates in your inbox.
          </p>

          <form class="fl-newsletter-form" @submit.prevent="subscribe">
            <i class="fa-regular fa-envelope fl-mail-icon"></i>
            <input
              v-model="email"
              type="email"
              required
              placeholder="Enter your email address"
              aria-label="Email address"
            >
            <button type="submit">
              {{ subscribed ? 'Subscribed!' : 'Subscribe' }}
            </button>
          </form>
        </div>
      </div>
    </div>
  </footer>
</template>

<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import { useSite } from '@/composables/useSite';

const { site } = useSite();
const email = ref('');
const subscribed = ref(false);

function subscribe() {
  if (!email.value.trim()) return;
  subscribed.value = true;
  email.value = '';
  setTimeout(() => {
    subscribed.value = false;
  }, 2500);
}
</script>

<style scoped>
.fl-footer {
  background-color: #14532D;
  border-top: 1px solid #0F4022;
  color: #E8F5DF;
  padding: 52px 0 44px;
  font-family: 'Plus Jakarta Sans', var(--font-main), sans-serif;
}

.fl-footer-grid {
  display: grid;
  grid-template-columns: 1.45fr 0.78fr 0.85fr 0.9fr 1.35fr;
  gap: 32px;
  align-items: start;
}

.fl-footer-brand {
  border-right: 1px solid rgba(255, 255, 255, 0.16);
  padding-right: 28px;
}

.fl-footer-logo {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  text-decoration: none;
  margin-bottom: 12px;
}

.fl-logo-leaf {
  font-size: 24px;
  color: #4ADE80;
}

.fl-footer-wordmark {
  font-size: 26px;
  font-weight: 800;
  color: #FFFFFF;
  letter-spacing: -0.025em;
}

.fl-footer-desc {
  font-size: 13.5px;
  line-height: 1.55;
  color: rgba(255, 255, 255, 0.8);
  margin: 0 0 20px 0;
}

.fl-trust-row {
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  margin-bottom: 22px;
}

.fl-trust-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  gap: 6px;
  padding: 2px 6px;
  border-right: 1px solid rgba(255, 255, 255, 0.16);
}

.fl-trust-item:last-child {
  border-right: none;
}

.fl-trust-item i {
  font-size: 18px;
  color: #4ADE80;
}

.fl-trust-item span {
  font-size: 11.5px;
  font-weight: 600;
  line-height: 1.25;
  color: #FFFFFF;
}

.fl-follow-row {
  display: flex;
  align-items: center;
  gap: 14px;
}

.fl-follow-label {
  font-size: 13.5px;
  font-weight: 700;
  color: #FFFFFF;
}

.fl-follow-icons {
  display: flex;
  align-items: center;
  gap: 8px;
}

.fl-follow-icons a {
  width: 30px;
  height: 30px;
  border-radius: 8px;
  border: 1px solid rgba(255, 255, 255, 0.25);
  background: rgba(255, 255, 255, 0.1);
  color: #FFFFFF;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 14px;
  text-decoration: none;
  transition: background-color 0.2s ease, border-color 0.2s ease;
}

.fl-follow-icons a:hover {
  background-color: #3F9C1E;
  border-color: #3F9C1E;
  color: #FFFFFF;
}

.fl-footer-heading {
  font-size: 16px;
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
  color: rgba(255, 255, 255, 0.82);
  font-size: 13.5px;
  font-weight: 500;
  text-decoration: none;
  transition: color 0.15s ease;
}

.fl-footer-links a:hover {
  color: #4ADE80;
}

.fl-newsletter-desc {
  font-size: 13.5px;
  line-height: 1.55;
  color: rgba(255, 255, 255, 0.8);
  margin: 0 0 16px 0;
}

.fl-newsletter-form {
  display: flex;
  align-items: center;
  gap: 8px;
  background: #FFFFFF;
  border: 1px solid rgba(255, 255, 255, 0.2);
  border-radius: 10px;
  padding: 4px 4px 4px 12px;
}

.fl-mail-icon {
  color: #14532D;
  font-size: 14px;
  flex-shrink: 0;
}

.fl-newsletter-form input {
  flex: 1;
  min-width: 0;
  border: none;
  outline: none;
  font-size: 13px;
  color: #111827;
  background: transparent;
  font-family: inherit;
}

.fl-newsletter-form button {
  background-color: #3F9C1E;
  color: #FFFFFF;
  border: none;
  border-radius: 8px;
  padding: 9px 16px;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  font-family: inherit;
  flex-shrink: 0;
  transition: background-color 0.2s ease;
}

.fl-newsletter-form button:hover {
  background-color: #2E7D1B;
}

@media (max-width: 1120px) {
  .fl-footer-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
  .fl-footer-brand {
    border-right: none;
    padding-right: 0;
    grid-column: 1 / -1;
  }
  .fl-newsletter-col {
    grid-column: 1 / -1;
  }
}

@media (max-width: 600px) {
  .fl-footer-grid {
    grid-template-columns: 1fr;
    gap: 24px;
  }
  .fl-trust-row {
    grid-template-columns: repeat(2, minmax(0, 1fr));
    row-gap: 12px;
  }
}
</style>
