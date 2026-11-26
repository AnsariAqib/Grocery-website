<template>
  <section v-if="testimonials.length" class="section-padding section-flush-top" style="position:relative; overflow:hidden;">
    <div class="watermark-backdrop" data-parallax="30">REVIEWS</div>
    <div class="container" style="position:relative; z-index:2;">
      <div class="section-header center stagger">
        <span class="section-badge reveal"><i class="fa-solid fa-quote-left"></i> Client Reviews</span>
        <h2 class="section-title reveal">What Our Clients & Partners Say</h2>
        <p class="section-subtitle reveal">
          Trusted by 1,000+ corporate clients, supermarket shoppers, and institutional procurement managers.
        </p>
      </div>

      <div class="testimonials-grid stagger" ref="sliderRef" @mouseenter="stopAutoScroll" @mouseleave="startAutoScroll" @touchstart="stopAutoScroll" @touchend="startAutoScroll">
        <div v-for="item in testimonials" :key="item.id" class="testimonial-card reveal">
          <div class="quote-bg-icon"><i class="fa-solid fa-quote-right"></i></div>
          <div class="star-rating">
            <i v-for="n in item.rating" :key="n" class="fa-solid fa-star"></i>
          </div>
          <p class="testimonial-quote">
            "{{ item.quote }}"
          </p>
          <div class="testimonial-author-row">
            <div class="author-avatar"><i class="fa-solid" :class="item.icon"></i></div>
            <div class="author-info">
              <h4 class="author-name">{{ item.name }}</h4>
              <span v-if="item.role" class="author-title">{{ item.role }}</span>
            </div>
            <span v-if="item.tag" class="verified-pill"><i class="fa-solid fa-circle-check"></i> {{ item.tag }}</span>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';

defineProps({
  // Written in the admin under Home Page -> Client Reviews.
  testimonials: { type: Array, default: () => [] },
});

const sliderRef = ref(null);
let intervalId = null;

onMounted(() => {
  // Only auto-scroll on mobile view
  if (window.innerWidth <= 768) {
    startAutoScroll();
  }
});

onUnmounted(() => {
  stopAutoScroll();
});

const startAutoScroll = () => {
  intervalId = setInterval(() => {
    if (sliderRef.value) {
      const slider = sliderRef.value;
      const scrollWidth = slider.scrollWidth;
      const clientWidth = slider.clientWidth;
      const maxScroll = scrollWidth - clientWidth;
      
      // Calculate next scroll position (move by one card width roughly)
      let nextScroll = slider.scrollLeft + clientWidth;
      
      // If we've reached the end, loop back to the start
      if (nextScroll >= maxScroll + 10) {
        nextScroll = 0;
      }
      
      slider.scrollTo({
        left: nextScroll,
        behavior: 'smooth'
      });
    }
  }, 3000); // 3 seconds per slide
};

const stopAutoScroll = () => {
  if (intervalId) clearInterval(intervalId);
};
</script>
