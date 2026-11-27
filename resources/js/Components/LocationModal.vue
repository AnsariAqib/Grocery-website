<template>
  <transition name="fl-loc-fade">
    <div v-if="isOpen" class="fl-loc-backdrop" @click.self="$emit('close')">
      <div class="fl-loc-modal" role="dialog" aria-modal="true" aria-labelledby="flLocTitle">
        <div class="fl-loc-head">
          <div>
            <h3 id="flLocTitle">Select Delivery Location</h3>
            <p>Pan India Delivery &bull; Enter your city &amp; pincode or detect automatically.</p>
          </div>
          <button type="button" class="fl-loc-close" aria-label="Close" @click="$emit('close')">
            <i class="fa-solid fa-xmark"></i>
          </button>
        </div>

        <!-- Detect Current Location -->
        <button
          type="button"
          class="fl-detect-btn"
          :disabled="detecting"
          @click="detectLocation"
        >
          <i class="fa-solid" :class="detecting ? 'fa-spinner fa-spin' : 'fa-location-crosshairs'"></i>
          <span>{{ detecting ? 'Detecting your location...' : 'Use My Current Location' }}</span>
        </button>

        <p v-if="geoError" class="fl-geo-error">{{ geoError }}</p>

        <div class="fl-loc-divider">
          <span>OR ENTER MANUALLY</span>
        </div>

        <!-- Manual City & Pincode Form -->
        <form class="fl-loc-form" @submit.prevent="submitManual">
          <div class="fl-loc-inputs">
            <label class="fl-loc-field">
              <span>City *</span>
              <input v-model="cityInput" type="text" required placeholder="e.g. Bareilly">
            </label>
            <label class="fl-loc-field">
              <span>Pincode *</span>
              <input
                v-model="pincodeInput"
                type="text"
                required
                maxlength="6"
                pattern="[0-9]{6}"
                placeholder="6-digit pincode"
              >
            </label>
          </div>

          <button type="submit" class="fl-save-loc-btn">
            Save Delivery Location <i class="fa-solid fa-check"></i>
          </button>
        </form>

        <!-- Quick City Presets -->
        <div class="fl-loc-presets">
          <span class="fl-presets-label">Popular Cities:</span>
          <div class="fl-presets-row">
            <button
              v-for="preset in presets"
              :key="preset.pincode"
              type="button"
              class="fl-preset-chip"
              @click="pickPreset(preset)"
            >
              {{ preset.city }} ({{ preset.pincode }})
            </button>
          </div>
        </div>
      </div>
    </div>
  </transition>
</template>

<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
  isOpen: Boolean,
  currentCity: { type: String, default: '' },
  currentPincode: { type: String, default: '' },
});

const emit = defineEmits(['close', 'save']);

const cityInput = ref(props.currentCity || '');
const pincodeInput = ref(props.currentPincode || '');
const detecting = ref(false);
const geoError = ref('');

const presets = [
  { city: 'Bareilly', pincode: '243001' },
  { city: 'Delhi', pincode: '110001' },
  { city: 'Mumbai', pincode: '400001' },
  { city: 'Lucknow', pincode: '226001' },
];

watch(
  () => props.isOpen,
  (open) => {
    if (open) {
      cityInput.value = props.currentCity || '';
      pincodeInput.value = props.currentPincode || '';
      geoError.value = '';
    }
  },
);

function submitManual() {
  const city = cityInput.value.trim();
  const pincode = pincodeInput.value.trim();
  if (!city || !/^[0-9]{6}$/.test(pincode)) return;
  emit('save', { city, pincode });
  emit('close');
}

function pickPreset(preset) {
  emit('save', { city: preset.city, pincode: preset.pincode });
  emit('close');
}

function detectLocation() {
  geoError.value = '';
  if (!navigator.geolocation) {
    geoError.value = 'Geolocation is not supported by your browser. Please enter your city & pincode.';
    return;
  }

  detecting.value = true;
  navigator.geolocation.getCurrentPosition(
    async (pos) => {
      try {
        const { latitude, longitude } = pos.coords;
        const res = await fetch(
          `https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${latitude}&lon=${longitude}`,
        );
        const data = await res.json();
        const addr = data?.address || {};
        const city = addr.city || addr.town || addr.village || addr.state_district || addr.state || 'India';
        const pincode = String(addr.postcode || '').replace(/\D/g, '').slice(0, 6);

        cityInput.value = city;
        if (pincode.length === 6) {
          pincodeInput.value = pincode;
          emit('save', { city, pincode });
          emit('close');
        } else {
          geoError.value = `Detected ${city}. Please enter your 6-digit pincode below.`;
        }
      } catch {
        geoError.value = 'Could not fetch address details. Please enter your city & pincode manually.';
      } finally {
        detecting.value = false;
      }
    },
    (err) => {
      detecting.value = false;
      if (err.code === err.PERMISSION_DENIED) {
        geoError.value = 'Location permission denied. Please enter your city & pincode manually.';
      } else {
        geoError.value = 'Unable to detect location. Please enter your city & pincode manually.';
      }
    },
    { enableHighAccuracy: false, timeout: 10000 },
  );
}
</script>

<style scoped>
.fl-loc-backdrop {
  position: fixed;
  inset: 0;
  z-index: 2000;
  background: rgba(15, 46, 26, 0.55);
  backdrop-filter: blur(4px);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 16px;
  font-family: 'Plus Jakarta Sans', var(--font-main), sans-serif;
}

.fl-loc-modal {
  width: 100%;
  max-width: 440px;
  background: #FFFFFF;
  border-radius: 20px;
  padding: 24px;
  box-shadow: 0 24px 50px rgba(15, 46, 26, 0.22);
  border: 1px solid #DCECD3;
}

.fl-loc-head {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 12px;
  margin-bottom: 18px;
}

.fl-loc-head h3 {
  margin: 0 0 4px 0;
  font-size: 19px;
  font-weight: 800;
  color: #14532D;
}

.fl-loc-head p {
  margin: 0;
  font-size: 12.5px;
  color: #6B7280;
}

.fl-loc-close {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  border: 1px solid #E5ECE1;
  background: #F8FBF6;
  color: #374151;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.fl-detect-btn {
  width: 100%;
  height: 44px;
  border-radius: 9999px;
  border: 1.5px solid #3F9C1E;
  background: #EBF7E3;
  color: #14532D;
  font-family: inherit;
  font-size: 13.5px;
  font-weight: 700;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 9px;
  cursor: pointer;
  transition: background-color 0.2s ease;
}

.fl-detect-btn:hover:not(:disabled) {
  background: #DCF0CF;
}

.fl-detect-btn:disabled {
  opacity: 0.7;
  cursor: wait;
}

.fl-geo-error {
  margin: 10px 0 0;
  font-size: 12px;
  font-weight: 600;
  color: #DC2626;
  text-align: center;
}

.fl-loc-divider {
  display: flex;
  align-items: center;
  gap: 12px;
  margin: 18px 0;
  color: #9CA3AF;
  font-size: 10.5px;
  font-weight: 800;
  letter-spacing: 0.08em;
}

.fl-loc-divider::before,
.fl-loc-divider::after {
  content: '';
  flex: 1;
  height: 1px;
  background: #E5ECE1;
}

.fl-loc-inputs {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
  margin-bottom: 14px;
}

.fl-loc-field {
  display: flex;
  flex-direction: column;
  gap: 5px;
}

.fl-loc-field span {
  font-size: 12px;
  font-weight: 700;
  color: #374151;
}

.fl-loc-field input {
  height: 40px;
  border: 1.5px solid #D5E4CE;
  border-radius: 10px;
  padding: 0 12px;
  font-size: 13.5px;
  font-family: inherit;
  color: #111827;
  outline: none;
}

.fl-loc-field input:focus {
  border-color: #14532D;
}

.fl-save-loc-btn {
  width: 100%;
  height: 44px;
  border-radius: 9999px;
  border: none;
  background: #14532D;
  color: #FFFFFF;
  font-family: inherit;
  font-size: 14px;
  font-weight: 700;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  cursor: pointer;
}

.fl-save-loc-btn:hover {
  background: #0F3A1D;
}

.fl-loc-presets {
  margin-top: 16px;
  padding-top: 14px;
  border-top: 1px solid #EEF2EC;
}

.fl-presets-label {
  display: block;
  font-size: 11.5px;
  font-weight: 700;
  color: #6B7280;
  margin-bottom: 8px;
}

.fl-presets-row {
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
}

.fl-preset-chip {
  background: #F4FAF0;
  border: 1px solid #DCECD3;
  color: #14532D;
  border-radius: 9999px;
  padding: 5px 11px;
  font-size: 11.5px;
  font-weight: 600;
  font-family: inherit;
  cursor: pointer;
}

.fl-preset-chip:hover {
  background: #E2F4D3;
}

.fl-loc-fade-enter-active,
.fl-loc-fade-leave-active {
  transition: opacity 0.2s ease;
}

.fl-loc-fade-enter-from,
.fl-loc-fade-leave-to {
  opacity: 0;
}
</style>

