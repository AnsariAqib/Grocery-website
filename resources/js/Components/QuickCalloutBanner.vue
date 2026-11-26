<template>
  <section class="section-padding section-flush-top" id="contact">
    <div class="container">
      <div class="contact-callout-card reveal stagger">
        <!-- Col 1: Location -->
        <div class="callout-info-item reveal">
          <div class="callout-icon"><i class="fa-solid fa-location-dot"></i></div>
          <div class="callout-text">
            <label>Headquarters & Stores</label>
            <strong>{{ site.addressShort }}</strong>
          </div>
        </div>

        <!-- Col 2: Hours -->
        <div class="callout-info-item reveal">
          <div class="callout-icon"><i class="fa-solid fa-clock"></i></div>
          <div class="callout-text">
            <label>Store & Dispatch Hours</label>
            <strong>{{ site.storeHours }}</strong>
          </div>
        </div>

        <!-- Col 3: Phone -->
        <div class="callout-info-item reveal">
          <div class="callout-icon"><i class="fa-solid fa-phone"></i></div>
          <div class="callout-text">
            <label>Direct Sales Helpline</label>
            <strong>{{ site.phoneDisplay }}</strong>
          </div>
        </div>

        <!-- Col 4: WhatsApp Quick Chat -->
        <div class="callout-action-orange reveal">
          <h4>Need Quick Assistance?</h4>
          <p>Chat directly with our supply team on WhatsApp.</p>
          <a v-if="whatsapp.url" :href="whatsapp.url" target="_blank" rel="noopener" class="btn-white-sm">
            <i class="fa-brands fa-whatsapp" style="color:#25D366; font-size:16px;"></i> WhatsApp Chat
          </a>
          <a v-else :href="`tel:+91${site.phonePrimary}`" class="btn-white-sm">
            <i class="fa-solid fa-phone" style="color:#E4652E; font-size:14px;"></i> Call Us Now
          </a>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { useSite } from '@/composables/useSite';

const { site, whatsapp } = useSite();
</script>
