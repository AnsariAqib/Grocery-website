<template>
  <form class="bulk-inquiry-form" @submit.prevent="handleSubmit" novalidate>
    <!-- Row 1: 4 Inputs -->
    <div class="bulk-form-row-4">
      <input 
        v-model="form.name" 
        @input="validateName" 
        @blur="validateName"
        type="text" 
        class="bulk-input" 
        :class="{ 'is-invalid': errors.name, 'is-valid': form.name && !errors.name }" 
        placeholder="Full Name*" 
        required 
      />
      <input 
        v-model="form.phone" 
        @input="handlePhoneInput" 
        @blur="validatePhone"
        type="tel" 
        class="bulk-input" 
        :class="{ 'is-invalid': errors.phone, 'is-valid': form.phone && !errors.phone }" 
        placeholder="Mobile Number (10 Digits)*" 
        maxlength="10" 
        required 
      />
      <input 
        v-model="form.email" 
        type="email" 
        class="bulk-input" 
        placeholder="Email Address (Optional)" 
      />
      <input 
        v-model="form.company" 
        type="text" 
        class="bulk-input" 
        placeholder="Company Name (Optional)" 
      />
    </div>

    <!-- Row 2: 4 Inputs / Elements -->
    <div class="bulk-form-row-4">
      <input 
        v-model="form.industry" 
        type="text" 
        class="bulk-input" 
        placeholder="Industry Name (Optional)" 
      />
      <select v-model="form.category" class="bulk-select" required>
        <option value="" disabled selected>Requirement Category*</option>
        <option value="food-canteen">Food Service & Canteen</option>
        <option value="housekeeping">Housekeeping & Hygiene</option>
        <option value="office-supplies">Office Supplies & Stationery</option>
        <option value="office-furniture">Office Furniture</option>
        <option value="electrical">Electrical & Lighting</option>
        <option value="beverages">Beverages & Packaged Food</option>
        <option value="other">Other Commercial Need</option>
      </select>
      <input 
        v-model="form.message" 
        type="text" 
        class="bulk-input" 
        placeholder="Requirement Details / Message*" 
        required 
      />
      <button type="submit" class="btn-bulk-submit" style="height:42px; width:100%; border-radius:8px;">
        Submit Inquiry <i class="fa-solid fa-arrow-right"></i>
      </button>
    </div>
  </form>
</template>

<script setup>
import { reactive } from 'vue';
import axios from 'axios';
import { useCustomAlert } from '../composables/useCustomAlert';

const { showSuccess, showError, showWarning } = useCustomAlert();

const form = reactive({
  name: '',
  phone: '',
  email: '',
  company: '',
  industry: '',
  category: '',
  message: ''
});

const errors = reactive({
  name: false,
  phone: false
});

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

  if (!isNameValid || !isPhoneValid || !form.message.trim()) {
    showWarning('Please enter a valid Name, 10-digit Mobile Number starting with 6-9, and Requirement Details.', 'Validation Error');
    return;
  }

  axios.post('/inquiry', {
    name: form.name,
    phone: form.phone,
    email: form.email,
    company: form.company,
    industry: form.industry,
    inquiry_type: 'b2b',
    category: form.category,
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
      form.category = '';
      form.message = '';
      errors.name = false;
      errors.phone = false;
    }
  })
  .catch(error => {
    console.error(error);
    showError('Something went wrong while submitting your inquiry. Please try again.', 'Submission Error');
  });
}
</script>
