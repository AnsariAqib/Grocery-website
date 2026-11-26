<template>
  <section class="cat-hero-section">
    <div class="container">
      <div
        class="cat-hero-card reveal"
        :class="{ 'has-bg-image': !!resolvedBgImage }"
      >
        <!-- Background Image layer if provided by admin -->
        <div v-if="resolvedBgImage" class="cat-hero-bg-layer">
          <img :src="resolvedBgImage" alt="Category Banner Background" class="cat-hero-bg-img" loading="eager" />
          <div class="cat-hero-overlay"></div>
        </div>

        <!-- Glow spheres when using gradient background -->
        <template v-else>
          <span class="cat-hero-glow one" data-parallax="18"></span>
          <span class="cat-hero-glow two" data-parallax="26"></span>
        </template>

        <div class="cat-hero-inner">
          <div class="cat-hero-top-row">
            <div class="breadcrumb-trail">
              <Link href="/"><i class="fa-solid fa-house"></i> Home</Link>
              <span>/</span>
              <span class="active">{{ crumb }}</span>
            </div>

            <span class="cat-hero-badge">
              <i class="fa-solid" :class="resolvedBadgeIcon"></i> {{ resolvedBadge }}
            </span>
          </div>

          <h1 class="cat-hero-title">
            {{ resolvedTitle }}
            <span class="cat-hero-highlight">{{ resolvedHighlight }}</span>
          </h1>

          <p v-if="resolvedSubtitle" class="cat-hero-sub">{{ resolvedSubtitle }}</p>

          <div class="cat-hero-meta">
            <span v-if="count > 0" class="cat-hero-chip">
              <i class="fa-solid fa-layer-group"></i> {{ count }} Categories
            </span>
            <span v-if="varieties > 0" class="cat-hero-chip">
              <i class="fa-solid fa-boxes-stacked"></i> {{ varieties }} Varieties
            </span>
            <Link :href="resolvedCtaUrl" class="cat-hero-cta">
              {{ resolvedCtaText }} <i class="fa-solid fa-arrow-right"></i>
            </Link>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
  banner: { type: Object, default: () => null },
  crumb: { type: String, default: 'Categories' },
  badge: { type: String, default: 'Catalogue' },
  badgeIcon: { type: String, default: 'fa-layer-group' },
  title: { type: String, default: '' },
  highlight: { type: String, default: '' },
  subtitle: { type: String, default: '' },
  ctaText: { type: String, default: 'Browse All Products' },
  ctaUrl: { type: String, default: '/products' },
  backgroundImage: { type: String, default: null },
  count: { type: Number, default: 0 },
  varieties: { type: Number, default: 0 },
});

const resolvedBadge = computed(() => props.banner?.badge || props.badge || 'Catalogue');
const resolvedBadgeIcon = computed(() => props.banner?.badgeIcon || props.badgeIcon || 'fa-layer-group');
const resolvedTitle = computed(() => props.banner?.title || props.title || '');
const resolvedHighlight = computed(() => props.banner?.highlight || props.highlight || '');
const resolvedSubtitle = computed(() => props.banner?.subtitle ?? props.subtitle ?? '');
const resolvedCtaText = computed(() => props.banner?.ctaText || props.ctaText || 'Browse All Products');
const resolvedCtaUrl = computed(() => props.banner?.ctaUrl || props.ctaUrl || '/products');
const resolvedBgImage = computed(() => props.banner?.backgroundImage || props.backgroundImage || null);
</script>

<style scoped>
.cat-hero-section {
  padding-top: 18px;
}

/* Compact, sleek modern banner */
.cat-hero-card {
  position: relative;
  overflow: hidden;
  border-radius: 20px;
  padding: 28px 36px;
  background:
    radial-gradient(120% 140% at 88% 8%, rgba(255, 255, 255, 0.20) 0%, rgba(255, 255, 255, 0) 55%),
    linear-gradient(118deg, #C9541F 0%, #E4652E 45%, #EE7B3D 78%, #F29163 100%);
  box-shadow: 0 12px 32px rgba(201, 84, 31, 0.20);
  color: #FFFFFF;
  transition: box-shadow 0.3s ease;
}

/* When custom background image is active */
.cat-hero-card.has-bg-image {
  background: #061427;
  box-shadow: 0 12px 34px rgba(6, 20, 39, 0.28);
}

.cat-hero-bg-layer {
  position: absolute;
  inset: 0;
  z-index: 1;
  overflow: hidden;
}

.cat-hero-bg-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  object-position: center;
  transform: scale(1.02);
}

.cat-hero-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(92deg, rgba(6, 20, 39, 0.90) 0%, rgba(6, 20, 39, 0.76) 48%, rgba(201, 84, 31, 0.68) 100%);
  -webkit-backdrop-filter: blur(1px);
  backdrop-filter: blur(1px);
}

/* Drifting soft glow spots for gradient banner */
.cat-hero-glow {
  position: absolute;
  border-radius: 50%;
  filter: blur(50px);
  pointer-events: none;
  transform: translateY(var(--parallax-y, 0px));
}

.cat-hero-glow.one {
  width: 240px;
  height: 240px;
  right: -30px;
  top: -80px;
  background: rgba(255, 255, 255, 0.26);
}

.cat-hero-glow.two {
  width: 200px;
  height: 200px;
  right: 140px;
  bottom: -100px;
  background: rgba(6, 20, 39, 0.24);
}

.cat-hero-inner {
  position: relative;
  z-index: 2;
  max-width: 840px;
}

.cat-hero-top-row {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  gap: 14px;
  margin-bottom: 10px;
}

.cat-hero-card :deep(.breadcrumb-trail) {
  color: rgba(255, 255, 255, 0.78);
  font-size: 13px;
  margin-bottom: 0;
}

.cat-hero-card :deep(.breadcrumb-trail a) {
  color: #FFFFFF;
}

.cat-hero-card :deep(.breadcrumb-trail .active) {
  color: rgba(255, 255, 255, 0.95);
  font-weight: 600;
}

.cat-hero-badge {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  font-size: 11px;
  font-weight: 800;
  letter-spacing: 0.6px;
  text-transform: uppercase;
  background: rgba(255, 255, 255, 0.20);
  border: 1px solid rgba(255, 255, 255, 0.35);
  -webkit-backdrop-filter: blur(6px);
  backdrop-filter: blur(6px);
  padding: 4px 12px;
  border-radius: 999px;
}

.cat-hero-title {
  font-family: var(--font-heading);
  font-size: 30px;
  font-weight: 800;
  line-height: 1.15;
  letter-spacing: -0.5px;
  margin: 0 0 8px 0;
}

.cat-hero-highlight {
  display: inline;
  color: #061427;
  background: rgba(255, 255, 255, 0.35);
  padding: 2px 8px;
  border-radius: 6px;
  margin-left: 4px;
}

.has-bg-image .cat-hero-highlight {
  color: #FFB37C;
  background: rgba(0, 0, 0, 0.35);
  border: 1px solid rgba(255, 179, 124, 0.3);
}

.cat-hero-sub {
  font-size: 13.5px;
  line-height: 1.5;
  color: rgba(255, 255, 255, 0.92);
  max-width: 620px;
  margin: 0 0 16px 0;
}

.cat-hero-meta {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  gap: 10px;
}

.cat-hero-chip {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  font-size: 12px;
  font-weight: 700;
  background: rgba(6, 20, 39, 0.25);
  border: 1px solid rgba(255, 255, 255, 0.24);
  padding: 6px 12px;
  border-radius: 999px;
}

.cat-hero-cta {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  font-size: 12.5px;
  font-weight: 800;
  color: #C9541F;
  background: #FFFFFF;
  padding: 8px 18px;
  border-radius: 999px;
  text-decoration: none;
  box-shadow: 0 6px 18px rgba(6, 20, 39, 0.16);
  transition: transform 0.22s var(--ease-interact), box-shadow 0.22s var(--ease-interact), background-color 0.22s ease;
}

.cat-hero-cta:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 24px rgba(6, 20, 39, 0.22);
  background: #FFF7ED;
}

@media (max-width: 1024px) {
  .cat-hero-card {
    padding: 24px 28px;
  }

  .cat-hero-title {
    font-size: 26px;
  }
}

@media (max-width: 768px) {
  .cat-hero-section {
    padding-top: 14px;
  }

  .cat-hero-card {
    padding: 20px 16px;
    border-radius: 16px;
  }

  .cat-hero-top-row {
    gap: 8px;
    margin-bottom: 8px;
  }

  .cat-hero-badge {
    font-size: 10px;
    padding: 3px 9px;
  }

  .cat-hero-title {
    font-size: 21px;
    letter-spacing: -0.3px;
  }

  .cat-hero-highlight {
    display: inline-block;
    margin-top: 4px;
    margin-left: 0;
  }

  .cat-hero-sub {
    font-size: 12.5px;
    margin-bottom: 14px;
  }

  .cat-hero-chip {
    font-size: 11px;
    padding: 5px 10px;
  }

  .cat-hero-cta {
    font-size: 11.5px;
    padding: 7px 14px;
    width: 100%;
    justify-content: center;
  }

  .cat-hero-glow {
    display: none;
  }
}
</style>
