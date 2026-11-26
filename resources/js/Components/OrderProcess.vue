<template>
  <section
    v-if="steps.length"
    ref="sectionRef"
    class="order-process"
    :style="manualScroll ? null : { height: `${scrollLength}px` }"
  >
    <div class="order-sticky">
      <div class="container">
        <div class="section-header center order-head">
          <span class="section-badge"><i class="fa-solid fa-route"></i> How it works</span>
          <h2 class="section-title">B2B Order Process</h2>
          <p class="section-subtitle">From your first enquiry to a delivered, invoiced order — six simple steps.</p>
        </div>
      </div>

      <!-- The rail slides sideways as the section scrolls past. On touch and
           reduced-motion it becomes an ordinary swipeable row instead. -->
      <div class="order-viewport" :class="{ 'is-manual': manualScroll }" :data-lenis-prevent="manualScroll || undefined">
        <div
          class="order-rail"
          :style="manualScroll ? null : { transform: `translate3d(${-railOffset}px, 0, 0)` }"
        >
          <article
            v-for="(step, i) in steps"
            :key="step.id"
            class="order-step"
            :class="{ 'is-live': !manualScroll && i <= liveIndex }"
          >
            <span class="order-step-no">{{ String(i + 1).padStart(2, '0') }}</span>
            <span class="order-step-icon"><i class="fa-solid" :class="step.icon || 'fa-list-check'"></i></span>
            <h3>{{ step.title }}</h3>
            <p v-if="step.description">{{ step.description }}</p>
            <span v-if="i < steps.length - 1" class="order-step-link"></span>
          </article>
        </div>
      </div>

      <div class="container">
        <div class="order-progress">
          <span class="order-progress-fill" :style="{ width: `${Math.round(progress * 100)}%` }"></span>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { getLenis } from '@/composables/useSmoothScroll';

const props = defineProps({
  steps: { type: Array, default: () => [] },
});

const sectionRef = ref(null);
const progress = ref(0);
const travel = ref(0);          // px the rail can slide
const manualScroll = ref(false); // touch / reduced motion fallback
const viewportH = ref(typeof window === 'undefined' ? 900 : window.innerHeight);
let ticking = false;
let lenisUnsubscribe = null;

// How much page scroll the pinned section consumes: one viewport to hold the
// panel still, plus enough extra to cover the rail's actual sideways travel at
// a readable pace. Deriving it from `travel` (rather than a fixed per-step
// figure) keeps the section exactly as tall as the animation needs — any
// surplus would show up as empty space once the rail has finished sliding.
const scrollLength = computed(() =>
  Math.round(viewportH.value + Math.max(travel.value * 1.4, 280))
);

const railOffset = computed(() => progress.value * travel.value);
const liveIndex = computed(() => Math.round(progress.value * (props.steps.length - 1)));

function measure() {
  ticking = false;

  const section = sectionRef.value;
  if (!section || manualScroll.value) return;

  const rail = section.querySelector('.order-rail');
  const viewport = section.querySelector('.order-viewport');
  if (rail && viewport) {
    // clientWidth includes the viewport's (large) side padding, and the rail is
    // laid out inside that padding — so measure against the content box or the
    // last card never fully clears the right edge.
    const cs = getComputedStyle(viewport);
    const inner = viewport.clientWidth
      - parseFloat(cs.paddingLeft || 0)
      - parseFloat(cs.paddingRight || 0);
    travel.value = Math.max(0, rail.scrollWidth - inner);
  }

  const rect = section.getBoundingClientRect();
  const distance = Math.max(1, section.offsetHeight - window.innerHeight);
  progress.value = Math.max(0, Math.min(1, -rect.top / distance));
}

function onScroll() {
  if (ticking) return;
  ticking = true;
  requestAnimationFrame(measure);
}

function evaluateMode() {
  viewportH.value = window.innerHeight;
  const coarse = window.matchMedia('(pointer: coarse)').matches;
  const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  const narrow = window.innerWidth < 900;

  manualScroll.value = coarse || reduced || narrow;
  measure();
}

function connectLenis() {
  const lenis = getLenis();
  if (lenis) {
    // Lenis fires its own 'scroll' callback on each RAF tick while animating
    lenisUnsubscribe = lenis.on('scroll', onScroll);
  }
}

onMounted(() => {
  evaluateMode();

  // Listen to native scroll as a baseline (Lenis still dispatches native scroll)
  window.addEventListener('scroll', onScroll, { passive: true });
  window.addEventListener('resize', evaluateMode, { passive: true });

  // Also hook into Lenis for more reliable frame-synced updates
  connectLenis();

  // If Lenis initializes after this component, retry once on next frame
  if (!getLenis()) {
    requestAnimationFrame(() => {
      connectLenis();
    });
  }
});

onUnmounted(() => {
  window.removeEventListener('scroll', onScroll);
  window.removeEventListener('resize', evaluateMode);
  if (lenisUnsubscribe) {
    const lenis = getLenis();
    if (lenis) {
      lenis.off('scroll', onScroll);
    }
    lenisUnsubscribe = null;
  }
});
</script>

<style scoped>
.order-process {
  position: relative;
  background: linear-gradient(180deg, #040E1B 0%, #061427 60%, #0B1E38 100%);
  color: #FFFFFF;
}

.order-sticky {
  position: sticky;
  top: 0;
  display: flex;
  flex-direction: column;
  justify-content: center;
  /* +16px on the nominal gap: .order-viewport carries a -16px margin so its
     overflow can hold the active card's lift, which eats into the flex gap. */
  gap: clamp(30px, 5vh, 46px);
  min-height: 100vh;
  /* Extra headroom on top: the sticky site header floats over the first ~72px
     of this panel, so equal padding would leave the content sitting low. */
  padding: clamp(92px, 12vh, 124px) 0 clamp(32px, 5vh, 56px);
  overflow: hidden;
}

.order-head {
  margin-bottom: 0;
}

.order-head :deep(.section-title) {
  color: #FFFFFF;
}

.order-head :deep(.section-subtitle) {
  color: rgba(255, 255, 255, 0.68);
}

.order-head :deep(.section-badge) {
  background: rgba(228, 101, 46, 0.18);
  border-color: rgba(228, 101, 46, 0.4);
  color: #F29163;
}

.order-viewport {
  overflow: hidden;
  /* Vertical padding is headroom for the active card's -6px lift, which the
     overflow: hidden would otherwise slice off. The matching negative margin
     cancels it again so the rail sits exactly where it did before. */
  padding: 16px max(32px, calc((100vw - 1480px) / 2 + 32px));
  margin: -16px 0;
}

.order-viewport.is-manual {
  overflow-x: auto;
  scroll-snap-type: x mandatory;
  -webkit-overflow-scrolling: touch;
  scrollbar-width: none;
}

.order-viewport.is-manual::-webkit-scrollbar {
  display: none;
}

.order-rail {
  display: flex;
  gap: 20px;
  will-change: transform;
  transition: transform 0.12s linear;
}

.order-viewport.is-manual .order-rail {
  transform: none !important;
  transition: none;
}

.order-step {
  position: relative;
  flex: 0 0 300px;
  display: flex;
  flex-direction: column;
  min-height: clamp(210px, 32vh, 290px);
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.12);
  border-radius: var(--radius-lg);
  padding: 28px 26px;
  scroll-snap-align: start;
  transition: background-color 0.4s var(--ease-entrance), border-color 0.4s var(--ease-entrance), transform 0.4s var(--ease-entrance);
}

.order-step.is-live {
  background: rgba(228, 101, 46, 0.14);
  border-color: rgba(228, 101, 46, 0.55);
  transform: translateY(-6px);
}

.order-step-no {
  font-family: var(--font-heading);
  font-size: 12px;
  font-weight: 800;
  letter-spacing: 1.5px;
  color: var(--primary-orange);
}

.order-step-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 52px;
  height: 52px;
  border-radius: 15px;
  background: rgba(255, 255, 255, 0.08);
  color: #FFFFFF;
  font-size: 20px;
  margin: 14px 0 auto 0;
}

.order-step p {
  margin-top: 0;
}

.order-step.is-live .order-step-icon {
  background: var(--primary-orange);
}

.order-step h3 {
  font-family: var(--font-heading);
  font-size: 18px;
  font-weight: 800;
  margin: 18px 0 8px 0;
  line-height: 1.25;
}

.order-step p {
  font-size: 13px;
  line-height: 1.6;
  color: rgba(255, 255, 255, 0.72);
  margin: 0;
}

/* Dashed connector between consecutive steps */
.order-step-link {
  position: absolute;
  top: 50%;
  right: -20px;
  width: 20px;
  border-top: 2px dashed rgba(255, 255, 255, 0.22);
}

.order-progress {
  height: 4px;
  border-radius: 2px;
  background: rgba(255, 255, 255, 0.12);
  overflow: hidden;
}

.order-progress-fill {
  display: block;
  height: 100%;
  border-radius: 2px;
  background: linear-gradient(90deg, var(--primary-orange), #F29163);
  transition: width 0.12s linear;
}

@media (max-width: 900px) {
  .order-sticky {
    position: static;
    min-height: 0;
    padding: var(--section-gap-sm) 0;
    gap: 20px;
  }

  .order-viewport {
    padding: 16px;
  }

  .order-step {
    flex-basis: 250px;
    min-height: 0;
    padding: 20px 18px;
    border-radius: 16px;
  }

  .order-step-icon {
    width: 42px;
    height: 42px;
    font-size: 17px;
    margin: 12px 0 14px 0;
  }

  .order-step h3 {
    margin-top: 0;
  }

  .order-step h3 {
    font-size: 15px;
  }

  .order-step p {
    font-size: 12px;
  }

  .order-progress {
    display: none;
  }
}
</style>
