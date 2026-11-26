import { ref, reactive } from 'vue';

const isVisible = ref(false);
const alertData = reactive({
  title: '',
  message: '',
  type: 'success', // 'success' | 'error' | 'warning' | 'info'
  confirmText: 'OK',
  cancelText: 'Close',
  showCancel: false,
  whatsappUrl: null,
  autoCloseMs: null,
  onConfirm: null,
  onCancel: null,
});

let timer = null;

export function redirectToWhatsApp(url) {
  if (!url) return;

  // Detect mobile device (Android / iOS / Tablets / Touch screens)
  const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)
    || (window.innerWidth <= 768 && ('ontouchstart' in window || (navigator.maxTouchPoints && navigator.maxTouchPoints > 0)));

  if (isMobile) {
    // On Mobile: Directly open WhatsApp App via wa.me link
    let mobileUrl = url;
    try {
      const parsed = new URL(url);
      const phone = (parsed.searchParams.get('phone') || '').replace(/\D/g, '');
      const text = parsed.searchParams.get('text') || '';
      mobileUrl = `https://wa.me/${phone}?text=${encodeURIComponent(text)}`;
    } catch (e) {
      mobileUrl = url;
    }
    window.location.href = mobileUrl;
  } else {
    // On Desktop: Open WhatsApp Web in a NEW TAB so user doesn't lose current page
    window.open(url, '_blank', 'noopener,noreferrer');
  }
}

export function useCustomAlert() {
  function showAlert({
    title = '',
    message = '',
    type = 'info',
    confirmText = 'Got it',
    cancelText = 'Close',
    showCancel = false,
    whatsappUrl = null,
    autoCloseMs = null,
    onConfirm = null,
    onCancel = null,
  }) {
    if (timer) {
      clearTimeout(timer);
      timer = null;
    }

    alertData.title = title || (
      type === 'success' ? 'Success!' :
      type === 'error' ? 'Oops! Something went wrong' :
      type === 'warning' ? 'Attention Required' : 'Information'
    );
    alertData.message = message;
    alertData.type = type;
    alertData.confirmText = confirmText;
    alertData.cancelText = cancelText;
    alertData.showCancel = showCancel;
    alertData.whatsappUrl = whatsappUrl;
    alertData.autoCloseMs = autoCloseMs;
    alertData.onConfirm = onConfirm;
    alertData.onCancel = onCancel;

    isVisible.value = true;

    if (autoCloseMs && autoCloseMs > 0) {
      timer = setTimeout(() => {
        handleConfirm();
      }, autoCloseMs);
    }
  }

  function handleConfirm() {
    if (timer) {
      clearTimeout(timer);
      timer = null;
    }
    const url = alertData.whatsappUrl;
    const callback = alertData.onConfirm;
    isVisible.value = false;

    if (callback && typeof callback === 'function') {
      callback();
    }

    if (url) {
      redirectToWhatsApp(url);
    }
  }

  function handleCancel() {
    if (timer) {
      clearTimeout(timer);
      timer = null;
    }
    const callback = alertData.onCancel;
    isVisible.value = false;

    if (callback && typeof callback === 'function') {
      callback();
    }
  }

  function closeAlert() {
    if (timer) {
      clearTimeout(timer);
      timer = null;
    }
    isVisible.value = false;
  }

  function showSuccess(message, title = 'Success!', options = {}) {
    showAlert({
      title,
      message,
      type: 'success',
      confirmText: options.whatsappUrl ? 'Open WhatsApp 💬' : 'Got it',
      showCancel: Boolean(options.whatsappUrl),
      cancelText: 'Stay Here',
      autoCloseMs: options.whatsappUrl ? 3500 : null,
      ...options,
    });
  }

  function showError(message, title = 'Error', options = {}) {
    showAlert({
      title,
      message,
      type: 'error',
      confirmText: 'Okay',
      showCancel: false,
      ...options,
    });
  }

  function showWarning(message, title = 'Please Note', options = {}) {
    showAlert({
      title,
      message,
      type: 'warning',
      confirmText: 'Understood',
      showCancel: false,
      ...options,
    });
  }

  function showInfo(message, title = 'Notice', options = {}) {
    showAlert({
      title,
      message,
      type: 'info',
      confirmText: 'OK',
      showCancel: false,
      ...options,
    });
  }

  return {
    isVisible,
    alertData,
    showAlert,
    showSuccess,
    showError,
    showWarning,
    showInfo,
    handleConfirm,
    handleCancel,
    closeAlert,
  };
}
