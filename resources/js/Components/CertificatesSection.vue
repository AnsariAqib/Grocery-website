<template>
  <section class="section-padding section-flush-top">
    <div class="container">
      <div class="dealers-catalogue-wrapper">
        
        <!-- LEFT: AUTHORISED DEALERSHIP CERTIFICATES MARQUEE -->
        <div class="info-block-card reveal-left">
          <div class="block-header stagger">
            <h3 class="block-title reveal">
              <i class="fa-solid fa-award" style="color:#E4652E; margin-right:8px;"></i> Authorised Distributorship Certificates
            </h3>
            <span class="live-pill reveal"><i class="fa-solid fa-magnifying-glass-plus" style="color:#E4652E;"></i> Click Any Card to Zoom</span>
          </div>

          <div class="certificates-marquee-wrapper">
            <div class="cert-marquee-track">
              <template v-for="pass in 4" :key="pass">
                <div
                  v-for="cert in certificates"
                  :key="`${pass}-${cert.id}`"
                  class="cert-card"
                  @click="openCertModal(cert)"
                >
                  <div class="cert-corner corner-tl"></div><div class="cert-corner corner-tr"></div>
                  <div class="cert-corner corner-bl"></div><div class="cert-corner corner-br"></div>
                  <div class="cert-logo"><img :src="cert.logo || cert.fullImage" :alt="cert.brand" class="cert-logo-img"></div>
                  <div class="cert-title">{{ cert.title || 'AUTHORISED DISTRIBUTOR' }}</div>
                  <div class="cert-script">{{ cert.description || cert.brand }}</div>
                  <div class="cert-footer-row">
                    <div class="cert-sig">{{ cert.signatory || cert.brand }}</div>
                    <div class="gold-seal-stamp"><div class="seal-inner-target"></div></div>
                  </div>
                  <span class="cert-click-hint"><i class="fa-solid fa-expand"></i> Click to Zoom</span>
                </div>
              </template>
            </div>
          </div>
        </div>

        <!-- RIGHT: CATALOGUE DOWNLOAD CARD -->
        <div class="download-box reveal-right">
          <div class="download-title-area">
            <i class="fa-solid fa-file-pdf"></i>
            <div>
              <div class="download-title">Product Catalogues</div>
              <p style="font-size:11px; color:#94A3B8; margin:0;">Download PDF Lists</p>
            </div>
          </div>
          <a href="#" class="download-btn" @click.prevent="downloadCatalogue('retail')">
            <i class="fa-solid fa-download"></i> Supermarket Catalogue
          </a>
          <a href="#" class="download-btn" @click.prevent="downloadCatalogue('b2b')">
            <i class="fa-solid fa-download"></i> Corporate B2B Catalogue
          </a>
        </div>

      </div>
    </div>

    <!-- CERTIFICATE FULL-SCREEN ZOOM MODAL -->
    <div class="modal-overlay" :class="{ active: isCertModalOpen }" @click.self="closeCertModal" data-lenis-prevent>
      <div class="cert-modal-card" data-lenis-prevent>
        <button class="modal-close" @click="closeCertModal"><i class="fa-solid fa-xmark"></i></button>
        <div class="section-badge" style="margin-bottom:8px;"><i class="fa-solid fa-certificate"></i> Official Distributorship Certificate</div>
        
        <div class="full-cert-box">
          <!-- ═══ ORIGINAL TYPE: Show uploaded certificate image as-is ═══ -->
          <template v-if="selectedCert.type === 'original'">
            <div v-if="selectedCert.fullImage" style="text-align:center;">
              <img
                :src="selectedCert.fullImage"
                :alt="`${selectedCert.brand} Certificate`"
                style="max-width:100%; max-height:520px; object-fit:contain; border-radius:12px; box-shadow:0 4px 20px rgba(0,0,0,0.1); border:1px solid #E2E8F0;"
              >
            </div>
            <div v-else style="text-align:center; padding:40px 20px; color:#94A3B8;">
              <i class="fa-solid fa-image" style="font-size:48px; margin-bottom:12px; display:block;"></i>
              <p>Certificate image not yet uploaded.</p>
            </div>
          </template>

          <!-- ═══ CUSTOM TYPE: Styled certificate card generated from details ═══ -->
          <template v-else>
            <div class="cert-modal-logo">
              <img :src="selectedCert.logo" :alt="selectedCert.brand">
            </div>
            <h3 class="cert-modal-title">{{ selectedCert.title || 'CERTIFICATE OF AUTHORISED AGENCY' }}</h3>
            <p class="cert-modal-body">
              This is to certify that <strong>Jay Tulsi Agency (Surat, Gujarat)</strong> is an officially appointed and authorised distribution agency for <strong>{{ selectedCert.brand }}</strong> across retail supermarkets and commercial B2B procurement networks.
            </p>
            <p v-if="selectedCert.description" class="cert-modal-body" style="font-style:italic; font-size:12px; margin-top:4px;">
              {{ selectedCert.description }}
            </p>
            <div class="cert-modal-footer">
              <div class="cert-modal-sig">
                <span class="sig-line">{{ selectedCert.signatory || 'Official Seal & Signature' }}</span>
                <span class="sig-sub">Authorized Brand Division Head</span>
              </div>
              <div class="gold-seal-stamp" style="width:40px; height:40px;">
                <div class="seal-inner-target" style="width:24px; height:24px;"></div>
              </div>
            </div>
          </template>
        </div>

        <!-- Action buttons -->
        <div style="display:flex; justify-content:flex-end; gap:10px; flex-wrap:wrap; margin-top:16px;">
          <button @click="closeCertModal" class="btn-navy" style="padding:8px 16px; font-size:12px;">Close</button>
          
          <!-- Open full image in new tab (original type) -->
          <a
            v-if="selectedCert.type === 'original' && selectedCert.fullImage"
            :href="selectedCert.fullImage"
            target="_blank"
            rel="noopener"
            class="btn-outline"
            style="padding:8px 16px; font-size:12px;"
          >
            <i class="fa-solid fa-expand"></i> Open Full Image
          </a>

          <!-- View PDF (original type) -->
          <a
            v-if="selectedCert.pdf"
            :href="selectedCert.pdf"
            target="_blank"
            rel="noopener"
            class="btn-primary"
            style="padding:8px 16px; font-size:12px;"
          >
            <i class="fa-solid fa-file-pdf"></i> View PDF
          </a>

          <button @click="printCert" class="btn-navy" style="padding:8px 16px; font-size:12px;"><i class="fa-solid fa-print"></i> Print</button>
        </div>
      </div>
    </div>

    <!-- Shown when the requested catalogue has not been uploaded in the admin -->
    <CatalogueModal :catalogue="missingCatalogue" @close="closeCatalogueModal" />
  </section>
</template>

<script setup>
import { ref } from 'vue';
import CatalogueModal from '@/Components/CatalogueModal.vue';
import { useCatalogues } from '@/composables/useCatalogues';

defineProps({
  certificates: {
    type: Array,
    default: () => []
  }
});

const { downloadCatalogue, missingCatalogue, closeCatalogueModal } = useCatalogues();

const isCertModalOpen = ref(false);
const selectedCert = ref({ type: 'custom', brand: '', logo: '', pdf: null, fullImage: null });

function openCertModal(cert) {
  selectedCert.value = cert;
  isCertModalOpen.value = true;
}

function closeCertModal() {
  isCertModalOpen.value = false;
}

function printCert() {
  window.print();
}
</script>

