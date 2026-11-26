<template>
  <div class="contact-page-root">
    <TopBar />
    <Navbar activePage="contact" @openModal="isModalOpen = true" />

    <main>
      <!-- Page Hero -->
      <section class="about-hero-section">
        <div class="container">
          <div class="about-hero-card reveal">
            <img src="https://images.unsplash.com/photo-1552664730-d307ca884978?auto=format&fit=crop&w=1600&q=80" alt="Talk to our team" class="about-hero-bg">
            <div class="about-hero-overlay"></div>
            <div class="about-hero-content">
              <div class="breadcrumb-trail">
                <Link href="/"><i class="fa-solid fa-house"></i> Home</Link>
                <span>/</span>
                <span class="active">Contact Us</span>
              </div>
              <h1 class="about-hero-title">
                Let's Talk About <br>
                <span class="hero-title-gradient shiny-text">Your Requirement</span>
              </h1>
              <p class="about-hero-sub">
                Retail enquiries, corporate bulk supply, dealership questions — reach the right desk in one message.
              </p>
            </div>
          </div>
        </div>
      </section>

      <!-- Quick contact tiles -->
      <section class="section-padding section-flush-bottom">
        <div class="container">
          <div class="contact-tiles stagger">
            <a :href="`tel:${primaryPhoneLink}`" class="contact-tile reveal">
              <span class="contact-tile-icon"><i class="fa-solid fa-phone"></i></span>
              <span class="contact-tile-label">Call us</span>
              <strong class="contact-tile-value">{{ site.phoneDisplay }}</strong>
            </a>

            <a :href="`mailto:${site.emailPrimary}`" class="contact-tile reveal">
              <span class="contact-tile-icon"><i class="fa-solid fa-envelope"></i></span>
              <span class="contact-tile-label">Email us</span>
              <strong class="contact-tile-value">{{ site.emailPrimary }}</strong>
            </a>

            <a
              v-if="whatsapp.enabled && whatsapp.url"
              :href="whatsapp.url"
              target="_blank"
              rel="noopener"
              class="contact-tile reveal"
            >
              <span class="contact-tile-icon whatsapp"><i class="fa-brands fa-whatsapp"></i></span>
              <span class="contact-tile-label">WhatsApp</span>
              <strong class="contact-tile-value">Chat with our team</strong>
            </a>

            <div class="contact-tile reveal">
              <span class="contact-tile-icon"><i class="fa-regular fa-clock"></i></span>
              <span class="contact-tile-label">Business hours</span>
              <strong class="contact-tile-value">{{ site.storeHours || 'Mon – Sun, 8:00 AM – 10:00 PM' }}</strong>
            </div>
          </div>
        </div>
      </section>

      <!-- Enquiry form + business information -->
      <section class="section-padding">
        <div class="container">
          <div class="contact-main-grid">
            <!-- FORM -->
            <div class="contact-form-card reveal-left">
              <span class="section-badge"><i class="fa-solid fa-paper-plane"></i> Send an enquiry</span>
              <h2 class="contact-form-title">Tell us what you need</h2>
              <p class="contact-form-sub">
                Fill this in and our team gets back on the same working day. Bulk requirements are quoted with GST pricing.
              </p>

              <form @submit.prevent="submit" novalidate>
                <div class="form-row-2col">
                  <div class="form-group">
                    <label for="cName">Full Name *</label>
                    <input
                      id="cName"
                      v-model="form.name"
                      type="text"
                      class="form-control"
                      :class="{ 'is-invalid': errors.name }"
                      placeholder="Enter your full name"
                    >
                    <span v-if="errors.name" class="field-error-msg">Please enter a valid name (letters only, min 2 chars).</span>
                  </div>

                  <div class="form-group">
                    <label for="cPhone">Phone Number * (10 digits)</label>
                    <input
                      id="cPhone"
                      v-model="form.phone"
                      type="tel"
                      inputmode="numeric"
                      maxlength="10"
                      class="form-control"
                      :class="{ 'is-invalid': errors.phone }"
                      placeholder="9876543210"
                      @input="onPhoneInput"
                    >
                    <span v-if="errors.phone" class="field-error-msg">Enter a valid 10-digit mobile number starting 6-9.</span>
                  </div>
                </div>

                <div class="form-row-2col">
                  <div class="form-group">
                    <label for="cEmail">Email Address <span class="optional">(optional)</span></label>
                    <input id="cEmail" v-model="form.email" type="email" class="form-control" placeholder="name@domain.com">
                  </div>

                  <div class="form-group">
                    <label for="cCompany">Company Name <span class="optional">(optional)</span></label>
                    <input id="cCompany" v-model="form.company" type="text" class="form-control" placeholder="Company / Business Name">
                  </div>
                </div>

                <div class="form-row-2col">
                  <div class="form-group">
                    <label for="cIndustry">Industry <span class="optional">(optional)</span></label>
                    <input id="cIndustry" v-model="form.industry" type="text" class="form-control" placeholder="e.g. Retail, Manufacturing">
                  </div>

                  <div class="form-group">
                    <label for="cType">Enquiry Type</label>
                    <select id="cType" v-model="form.inquiryType" class="form-control">
                      <option value="retail">Retail Store Enquiry</option>
                      <option value="b2b">B2B Business Supply Enquiry</option>
                      <option value="dealership">Brand / Dealership Enquiry</option>
                    </select>
                  </div>
                </div>

                <div class="form-group">
                  <label for="cMessage">Message / Product Requirement</label>
                  <textarea id="cMessage" v-model="form.message" class="form-control" rows="4" placeholder="Share item names, quantities or any question you have..."></textarea>
                </div>

                <button type="submit" class="btn-primary contact-submit" :disabled="sending">
                  {{ sending ? 'Sending…' : 'Submit Enquiry' }} <i class="fa-solid fa-paper-plane"></i>
                </button>

                <p v-if="sent" class="contact-sent-note">
                  <i class="fa-solid fa-circle-check"></i> Thank you — your enquiry has reached our team.
                </p>
              </form>
            </div>

            <!-- BUSINESS INFO -->
            <aside class="contact-info-card reveal-right">
              <span class="section-badge"><i class="fa-solid fa-building"></i> Business information</span>
              <h2 class="contact-form-title">Company details</h2>

              <ul class="business-list">
                <li>
                  <span class="business-label">Registered name</span>
                  <span class="business-value">{{ business.legalName }}</span>
                </li>
                <li v-if="business.gstNumber">
                  <span class="business-label">GST number</span>
                  <span class="business-value mono">{{ business.gstNumber }}</span>
                </li>
                <li>
                  <span class="business-label">In business since</span>
                  <span class="business-value">{{ business.established }}</span>
                </li>
                <li>
                  <span class="business-label">Service area</span>
                  <span class="business-value">{{ business.serviceArea }}</span>
                </li>
                <li>
                  <span class="business-label">Working days</span>
                  <span class="business-value">{{ business.workingDays }}</span>
                </li>
                <li v-if="site.addressFull">
                  <span class="business-label">Head office</span>
                  <span class="business-value">{{ site.addressFull }}</span>
                </li>
                <li v-if="site.emailSecondary">
                  <span class="business-label">Corporate desk</span>
                  <a class="business-value link" :href="`mailto:${site.emailSecondary}`">{{ site.emailSecondary }}</a>
                </li>
              </ul>

              <div v-if="hasSocials" class="contact-socials">
                <span class="business-label">Follow us</span>
                <div class="top-social">
                  <a v-if="site.social.facebook" :href="site.social.facebook" target="_blank" rel="noopener noreferrer" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                  <a v-if="site.social.instagram" :href="site.social.instagram" target="_blank" rel="noopener noreferrer" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
                  <a v-if="site.social.linkedin" :href="site.social.linkedin" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>
                </div>
              </div>
            </aside>
          </div>
        </div>
      </section>

      <!-- Store locations -->
      <section class="section-padding bg-light-surface" style="position:relative; overflow:hidden;">
        <div class="watermark-backdrop" data-parallax="26">VISIT US</div>
        <div class="container" style="position:relative; z-index:2;">
          <div class="section-header center">
            <span class="section-badge"><i class="fa-solid fa-store"></i> Store locations</span>
            <h2 class="section-title">Come And See Us</h2>
            <p class="section-subtitle">Every outlet, with its phone numbers, GST details and an exact map link.</p>
          </div>

          <div class="contact-stores-grid stagger">
            <div v-for="store in stores" :key="store.id" class="contact-store-card reveal">
              <div class="contact-store-head">
                <h3>{{ store.name }}</h3>
                <span v-if="store.type" class="contact-store-type">{{ store.type }}</span>
              </div>

              <p class="contact-store-address"><i class="fa-solid fa-location-dot"></i> {{ store.address }}</p>

              <div v-if="store.phoneLinks.length" class="contact-store-phones">
                <a v-for="(num, i) in store.phoneLinks" :key="i" :href="`tel:+91${num}`">+91 {{ num }}</a>
              </div>

              <p v-if="store.email" class="contact-store-line">
                <i class="fa-solid fa-envelope"></i>
                <a :href="`mailto:${store.email}`">{{ store.email }}</a>
              </p>

              <p v-if="store.gstNumber" class="contact-store-line">
                <i class="fa-solid fa-file-invoice"></i>
                <span class="mono">{{ store.gstNumber }}</span>
              </p>

              <div class="contact-store-actions">
                <a :href="store.mapUrl" target="_blank" rel="noopener" class="btn-outline contact-store-btn">
                  <i class="fa-solid fa-map-location-dot"></i> View on Map
                </a>
                <a :href="store.directionsUrl" target="_blank" rel="noopener" class="btn-primary contact-store-btn">
                  <i class="fa-solid fa-diamond-turn-right"></i> Directions
                </a>
              </div>
            </div>
          </div>
        </div>
      </section>

      <QuickCalloutBanner />
    </main>

    <Footer />
    <ContactModal :isOpen="isModalOpen" @close="isModalOpen = false" />
    <WhatsAppFloat />
    <PromoPopup @openEnquiry="isModalOpen = true" />
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue';
import { Link } from '@inertiajs/vue3';
import axios from 'axios';
import TopBar from '@/Components/TopBar.vue';
import Navbar from '@/Components/Navbar.vue';
import QuickCalloutBanner from '@/Components/QuickCalloutBanner.vue';
import Footer from '@/Components/Footer.vue';
import ContactModal from '@/Components/ContactModal.vue';
import WhatsAppFloat from '@/Components/WhatsAppFloat.vue';
import PromoPopup from '@/Components/PromoPopup.vue';
import { useSite } from '@/composables/useSite';
import { useParallax } from '@/composables/useParallax';
import { useCustomAlert } from '@/composables/useCustomAlert';

defineProps({
  stores: { type: Array, default: () => [] },
  business: { type: Object, default: () => ({}) },
  meta: Object,
});

const { site, whatsapp } = useSite();
const { showSuccess, showError, showWarning } = useCustomAlert();
useParallax();

const isModalOpen = ref(false);
const sending = ref(false);
const sent = ref(false);

const primaryPhoneLink = computed(() => (site.value.phonePrimary || '').replace(/[^\d]/g, ''));

// "Follow us" is only worth a heading when there is something to follow.
const hasSocials = computed(
  () => Boolean(site.value.social.facebook || site.value.social.instagram || site.value.social.linkedin),
);

const form = reactive({
  name: '',
  phone: '',
  email: '',
  company: '',
  industry: '',
  inquiryType: 'retail',
  message: '',
});

const errors = reactive({ name: false, phone: false });

function validateName() {
  const nameRegex = /^[a-zA-Z\s]{2,}$/;
  errors.name = !form.name.trim() || !nameRegex.test(form.name.trim());
  return !errors.name;
}

function validatePhone() {
  const phoneRegex = /^[6-9]\d{9}$/;
  errors.phone = !form.phone.trim() || !phoneRegex.test(form.phone.trim());
  return !errors.phone;
}

function handlePhoneInput() {
  form.phone = form.phone.replace(/\D/g, '').slice(0, 10);
  validatePhone();
}
const onPhoneInput = handlePhoneInput;

function handleSubmit() {
  const isNameValid = validateName();
  const isPhoneValid = validatePhone();

  if (!isNameValid || !isPhoneValid) {
    showWarning('Please enter a valid Name and 10-digit Mobile Number starting with 6-9.', 'Validation Error');
    return;
  }

  if (sending.value) return;

  sending.value = true;
  sent.value = false;

  axios.post('/inquiry', {
    name: form.name,
    phone: form.phone,
    email: form.email,
    company: form.company,
    industry: form.industry,
    inquiry_type: form.inquiryType,
    message: form.message,
  })
    .then(response => {
      if (!response.data.success) return;

      sent.value = true;
      showSuccess(response.data.message, 'Inquiry Submitted', {
        whatsappUrl: response.data.whatsapp_url,
      });

      Object.assign(form, {
        name: '', phone: '', email: '', company: '', industry: '', message: '',
      });
    })
    .catch(() => {
      showError('Something went wrong. Please try again, or call us directly.', 'Submission Error');
    })
    .finally(() => {
      sending.value = false;
    });
}
const submit = handleSubmit;

onMounted(() => {
  const revealElements = document.querySelectorAll('.reveal, .reveal-left, .reveal-right, .reveal-scale');
  if ('IntersectionObserver' in window) {
    const observer = new IntersectionObserver((entries, obs) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('active');
          obs.unobserve(entry.target);
        }
      });
    }, { threshold: 0.08, rootMargin: '0px 0px -20px 0px' });
    revealElements.forEach(el => observer.observe(el));
  } else {
    revealElements.forEach(el => el.classList.add('active'));
  }
});
</script>

<style scoped>
/* ---- Quick contact tiles ---- */
.contact-tiles {
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 18px;
}

.contact-tile {
  display: flex;
  flex-direction: column;
  gap: 4px;
  padding: 22px 20px;
  background: #FFFFFF;
  border: 1px solid var(--border-color);
  border-radius: var(--radius-lg);
  box-shadow: var(--shadow-sm);
  text-decoration: none;
  transition: transform 0.3s var(--ease-interact), box-shadow 0.3s var(--ease-interact), border-color 0.3s var(--ease-interact);
}

a.contact-tile:hover {
  transform: translateY(var(--lift-card));
  border-color: var(--primary-orange);
  box-shadow: var(--shadow-hover-orange);
}

.contact-tile-icon {
  width: 44px;
  height: 44px;
  border-radius: 14px;
  background: var(--primary-orange-light);
  color: var(--primary-orange);
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 18px;
  margin-bottom: 10px;
}

.contact-tile-icon.whatsapp {
  background: #E7F8EE;
  color: #1FA855;
}

.contact-tile-label {
  font-size: 11px;
  font-weight: 800;
  letter-spacing: 0.6px;
  text-transform: uppercase;
  color: var(--text-muted);
}

.contact-tile-value {
  font-family: var(--font-heading);
  font-size: 15px;
  font-weight: 800;
  color: var(--dark-navy);
  line-height: 1.35;
  word-break: break-word;
}

/* ---- Form + info ---- */
.contact-main-grid {
  display: grid;
  grid-template-columns: minmax(0, 1.35fr) minmax(0, 1fr);
  gap: 28px;
  align-items: start;
}

.contact-form-card,
.contact-info-card {
  background: #FFFFFF;
  border: 1px solid var(--border-color);
  border-radius: var(--radius-xl);
  padding: 32px;
  box-shadow: var(--shadow-sm);
}

.contact-info-card {
  background: linear-gradient(160deg, #061427 0%, #0F2544 100%);
  border-color: transparent;
  color: #FFFFFF;
}

.contact-form-title {
  font-family: var(--font-heading);
  font-size: 24px;
  font-weight: 800;
  color: var(--dark-navy);
  margin: 12px 0 6px 0;
}

.contact-info-card .contact-form-title {
  color: #FFFFFF;
}

.contact-form-sub {
  font-size: 13.5px;
  color: var(--text-muted);
  line-height: 1.6;
  margin: 0 0 22px 0;
}

.form-row-2col {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
}

.optional {
  font-weight: 400;
  color: var(--text-light);
  font-size: 11px;
}

.contact-submit {
  width: 100%;
  justify-content: center;
  margin-top: 6px;
  padding: 14px 20px;
}

.contact-submit[disabled] {
  opacity: 0.7;
  cursor: not-allowed;
}

.contact-sent-note {
  margin: 14px 0 0 0;
  font-size: 13px;
  font-weight: 700;
  color: #1FA855;
  display: flex;
  align-items: center;
  gap: 8px;
}

/* ---- Business information ---- */
.business-list {
  list-style: none;
  padding: 0;
  margin: 18px 0 0 0;
}

.business-list li {
  display: flex;
  flex-direction: column;
  gap: 3px;
  padding: 13px 0;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.business-list li:last-child {
  border-bottom: none;
}

.business-label {
  font-size: 10.5px;
  font-weight: 800;
  letter-spacing: 0.7px;
  text-transform: uppercase;
  color: #94A3B8;
}

.business-value {
  font-size: 14px;
  font-weight: 600;
  color: #FFFFFF;
  line-height: 1.5;
}

.business-value.link {
  color: var(--primary-orange);
}

.mono {
  font-variant-numeric: tabular-nums;
  letter-spacing: 0.5px;
}

.contact-socials {
  margin-top: 20px;
  padding-top: 18px;
  border-top: 1px solid rgba(255, 255, 255, 0.1);
  display: flex;
  flex-direction: column;
  gap: 10px;
}

/* ---- Store cards ---- */
.contact-stores-grid {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 20px;
}

.contact-store-card {
  background: #FFFFFF;
  border: 1px solid var(--border-color);
  border-radius: var(--radius-lg);
  padding: 24px;
  box-shadow: var(--shadow-sm);
  display: flex;
  flex-direction: column;
  transition: transform 0.3s var(--ease-interact), box-shadow 0.3s var(--ease-interact), border-color 0.3s var(--ease-interact);
}

.contact-store-card:hover {
  transform: translateY(var(--lift-card));
  border-color: rgba(228, 101, 46, 0.35);
  box-shadow: var(--shadow-hover);
}

.contact-store-head h3 {
  font-family: var(--font-heading);
  font-size: 18px;
  font-weight: 800;
  color: var(--dark-navy);
  margin: 0 0 6px 0;
}

.contact-store-type {
  display: inline-block;
  font-size: 10.5px;
  font-weight: 800;
  letter-spacing: 0.5px;
  text-transform: uppercase;
  color: var(--primary-orange);
  background: var(--primary-orange-light);
  padding: 4px 10px;
  border-radius: 999px;
  margin-bottom: 14px;
}

.contact-store-address {
  font-size: 13px;
  color: #334155;
  line-height: 1.55;
  margin: 0 0 12px 0;
  display: flex;
  gap: 8px;
}

.contact-store-address i,
.contact-store-line i {
  color: var(--primary-orange);
  margin-top: 3px;
  flex-shrink: 0;
}

.contact-store-phones {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  margin-bottom: 10px;
}

.contact-store-phones a {
  font-size: 12.5px;
  font-weight: 700;
  color: var(--dark-navy);
  background: #F1F5F9;
  padding: 4px 10px;
  border-radius: 6px;
}

.contact-store-phones a:hover {
  background: var(--primary-orange);
  color: #FFFFFF;
}

.contact-store-line {
  display: flex;
  gap: 8px;
  font-size: 12.5px;
  color: #475569;
  margin: 0 0 8px 0;
  word-break: break-word;
}

.contact-store-line a {
  color: var(--primary-orange);
  font-weight: 600;
}

.contact-store-actions {
  display: flex;
  gap: 10px;
  margin-top: auto;
  padding-top: 16px;
  border-top: 1px dashed var(--border-color);
}

.contact-store-btn {
  flex: 1;
  justify-content: center;
  font-size: 12px;
  padding: 9px 10px;
  border-radius: 9px;
  display: inline-flex;
  align-items: center;
  gap: 7px;
  text-align: center;
}

/* ---- Responsive ---- */
@media (max-width: 1024px) {
  .contact-tiles {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }

  .contact-main-grid {
    grid-template-columns: 1fr;
  }

  .contact-stores-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}

@media (max-width: 768px) {
  .contact-tiles {
    gap: 12px;
  }

  .contact-tile {
    padding: 16px 14px;
    border-radius: 16px;
  }

  .contact-tile-icon {
    width: 36px;
    height: 36px;
    font-size: 15px;
    border-radius: 11px;
    margin-bottom: 8px;
  }

  .contact-tile-value {
    font-size: 12.5px;
  }

  .contact-form-card,
  .contact-info-card {
    padding: 20px 16px;
    border-radius: 18px;
  }

  .contact-form-title {
    font-size: 19px;
  }

  .form-row-2col {
    grid-template-columns: 1fr;
    gap: 0;
  }

  .contact-stores-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 12px;
  }

  .contact-store-card {
    padding: 16px 13px;
    border-radius: 16px;
  }

  .contact-store-head h3 {
    font-size: 15px;
  }

  .contact-store-type {
    font-size: 9px;
    margin-bottom: 10px;
  }

  .contact-store-address,
  .contact-store-line {
    font-size: 11.5px;
  }

  .contact-store-phones a {
    font-size: 11px;
  }

  .contact-store-actions {
    flex-direction: column;
    gap: 8px;
  }
}
</style>
