<template>
  <a
    v-if="whatsapp.enabled && whatsapp.url"
    :href="whatsapp.url"
    target="_blank"
    rel="noopener"
    class="whatsapp-float"
    aria-label="Chat on WhatsApp"
  >
    <i class="fa-brands fa-whatsapp"></i>
    <span class="whatsapp-float-label">Chat with us</span>
  </a>
</template>

<script setup>
import { useSite } from '@/composables/useSite';

const { whatsapp } = useSite();
</script>

<style scoped>
/* Sits above page content but below the contact modal overlay. */
.whatsapp-float {
  position: fixed;
  right: 22px;
  bottom: 22px;
  z-index: 900;
  display: inline-flex;
  align-items: center;
  gap: 0;
  height: 56px;
  padding: 0 16px;
  border-radius: 999px;
  background: #25D366;
  color: #FFFFFF;
  box-shadow: 0 10px 26px rgba(37, 211, 102, 0.36), 0 2px 8px rgba(6, 20, 39, 0.12);
  text-decoration: none;
  transition: box-shadow 0.25s cubic-bezier(0.4, 0, 0.2, 1),
              transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}

.whatsapp-float i {
  font-size: 28px;
  line-height: 1;
}

/* Label stays collapsed until hover so the button keeps its circular footprint. */
.whatsapp-float-label {
  max-width: 0;
  overflow: hidden;
  white-space: nowrap;
  font-size: 14px;
  font-weight: 700;
  opacity: 0;
  transition: max-width 0.3s cubic-bezier(0.16, 1, 0.3, 1),
              opacity 0.2s ease,
              margin-left 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}

.whatsapp-float:hover {
  transform: translateY(-2px);
  box-shadow: 0 14px 32px rgba(37, 211, 102, 0.45), 0 3px 10px rgba(6, 20, 39, 0.16);
}

.whatsapp-float:hover .whatsapp-float-label {
  max-width: 130px;
  margin-left: 10px;
  opacity: 1;
}

@media (max-width: 576px) {
  .whatsapp-float {
    right: 16px;
    bottom: 16px;
    height: 52px;
    padding: 0 14px;
  }

  .whatsapp-float i {
    font-size: 26px;
  }

  /* No hover on touch, so never reserve room for the label. */
  .whatsapp-float:hover .whatsapp-float-label {
    max-width: 0;
    margin-left: 0;
    opacity: 0;
  }
}

@media (prefers-reduced-motion: reduce) {
  .whatsapp-float,
  .whatsapp-float-label {
    transition: none;
  }
}
</style>
