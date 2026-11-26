<template>
  <!-- Nothing left inside its window means no section at all, rather than a
       heading over an empty grid. -->
  <section v-if="livePromotions.length" ref="sectionRef" class="section-padding" id="promotions">
    <div class="container">
      <div class="section-header stagger">
        <h2 class="section-title reveal">Promotions</h2>
        <Link href="/promotions" class="view-all-link reveal">View All Offers <i class="fa-solid fa-arrow-right"></i></Link>
      </div>

      <div class="promotions-grid">
        <!-- The positional promo-card-N class carries each card's own styling,
             so it is derived from the index rather than stored per record. -->
        <div
          v-for="(promo, i) in livePromotions"
          :key="promo.id"
          class="promo-card"
          :class="[`promo-card-${i + 1}`, cardRevealClass(i)]"
        >
          <div class="promo-content">
            <span class="promo-badge" :style="{ color: promo.badgeColor }">{{ promo.badge }}</span>
            <h3 class="promo-title">{{ promo.title }}</h3>
            <p class="promo-desc">{{ promo.description }}</p>
            <!-- A link set in the admin wins; without one the button still
                 opens the enquiry form. -->
            <Link v-if="promo.ctaUrl" :href="promo.ctaUrl" class="btn-shop-sm">
              {{ promo.ctaText }} <i class="fa-solid fa-arrow-right"></i>
            </Link>
            <button v-else @click="$emit('openModal')" class="btn-shop-sm">
              {{ promo.ctaText }} <i class="fa-solid fa-arrow-right"></i>
            </button>
          </div>
          <img :src="promo.image" :alt="promo.title" class="promo-graphic">
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { nextTick, ref, watch } from 'vue';
import { Link } from '@inertiajs/vue3';
import { activateReveals, useLiveItems } from '@/composables/useLiveSchedule';

const sectionRef = ref(null);

const props = defineProps({
  promotions: {
    type: Array,
    default: () => []
  }
});

defineEmits(['openModal']);

// The controller already filtered these, but an offer can expire while the tab
// is open — the ticker drops the card at its expiry instead of at next reload.
const livePromotions = useLiveItems(() => props.promotions);

// A promotion can also *start* mid-visit. The page's scroll observer is long
// past those nodes by then, so reveal them here instead of leaving them at
// opacity 0.
//
// Watched as an id string rather than the array: the ticker hands back a fresh
// array every 15s, and firing on that would force every card visible before
// the reader has scrolled to it — killing the entrance animation. Only an
// actual new id counts.
watch(
  () => livePromotions.value.map(promo => promo.id).join(','),
  (ids, previous) => {
    const before = new Set((previous ?? '').split(','));
    if (ids.split(',').some(id => id && !before.has(id))) {
      nextTick(() => activateReveals(sectionRef.value));
    }
  }
);

// First card slides in from the left, last from the right, the middle ones fade
// up — the reveal directions the section was originally built with.
function cardRevealClass(index) {
  if (index === 0) return 'reveal-left';
  if (index === livePromotions.value.length - 1) return 'reveal-right';
  return 'reveal';
}
</script>
