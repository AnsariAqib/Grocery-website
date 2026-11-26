<template>
  <div class="modal-overlay" :class="{ active: !!catalogue }" @click.self="$emit('close')" data-lenis-prevent>
    <div class="modal-card catalogue-modal-card" data-lenis-prevent>
      <button class="modal-close" @click="$emit('close')" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>

      <div class="catalogue-modal-icon">
        <i class="fa-solid fa-file-circle-exclamation"></i>
      </div>

      <h3 class="catalogue-modal-title">{{ catalogue?.label || 'Catalogue' }} Coming Soon</h3>
      <p class="catalogue-modal-text">
        This catalogue is being updated and isn't available for download right now.
        Share your requirement with us and our team will send you the latest product
        list and pricing directly.
      </p>

      <div class="catalogue-modal-actions">
        <a
          v-if="whatsapp.enabled && whatsapp.url"
          :href="whatsapp.url"
          target="_blank"
          rel="noopener"
          class="btn-primary catalogue-modal-btn"
        >
          <i class="fa-brands fa-whatsapp"></i> Request on WhatsApp
        </a>
        <a
          v-if="site.phonePrimary"
          :href="`tel:+91${site.phonePrimary}`"
          class="btn-navy catalogue-modal-btn"
        >
          <i class="fa-solid fa-phone"></i> Call {{ site.phoneDisplay || site.phonePrimary }}
        </a>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useSite } from '@/composables/useSite';

// `catalogue` is null when closed, or { type, label } for the requested file.
defineProps({
  catalogue: {
    type: Object,
    default: null,
  },
});

defineEmits(['close']);

const { site, whatsapp } = useSite();
</script>

<style scoped>
.catalogue-modal-card {
  max-width: 440px;
  text-align: center;
}

.catalogue-modal-icon {
  width: 68px;
  height: 68px;
  margin: 4px auto 16px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(228, 101, 46, 0.1);
  color: var(--primary-orange);
  font-size: 28px;
}

.catalogue-modal-title {
  font-family: var(--font-main);
  font-size: 21px;
  font-weight: 700;
  color: var(--dark-navy);
  margin: 0 0 10px;
}

.catalogue-modal-text {
  font-size: 14px;
  line-height: 1.65;
  color: #64748B;
  margin: 0 0 22px;
}

.catalogue-modal-actions {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.catalogue-modal-btn {
  width: 100%;
  justify-content: center;
  font-size: 14px;
  padding: 12px 20px;
}

@media (max-width: 480px) {
  .catalogue-modal-title {
    font-size: 18px;
  }

  .catalogue-modal-text {
    font-size: 13px;
  }
}
</style>
