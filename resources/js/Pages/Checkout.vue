<template>
  <div class="fl-checkout-page">
    <TopBar />
    <Navbar activePage="cart" @openModal="isModalOpen = true" />

    <main class="fl-co-main">
      <div class="container">
        <!-- Header Row -->
        <div class="fl-co-header">
          <div class="fl-co-title-wrap">
            <i class="fa-solid fa-lock fl-co-head-icon"></i>
            <h1 class="fl-co-title">Checkout</h1>
          </div>

          <Link href="/cart" class="fl-back-link">
            <i class="fa-solid fa-arrow-left"></i> Back to Cart
          </Link>
        </div>

        <!-- Order Placed Confirmation State -->
        <div v-if="orderPlaced" class="fl-order-success">
          <div class="fl-success-icon">
            <i class="fa-solid fa-circle-check"></i>
          </div>
          <h2>Order Placed Successfully!</h2>
          <p>
            Thank you, <strong>{{ form.name || 'Customer' }}</strong>! Your order
            <strong>#{{ orderId }}</strong> has been confirmed for
            <strong>{{ form.slot }}</strong>.
          </p>
          <div class="fl-success-actions">
            <Link href="/" class="fl-place-btn" style="width:auto; padding:0 28px; text-decoration:none;">
              Back to Home <i class="fa-solid fa-arrow-right"></i>
            </Link>
            <Link href="/products" class="fl-outline-btn">
              Continue Shopping
            </Link>
          </div>
        </div>

        <!-- 2-Column Checkout Form + Order Summary -->
        <form v-else class="fl-co-grid" @submit.prevent="placeOrder">
          <!-- LEFT COLUMN: Address, Delivery Slot, Payment -->
          <div class="fl-co-left">
            <!-- 1. Delivery Address -->
            <section class="fl-co-card">
              <div class="fl-co-card-head">
                <span class="fl-step-num">1</span>
                <h2>Delivery Address</h2>
              </div>

              <div class="fl-form-grid">
                <label class="fl-field">
                  <span>Full Name *</span>
                  <input v-model="form.name" type="text" required placeholder="e.g. Aarav Patel">
                </label>

                <label class="fl-field">
                  <span>Phone Number *</span>
                  <input v-model="form.phone" type="tel" required placeholder="10-digit mobile number" pattern="[6-9][0-9]{9}">
                </label>

                <label class="fl-field fl-span-2">
                  <span>Street Address / House No. *</span>
                  <input v-model="form.address" type="text" required placeholder="Flat / House No., Building, Street">
                </label>

                <label class="fl-field">
                  <span>City *</span>
                  <input v-model="form.city" type="text" required placeholder="e.g. Surat">
                </label>

                <label class="fl-field">
                  <span>Pincode *</span>
                  <input v-model="form.pincode" type="text" required placeholder="6-digit pincode" pattern="[0-9]{6}">
                </label>
              </div>
            </section>

            <!-- 2. Delivery Slot -->
            <section class="fl-co-card">
              <div class="fl-co-card-head">
                <span class="fl-step-num">2</span>
                <h2>Preferred Delivery Slot</h2>
              </div>

              <div class="fl-slot-grid">
                <button
                  v-for="slot in deliverySlots"
                  :key="slot.label"
                  type="button"
                  class="fl-slot-pill"
                  :class="{ active: form.slot === slot.label }"
                  @click="form.slot = slot.label"
                >
                  <i class="fa-solid" :class="slot.icon"></i>
                  <div>
                    <strong>{{ slot.label }}</strong>
                    <span>{{ slot.sub }}</span>
                  </div>
                </button>
              </div>
            </section>

            <!-- 3. Payment Method -->
            <section class="fl-co-card">
              <div class="fl-co-card-head">
                <span class="fl-step-num">3</span>
                <h2>Payment Method</h2>
              </div>

              <div class="fl-pay-list">
                <label
                  v-for="method in paymentMethods"
                  :key="method.id"
                  class="fl-pay-option"
                  :class="{ active: form.payment === method.id }"
                >
                  <input type="radio" name="payment" :value="method.id" v-model="form.payment">
                  <i class="fa-solid" :class="method.icon"></i>
                  <div class="fl-pay-copy">
                    <strong>{{ method.title }}</strong>
                    <span>{{ method.sub }}</span>
                  </div>
                </label>
              </div>
            </section>
          </div>

          <!-- RIGHT COLUMN: Order Summary -->
          <aside class="fl-co-summary-card">
            <h2 class="fl-summary-title">Order Summary ({{ totalCount }} Items)</h2>

            <div class="fl-co-items-mini">
              <div v-for="item in cartItems" :key="item.id" class="fl-co-mini-row">
                <div class="fl-co-mini-thumb">
                  <img :src="item.image" :alt="item.name">
                </div>
                <div class="fl-co-mini-info">
                  <strong>{{ item.name }}</strong>
                  <span>Qty: {{ item.qty }}{{ item.unitLabel ? ` ${item.unitLabel}` : '' }}</span>
                </div>
                <span class="fl-co-mini-price">₹{{ item.price * item.qty }}</span>
              </div>
            </div>

            <div class="fl-summary-divider"></div>

            <div class="fl-summary-rows">
              <div class="fl-summary-line">
                <span>Items Total</span>
                <strong>₹{{ mrpTotal }}</strong>
              </div>
              <div class="fl-summary-line">
                <span>Item Discount</span>
                <strong class="fl-discount-text">− ₹{{ discountTotal }}</strong>
              </div>
              <div class="fl-summary-line">
                <span>Delivery Fee</span>
                <strong>₹{{ deliveryFee }}</strong>
              </div>
            </div>

            <div class="fl-total-payable-bar">
              <span>Total Payable</span>
              <strong>₹{{ subtotal + deliveryFee }}</strong>
            </div>

            <button type="submit" class="fl-place-btn" :disabled="submitting">
              <i class="fa-solid fa-circle-check"></i>
              <span>{{ submitting ? 'Placing Order...' : 'Place Order' }}</span>
              <i class="fa-solid fa-arrow-right"></i>
            </button>
          </aside>
        </form>

        <!-- Bottom 3-Column Trust Strip (matching Cart page) -->
        <div class="fl-co-trust-strip">
          <div class="fl-trust-item">
            <i class="fa-solid fa-leaf"></i>
            <span>Farm Fresh<br>Produce</span>
          </div>
          <div class="fl-trust-item">
            <i class="fa-solid fa-truck-fast"></i>
            <span>Reliable<br>Delivery</span>
          </div>
          <div class="fl-trust-item">
            <i class="fa-solid fa-shield-halved"></i>
            <span>Safe &amp;<br>Quality Assured</span>
          </div>
        </div>
      </div>
    </main>

    <Footer />
    <ContactModal :isOpen="isModalOpen" @close="isModalOpen = false" />
    <WhatsAppFloat />
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue';
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
  clearCart,
} = useCart();

const isModalOpen = ref(false);
const submitting = ref(false);
const orderPlaced = ref(false);
const orderId = ref('');

const form = reactive({
  name: '',
  phone: '',
  address: '',
  city: 'Surat',
  pincode: '',
  slot: 'Express Delivery',
  payment: 'cod',
});

const deliverySlots = [
  { label: 'Express Delivery', sub: 'Within 2 Hours', icon: 'fa-bolt' },
  { label: 'Morning Slot', sub: '7:00 AM – 10:00 AM', icon: 'fa-sun' },
  { label: 'Evening Slot', sub: '5:00 PM – 8:00 PM', icon: 'fa-moon' },
];

const paymentMethods = [
  { id: 'cod', title: 'Cash on Delivery', sub: 'Pay with cash or UPI at your doorstep', icon: 'fa-money-bill-wave' },
  { id: 'upi', title: 'UPI Payment', sub: 'Google Pay, PhonePe, Paytm, BHIM', icon: 'fa-mobile-screen-button' },
  { id: 'card', title: 'Credit / Debit Card', sub: 'Visa, Mastercard, RuPay', icon: 'fa-credit-card' },
];

function placeOrder() {
  submitting.value = true;
  setTimeout(() => {
    orderId.value = `FL-${Math.floor(100000 + Math.random() * 900000)}`;
    submitting.value = false;
    orderPlaced.value = true;
    clearCart();
  }, 450);
}
</script>

<style scoped>
.fl-checkout-page {
  background-color: #F6FAF2;
  min-height: 100vh;
  font-family: 'Plus Jakarta Sans', var(--font-main), sans-serif;
}

.fl-co-main {
  padding: 28px 0 64px;
}

.fl-co-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 22px;
  gap: 16px;
}

.fl-co-title-wrap {
  display: flex;
  align-items: center;
  gap: 12px;
}

.fl-co-head-icon {
  font-size: 22px;
  color: #124E26;
}

.fl-co-title {
  font-size: 28px;
  font-weight: 800;
  color: #124E26;
  margin: 0;
  letter-spacing: -0.02em;
}

.fl-back-link {
  font-size: 14px;
  font-weight: 600;
  color: #14532D;
  text-decoration: underline;
  display: inline-flex;
  align-items: center;
  gap: 8px;
}

.fl-co-grid {
  display: grid;
  grid-template-columns: 1.5fr 0.9fr;
  gap: 24px;
  align-items: start;
}

.fl-co-left {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.fl-co-card,
.fl-co-summary-card {
  background: #FFFFFF;
  border: 1px solid #E2ECD9;
  border-radius: 16px;
  padding: 22px 24px;
  box-shadow: 6px 10px 26px rgba(20, 83, 45, 0.1), -4px -2px 14px rgba(20, 83, 45, 0.05);
}

.fl-co-summary-card {
  position: sticky;
  top: 120px;
}

.fl-co-card-head {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 18px;
}

.fl-step-num {
  width: 28px;
  height: 28px;
  border-radius: 50%;
  background: #14532D;
  color: #FFFFFF;
  font-size: 13px;
  font-weight: 800;
  display: inline-flex;
  align-items: center;
  justify-content: center;
}

.fl-co-card-head h2 {
  font-size: 18px;
  font-weight: 800;
  color: #124E26;
  margin: 0;
}

.fl-form-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 14px;
}

.fl-span-2 {
  grid-column: 1 / -1;
}

.fl-field {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.fl-field span {
  font-size: 12.5px;
  font-weight: 700;
  color: #374151;
}

.fl-field input {
  height: 42px;
  border: 1px solid #D5E4CE;
  border-radius: 10px;
  padding: 0 14px;
  font-size: 14px;
  color: #111827;
  font-family: inherit;
  outline: none;
  transition: border-color 0.2s ease;
}

.fl-field input:focus {
  border-color: #14532D;
}

/* Delivery Slots */
.fl-slot-grid {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 12px;
}

.fl-slot-pill {
  border: 1.5px solid #D5E4CE;
  background: #FFFFFF;
  border-radius: 12px;
  padding: 12px 14px;
  display: flex;
  align-items: center;
  gap: 10px;
  text-align: left;
  cursor: pointer;
  font-family: inherit;
  transition: border-color 0.2s ease, background-color 0.2s ease;
}

.fl-slot-pill i {
  font-size: 16px;
  color: #3F9C1E;
}

.fl-slot-pill strong {
  display: block;
  font-size: 13px;
  color: #111827;
}

.fl-slot-pill span {
  display: block;
  font-size: 11.5px;
  color: #6B7280;
}

.fl-slot-pill.active {
  background: #EBF7E3;
  border-color: #14532D;
}

/* Payment Options */
.fl-pay-list {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.fl-pay-option {
  border: 1.5px solid #E5ECE1;
  border-radius: 12px;
  padding: 12px 16px;
  display: flex;
  align-items: center;
  gap: 14px;
  cursor: pointer;
  transition: border-color 0.2s ease, background-color 0.2s ease;
}

.fl-pay-option input {
  accent-color: #14532D;
  width: 16px;
  height: 16px;
}

.fl-pay-option i {
  font-size: 18px;
  color: #14532D;
}

.fl-pay-copy strong {
  display: block;
  font-size: 14px;
  color: #111827;
}

.fl-pay-copy span {
  display: block;
  font-size: 12px;
  color: #6B7280;
}

.fl-pay-option.active {
  background: #EBF7E3;
  border-color: #14532D;
}

/* Summary Card */
.fl-summary-title {
  font-size: 19px;
  font-weight: 800;
  color: #124E26;
  margin: 0 0 16px 0;
}

.fl-co-items-mini {
  display: flex;
  flex-direction: column;
  gap: 12px;
  max-height: 240px;
  overflow-y: auto;
  margin-bottom: 14px;
}

.fl-co-mini-row {
  display: flex;
  align-items: center;
  gap: 10px;
}

.fl-co-mini-thumb {
  width: 44px;
  height: 44px;
  border-radius: 8px;
  background: #F4F5EF;
  padding: 4px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.fl-co-mini-thumb img {
  width: 100%;
  height: 100%;
  object-fit: contain;
}

.fl-co-mini-info {
  flex: 1;
  min-width: 0;
}

.fl-co-mini-info strong {
  display: block;
  font-size: 13px;
  color: #111827;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.fl-co-mini-info span {
  font-size: 11.5px;
  color: #6B7280;
}

.fl-co-mini-price {
  font-size: 13.5px;
  font-weight: 700;
  color: #14532D;
}

.fl-summary-divider {
  height: 1px;
  background: #EEF2EC;
  margin: 12px 0;
}

.fl-summary-rows {
  display: flex;
  flex-direction: column;
  gap: 10px;
  margin-bottom: 14px;
}

.fl-summary-line {
  display: flex;
  align-items: center;
  justify-content: space-between;
  font-size: 14px;
  color: #374151;
}

.fl-discount-text {
  color: #3F9C1E;
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

.fl-place-btn {
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

.fl-place-btn:hover {
  background: #0F4022;
}

.fl-outline-btn {
  height: 44px;
  padding: 0 24px;
  border: 1.5px solid #14532D;
  border-radius: 10px;
  color: #14532D;
  font-size: 14px;
  font-weight: 700;
  display: inline-flex;
  align-items: center;
  text-decoration: none;
}

.fl-order-success {
  background: #FFFFFF;
  border: 1px solid #E2ECD9;
  border-radius: 18px;
  padding: 52px 24px;
  text-align: center;
  box-shadow: 6px 10px 26px rgba(20, 83, 45, 0.1), -4px -2px 14px rgba(20, 83, 45, 0.05);
}

.fl-success-icon {
  font-size: 52px;
  color: #3F9C1E;
  margin-bottom: 14px;
}

.fl-order-success h2 {
  font-size: 26px;
  font-weight: 800;
  color: #124E26;
  margin: 0 0 8px;
}

.fl-order-success p {
  font-size: 15px;
  color: #4A5548;
  margin: 0 0 24px;
}

.fl-success-actions {
  display: flex;
  justify-content: center;
  gap: 12px;
  flex-wrap: wrap;
}

.fl-co-trust-strip {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  margin-top: 28px;
  padding-top: 20px;
  border-top: 1px solid #DCECD3;
}

.fl-trust-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  gap: 6px;
  padding: 6px 8px;
  font-size: 12.5px;
  font-weight: 700;
  line-height: 1.3;
  color: #124E26;
}

.fl-trust-item + .fl-trust-item {
  border-left: 1px solid #DCECD3;
}

.fl-trust-item i {
  font-size: 20px;
  color: #3F9C1E;
}

@media (max-width: 960px) {
  .fl-co-grid {
    grid-template-columns: 1fr;
    gap: 18px;
  }
  .fl-co-summary-card {
    position: static;
  }
}

@media (max-width: 640px) {
  .fl-co-main {
    padding: 18px 0 28px;
  }
  .fl-co-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 6px;
    margin-bottom: 16px;
  }
  .fl-co-title {
    font-size: 22px;
  }
  .fl-co-head-icon {
    font-size: 19px;
  }
  .fl-co-card,
  .fl-co-summary-card {
    padding: 16px 15px;
    border-radius: 14px;
  }
  .fl-co-card-head {
    margin-bottom: 14px;
    gap: 10px;
  }
  .fl-co-card-head h2 {
    font-size: 16px;
  }
  .fl-form-grid,
  .fl-slot-grid {
    grid-template-columns: 1fr;
    gap: 11px;
  }
  .fl-slot-pill,
  .fl-pay-option {
    padding: 11px 12px;
  }
  .fl-trust-item {
    font-size: 11.5px;
  }
}
</style>

