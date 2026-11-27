<template>
  <div class="fl-cart-page">
    <TopBar />
    <Navbar activePage="cart" @openModal="isModalOpen = true" />

    <main class="fl-cart-main">
      <div class="container">
        <!-- Top Header Row -->
        <div class="fl-cart-header">
          <div class="fl-cart-title-wrap">
            <i class="fa-solid fa-cart-shopping fl-cart-head-icon"></i>
            <h1 class="fl-cart-title">Your Cart</h1>
            <span class="fl-cart-count">({{ totalCount }} Items)</span>
          </div>

          <Link href="/products" class="fl-continue-link">
            <i class="fa-solid fa-arrow-left"></i> Continue Shopping
          </Link>
        </div>

        <!-- Empty Cart State -->
        <div v-if="cartItems.length === 0" class="fl-cart-empty">
          <i class="fa-solid fa-basket-shopping"></i>
          <h2>Your cart is empty</h2>
          <p>Looks like you haven't added any farm-fresh groceries yet.</p>
          <Link href="/products" class="fl-checkout-btn" style="width:auto; padding:0 28px; text-decoration:none;">
            Browse Products <i class="fa-solid fa-arrow-right"></i>
          </Link>
        </div>

        <!-- 2-Column Cart Layout -->
        <div v-else class="fl-cart-grid">
          <!-- LEFT: Cart Items List Card -->
          <div class="fl-cart-items-card">
            <div
              v-for="item in cartItems"
              :key="item.id"
              class="fl-cart-row"
            >
              <div class="fl-cart-thumb">
                <img :src="item.image" :alt="item.name">
              </div>

              <div class="fl-cart-item-info">
                <h3 class="fl-cart-item-name">{{ item.name }}</h3>
                <p class="fl-cart-item-sub">{{ item.subtitle }}</p>
              </div>

              <div class="fl-cart-price-col">
                <div class="fl-cart-price-line">
                  <strong class="fl-cart-price">₹{{ item.price * item.qty }}</strong>
                  <span class="fl-cart-old">₹{{ item.oldPrice * item.qty }}</span>
                </div>
              </div>

              <div class="fl-cart-controls">
                <div class="fl-cart-stepper">
                  <button type="button" aria-label="Decrease quantity" @click="updateQty(item.id, -1)">
                    <i class="fa-solid fa-minus"></i>
                  </button>
                  <span>{{ item.qty }}{{ item.unitLabel ? ` ${item.unitLabel}` : '' }}</span>
                  <button type="button" aria-label="Increase quantity" @click="updateQty(item.id, 1)">
                    <i class="fa-solid fa-plus"></i>
                  </button>
                </div>

                <button
                  type="button"
                  class="fl-cart-del-btn"
                  aria-label="Remove item"
                  @click="removeItem(item.id)"
                >
                  <i class="fa-regular fa-trash-can"></i>
                </button>
              </div>
            </div>
          </div>

          <!-- RIGHT: Order Summary Card -->
          <aside class="fl-summary-card">
            <h2 class="fl-summary-title">Order Summary</h2>

            <div class="fl-summary-rows">
              <div class="fl-summary-line">
                <span>Items ({{ totalCount }})</span>
                <strong>₹{{ mrpTotal }}</strong>
              </div>
              <div class="fl-summary-line">
                <span>Item Discount</span>
                <strong class="fl-discount-text">− ₹{{ discountTotal }}</strong>
              </div>

              <div class="fl-summary-divider"></div>

              <div class="fl-summary-line is-bold">
                <span>Subtotal</span>
                <strong>₹{{ subtotal }}</strong>
              </div>
              <div class="fl-summary-line">
                <span>Delivery Fee <i class="fa-solid fa-circle-info fl-info-icon" title="Standard doorstep delivery charge"></i></span>
                <strong>₹{{ deliveryFee }}</strong>
              </div>
              <div v-if="couponDiscount > 0" class="fl-summary-line">
                <span>Coupon Applied</span>
                <strong class="fl-discount-text">− ₹{{ couponDiscount }}</strong>
              </div>
            </div>

            <div class="fl-total-payable-bar">
              <span>Total Payable</span>
              <strong>₹{{ finalPayable }}</strong>
            </div>

            <form class="fl-coupon-form" @submit.prevent="applyCoupon">
              <div class="fl-coupon-input-wrap">
                <i class="fa-solid fa-ticket"></i>
                <input
                  v-model="couponCode"
                  type="text"
                  placeholder="Enter coupon code"
                  aria-label="Coupon code"
                >
              </div>
              <button type="submit" class="fl-coupon-apply-btn">
                {{ couponDiscount > 0 ? 'Applied' : 'Apply' }}
              </button>
            </form>

            <Link href="/checkout" class="fl-checkout-btn" style="text-decoration:none;">
              <i class="fa-solid fa-lock"></i>
              <span>Proceed to Checkout</span>
              <i class="fa-solid fa-arrow-right"></i>
            </Link>
          </aside>
        </div>
      </div>
    </main>

    <Footer />
    <ContactModal :isOpen="isModalOpen" :prefill="checkoutPrefill" @close="isModalOpen = false" />
    <WhatsAppFloat />
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import TopBar from '@/Components/TopBar.vue';
import Navbar from '@/Components/Navbar.vue';
import Footer from '@/Components/Footer.vue';
import ContactModal from '@/Components/ContactModal.vue';
import WhatsAppFloat from '@/Components/WhatsAppFloat.vue';
import { useCart } from '@/composables/useCart';

const {
  cartItems,
  totalCount,
  mrpTotal,
  subtotal,
  discountTotal,
  deliveryFee,
  updateQty,
  removeItem,
} = useCart();

const isModalOpen = ref(false);
const couponCode = ref('');
const couponDiscount = ref(0);

const finalPayable = computed(() =>
  Math.max(0, subtotal.value + deliveryFee.value - couponDiscount.value),
);

const checkoutPrefill = computed(() => {
  const lines = cartItems.value.map(
    (item) => `- ${item.name} (${item.qty}${item.unitLabel ? ` ${item.unitLabel}` : ''}) = ₹${item.price * item.qty}`,
  );
  return {
    inquiryType: 'retail',
    productName: `Cart Order (${totalCount.value} Items)`,
    message: `Order Summary:\n${lines.join('\n')}\n\nTotal Payable: ₹${finalPayable.value}`,
  };
});

function applyCoupon() {
  if (!couponCode.value.trim()) return;
  couponDiscount.value = 20;
}

function proceedCheckout() {
  isModalOpen.value = true;
}
</script>

<style scoped>
.fl-cart-page {
  background-color: #F6FAF2;
  min-height: 100vh;
  font-family: 'Plus Jakarta Sans', var(--font-main), sans-serif;
}

.fl-cart-main {
  padding: 28px 0 64px;
}

.fl-cart-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 22px;
  gap: 16px;
  flex-wrap: wrap;
}

.fl-cart-title-wrap {
  display: flex;
  align-items: center;
  gap: 12px;
}

.fl-cart-head-icon {
  font-size: 24px;
  color: #124E26;
}

.fl-cart-title {
  font-size: 28px;
  font-weight: 800;
  color: #124E26;
  margin: 0;
  letter-spacing: -0.02em;
}

.fl-cart-count {
  font-size: 16px;
  font-weight: 500;
  color: #4A5548;
}

.fl-continue-link {
  font-size: 14px;
  font-weight: 600;
  color: #14532D;
  text-decoration: underline;
  display: inline-flex;
  align-items: center;
  gap: 8px;
}

/* 2-Column Grid */
.fl-cart-grid {
  display: grid;
  grid-template-columns: 1.55fr 0.85fr;
  gap: 24px;
  align-items: start;
}

/* Left Items Card */
.fl-cart-items-card {
  background: #FFFFFF;
  border: 1px solid #E2ECD9;
  border-radius: 16px;
  padding: 6px 24px;
  box-shadow: 6px 10px 26px rgba(20, 83, 45, 0.1), -4px -2px 14px rgba(20, 83, 45, 0.05);
}

.fl-cart-row {
  display: grid;
  grid-template-columns: 78px 1.3fr 0.8fr auto;
  align-items: center;
  gap: 18px;
  padding: 18px 0;
  border-bottom: 1px solid #EEF2EC;
}

.fl-cart-row:last-child {
  border-bottom: none;
}

.fl-cart-thumb {
  width: 78px;
  height: 78px;
  border-radius: 12px;
  background: #F4F5EF;
  padding: 6px;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
}

.fl-cart-thumb img {
  width: 100%;
  height: 100%;
  object-fit: contain;
}

.fl-cart-item-name {
  font-size: 16px;
  font-weight: 800;
  color: #111827;
  margin: 0 0 3px 0;
}

.fl-cart-item-sub {
  font-size: 13px;
  color: #4A5548;
  margin: 0 0 8px 0;
}

.fl-farmer-pill {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  background: #E2F5C8;
  color: #14532D;
  font-size: 11.5px;
  font-weight: 700;
  padding: 3px 10px;
  border-radius: 9999px;
}

.fl-cart-price-col {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  gap: 5px;
}

.fl-cart-price-line {
  display: flex;
  align-items: baseline;
  gap: 7px;
}

.fl-cart-price {
  font-size: 18px;
  font-weight: 800;
  color: #14532D;
}

.fl-cart-old {
  font-size: 13px;
  color: #9CA3AF;
  text-decoration: line-through;
}

.fl-cart-off-pill {
  background: #E2F5C8;
  color: #14532D;
  font-size: 11px;
  font-weight: 700;
  padding: 2px 8px;
  border-radius: 6px;
}

.fl-cart-controls {
  display: flex;
  align-items: center;
  gap: 10px;
}

.fl-cart-stepper {
  display: inline-flex;
  align-items: center;
  border: 1px solid #D5E4CE;
  border-radius: 8px;
  height: 36px;
  padding: 0 6px;
  gap: 12px;
  background: #FFFFFF;
}

.fl-cart-stepper button {
  width: 24px;
  height: 24px;
  border: none;
  background: transparent;
  color: #111827;
  cursor: pointer;
  font-size: 11px;
}

.fl-cart-stepper span {
  font-size: 13.5px;
  font-weight: 700;
  color: #111827;
  min-width: 34px;
  text-align: center;
}

.fl-cart-del-btn {
  width: 36px;
  height: 36px;
  border-radius: 8px;
  border: 1px solid #E5ECE1;
  background: #FFFFFF;
  color: #4B5563;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  transition: color 0.15s ease, border-color 0.15s ease;
}

.fl-cart-del-btn:hover {
  color: #DC2626;
  border-color: #FCA5A5;
}

/* Right Summary Card */
.fl-summary-card {
  background: #FFFFFF;
  border: 1px solid #E2ECD9;
  border-radius: 16px;
  padding: 22px;
  box-shadow: 6px 10px 26px rgba(20, 83, 45, 0.1), -4px -2px 14px rgba(20, 83, 45, 0.05);
  position: sticky;
  top: 120px;
}

.fl-summary-title {
  font-size: 20px;
  font-weight: 800;
  color: #124E26;
  margin: 0 0 18px 0;
}

.fl-summary-rows {
  display: flex;
  flex-direction: column;
  gap: 12px;
  margin-bottom: 16px;
}

.fl-summary-line {
  display: flex;
  align-items: center;
  justify-content: space-between;
  font-size: 14px;
  color: #374151;
}

.fl-summary-line.is-bold {
  font-weight: 700;
  color: #111827;
}

.fl-discount-text {
  color: #3F9C1E;
}

.fl-info-icon {
  font-size: 12px;
  color: #9CA3AF;
  margin-left: 4px;
}

.fl-summary-divider {
  height: 1px;
  background: #EEF2EC;
  margin: 2px 0;
}

.fl-total-payable-bar {
  background: #E2F5C8;
  border-radius: 10px;
  padding: 12px 14px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  font-size: 16px;
  font-weight: 800;
  color: #124E26;
  margin-bottom: 16px;
}

.fl-coupon-form {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 16px;
}

.fl-coupon-input-wrap {
  flex: 1;
  display: flex;
  align-items: center;
  gap: 8px;
  border: 1px solid #D5E4CE;
  border-radius: 8px;
  padding: 0 12px;
  height: 40px;
  background: #FFFFFF;
}

.fl-coupon-input-wrap i {
  color: #6B7280;
  font-size: 13px;
}

.fl-coupon-input-wrap input {
  width: 100%;
  border: none;
  outline: none;
  font-size: 13px;
  color: #111827;
  font-family: inherit;
}

.fl-coupon-apply-btn {
  height: 40px;
  padding: 0 16px;
  border-radius: 8px;
  border: 1px solid #C8E4B7;
  background: #EBF7E3;
  color: #14532D;
  font-size: 13.5px;
  font-weight: 700;
  cursor: pointer;
  font-family: inherit;
}

.fl-checkout-btn {
  width: 100%;
  height: 44px;
  border: none;
  border-radius: 10px;
  background: #14532D;
  color: #FFFFFF;
  font-size: 14.5px;
  font-weight: 700;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  cursor: pointer;
  font-family: inherit;
  transition: background-color 0.2s ease;
}

.fl-checkout-btn:hover {
  background: #0F4022;
}

.fl-cart-empty {
  background: #FFFFFF;
  border: 1px solid #E5ECE1;
  border-radius: 16px;
  padding: 56px 24px;
  text-align: center;
}

.fl-cart-empty i {
  font-size: 44px;
  color: #3F9C1E;
  margin-bottom: 12px;
}

.fl-cart-empty h2 {
  color: #124E26;
  margin: 0 0 6px;
}

.fl-cart-empty p {
  color: #6B7280;
  margin: 0 0 20px;
}

@media (max-width: 960px) {
  .fl-cart-grid {
    grid-template-columns: 1fr;
    gap: 18px;
  }
  .fl-summary-card {
    position: static;
  }
}

@media (max-width: 640px) {
  .fl-cart-main {
    padding: 18px 0 28px;
  }
  .fl-cart-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 6px;
    margin-bottom: 16px;
  }
  .fl-cart-title {
    font-size: 22px;
  }
  .fl-cart-items-card,
  .fl-summary-card {
    padding: 14px;
    border-radius: 14px;
  }
  .fl-cart-row {
    grid-template-columns: 58px 1fr auto;
    grid-template-rows: auto auto;
    gap: 4px 10px;
    padding: 12px 0;
  }
  .fl-cart-thumb {
    grid-column: 1;
    grid-row: 1 / 3;
    width: 58px;
    height: 58px;
    align-self: center;
  }
  .fl-cart-item-info {
    grid-column: 2;
    grid-row: 1;
    min-width: 0;
  }
  .fl-cart-item-name {
    font-size: 13.5px;
    line-height: 1.25;
  }
  .fl-cart-item-sub {
    font-size: 11px;
    margin-bottom: 0;
  }
  .fl-cart-price-col {
    grid-column: 2;
    grid-row: 2;
    text-align: left;
  }
  .fl-cart-price-line {
    justify-content: flex-start;
    gap: 6px;
  }
  .fl-cart-price {
    font-size: 13.5px;
  }
  .fl-cart-old {
    font-size: 11.5px;
  }
  .fl-cart-controls {
    grid-column: 3;
    grid-row: 1 / 3;
    align-self: center;
    gap: 6px;
  }
  .fl-cart-stepper {
    height: 30px;
    padding: 0 4px;
    gap: 4px;
  }
  .fl-cart-stepper button {
    width: 22px;
    height: 22px;
    font-size: 10px;
  }
  .fl-cart-stepper span {
    font-size: 12px;
    min-width: 18px;
  }
  .fl-cart-del-btn {
    width: 30px;
    height: 30px;
    font-size: 13px;
  }
}
</style>

