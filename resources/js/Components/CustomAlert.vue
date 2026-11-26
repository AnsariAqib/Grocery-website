<template>
  <Transition name="custom-alert-fade">
    <div
      v-if="isVisible"
      class="custom-alert-overlay"
      @click.self="handleOverlayClick"
      tabindex="-1"
      @keydown.esc="closeAlert"
    >
      <div class="custom-alert-dialog" role="dialog" aria-modal="true">
        <!-- Top countdown progress bar if autoClose is active -->
        <div v-if="alertData.whatsappUrl && alertData.autoCloseMs" class="custom-alert-progress-track">
          <div class="custom-alert-progress-bar" :style="{ animationDuration: `${alertData.autoCloseMs}ms` }"></div>
        </div>

        <!-- Close button -->
        <button
          type="button"
          class="custom-alert-close-btn"
          @click="closeAlert"
          aria-label="Close modal"
        >
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>

        <!-- Icon Area with Gradient Glow -->
        <div class="custom-alert-icon-wrapper" :class="`icon-${alertData.type}`">
          <!-- WhatsApp Icon -->
          <template v-if="alertData.type === 'success' && alertData.whatsappUrl">
            <div class="whatsapp-icon-circle">
              <svg width="34" height="34" viewBox="0 0 24 24" fill="currentColor">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.885-9.884 9.885m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
              </svg>
            </div>
          </template>

          <!-- Standard Success Icon -->
          <template v-else-if="alertData.type === 'success'">
            <div class="status-icon-circle success-circle">
              <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="20 6 9 17 4 12"></polyline>
              </svg>
            </div>
          </template>

          <!-- Error Icon -->
          <template v-else-if="alertData.type === 'error'">
            <div class="status-icon-circle error-circle">
              <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
              </svg>
            </div>
          </template>

          <!-- Warning Icon -->
          <template v-else-if="alertData.type === 'warning'">
            <div class="status-icon-circle warning-circle">
              <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                <line x1="12" y1="9" x2="12" y2="13"></line>
                <line x1="12" y1="17" x2="12.01" y2="17"></line>
              </svg>
            </div>
          </template>

          <!-- Info Icon -->
          <template v-else>
            <div class="status-icon-circle info-circle">
              <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="16" x2="12" y2="12"></line>
                <line x1="12" y1="8" x2="12.01" y2="8"></line>
              </svg>
            </div>
          </template>
        </div>

        <!-- Content Body -->
        <div class="custom-alert-body">
          <h3 class="custom-alert-title">{{ alertData.title }}</h3>
          <p class="custom-alert-message">{{ alertData.message }}</p>
          
          <div v-if="alertData.whatsappUrl" class="custom-alert-redirect-pill">
            <span class="pulse-live-dot"></span>
            <span>Opening WhatsApp in a moment...</span>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="custom-alert-actions" :class="{ 'has-cancel': alertData.showCancel }">
          <button
            v-if="alertData.showCancel"
            type="button"
            class="btn-alert-secondary"
            @click="handleCancel"
          >
            {{ alertData.cancelText || 'Stay Here' }}
          </button>

          <button
            type="button"
            class="btn-alert-primary"
            :class="`btn-${alertData.type}`"
            @click="handleConfirm"
          >
            <span v-if="alertData.whatsappUrl" class="btn-wa-icon">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.945C.16 5.335 5.495 0 12.05 0a11.815 11.815 0 018.413 3.488A11.821 11.821 0 0123.943 11.9c-.003 6.558-5.339 11.893-11.893 11.893h-.005a11.882 11.882 0 01-5.683-1.448L.057 24z" />
              </svg>
            </span>
            <span>{{ alertData.confirmText || 'Got it' }}</span>
          </button>
        </div>
      </div>
    </div>
  </Transition>
</template>

<script setup>
import { useCustomAlert } from '../composables/useCustomAlert';

const {
  isVisible,
  alertData,
  handleConfirm,
  handleCancel,
  closeAlert,
} = useCustomAlert();

function handleOverlayClick() {
  if (!alertData.whatsappUrl) {
    closeAlert();
  }
}
</script>

<style scoped>
.custom-alert-overlay {
  position: fixed;
  inset: 0;
  z-index: 999999;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 16px;
  background-color: rgba(6, 20, 39, 0.72);
  backdrop-filter: blur(8px);
  -webkit-backdrop-filter: blur(8px);
}

.custom-alert-dialog {
  position: relative;
  width: 100%;
  max-width: 440px;
  background: #ffffff;
  border-radius: 24px;
  padding: 28px 24px 22px;
  text-align: center;
  box-shadow: 0 25px 60px -10px rgba(6, 20, 39, 0.35), 0 0 0 1px rgba(255, 255, 255, 0.1);
  transform: translateY(0);
  transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
  overflow: hidden;
}

/* Progress bar at top */
.custom-alert-progress-track {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
  background: #E2E8F0;
  overflow: hidden;
}

.custom-alert-progress-bar {
  height: 100%;
  width: 100%;
  background: linear-gradient(90deg, #10B981, #25D366);
  transform-origin: left center;
  animation: progressBarDrain linear forwards;
}

@keyframes progressBarDrain {
  0% { transform: scaleX(1); }
  100% { transform: scaleX(0); }
}

/* Close button */
.custom-alert-close-btn {
  position: absolute;
  top: 14px;
  right: 14px;
  width: 32px;
  height: 32px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #F1F5F9;
  color: #64748B;
  border: none;
  cursor: pointer;
  transition: all 0.2s ease;
  z-index: 10;
}

.custom-alert-close-btn:hover {
  background: #E2E8F0;
  color: #0F172A;
  transform: scale(1.08);
}

/* Icon Area */
.custom-alert-icon-wrapper {
  margin: 0 auto 16px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.whatsapp-icon-circle {
  width: 68px;
  height: 68px;
  border-radius: 50%;
  background: linear-gradient(135deg, #25D366, #128C7E);
  color: #ffffff;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 10px 25px rgba(37, 211, 102, 0.38), 0 0 0 6px rgba(37, 211, 102, 0.15);
  animation: pulseIcon 2.5s infinite ease-in-out;
}

.status-icon-circle {
  width: 64px;
  height: 64px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.success-circle {
  background: linear-gradient(135deg, #10B981, #059669);
  color: #ffffff;
  box-shadow: 0 10px 25px rgba(16, 185, 129, 0.35), 0 0 0 6px rgba(16, 185, 129, 0.15);
}

.error-circle {
  background: linear-gradient(135deg, #EF4444, #DC2626);
  color: #ffffff;
  box-shadow: 0 10px 25px rgba(239, 68, 68, 0.35), 0 0 0 6px rgba(239, 68, 68, 0.15);
}

.warning-circle {
  background: linear-gradient(135deg, #F59E0B, #D97706);
  color: #ffffff;
  box-shadow: 0 10px 25px rgba(245, 158, 11, 0.35), 0 0 0 6px rgba(245, 158, 11, 0.15);
}

.info-circle {
  background: linear-gradient(135deg, #0EA5E9, #0284C7);
  color: #ffffff;
  box-shadow: 0 10px 25px rgba(14, 165, 233, 0.35), 0 0 0 6px rgba(14, 165, 233, 0.15);
}

@keyframes pulseIcon {
  0%, 100% {
    transform: scale(1);
    box-shadow: 0 10px 25px rgba(37, 211, 102, 0.38), 0 0 0 6px rgba(37, 211, 102, 0.15);
  }
  50% {
    transform: scale(1.04);
    box-shadow: 0 14px 30px rgba(37, 211, 102, 0.5), 0 0 0 10px rgba(37, 211, 102, 0.22);
  }
}

/* Typography */
.custom-alert-body {
  margin-bottom: 22px;
}

.custom-alert-title {
  font-family: 'Outfit', 'Plus Jakarta Sans', sans-serif;
  font-size: 21px;
  font-weight: 800;
  color: #061427;
  margin: 0 0 8px 0;
  line-height: 1.25;
  letter-spacing: -0.3px;
}

.custom-alert-message {
  font-size: 13.5px;
  color: #475569;
  line-height: 1.55;
  margin: 0;
  padding: 0 8px;
}

/* Auto redirect status badge */
.custom-alert-redirect-pill {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  margin-top: 14px;
  padding: 6px 14px;
  background: #ECFDF5;
  border: 1px solid #A7F3D0;
  border-radius: 999px;
  font-size: 12px;
  font-weight: 700;
  color: #047857;
}

.pulse-live-dot {
  width: 7px;
  height: 7px;
  border-radius: 50%;
  background: #10B981;
  box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
  animation: liveDotPulse 1.5s infinite;
}

@keyframes liveDotPulse {
  0% {
    transform: scale(0.95);
    box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
  }
  70% {
    transform: scale(1);
    box-shadow: 0 0 0 6px rgba(16, 185, 129, 0);
  }
  100% {
    transform: scale(0.95);
    box-shadow: 0 0 0 0 rgba(16, 185, 129, 0);
  }
}

/* Action Buttons */
.custom-alert-actions {
  display: grid;
  grid-template-columns: 1fr;
  gap: 10px;
}

.custom-alert-actions.has-cancel {
  grid-template-columns: 1fr 1.35fr;
}

.btn-alert-primary {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  height: 44px;
  padding: 0 18px;
  border-radius: 12px;
  font-size: 14px;
  font-weight: 700;
  color: #ffffff;
  border: none;
  cursor: pointer;
  transition: all 0.25s ease;
  background: linear-gradient(135deg, #25D366, #128C7E);
  box-shadow: 0 6px 18px rgba(37, 211, 102, 0.35);
}

.btn-alert-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 24px rgba(37, 211, 102, 0.45);
  filter: brightness(1.05);
}

.btn-alert-primary.btn-error {
  background: linear-gradient(135deg, #EF4444, #DC2626);
  box-shadow: 0 6px 18px rgba(239, 68, 68, 0.35);
}

.btn-alert-primary.btn-warning {
  background: linear-gradient(135deg, #F59E0B, #D97706);
  box-shadow: 0 6px 18px rgba(245, 158, 11, 0.35);
}

.btn-alert-primary.btn-info {
  background: linear-gradient(135deg, #0EA5E9, #0284C7);
  box-shadow: 0 6px 18px rgba(14, 165, 233, 0.35);
}

.btn-wa-icon {
  display: flex;
  align-items: center;
  justify-content: center;
}

.btn-alert-secondary {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  height: 44px;
  padding: 0 14px;
  border-radius: 12px;
  font-size: 13.5px;
  font-weight: 700;
  color: #475569;
  background: #F8FAFC;
  border: 1.5px solid #E2E8F0;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-alert-secondary:hover {
  background: #F1F5F9;
  color: #0F172A;
  border-color: #CBD5E1;
}

/* Transitions */
.custom-alert-fade-enter-active,
.custom-alert-fade-leave-active {
  transition: opacity 0.25s ease;
}

.custom-alert-fade-enter-active .custom-alert-dialog {
  animation: alertModalIn 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

.custom-alert-fade-leave-active .custom-alert-dialog {
  animation: alertModalOut 0.2s ease forwards;
}

.custom-alert-fade-enter-from,
.custom-alert-fade-leave-to {
  opacity: 0;
}

@keyframes alertModalIn {
  0% {
    opacity: 0;
    transform: scale(0.92) translateY(16px);
  }
  100% {
    opacity: 1;
    transform: scale(1) translateY(0);
  }
}

@keyframes alertModalOut {
  0% {
    opacity: 1;
    transform: scale(1);
  }
  100% {
    opacity: 0;
    transform: scale(0.95);
  }
}

@media (max-width: 480px) {
  .custom-alert-dialog {
    padding: 24px 18px 18px;
    border-radius: 20px;
  }

  .custom-alert-title {
    font-size: 19px;
  }

  .custom-alert-message {
    font-size: 13px;
    padding: 0;
  }

  .custom-alert-actions.has-cancel {
    grid-template-columns: 1fr;
    gap: 8px;
  }

  .btn-alert-primary,
  .btn-alert-secondary {
    height: 42px;
    font-size: 13.5px;
    width: 100%;
  }
}
</style>
