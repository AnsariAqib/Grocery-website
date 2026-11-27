<template>
  <div class="modal-overlay" :class="{ active: isOpen }" @click.self="close" data-lenis-prevent>
    <div class="modal-card contact-modal-card" data-lenis-prevent>
      <button class="modal-close" @click="close" aria-label="Close Modal"><i class="fa-solid fa-xmark"></i></button>
      
      <div class="modal-header-area">
        <h3 class="modal-title">Get in Touch with Us</h3>
        <p class="modal-sub">Fill out the form below and our VIZORA FOODS team will get back to you immediately.</p>
      </div>

      <!-- Set by a catalogue "Ask Now": names the card that opened the form. -->
      <p v-if="enquiryProduct" class="modal-product-note">
        <i class="fa-solid fa-box-open"></i>
        <span>Enquiry for <strong>{{ enquiryProduct }}</strong></span>
      </p>

      <form @submit.prevent="handleSubmit" novalidate class="modal-form">
        <!-- Row 1: Name & Phone -->
        <div class="form-row-2col">
          <div class="form-group">
            <label for="modalName">Full Name *</label>
            <input 
              v-model="form.name"
              @input="validateName"
              type="text" 
              id="modalName" 
              class="form-control" 
              :class="{ 'is-invalid': errors.name, 'is-valid': form.name && !errors.name }"
              placeholder="Enter your full name" 
              required
            />
            <span v-if="errors.name" class="field-error-msg">Valid name required (min 2 chars).</span>
          </div>

          <div class="form-group">
            <label for="modalPhone">Phone Number * (10 Digits)</label>
            <input 
              v-model="form.phone"
              @input="handlePhoneInput"
              type="tel" 
              id="modalPhone" 
              class="form-control" 
              :class="{ 'is-invalid': errors.phone, 'is-valid': form.phone && !errors.phone }"
              placeholder="10-digit mobile number" 
              maxlength="10"
              required
            />
            <span v-if="errors.phone" class="field-error-msg">Valid 10-digit mobile (starting 6-9).</span>
          </div>
        </div>

        <!-- Row 2: Email & Inquiry Type -->
        <div class="form-row-2col">
          <div class="form-group">
            <label for="modalEmail">Email Address <span class="optional-label">(Optional)</span></label>
            <input 
              v-model="form.email"
              type="email" 
              id="modalEmail" 
              class="form-control" 
              placeholder="name@domain.com"
            />
          </div>

          <div class="form-group">
            <label for="modalType">Inquiry Type</label>
            <SelectField
              ref="typeRef"
              v-model="form.inquiryType"
              id="modalType"
              :options="inquiryTypes"
              sheet-title="Inquiry Type"
            />
          </div>
        </div>

        <!-- Row 3: Company & Industry -->
        <div class="form-row-2col">
          <div class="form-group">
            <label for="modalCompany">Company Name <span class="optional-label">(Optional)</span></label>
            <input v-model="form.company" type="text" id="modalCompany" class="form-control" placeholder="Company / Business Name">
          </div>
          <div class="form-group">
            <label for="modalIndustry">Industry Name <span class="optional-label">(Optional)</span></label>
            <input v-model="form.industry" type="text" id="modalIndustry" class="form-control" placeholder="e.g. Retail, Supermarket, B2B">
          </div>
        </div>

        <!-- Row 4: Message -->
        <div class="form-group">
          <label for="modalMessage">Message / Product Request</label>
          <textarea v-model="form.message" id="modalMessage" class="form-control modal-textarea" rows="6" placeholder="Tell us what you are looking for..."></textarea>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn-primary modal-submit-btn">
          Submit Inquiry <i class="fa-solid fa-paper-plane"></i>
        </button>
      </form>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref, watch, onUnmounted } from 'vue';
import axios from 'axios';
import SelectField from './SelectField.vue';
import { useCustomAlert } from '../composables/useCustomAlert';

// Values must match InquiryRequest's allowed inquiry_type list.
const inquiryTypes = [
  { value: 'retail', label: 'Retail Store Inquiry' },
  { value: 'b2b', label: 'B2B Business Supply Inquiry' },
  { value: 'dealership', label: 'Brand / Dealership Inquiry' },
];

const { showSuccess, showError, showWarning } = useCustomAlert();

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false
  },
  // Set by the catalogue's "Ask Now" buttons:
  // { inquiryType, productName, message }.
  prefill: {
    type: Object,
    default: null
  }
});

const emit = defineEmits(['close']);

const form = reactive({
  name: '',
  phone: '',
  email: '',
  company: '',
  industry: '',
  inquiryType: 'retail',
  product_name: '',
  message: ''
});

const errors = reactive({
  name: false,
  phone: false
});

// Display only — the product also travels inside the message and product_name field that gets saved.
const enquiryProduct = ref('');

const typeRef = ref(null);

// Applied each time the modal opens rather than on the prop alone, so asking
// about a second product refills the form even if the previous answer is still
// sitting in it — but only the fields the caller owns.
watch(() => props.isOpen, (open) => {
  if (typeof document !== 'undefined') {
    if (open) {
      document.body.style.overflow = 'hidden';
    } else {
      document.body.style.overflow = '';
    }
  }

  // The overlay only fades out — it stays mounted — so a dropdown left open
  // would keep its teleported panel on screen after the modal disappeared.
  if (!open) {
    typeRef.value?.close();
    return;
  }

  if (props.prefill) {
    if (props.prefill.inquiryType) form.inquiryType = props.prefill.inquiryType;
    if ('message' in props.prefill) form.message = props.prefill.message ?? '';
    if ('productName' in props.prefill) {
      enquiryProduct.value = props.prefill.productName ?? '';
      form.product_name = props.prefill.productName ?? '';
    }
  } else {
    enquiryProduct.value = '';
    form.product_name = '';
  }
});

onUnmounted(() => {
  if (typeof document !== 'undefined') {
    document.body.style.overflow = '';
  }
});

function close() {
  emit('close');
}

// Format mobile phone number input
function handlePhoneInput() {
  form.phone = form.phone.replace(/\D/g, '').slice(0, 10);
  validatePhone();
}

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

function handleSubmit() {
  const isNameValid = validateName();
  const isPhoneValid = validatePhone();

  if (!isNameValid || !isPhoneValid) {
    showWarning('Please enter a valid Name and 10-digit Mobile Number starting with 6, 7, 8, or 9.', 'Validation Error');
    return;
  }

  axios.post('/inquiry', {
    name: form.name,
    phone: form.phone,
    email: form.email,
    company: form.company,
    industry: form.industry,
    inquiry_type: form.inquiryType,
    product_name: form.product_name || enquiryProduct.value || null,
    message: form.message
  })
  .then(response => {
    if (response.data.success) {
      showSuccess(response.data.message, 'Inquiry Submitted', {
        whatsappUrl: response.data.whatsapp_url,
      });

      form.name = '';
      form.phone = '';
      form.email = '';
      form.company = '';
      form.industry = '';
      form.product_name = '';
      form.message = '';
      enquiryProduct.value = '';
      errors.name = false;
      errors.phone = false;
      close();
    }
  })
  .catch(error => {
    console.error(error);
    showError('Something went wrong while submitting your inquiry. Please try again.', 'Submission Error');
  });
}
</script>

<style scoped>
.contact-modal-card {
  max-width: 580px;
  padding: 22px 24px;
}

.modal-header-area {
  padding-right: 28px;
}

.modal-title {
  font-family: var(--font-heading);
  font-size: 21px;
  font-weight: 800;
  color: var(--dark-navy);
  margin-bottom: 3px;
  line-height: 1.2;
}

.modal-sub {
  font-size: 12.5px;
  color: var(--text-muted);
  margin-bottom: 12px;
  line-height: 1.4;
}

.modal-product-note {
  display: flex;
  align-items: center;
  gap: 8px;
  background: #FDF3ED;
  border: 1px solid #F3D2BF;
  border-radius: 10px;
  padding: 7px 12px;
  margin: 0 0 10px 0;
  font-size: 12px;
  font-weight: 600;
  color: #8A4520;
  line-height: 1.35;
}

.modal-product-note i {
  color: #E4652E;
  font-size: 13px;
  flex-shrink: 0;
}

.modal-product-note strong {
  font-weight: 800;
  color: #061427;
}

.optional-label {
  font-weight: 400;
  color: #94A3B8;
  font-size: 10.5px;
}

.form-group {
  margin-bottom: 9px;
}

.form-group label {
  display: block;
  font-size: 11.5px;
  font-weight: 700;
  color: var(--dark-navy);
  margin-bottom: 4px;
}

.form-control {
  width: 100%;
  padding: 7px 11px;
  height: 38px;
  border: 1px solid var(--border-color);
  border-radius: 8px;
  font-family: inherit;
  font-size: 13px;
  outline: none;
  transition: var(--transition);
  box-sizing: border-box;
}

.form-control:focus {
  border-color: var(--primary-orange);
  box-shadow: 0 0 0 3px rgba(228, 101, 46, 0.15);
}

/* Tall enough to read what is already in it: a product enquiry arrives
   prefilled with the name, brand, pack size and code, which is five or six
   lines before the visitor has typed a word. */
.modal-textarea {
  height: 150px;
  min-height: 150px;
  resize: vertical;
  line-height: 1.5;
}

.field-error-msg {
  font-size: 10.5px;
  color: #ef4444;
  font-weight: 600;
  margin-top: 2px;
  display: block;
}

.form-row-2col {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
}

.modal-submit-btn {
  width: 100%;
  justify-content: center;
  margin-top: 4px;
  padding: 9px 18px;
  height: 40px;
  font-size: 13.5px;
}

@media (max-width: 576px) {
  .contact-modal-card {
    padding: 20px 16px 18px;
    width: calc(100% - 20px);
    max-width: 100%;
    /* vh first, dvh second: where dvh is supported it wins, and the card stops
       hiding behind the mobile browser's collapsing toolbar. */
    max-height: calc(100vh - 24px);
    max-height: calc(100dvh - 24px);
    border-radius: 18px;
  }

  /* The close icon alone was a ~20px tap target. Give it a real one and keep
     the heading clear of it. */
  .modal-close {
    top: 10px;
    right: 10px;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .modal-header-area {
    padding-right: 40px;
  }

  .modal-title {
    font-size: 19px;
  }

  .modal-sub {
    font-size: 12px;
    margin-bottom: 10px;
  }

  /* One field per row. Two columns left roughly 165px per field, which clipped
     every placeholder mid-word and pushed the select's arrow over its own
     text — the card scrolls, so the extra height costs nothing. */
  .form-row-2col {
    grid-template-columns: 1fr;
    gap: 0;
  }

  .form-group {
    margin-bottom: 11px;
  }

  .form-group label {
    font-size: 12px;
    margin-bottom: 5px;
  }

  /* 16px is the threshold: below it, iOS Safari zooms the whole page in on
     focus and the visitor has to pinch back out to reach the next field. */
  .form-control {
    height: 44px;
    padding: 10px 13px;
    font-size: 16px;
    border-radius: 10px;
  }

  /* Shorter than the desktop box only because the modal has to stay inside a
     phone screen alongside the rest of the form — still four times what it
     was. */
  .modal-textarea {
    height: 132px;
    min-height: 132px;
    padding: 10px 13px;
    font-size: 16px;
  }

  .field-error-msg {
    font-size: 11.5px;
    margin-top: 3px;
  }

  .optional-label {
    font-size: 11px;
  }

  .modal-submit-btn {
    height: 48px;
    font-size: 15px;
    margin-top: 6px;
  }

  .modal-product-note {
    padding: 8px 11px;
    font-size: 12px;
    margin-bottom: 10px;
  }
}
</style>
