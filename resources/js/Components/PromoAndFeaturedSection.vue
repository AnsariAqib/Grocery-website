<template>
  <div>
    <!-- 1. Three Promotional Offer Cards -->
    <section class="fl-offers-section">
      <div class="container">
        <div class="fl-offers-grid">
          <div
            v-for="offer in offers"
            :key="offer.title"
            class="fl-offer-card"
            :style="{ background: offer.bg }"
          >
            <div class="fl-offer-copy">
              <span class="fl-offer-badge">
                <i class="fa-solid" :class="offer.icon" :style="{ color: offer.iconColor }"></i>
                {{ offer.badge }}
              </span>
              <h3 class="fl-offer-title">{{ offer.title }}</h3>
              <p class="fl-offer-sub">{{ offer.subtitle }}</p>
              <Link :href="offer.href" class="fl-offer-btn">
                Shop Now <i class="fa-solid fa-arrow-right"></i>
              </Link>
            </div>
            <div class="fl-offer-art">
              <img :src="offer.image" :alt="offer.title" loading="lazy">
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- 2. Featured Products + Mega Grocery Sale Banner + Our Best Sellers -->
    <template v-for="(section, sIdx) in productSections" :key="section.titleLead + section.titleAccent">
      <section
        class="fl-showcase-section"
        :class="{ 'is-alt': section.altBg }"
      >
        <div class="container">
          <div class="fl-showcase-header">
            <div>
              <h2 class="fl-showcase-title">
                {{ section.titleLead }} <span>{{ section.titleAccent }}</span>
              </h2>
              <p class="fl-showcase-sub">{{ section.subtitle }}</p>
            </div>
            <Link :href="section.viewAllHref" class="fl-showcase-view-all">
              View All <i class="fa-solid fa-arrow-right"></i>
            </Link>
          </div>

          <div class="fl-showcase-grid">
            <div
              v-for="product in section.items"
              :key="product.id"
              class="fl-showcase-card"
            >
              <Link :href="productHref(product)" class="fl-card-link">
                <div class="fl-card-img-box">
                  <img :src="product.image" :alt="product.name" loading="lazy">
                </div>

                <div class="fl-card-info">
                  <h3 class="fl-card-title">{{ product.name }}</h3>
                  <p class="fl-card-unit">{{ product.unit }}</p>

                  <div class="fl-card-rating">
                    <i class="fa-solid fa-star"></i>
                    <strong>{{ product.rating }}</strong>
                    <span>({{ product.reviews }} reviews)</span>
                  </div>

                  <div class="fl-card-price-row">
                    <span class="fl-card-price">₹{{ product.price }}</span>
                    <span class="fl-card-old">₹{{ product.oldPrice }}</span>
                    <span class="fl-card-off">{{ product.off }}</span>
                  </div>
                </div>
              </Link>

              <div class="fl-card-body">
                <button
                  type="button"
                  class="fl-card-cart-btn"
                  @click="addToCart(product)"
                >
                  <i class="fa-solid fa-cart-shopping"></i>
                  {{ addedId === product.id ? 'Added!' : 'Add to Cart' }}
                </button>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Mega Grocery Sale Spotlight Banner (Between Featured Products & Best Sellers) -->
      <section v-if="sIdx === 0" class="fl-mega-sale-section">
        <div class="container">
          <div class="fl-mega-sale-card">
            <div class="fl-mega-overlay"></div>
            <div class="fl-mega-left">
              <h2 class="fl-mega-title">Mega Grocery Sale</h2>
              <p class="fl-mega-sub">Fresh essentials at special prices.</p>

              <div class="fl-mega-meta">
                <span class="fl-mega-off-pill">Up to <strong>50% OFF</strong></span>
                <span class="fl-mega-sep"></span>
                <span class="fl-mega-date">
                  <i class="fa-regular fa-calendar"></i> 25 – 30 Nov
                </span>
              </div>

              <Link href="/products" class="fl-mega-btn">
                Shop Now <i class="fa-solid fa-arrow-right"></i>
              </Link>
            </div>
          </div>
        </div>
      </section>

      <!-- Organic & Pesticide-Free Banner (Right below Our Best Sellers) -->
      <section v-if="sIdx === 1" class="fl-organic-banner-section">
        <div class="container">
          <div class="fl-organic-banner-card">
            <div class="fl-organic-overlay"></div>
            <div class="fl-organic-copy">
              <h2 class="fl-organic-title">
                Organic &amp;<br>
                Pesticide-Free Produce
              </h2>
              <p class="fl-organic-sub">
                Naturally grown. Healthier for you and your family.
              </p>
              <Link href="/products" class="fl-organic-btn">
                Explore Organic Range <i class="fa-solid fa-arrow-right"></i>
              </Link>
            </div>
          </div>
        </div>
      </section>
    </template>

    <!-- 3. Meet Our Popular Farmers Section -->
    <section class="fl-farmers-section">
      <div class="container">
        <div class="fl-farmers-header">
          <h2 class="fl-farmers-title">Meet Our Popular Farmers</h2>
          <p class="fl-farmers-sub">
            The dedicated local farmers who grow and harvest with care, bringing fresh,
            naturally cultivated, and quality produce directly from their fields to your home.
          </p>
        </div>

        <div class="fl-farmers-grid">
          <div
            v-for="farmer in farmers"
            :key="farmer.name"
            class="fl-farmer-card"
          >
            <img :src="farmer.image" :alt="farmer.name" class="fl-farmer-bg" loading="lazy">
            <div class="fl-farmer-overlay">
              <h3 class="fl-farmer-name">{{ farmer.name }}</h3>
              <p class="fl-farmer-loc">
                <i class="fa-solid fa-location-dot"></i> {{ farmer.location }}
              </p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- 4. Fresh Feed from Local Mandi (Reels / Video Feed) -->
    <section class="fl-mandi-section">
      <div class="container">
        <div class="fl-mandi-header">
          <div class="fl-mandi-title-group">
            <span class="fl-mandi-icon-box">
              <i class="fa-solid fa-play"></i>
            </span>
            <div>
              <h2 class="fl-mandi-title">Fresh Feed from Local Mandi</h2>
              <p class="fl-mandi-sub">Real updates, fresh arrivals and a look inside the local mandi.</p>
            </div>
          </div>

          <Link href="/promotions" class="fl-mandi-watch-btn">
            <i class="fa-solid fa-clapperboard"></i>
            <span>Watch All Videos</span>
            <i class="fa-solid fa-chevron-right"></i>
          </Link>
        </div>

        <div class="fl-mandi-grid">
          <a
            v-for="reel in mandiReels"
            :key="reel.id"
            href="#categories"
            class="fl-mandi-card"
          >
            <img :src="reel.image" :alt="reel.title" loading="lazy">
          </a>
        </div>
      </div>
    </section>

    <!-- 5. Customer Reviews Section -->
    <section class="fl-reviews-section">
      <div class="container">
        <div class="fl-reviews-top">
          <h2 class="fl-reviews-heading">
            Read reviews,<br>
            ride with <span class="fl-rev-accent">confidence.</span>
          </h2>
          <div class="fl-reviews-trust">
            <span>4.8/5</span>
            <span class="fl-rev-sep">|</span>
            <span class="fl-tp-badge"><i class="fa-solid fa-star"></i> Trustpilot</span>
            <span class="fl-rev-sep">|</span>
            <span>Based on 5210 reviews</span>
          </div>
        </div>

        <div class="fl-reviews-grid">
          <!-- Left Intro & Arrows -->
          <div class="fl-rev-left">
            <div class="fl-rev-big-quote"><i class="fa-solid fa-quote-left"></i></div>
            <h3 class="fl-rev-left-title">
              What our<br>
              customers are<br>
              <span>saying</span>
            </h3>
            <span class="fl-rev-line"></span>
            <div class="fl-rev-arrows">
              <button type="button" class="fl-rev-arrow prev" aria-label="Previous review" @click="shiftReviews(-1)">
                <i class="fa-solid fa-arrow-left"></i>
              </button>
              <button type="button" class="fl-rev-arrow next" aria-label="Next review" @click="shiftReviews(1)">
                <i class="fa-solid fa-arrow-right"></i>
              </button>
            </div>
          </div>

          <!-- 3 Review Cards -->
          <article
            v-for="(rev, idx) in visibleReviews"
            :key="rev.name"
            class="fl-rev-card"
            :class="{ 'is-mint': idx === 1 }"
          >
            <div class="fl-rev-card-quote"><i class="fa-solid fa-quote-left"></i></div>
            <p class="fl-rev-text">{{ rev.text }}</p>
            <div class="fl-rev-stars">
              <i v-for="n in 5" :key="n" class="fa-solid fa-star"></i>
            </div>
            <div class="fl-rev-author">
              <img :src="rev.avatar" :alt="rev.name" loading="lazy">
              <div>
                <strong>{{ rev.name }}</strong>
                <span>{{ rev.location }}</span>
              </div>
            </div>
          </article>
        </div>
      </div>
    </section>

    <!-- 6. FAQ Section -->
    <section class="fl-faq-section">
      <div class="container">
        <div class="fl-faq-grid">
          <!-- Left Intro + Basket PNG -->
          <div class="fl-faq-left">
            <span class="fl-faq-pill">
              <i class="fa-solid fa-leaf"></i> FAQ
            </span>
            <h2 class="fl-faq-title">
              Frequently<br>
              Asked<br>
              <span>Questions</span>
            </h2>
            <p class="fl-faq-sub">
              Find quick answers about orders, delivery, payments and more.
            </p>
            <div class="fl-faq-art">
              <img src="/assets/mega-grocery-basket.png" alt="Fresh Farm Produce" loading="lazy">
            </div>
          </div>

          <!-- Right Accordion List -->
          <div class="fl-faq-list">
            <div
              v-for="(item, idx) in faqs"
              :key="item.q"
              class="fl-faq-item"
              :class="{ open: openFaqIdx === idx }"
            >
              <button
                type="button"
                class="fl-faq-q"
                :aria-expanded="openFaqIdx === idx"
                @click="openFaqIdx = openFaqIdx === idx ? -1 : idx"
              >
                <span>{{ item.q }}</span>
                <i class="fa-solid" :class="openFaqIdx === idx ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
              </button>
              <div v-show="openFaqIdx === idx" class="fl-faq-a">
                {{ item.a }}
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { useCart } from '@/composables/useCart';

const { addItem } = useCart();
const addedId = ref(null);
const reviewOffset = ref(0);
const openFaqIdx = ref(0);

const offers = [
  {
    badge: 'Seasonal Fresh',
    icon: 'fa-leaf',
    iconColor: '#3F9C1E',
    title: 'Up to 30% OFF',
    subtitle: 'on Fresh Fruits',
    href: '/products',
    image: '/assets/fruits and veggies.png',
    bg: 'linear-gradient(135deg, #E8F5DF 0%, #D4ECC4 100%)',
  },
  {
    badge: 'Pantry Savings',
    icon: 'fa-tag',
    iconColor: '#D97706',
    title: 'Up to 25% OFF',
    subtitle: 'on Everyday Staples',
    href: '/products',
    image: '/assets/store-categories-products.png',
    bg: 'linear-gradient(135deg, #FDF4E1 0%, #F9E6BF 100%)',
  },
  {
    badge: 'Snack Bonanza',
    icon: 'fa-gift',
    iconColor: '#0D9488',
    title: 'Buy 1 Get 1 FREE',
    subtitle: 'on Selected Snacks',
    href: '/promotions',
    image: '/assets/cat-7.png',
    bg: 'linear-gradient(135deg, #E4F0FB 0%, #CEE2F7 100%)',
  },
];

const productSections = [
  {
    titleLead: 'Featured',
    titleAccent: 'Products',
    subtitle: 'Handpicked for you from our fresh and daily essentials.',
    viewAllHref: '/products',
    altBg: false,
    items: [
      { id: 'f1', name: 'Fresh Vegetables', unit: '1 kg', rating: '4.8', reviews: '2,426', price: 149, oldPrice: 179, off: '17% OFF', image: '/assets/feat-1.jpg' },
      { id: 'f2', name: 'Organic Milk', unit: '1 L', rating: '4.7', reviews: '1,920', price: 79, oldPrice: 89, off: '11% OFF', image: '/assets/feat-2.jpg' },
      { id: 'f3', name: 'Grocery Essentials', unit: '500 g', rating: '4.5', reviews: '1,348', price: 149, oldPrice: 169, off: '12% OFF', image: '/assets/feat-3.jpg' },
      { id: 'f4', name: 'Daily Snacks Pack', unit: '400 g', rating: '4.6', reviews: '1,072', price: 229, oldPrice: 259, off: '12% OFF', image: '/assets/feat-4.jpg' },
      { id: 'f5', name: 'Dry Fruits Mix', unit: '250 g', rating: '4.7', reviews: '892', price: 249, oldPrice: 299, off: '17% OFF', image: '/assets/feat-5.jpg' },
      { id: 'f6', name: 'Fresh Fruits', unit: '1 kg', rating: '4.5', reviews: '1,560', price: 179, oldPrice: 199, off: '10% OFF', image: '/assets/feat-6.jpg' },
    ],
  },
  {
    titleLead: 'Our',
    titleAccent: 'Best Sellers',
    subtitle: 'Most loved products, handpicked for you.',
    viewAllHref: '/products',
    altBg: true,
    items: [
      { id: 'b1', name: 'Fresh Red Apples', unit: '1 kg', rating: '4.6', reviews: '1,248', price: 149, oldPrice: 209, off: '29% OFF', image: '/assets/feat-6.jpg' },
      { id: 'b2', name: 'Fresh Bananas', unit: '1 kg', rating: '4.5', reviews: '862', price: 59, oldPrice: 79, off: '23% OFF', image: '/assets/feat-6.jpg' },
      { id: 'b3', name: 'Fresh Tomatoes', unit: '1 kg', rating: '4.7', reviews: '1,356', price: 39, oldPrice: 54, off: '28% OFF', image: '/assets/feat-1.jpg' },
      { id: 'b4', name: 'Fresh Potatoes', unit: '2 kg', rating: '4.5', reviews: '983', price: 69, oldPrice: 99, off: '29% OFF', image: '/assets/feat-3.jpg' },
      { id: 'b5', name: 'Onions', unit: '1 kg', rating: '4.6', reviews: '1,142', price: 45, oldPrice: 65, off: '30% OFF', image: '/assets/feat-1.jpg' },
      { id: 'b6', name: 'Fresh Carrots', unit: '1 kg', rating: '4.5', reviews: '768', price: 55, oldPrice: 74, off: '25% OFF', image: '/assets/feat-5.jpg' },
    ],
  },
  {
    titleLead: 'New',
    titleAccent: 'Arrivals',
    subtitle: 'Freshly stocked farm harvests & pantry launches.',
    viewAllHref: '/products',
    altBg: false,
    items: [
      { id: 'n1', name: 'Cold-Pressed Mustard Oil', unit: '1 L', rating: '4.8', reviews: '312', price: 215, oldPrice: 260, off: '17% OFF', image: '/assets/feat-3.jpg' },
      { id: 'n2', name: 'Raw Forest Honey', unit: '500 g', rating: '4.9', reviews: '428', price: 349, oldPrice: 420, off: '17% OFF', image: '/assets/feat-5.jpg' },
      { id: 'n3', name: 'Organic Brown Chana', unit: '1 kg', rating: '4.6', reviews: '245', price: 115, oldPrice: 140, off: '18% OFF', image: '/assets/feat-4.jpg' },
      { id: 'n4', name: 'A2 Cow Ghee', unit: '500 ml', rating: '4.9', reviews: '519', price: 599, oldPrice: 720, off: '17% OFF', image: '/assets/feat-2.jpg' },
      { id: 'n5', name: 'Himalayan Pink Salt', unit: '1 kg', rating: '4.7', reviews: '384', price: 89, oldPrice: 115, off: '23% OFF', image: '/assets/store-categories-products.png' },
      { id: 'n6', name: 'Seasonal Fruit Box', unit: '3 kg', rating: '4.8', reviews: '276', price: 299, oldPrice: 375, off: '20% OFF', image: '/assets/feat-1.jpg' },
    ],
  },
];

const farmers = [
  { name: 'Mohd. Salim', location: 'Bareilly, UP', image: '/assets/farmer-1.jpg' },
  { name: 'Rizwan Khan', location: 'Pilibhit, UP', image: '/assets/farmer-2.jpg' },
  { name: 'Irfan Ali', location: 'Rampur, UP', image: '/assets/farmer-3.jpg' },
  { name: 'Naseem Ahmad', location: 'Shahjahanpur, UP', image: '/assets/farmer-4.jpg' },
  { name: 'Azeem Khan', location: 'Badaun, UP', image: '/assets/farmer-5.jpg' },
];

const mandiReels = [
  { id: 1, title: 'Fresh Carrots Harvest', image: '/assets/mandi-1.jpg' },
  { id: 2, title: 'Vine Tomatoes Arrival', image: '/assets/mandi-2.jpg' },
  { id: 3, title: 'Farm Potatoes Stock', image: '/assets/mandi-3.jpg' },
  { id: 4, title: 'Crisp Leafy Greens', image: '/assets/mandi-4.jpg' },
  { id: 5, title: 'Morning Local Mandi', image: '/assets/mandi-5.jpg' },
];

const reviews = [
  {
    name: 'Kiran',
    location: 'Bareilly, UP',
    avatar: '/assets/reviewer-1.jpg',
    text: 'The produce is always fresh and the quality is very good. Delivery is on time and packaging is neat.',
  },
  {
    name: 'Sana Parveen',
    location: 'Rampur, UP',
    avatar: '/assets/reviewer-2.jpg',
    text: 'I love the farm-fresh quality. The vegetables and fruits stay fresh for longer.',
  },
  {
    name: 'Mohd. Faizan',
    location: 'Lucknow, UP',
    avatar: '/assets/reviewer-3.jpg',
    text: 'Great experience! Easy to order, fair prices and a good variety of daily groceries.',
  },
];

const visibleReviews = computed(() =>
  reviews.map((_, i) => reviews[(i + reviewOffset.value) % reviews.length]),
);

function shiftReviews(dir) {
  reviewOffset.value = (reviewOffset.value + dir + reviews.length) % reviews.length;
}

const faqs = [
  {
    q: 'How does VIZORA work?',
    a: 'VIZORA FOODS connects you directly with local farmers. You can browse fresh fruits, vegetables and daily essentials, place an order and get it delivered to your doorstep.',
  },
  {
    q: 'Do you deliver in my area?',
    a: 'Yes, we deliver across major residential areas and townships daily between 7:00 AM and 10:00 PM. Select your location in the header to check available delivery slots.',
  },
  {
    q: 'How can I place an order?',
    a: 'Simply browse our categories or search for your daily essentials, add items to your cart, and complete checkout in a few clicks.',
  },
  {
    q: 'What payment methods are available?',
    a: 'We accept Cash on Delivery (COD), UPI (Google Pay, PhonePe, Paytm), and all major Credit and Debit cards.',
  },
  {
    q: 'Can I change or cancel my order?',
    a: 'Yes, you can modify or cancel your order anytime before it is dispatched by reaching out to our customer support team.',
  },
  {
    q: 'Are the products really fresh and farm-sourced?',
    a: 'Absolutely! Every fruit, vegetable, and pantry staple is sourced directly from trusted local farmers and quality-checked before packing.',
  },
];

function slugify(name) {
  return String(name || '')
    .toLowerCase()
    .trim()
    .replace(/[^a-z0-9]+/g, '-')
    .replace(/^-+|-+$/g, '');
}

function productHref(product) {
  const slug = slugify(product.name);
  const params = new URLSearchParams({
    name: product.name,
    image: product.image,
    price: String(product.price),
    oldPrice: String(product.oldPrice),
    unit: product.unit,
    off: product.off,
    rating: product.rating,
    reviews: product.reviews,
  });

  return `/products/${slug}?${params.toString()}`;
}

function addToCart(product) {
  addItem(product);
  addedId.value = product.id;
  setTimeout(() => {
    if (addedId.value === product.id) addedId.value = null;
  }, 1200);
}
</script>

<style scoped>
.fl-offers-section {
  padding: 20px 0 28px;
  background: #FFFFFF;
  font-family: 'Plus Jakarta Sans', var(--font-main), sans-serif;
}

.fl-offers-grid {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 20px;
}

.fl-offer-card {
  border-radius: 16px;
  padding: 20px 18px 20px 22px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  min-height: 176px;
  position: relative;
  overflow: hidden;
}

.fl-offer-copy {
  position: relative;
  z-index: 2;
  flex: 1 1 auto;
}

.fl-offer-badge {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: rgba(255, 255, 255, 0.92);
  color: #1F2937;
  font-size: 11.5px;
  font-weight: 600;
  padding: 4px 10px;
  border-radius: 9999px;
  margin-bottom: 10px;
}

.fl-offer-title {
  font-size: 22px;
  font-weight: 800;
  color: #111827;
  line-height: 1.15;
  margin: 0 0 4px 0;
  letter-spacing: -0.02em;
}

.fl-offer-sub {
  font-size: 13.5px;
  font-weight: 500;
  color: #374151;
  margin: 0 0 14px 0;
}

.fl-offer-btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background-color: #14532D;
  color: #FFFFFF;
  font-size: 12.5px;
  font-weight: 600;
  padding: 8px 16px;
  border-radius: 9999px;
  text-decoration: none;
  transition: background-color 0.2s ease, transform 0.2s ease;
}

.fl-offer-btn:hover {
  background-color: #0F4022;
  transform: translateY(-1px);
}

.fl-offer-art {
  flex: 0 0 128px;
  height: 132px;
  display: flex;
  align-items: flex-end;
  justify-content: flex-end;
}

.fl-offer-art img {
  max-width: 100%;
  max-height: 100%;
  object-fit: contain;
}

/* Featured Products & Best Sellers Shared Showcase Design */
.fl-showcase-section {
  padding: 40px 0;
  background: #F6FAF2;
  font-family: 'Plus Jakarta Sans', var(--font-main), sans-serif;
}

.fl-showcase-section.is-alt {
  background: #FFFFFF;
}

.fl-showcase-header {
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  gap: 16px;
  margin-bottom: 24px;
}

.fl-eyebrow-row {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 4px;
}

.fl-eyebrow {
  font-size: 12px;
  font-weight: 700;
  letter-spacing: 0.08em;
  color: #3F9C1E;
  text-transform: uppercase;
}

.fl-eyebrow-line {
  width: 52px;
  height: 1px;
  background-color: #A7D493;
  display: inline-block;
}

.fl-showcase-title {
  font-size: 34px;
  font-weight: 800;
  color: #0F2E1A;
  line-height: 1.15;
  margin: 0 0 4px 0;
  letter-spacing: -0.025em;
}

.fl-showcase-title span {
  color: #3F9C1E;
}

.fl-showcase-sub {
  font-size: 14px;
  color: #4A5548;
  margin: 0;
  font-weight: 400;
}

.fl-showcase-view-all {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: transparent;
  border: 1.5px solid #14532D;
  border-radius: 9999px;
  padding: 9px 20px;
  font-size: 13.5px;
  font-weight: 700;
  color: #124E26;
  text-decoration: none;
  white-space: nowrap;
  transition: background-color 0.2s ease, color 0.2s ease;
}

.fl-showcase-view-all:hover {
  background-color: #14532D;
  color: #FFFFFF;
}

.fl-showcase-grid {
  display: grid;
  grid-template-columns: repeat(6, minmax(0, 1fr));
  gap: 16px;
}

.fl-showcase-card {
  background: #FFFFFF;
  border: 1px solid #ECF1E7;
  border-radius: 14px;
  padding: 10px;
  box-shadow: 0 2px 12px rgba(20, 83, 45, 0.05);
  display: flex;
  flex-direction: column;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.fl-showcase-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 20px rgba(20, 83, 45, 0.09);
}

.fl-card-img-box {
  background-color: #F4F5EF;
  border-radius: 10px;
  height: 148px;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0;
  overflow: hidden;
}

.fl-card-img-box img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.fl-card-link {
  display: flex;
  flex-direction: column;
  flex: 1;
  text-decoration: none;
  color: inherit;
  cursor: pointer;
}

.fl-card-link:hover .fl-card-title {
  color: #14532D;
}

.fl-card-info {
  padding: 10px 4px 0;
  display: flex;
  flex-direction: column;
  flex: 1;
}

.fl-card-body {
  padding: 0 4px 2px;
  display: flex;
  flex-direction: column;
}

.fl-card-title {
  font-size: 14px;
  font-weight: 700;
  color: #111827;
  margin: 0 0 2px 0;
  line-height: 1.3;
}

.fl-card-unit {
  font-size: 12px;
  color: #6B7280;
  margin: 0 0 6px 0;
}

.fl-card-rating {
  display: flex;
  align-items: center;
  gap: 4px;
  font-size: 12px;
  margin-bottom: 8px;
}

.fl-card-rating i {
  color: #F59E0B;
  font-size: 12px;
}

.fl-card-rating strong {
  color: #111827;
  font-weight: 700;
}

.fl-card-rating span {
  color: #6B7280;
  font-size: 11.5px;
}

.fl-card-price-row {
  display: flex;
  align-items: center;
  gap: 6px;
  flex-wrap: wrap;
  margin-bottom: 10px;
  margin-top: auto;
}

.fl-card-price {
  font-size: 16px;
  font-weight: 800;
  color: #111827;
}

.fl-card-old {
  font-size: 12px;
  color: #9CA3AF;
  text-decoration: line-through;
}

.fl-card-off {
  background-color: #DCF0CF;
  color: #14532D;
  font-size: 10.5px;
  font-weight: 700;
  padding: 2px 7px;
  border-radius: 9999px;
}

.fl-card-cart-btn {
  width: 100%;
  height: 36px;
  border: none;
  border-radius: 8px;
  background-color: #14532D;
  color: #FFFFFF;
  font-family: inherit;
  font-size: 12.5px;
  font-weight: 600;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 7px;
  cursor: pointer;
  transition: background-color 0.2s ease;
}

.fl-card-cart-btn:hover {
  background-color: #0F4022;
}

/* Mega Grocery Sale Full-Bleed Photographic Banner */
.fl-mega-sale-section {
  padding: 40px 0 32px;
  background: #F6FAF2;
  font-family: 'Plus Jakarta Sans', var(--font-main), sans-serif;
}

.fl-mega-sale-card {
  position: relative;
  border-radius: 20px;
  height: 275px;
  min-height: 275px;
  padding: 36px 48px;
  display: flex;
  align-items: center;
  overflow: hidden;
  background-color: #133B1D;
  background-image: url('/assets/mega-sale-banner-bg.jpg');
  background-size: cover;
  background-position: right center;
  box-shadow: 0 12px 30px rgba(20, 83, 45, 0.14);
}

.fl-mega-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(95deg, rgba(16, 54, 24, 0.96) 0%, rgba(19, 62, 28, 0.88) 44%, rgba(19, 62, 28, 0.18) 74%, rgba(19, 62, 28, 0) 100%);
  pointer-events: none;
}

.fl-mega-left {
  position: relative;
  z-index: 2;
  max-width: 560px;
}

.fl-deal-pill {
  display: inline-flex;
  align-items: center;
  gap: 7px;
  background-color: rgba(74, 222, 128, 0.18);
  border: 1px solid rgba(74, 222, 128, 0.35);
  color: #BBF7D0;
  font-size: 13.5px;
  font-weight: 700;
  padding: 5px 14px;
  border-radius: 9999px;
  margin-bottom: 12px;
}

.fl-mega-title {
  font-size: 46px;
  font-weight: 800;
  color: #FFFFFF;
  line-height: 1.08;
  margin: 0 0 10px 0;
  letter-spacing: -0.025em;
}

.fl-mega-sub {
  font-size: 17px;
  color: rgba(255, 255, 255, 0.88);
  margin: 0 0 20px 0;
}

.fl-mega-meta {
  display: flex;
  align-items: center;
  gap: 16px;
  flex-wrap: wrap;
  margin-bottom: 24px;
}

.fl-mega-off-pill {
  background-color: #DCF7B8;
  color: #124E26;
  font-size: 16px;
  font-weight: 700;
  padding: 6px 16px;
  border-radius: 9999px;
}

.fl-mega-off-pill strong {
  font-weight: 800;
  font-size: 18px;
}

.fl-mega-sep {
  width: 1px;
  height: 22px;
  background-color: rgba(255, 255, 255, 0.28);
}

.fl-mega-date {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  font-size: 16px;
  font-weight: 700;
  color: #FFFFFF;
}

.fl-mega-btn {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  background-color: #469B25;
  color: #FFFFFF;
  font-size: 14.5px;
  font-weight: 700;
  padding: 12px 28px;
  border-radius: 9999px;
  text-decoration: none;
  box-shadow: 0 6px 16px rgba(70, 155, 37, 0.35);
  transition: background-color 0.2s ease, transform 0.2s ease;
}

.fl-mega-btn:hover {
  background-color: #3A841E;
  transform: translateY(-1px);
}

/* Organic & Pesticide-Free Banner */
.fl-organic-banner-section {
  padding: 24px 0 36px;
  background: #FFFFFF;
  font-family: 'Plus Jakarta Sans', var(--font-main), sans-serif;
}

.fl-organic-banner-card {
  position: relative;
  border-radius: 20px;
  height: 275px;
  min-height: 275px;
  padding: 36px 48px;
  display: flex;
  align-items: center;
  overflow: hidden;
  background-color: #133B1D;
  background-image: url('/assets/organic-banner-bg.jpg');
  background-size: cover;
  background-position: right center;
  box-shadow: 0 12px 30px rgba(20, 83, 45, 0.14);
}

.fl-organic-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(95deg, rgba(16, 54, 24, 0.96) 0%, rgba(19, 62, 28, 0.88) 42%, rgba(19, 62, 28, 0.18) 72%, rgba(19, 62, 28, 0) 100%);
  pointer-events: none;
}

.fl-organic-copy {
  position: relative;
  z-index: 2;
  max-width: 580px;
}

.fl-organic-title {
  font-size: 46px;
  font-weight: 800;
  color: #FFFFFF;
  line-height: 1.08;
  margin: 0 0 10px 0;
  letter-spacing: -0.025em;
}

.fl-organic-sub {
  font-size: 17px;
  color: rgba(255, 255, 255, 0.88);
  margin: 0 0 24px 0;
}

.fl-organic-btn {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  background: #469B25;
  color: #FFFFFF;
  font-size: 14.5px;
  font-weight: 700;
  padding: 12px 28px;
  border-radius: 9999px;
  text-decoration: none;
  box-shadow: 0 6px 16px rgba(70, 155, 37, 0.35);
  transition: background-color 0.2s ease, transform 0.2s ease;
}

.fl-organic-btn:hover {
  background: #3A841E;
  transform: translateY(-1px);
}

@media (max-width: 768px) {
  .fl-mega-sale-card,
  .fl-organic-banner-card {
    height: auto;
    min-height: 210px;
    padding: 24px 20px;
    background-position: right center;
  }
  .fl-mega-overlay,
  .fl-organic-overlay {
    background: linear-gradient(95deg, rgba(16, 54, 24, 0.94) 0%, rgba(16, 54, 24, 0.78) 58%, rgba(16, 54, 24, 0.22) 100%);
  }
  .fl-mega-title,
  .fl-organic-title {
    font-size: 25px;
  }
  .fl-mega-sub,
  .fl-organic-sub {
    font-size: 13.5px;
    margin-bottom: 14px;
  }
  .fl-mega-meta {
    gap: 10px;
    margin-bottom: 16px;
  }
  .fl-mega-off-pill {
    font-size: 13px;
    padding: 5px 12px;
  }
  .fl-mega-off-pill strong {
    font-size: 14.5px;
  }
  .fl-mega-date {
    font-size: 13px;
  }
  .fl-mega-btn,
  .fl-organic-btn {
    padding: 10px 20px;
    font-size: 13px;
  }
}

@media (max-width: 900px) {
  .fl-mega-sale-card {
    flex-direction: column;
    align-items: flex-start;
    padding: 24px 20px;
  }
  .fl-mega-right {
    width: 100%;
    justify-content: center;
  }
  .fl-mega-title {
    font-size: 28px;
  }
}

/* Meet Our Popular Farmers Section */
.fl-farmers-section {
  padding: 52px 0;
  background: #FFFFFF;
  font-family: 'Plus Jakarta Sans', var(--font-main), sans-serif;
}

.fl-farmers-header {
  text-align: center;
  margin-bottom: 34px;
}

.fl-farmers-title {
  font-size: 34px;
  font-weight: 800;
  color: #124E26;
  margin: 0 0 12px 0;
  letter-spacing: -0.025em;
}

.fl-farmers-sub {
  font-size: 16px;
  line-height: 1.6;
  color: #4A5548;
  max-width: 650px;
  margin: 0 auto;
  font-weight: 400;
}

.fl-farmers-grid {
  display: grid;
  grid-template-columns: repeat(5, minmax(0, 1fr));
  gap: 18px;
}

.fl-farmer-card {
  position: relative;
  border-radius: 16px;
  overflow: hidden;
  height: 345px;
  background: #1A241E;
  box-shadow: 0 8px 22px rgba(20, 83, 45, 0.12);
  transition: transform 0.25s ease, box-shadow 0.25s ease;
}

.fl-farmer-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 14px 30px rgba(20, 83, 45, 0.2);
}

.fl-farmer-bg {
  width: 100%;
  height: 100%;
  object-fit: cover;
  object-position: top center;
  display: block;
  transition: transform 0.35s ease;
}

.fl-farmer-card:hover .fl-farmer-bg {
  transform: scale(1.04);
}

.fl-farmer-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(180deg, rgba(0, 0, 0, 0) 62%, rgba(0, 0, 0, 0.72) 100%);
  display: flex;
  flex-direction: column;
  justify-content: flex-end;
  align-items: center;
  text-align: center;
  padding: 18px 14px;
}

.fl-farmer-name {
  font-size: 16.5px;
  font-weight: 800;
  color: #FFFFFF;
  margin: 0 0 4px 0;
}

.fl-farmer-loc {
  font-size: 12.5px;
  color: #DCF0CF;
  font-weight: 500;
  margin: 0;
  display: inline-flex;
  align-items: center;
  gap: 5px;
}

.fl-farmer-loc i {
  color: #4ADE80;
  font-size: 11.5px;
}

/* Fresh Feed from Local Mandi Section */
.fl-mandi-section {
  padding: 52px 0;
  background: #F6FAF2;
  font-family: 'Plus Jakarta Sans', var(--font-main), sans-serif;
}

.fl-mandi-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  flex-wrap: wrap;
  margin-bottom: 26px;
}

.fl-mandi-title-group {
  display: flex;
  align-items: flex-start;
  gap: 14px;
}

.fl-mandi-icon-box {
  width: 38px;
  height: 28px;
  border: 2px solid #7AC142;
  border-radius: 8px;
  color: #7AC142;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 12px;
  margin-top: 6px;
  flex-shrink: 0;
}

.fl-mandi-title {
  font-size: 32px;
  font-weight: 800;
  color: #124E26;
  margin: 0 0 6px 0;
  letter-spacing: -0.025em;
  line-height: 1.15;
}

.fl-mandi-sub {
  font-size: 15px;
  color: #4A5548;
  margin: 0;
}

.fl-mandi-watch-btn {
  display: inline-flex;
  align-items: center;
  gap: 9px;
  background: #FFFFFF;
  border: 1.5px solid #14532D;
  border-radius: 9999px;
  padding: 9px 20px;
  font-size: 13.5px;
  font-weight: 700;
  color: #124E26;
  text-decoration: none;
  white-space: nowrap;
  transition: background-color 0.2s ease, color 0.2s ease;
}

.fl-mandi-watch-btn:hover {
  background-color: #14532D;
  color: #FFFFFF;
}

.fl-mandi-grid {
  display: grid;
  grid-template-columns: repeat(5, minmax(0, 1fr));
  gap: 16px;
}

.fl-mandi-card {
  display: block;
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 6px 18px rgba(20, 83, 45, 0.1);
  transition: transform 0.22s ease, box-shadow 0.22s ease;
  background: #14532D;
}

.fl-mandi-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 26px rgba(20, 83, 45, 0.16);
}

.fl-mandi-card img {
  width: 100%;
  height: 100%;
  aspect-ratio: 9 / 13.5;
  object-fit: cover;
  display: block;
}

/* Customer Reviews Section */
.fl-reviews-section {
  padding: 56px 0 64px;
  background: #FFFFFF;
  font-family: 'Plus Jakarta Sans', var(--font-main), sans-serif;
}

.fl-reviews-top {
  text-align: center;
  margin-bottom: 40px;
}

.fl-reviews-heading {
  font-size: 38px;
  font-weight: 800;
  color: #0F2E1A;
  line-height: 1.15;
  margin: 0 0 16px 0;
  letter-spacing: -0.025em;
}

.fl-rev-accent {
  color: #3F9C1E;
  position: relative;
  display: inline-block;
}

.fl-rev-accent::after {
  content: '';
  position: absolute;
  left: 0;
  right: 0;
  bottom: -4px;
  height: 4px;
  background-color: #65B73B;
  border-radius: 9999px;
}

.fl-reviews-trust {
  display: inline-flex;
  align-items: center;
  gap: 14px;
  font-size: 13.5px;
  font-weight: 600;
  color: #374151;
  flex-wrap: wrap;
  justify-content: center;
}

.fl-rev-sep {
  color: #D1D5DB;
}

.fl-tp-badge {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  font-weight: 700;
  color: #111827;
}

.fl-tp-badge i {
  color: #3F9C1E;
}

.fl-reviews-grid {
  display: grid;
  grid-template-columns: 0.72fr 1fr 1fr 1fr;
  gap: 20px;
  align-items: center;
}

.fl-rev-big-quote {
  font-size: 42px;
  color: #CFE4C2;
  line-height: 1;
  margin-bottom: 10px;
}

.fl-rev-left-title {
  font-size: 24px;
  font-weight: 800;
  color: #0F2E1A;
  line-height: 1.2;
  margin: 0;
}

.fl-rev-left-title span {
  color: #3F9C1E;
}

.fl-rev-line {
  display: block;
  width: 68px;
  height: 2px;
  background-color: #A7D493;
  margin: 16px 0 18px;
}

.fl-rev-arrows {
  display: flex;
  align-items: center;
  gap: 10px;
}

.fl-rev-arrow {
  width: 38px;
  height: 38px;
  border-radius: 50%;
  border: 1px solid #D5E4CE;
  background: #FFFFFF;
  color: #14532D;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  font-size: 13px;
  transition: transform 0.15s ease;
}

.fl-rev-arrow.next {
  background: #14532D;
  border-color: #14532D;
  color: #FFFFFF;
}

.fl-rev-arrow:hover {
  transform: scale(1.06);
}

.fl-rev-card {
  background: #FFFFFF;
  border: 1px solid #EDF2EA;
  border-radius: 16px;
  padding: 24px 22px;
  box-shadow: 0 6px 22px rgba(20, 83, 45, 0.06);
  display: flex;
  flex-direction: column;
  min-height: 250px;
}

.fl-rev-card.is-mint {
  background: #EBF5E4;
  border-color: #DCECD3;
}

.fl-rev-card-quote {
  font-size: 24px;
  color: #B9D8A8;
  line-height: 1;
  margin-bottom: 12px;
}

.fl-rev-text {
  font-size: 13.5px;
  line-height: 1.6;
  color: #1F2937;
  margin: 0 0 20px 0;
  flex: 1;
}

.fl-rev-stars {
  display: flex;
  gap: 4px;
  color: #3F9C1E;
  font-size: 14px;
  margin-bottom: 14px;
}

.fl-rev-author {
  display: flex;
  align-items: center;
  gap: 12px;
}

.fl-rev-author img {
  width: 42px;
  height: 42px;
  border-radius: 50%;
  object-fit: cover;
  flex-shrink: 0;
}

.fl-rev-author strong {
  display: block;
  font-size: 14px;
  font-weight: 800;
  color: #111827;
}

.fl-rev-author span {
  display: block;
  font-size: 12px;
  color: #6B7280;
}

/* FAQ Section */
.fl-faq-section {
  padding: 60px 0 64px;
  background: #F8FBF6;
  font-family: 'Plus Jakarta Sans', var(--font-main), sans-serif;
}

.fl-faq-grid {
  display: grid;
  grid-template-columns: 0.78fr 1.22fr;
  gap: 48px;
  align-items: start;
}

.fl-faq-pill {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: #E4F2DA;
  color: #14532D;
  font-size: 12.5px;
  font-weight: 700;
  padding: 5px 14px;
  border-radius: 9999px;
  margin-bottom: 14px;
}

.fl-faq-pill i {
  color: #3F9C1E;
}

.fl-faq-title {
  font-size: 40px;
  font-weight: 800;
  color: #0F2E1A;
  line-height: 1.08;
  margin: 0 0 14px 0;
  letter-spacing: -0.025em;
}

.fl-faq-title span {
  color: #14532D;
}

.fl-faq-sub {
  font-size: 14.5px;
  line-height: 1.55;
  color: #4A5548;
  max-width: 290px;
  margin: 0 0 24px 0;
}

.fl-faq-art {
  max-width: 290px;
}

.fl-faq-art img {
  width: 100%;
  height: auto;
  object-fit: contain;
  display: block;
}

.fl-faq-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.fl-faq-item {
  background: #FFFFFF;
  border: 1px solid #E5ECE1;
  border-radius: 12px;
  transition: background-color 0.2s ease, border-color 0.2s ease;
}

.fl-faq-item.open {
  background: #E9F5E0;
  border-color: #C8E2B8;
}

.fl-faq-q {
  width: 100%;
  background: transparent;
  border: none;
  padding: 17px 22px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  text-align: left;
  font-family: inherit;
  font-size: 15.5px;
  font-weight: 700;
  color: #0F2E1A;
  cursor: pointer;
}

.fl-faq-q i {
  font-size: 12px;
  color: #14532D;
  flex-shrink: 0;
}

.fl-faq-a {
  padding: 0 22px 18px;
  font-size: 14px;
  line-height: 1.6;
  color: #374151;
}

@media (max-width: 960px) {
  .fl-faq-grid {
    grid-template-columns: 1fr;
    gap: 28px;
  }
  .fl-faq-art {
    display: none;
  }
  .fl-faq-title {
    font-size: 30px;
  }
}

@media (max-width: 1024px) {
  .fl-reviews-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
  .fl-rev-left {
    grid-column: 1 / -1;
  }
}

@media (max-width: 600px) {
  .fl-reviews-grid {
    grid-template-columns: 1fr;
  }
  .fl-reviews-heading {
    font-size: 28px;
  }
}

/* FAQ Section */
.fl-faq-section {
  padding: 60px 0 64px;
  background: #F8FBF6;
  font-family: 'Plus Jakarta Sans', var(--font-main), sans-serif;
}

.fl-faq-grid {
  display: grid;
  grid-template-columns: 0.78fr 1.22fr;
  gap: 48px;
  align-items: start;
}

.fl-faq-pill {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: #E4F2DA;
  color: #14532D;
  font-size: 12.5px;
  font-weight: 700;
  padding: 5px 14px;
  border-radius: 9999px;
  margin-bottom: 14px;
}

.fl-faq-pill i {
  color: #3F9C1E;
}

.fl-faq-title {
  font-size: 40px;
  font-weight: 800;
  color: #0F2E1A;
  line-height: 1.08;
  margin: 0 0 14px 0;
  letter-spacing: -0.025em;
}

.fl-faq-title span {
  color: #14532D;
}

.fl-faq-sub {
  font-size: 14.5px;
  line-height: 1.55;
  color: #4A5548;
  max-width: 290px;
  margin: 0 0 24px 0;
}

.fl-faq-art {
  max-width: 290px;
}

.fl-faq-art img {
  width: 100%;
  height: auto;
  object-fit: contain;
  display: block;
}

.fl-faq-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.fl-faq-item {
  background: #FFFFFF;
  border: 1px solid #E5ECE1;
  border-radius: 12px;
  transition: background-color 0.2s ease, border-color 0.2s ease;
}

.fl-faq-item.open {
  background: #E9F5E0;
  border-color: #C8E2B8;
}

.fl-faq-q {
  width: 100%;
  background: transparent;
  border: none;
  padding: 17px 22px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  text-align: left;
  font-family: inherit;
  font-size: 15.5px;
  font-weight: 700;
  color: #0F2E1A;
  cursor: pointer;
}

.fl-faq-q i {
  font-size: 12px;
  color: #14532D;
  flex-shrink: 0;
}

.fl-faq-a {
  padding: 0 22px 18px;
  font-size: 14px;
  line-height: 1.6;
  color: #374151;
}

@media (max-width: 960px) {
  .fl-faq-grid {
    grid-template-columns: 1fr;
    gap: 28px;
  }
  .fl-faq-art {
    display: none;
  }
  .fl-faq-title {
    font-size: 30px;
  }
}

@media (max-width: 1024px) {
  .fl-mandi-grid {
    grid-template-columns: repeat(3, minmax(0, 1fr));
  }
}

@media (max-width: 640px) {
  .fl-mandi-grid {
    display: flex;
    overflow-x: auto;
    gap: 10px;
    padding-bottom: 4px;
    scrollbar-width: none;
    -webkit-overflow-scrolling: touch;
  }
  .fl-mandi-grid::-webkit-scrollbar {
    display: none;
  }
  .fl-mandi-grid > * {
    flex: 0 0 40%;
    min-width: 142px;
  }
  .fl-mandi-card img {
    aspect-ratio: 9 / 16;
  }
  .fl-mandi-title {
    font-size: 22px;
  }
}

/* Customer Reviews Section */
.fl-reviews-section {
  padding: 56px 0 64px;
  background: #FFFFFF;
  font-family: 'Plus Jakarta Sans', var(--font-main), sans-serif;
}

.fl-reviews-top {
  text-align: center;
  margin-bottom: 40px;
}

.fl-reviews-heading {
  font-size: 38px;
  font-weight: 800;
  color: #0F2E1A;
  line-height: 1.15;
  margin: 0 0 16px 0;
  letter-spacing: -0.025em;
}

.fl-rev-accent {
  color: #3F9C1E;
  position: relative;
  display: inline-block;
}

.fl-rev-accent::after {
  content: '';
  position: absolute;
  left: 0;
  right: 0;
  bottom: -4px;
  height: 4px;
  background-color: #65B73B;
  border-radius: 9999px;
}

.fl-reviews-trust {
  display: inline-flex;
  align-items: center;
  gap: 14px;
  font-size: 13.5px;
  font-weight: 600;
  color: #374151;
  flex-wrap: wrap;
  justify-content: center;
}

.fl-rev-sep {
  color: #D1D5DB;
}

.fl-tp-badge {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  font-weight: 700;
  color: #111827;
}

.fl-tp-badge i {
  color: #3F9C1E;
}

.fl-reviews-grid {
  display: grid;
  grid-template-columns: 0.72fr 1fr 1fr 1fr;
  gap: 20px;
  align-items: center;
}

.fl-rev-big-quote {
  font-size: 42px;
  color: #CFE4C2;
  line-height: 1;
  margin-bottom: 10px;
}

.fl-rev-left-title {
  font-size: 24px;
  font-weight: 800;
  color: #0F2E1A;
  line-height: 1.2;
  margin: 0;
}

.fl-rev-left-title span {
  color: #3F9C1E;
}

.fl-rev-line {
  display: block;
  width: 68px;
  height: 2px;
  background-color: #A7D493;
  margin: 16px 0 18px;
}

.fl-rev-arrows {
  display: flex;
  align-items: center;
  gap: 10px;
}

.fl-rev-arrow {
  width: 38px;
  height: 38px;
  border-radius: 50%;
  border: 1px solid #D5E4CE;
  background: #FFFFFF;
  color: #14532D;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  font-size: 13px;
  transition: transform 0.15s ease;
}

.fl-rev-arrow.next {
  background: #14532D;
  border-color: #14532D;
  color: #FFFFFF;
}

.fl-rev-arrow:hover {
  transform: scale(1.06);
}

.fl-rev-card {
  background: #FFFFFF;
  border: 1px solid #EDF2EA;
  border-radius: 16px;
  padding: 24px 22px;
  box-shadow: 0 6px 22px rgba(20, 83, 45, 0.06);
  display: flex;
  flex-direction: column;
  min-height: 250px;
}

.fl-rev-card.is-mint {
  background: #EBF5E4;
  border-color: #DCECD3;
}

.fl-rev-card-quote {
  font-size: 24px;
  color: #B9D8A8;
  line-height: 1;
  margin-bottom: 12px;
}

.fl-rev-text {
  font-size: 13.5px;
  line-height: 1.6;
  color: #1F2937;
  margin: 0 0 20px 0;
  flex: 1;
}

.fl-rev-stars {
  display: flex;
  gap: 4px;
  color: #3F9C1E;
  font-size: 14px;
  margin-bottom: 14px;
}

.fl-rev-author {
  display: flex;
  align-items: center;
  gap: 12px;
}

.fl-rev-author img {
  width: 42px;
  height: 42px;
  border-radius: 50%;
  object-fit: cover;
  flex-shrink: 0;
}

.fl-rev-author strong {
  display: block;
  font-size: 14px;
  font-weight: 800;
  color: #111827;
}

.fl-rev-author span {
  display: block;
  font-size: 12px;
  color: #6B7280;
}

/* FAQ Section */
.fl-faq-section {
  padding: 60px 0 64px;
  background: #F8FBF6;
  font-family: 'Plus Jakarta Sans', var(--font-main), sans-serif;
}

.fl-faq-grid {
  display: grid;
  grid-template-columns: 0.78fr 1.22fr;
  gap: 48px;
  align-items: start;
}

.fl-faq-pill {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: #E4F2DA;
  color: #14532D;
  font-size: 12.5px;
  font-weight: 700;
  padding: 5px 14px;
  border-radius: 9999px;
  margin-bottom: 14px;
}

.fl-faq-pill i {
  color: #3F9C1E;
}

.fl-faq-title {
  font-size: 40px;
  font-weight: 800;
  color: #0F2E1A;
  line-height: 1.08;
  margin: 0 0 14px 0;
  letter-spacing: -0.025em;
}

.fl-faq-title span {
  color: #14532D;
}

.fl-faq-sub {
  font-size: 14.5px;
  line-height: 1.55;
  color: #4A5548;
  max-width: 290px;
  margin: 0 0 24px 0;
}

.fl-faq-art {
  max-width: 290px;
}

.fl-faq-art img {
  width: 100%;
  height: auto;
  object-fit: contain;
  display: block;
}

.fl-faq-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.fl-faq-item {
  background: #FFFFFF;
  border: 1px solid #E5ECE1;
  border-radius: 12px;
  transition: background-color 0.2s ease, border-color 0.2s ease;
}

.fl-faq-item.open {
  background: #E9F5E0;
  border-color: #C8E2B8;
}

.fl-faq-q {
  width: 100%;
  background: transparent;
  border: none;
  padding: 17px 22px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  text-align: left;
  font-family: inherit;
  font-size: 15.5px;
  font-weight: 700;
  color: #0F2E1A;
  cursor: pointer;
}

.fl-faq-q i {
  font-size: 12px;
  color: #14532D;
  flex-shrink: 0;
}

.fl-faq-a {
  padding: 0 22px 18px;
  font-size: 14px;
  line-height: 1.6;
  color: #374151;
}

@media (max-width: 960px) {
  .fl-faq-grid {
    grid-template-columns: 1fr;
    gap: 28px;
  }
  .fl-faq-art {
    display: none;
  }
  .fl-faq-title {
    font-size: 30px;
  }
}

@media (max-width: 1024px) {
  .fl-reviews-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
  .fl-rev-left {
    grid-column: 1 / -1;
  }
}

@media (max-width: 600px) {
  .fl-reviews-grid {
    grid-template-columns: 1fr;
  }
  .fl-reviews-heading {
    font-size: 28px;
  }
}

/* FAQ Section */
.fl-faq-section {
  padding: 60px 0 64px;
  background: #F8FBF6;
  font-family: 'Plus Jakarta Sans', var(--font-main), sans-serif;
}

.fl-faq-grid {
  display: grid;
  grid-template-columns: 0.78fr 1.22fr;
  gap: 48px;
  align-items: start;
}

.fl-faq-pill {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: #E4F2DA;
  color: #14532D;
  font-size: 12.5px;
  font-weight: 700;
  padding: 5px 14px;
  border-radius: 9999px;
  margin-bottom: 14px;
}

.fl-faq-pill i {
  color: #3F9C1E;
}

.fl-faq-title {
  font-size: 40px;
  font-weight: 800;
  color: #0F2E1A;
  line-height: 1.08;
  margin: 0 0 14px 0;
  letter-spacing: -0.025em;
}

.fl-faq-title span {
  color: #14532D;
}

.fl-faq-sub {
  font-size: 14.5px;
  line-height: 1.55;
  color: #4A5548;
  max-width: 290px;
  margin: 0 0 24px 0;
}

.fl-faq-art {
  max-width: 290px;
}

.fl-faq-art img {
  width: 100%;
  height: auto;
  object-fit: contain;
  display: block;
}

.fl-faq-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.fl-faq-item {
  background: #FFFFFF;
  border: 1px solid #E5ECE1;
  border-radius: 12px;
  transition: background-color 0.2s ease, border-color 0.2s ease;
}

.fl-faq-item.open {
  background: #E9F5E0;
  border-color: #C8E2B8;
}

.fl-faq-q {
  width: 100%;
  background: transparent;
  border: none;
  padding: 17px 22px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  text-align: left;
  font-family: inherit;
  font-size: 15.5px;
  font-weight: 700;
  color: #0F2E1A;
  cursor: pointer;
}

.fl-faq-q i {
  font-size: 12px;
  color: #14532D;
  flex-shrink: 0;
}

.fl-faq-a {
  padding: 0 22px 18px;
  font-size: 14px;
  line-height: 1.6;
  color: #374151;
}

@media (max-width: 960px) {
  .fl-faq-grid {
    grid-template-columns: 1fr;
    gap: 28px;
  }
  .fl-faq-art {
    display: none;
  }
  .fl-faq-title {
    font-size: 30px;
  }
}

@media (max-width: 1024px) {
  .fl-farmers-grid {
    grid-template-columns: repeat(3, minmax(0, 1fr));
  }
}

@media (max-width: 640px) {
  .fl-farmers-section {
    padding: 28px 0;
  }
  .fl-farmers-grid {
    display: flex;
    overflow-x: auto;
    gap: 10px;
    padding-bottom: 4px;
    scrollbar-width: none;
    -webkit-overflow-scrolling: touch;
  }
  .fl-farmers-grid::-webkit-scrollbar {
    display: none;
  }
  .fl-farmers-grid > * {
    flex: 0 0 38%;
    min-width: 136px;
    height: 165px;
  }
  .fl-farmers-title {
    font-size: 18px;
  }
  .fl-farmers-sub {
    font-size: 12.5px;
  }
}

@media (max-width: 1140px) {
  .fl-showcase-grid {
    grid-template-columns: repeat(3, minmax(0, 1fr));
  }
}

/* Organic & Pesticide-Free Banner */
.fl-organic-banner-section {
  padding: 24px 0 36px;
  background: #FFFFFF;
  font-family: 'Plus Jakarta Sans', var(--font-main), sans-serif;
}

.fl-organic-banner-card {
  position: relative;
  border-radius: 20px;
  height: 275px;
  min-height: 275px;
  padding: 36px 48px;
  display: flex;
  align-items: center;
  overflow: hidden;
  background-color: #133B1D;
  background-image: url('/assets/organic-banner-bg.jpg');
  background-size: cover;
  background-position: right center;
  box-shadow: 0 12px 30px rgba(20, 83, 45, 0.14);
}

.fl-organic-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(95deg, rgba(16, 54, 24, 0.96) 0%, rgba(19, 62, 28, 0.88) 42%, rgba(19, 62, 28, 0.18) 72%, rgba(19, 62, 28, 0) 100%);
  pointer-events: none;
}

.fl-organic-copy {
  position: relative;
  z-index: 2;
  max-width: 580px;
}

.fl-organic-title {
  font-size: 46px;
  font-weight: 800;
  color: #FFFFFF;
  line-height: 1.08;
  margin: 0 0 10px 0;
  letter-spacing: -0.025em;
}

.fl-organic-sub {
  font-size: 17px;
  color: rgba(255, 255, 255, 0.88);
  margin: 0 0 24px 0;
}

.fl-organic-btn {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  background: #469B25;
  color: #FFFFFF;
  font-size: 14.5px;
  font-weight: 700;
  padding: 12px 28px;
  border-radius: 9999px;
  text-decoration: none;
  box-shadow: 0 6px 16px rgba(70, 155, 37, 0.35);
  transition: background-color 0.2s ease, transform 0.2s ease;
}

.fl-organic-btn:hover {
  background: #3A841E;
  transform: translateY(-1px);
}

@media (max-width: 768px) {
  .fl-mega-sale-card,
  .fl-organic-banner-card {
    height: auto;
    min-height: 240px;
    padding: 28px 24px;
    background-position: center;
  }
  .fl-organic-overlay {
    background: linear-gradient(180deg, rgba(16, 54, 24, 0.92) 0%, rgba(16, 54, 24, 0.72) 100%);
  }
  .fl-organic-title {
    font-size: 26px;
  }
}

@media (max-width: 900px) {
  .fl-offers-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}

@media (max-width: 640px) {
  .fl-offers-section {
    padding: 10px 0 14px;
  }
  .fl-offers-grid {
    display: flex;
    overflow-x: auto;
    gap: 10px;
    padding-bottom: 4px;
    scrollbar-width: none;
    -webkit-overflow-scrolling: touch;
  }
  .fl-offers-grid::-webkit-scrollbar {
    display: none;
  }
  .fl-offer-card {
    flex: 0 0 88%;
    min-width: 295px;
    min-height: 132px;
    padding: 14px 12px 14px 16px;
    gap: 8px;
  }
  .fl-offer-badge {
    white-space: nowrap;
    font-size: 10.5px;
    padding: 4px 9px;
  }
  .fl-offer-title {
    font-size: 16px;
  }
  .fl-offer-sub {
    font-size: 12px;
  }
  .fl-offer-btn {
    white-space: nowrap;
    padding: 7px 14px;
    font-size: 12px;
  }
  .fl-offer-art {
    width: 96px;
    height: 96px;
    flex-shrink: 0;
  }
  .fl-showcase-section {
    padding: 22px 0;
  }
  .fl-showcase-header {
    margin-bottom: 14px;
  }
  .fl-showcase-sub {
    font-size: 12.5px;
  }
  .fl-showcase-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 10px;
  }
  .fl-showcase-card {
    padding: 8px;
    border-radius: 12px;
  }
  .fl-card-img-box {
    height: 118px;
    border-radius: 9px;
  }
  .fl-card-info {
    padding: 8px 2px 0;
  }
  .fl-card-title {
    font-size: 13px;
    line-height: 1.25;
  }
  .fl-card-unit {
    font-size: 11px;
    margin-bottom: 5px;
  }
  .fl-card-rating {
    font-size: 11px;
    margin-bottom: 6px;
  }
  .fl-card-price-row {
    gap: 6px;
    margin-bottom: 9px;
  }
  .fl-card-price {
    font-size: 15px;
  }
  .fl-card-old {
    font-size: 11.5px;
  }
  .fl-card-off {
    font-size: 9.5px;
    padding: 2px 6px;
  }
  .fl-card-body {
    padding: 0 2px 2px;
  }
  .fl-card-cart-btn {
    height: 34px;
    font-size: 12.5px;
    border-radius: 8px;
    gap: 6px;
  }
  .fl-showcase-title {
    font-size: 20px;
  }
  .fl-showcase-view-all {
    font-size: 12.5px;
  }
}
</style>
