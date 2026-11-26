/* ==========================================================================
   JAY TULSI AGENCY - INTERACTIVE JAVASCRIPT
   Modal Controllers, Sliders, Category Filters, and Form Submissions
   ========================================================================== */

document.addEventListener('DOMContentLoaded', () => {
  // 1. Quick Contact Modal Handlers
  const modalOverlay = document.getElementById('contactModal');
  const openModalBtns = document.querySelectorAll('.open-contact-modal');
  const closeModalBtn = document.getElementById('closeModalBtn');
  const contactForm = document.getElementById('quickContactForm');

  function openModal() {
    if (modalOverlay) {
      modalOverlay.classList.add('active');
      document.body.style.overflow = 'hidden';
    }
  }

  function closeModal() {
    if (modalOverlay) {
      modalOverlay.classList.remove('active');
      document.body.style.overflow = '';
    }
  }

  openModalBtns.forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      openModal();
    });
  });

  if (closeModalBtn) {
    closeModalBtn.addEventListener('click', closeModal);
  }

  if (modalOverlay) {
    modalOverlay.addEventListener('click', (e) => {
      if (e.target === modalOverlay) closeModal();
    });
  }

  // Live Validation Handlers for Contact Form
  const modalNameInput = document.getElementById('modalName');
  const modalPhoneInput = document.getElementById('modalPhone');
  const modalEmailInput = document.getElementById('modalEmail');
  const nameError = document.getElementById('nameError');
  const phoneError = document.getElementById('phoneError');
  const emailError = document.getElementById('emailError');

  // Enforce numbers-only on 10-digit phone input
  if (modalPhoneInput) {
    modalPhoneInput.addEventListener('input', (e) => {
      e.target.value = e.target.value.replace(/\D/g, '').slice(0, 10);
      validatePhone();
    });
    modalPhoneInput.addEventListener('blur', validatePhone);
  }

  if (modalNameInput) {
    modalNameInput.addEventListener('input', validateName);
    modalNameInput.addEventListener('blur', validateName);
  }

  if (modalEmailInput) {
    modalEmailInput.addEventListener('input', validateEmail);
    modalEmailInput.addEventListener('blur', validateEmail);
  }

  function validateName() {
    if (!modalNameInput) return true;
    const val = modalNameInput.value.trim();
    const nameRegex = /^[a-zA-Z\s]{2,}$/;
    if (!val || !nameRegex.test(val)) {
      modalNameInput.classList.add('is-invalid');
      modalNameInput.classList.remove('is-valid');
      if (nameError) nameError.style.display = 'block';
      return false;
    } else {
      modalNameInput.classList.remove('is-invalid');
      modalNameInput.classList.add('is-valid');
      if (nameError) nameError.style.display = 'none';
      return true;
    }
  }

  function validatePhone() {
    if (!modalPhoneInput) return true;
    const val = modalPhoneInput.value.trim();
    const phoneRegex = /^[6-9]\d{9}$/;
    if (!val || !phoneRegex.test(val)) {
      modalPhoneInput.classList.add('is-invalid');
      modalPhoneInput.classList.remove('is-valid');
      if (phoneError) phoneError.style.display = 'block';
      return false;
    } else {
      modalPhoneInput.classList.remove('is-invalid');
      modalPhoneInput.classList.add('is-valid');
      if (phoneError) phoneError.style.display = 'none';
      return true;
    }
  }

  function validateEmail() {
    if (!modalEmailInput) return true;
    const val = modalEmailInput.value.trim();
    if (!val) {
      modalEmailInput.classList.remove('is-invalid', 'is-valid');
      if (emailError) emailError.style.display = 'none';
      return true;
    }
    const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    if (!emailRegex.test(val)) {
      modalEmailInput.classList.add('is-invalid');
      modalEmailInput.classList.remove('is-valid');
      if (emailError) emailError.style.display = 'block';
      return false;
    } else {
      modalEmailInput.classList.remove('is-invalid');
      modalEmailInput.classList.add('is-valid');
      if (emailError) emailError.style.display = 'none';
      return true;
    }
  }

  if (contactForm) {
    contactForm.addEventListener('submit', (e) => {
      e.preventDefault();

      const isNameValid = validateName();
      const isPhoneValid = validatePhone();
      const isEmailValid = validateEmail();

      if (!isNameValid || !isPhoneValid || !isEmailValid) {
        showNotification('Please enter a valid Name and 10-digit Mobile Number (starting 6-9).');
        if (!isNameValid) modalNameInput.focus();
        else if (!isPhoneValid) modalPhoneInput.focus();
        return;
      }

      const name = modalNameInput.value.trim();
      const phone = modalPhoneInput.value.trim();
      const company = document.getElementById('modalCompany')?.value || '';
      
      const compText = company ? ` (${company})` : '';
      alert(`Thank you, ${name}${compText}! Your inquiry has been received. Our team will call you back shortly at ${phone}.`);
      
      contactForm.reset();
      [modalNameInput, modalPhoneInput, modalEmailInput].forEach(el => {
        if (el) el.classList.remove('is-valid', 'is-invalid');
      });
      closeModal();
    });
  }

  // Live Validation Handlers for Bulk Supply Banner Form
  const bulkForm = document.getElementById('bulkInquiryForm');
  const bulkNameInput = document.getElementById('bulkName');
  const bulkPhoneInput = document.getElementById('bulkPhone');
  const bulkEmailInput = document.getElementById('bulkEmail');
  const bulkMessageInput = document.getElementById('bulkMessage');

  if (bulkPhoneInput) {
    bulkPhoneInput.addEventListener('input', (e) => {
      e.target.value = e.target.value.replace(/\D/g, '').slice(0, 10);
      validateBulkPhone();
    });
    bulkPhoneInput.addEventListener('blur', validateBulkPhone);
  }

  if (bulkNameInput) {
    bulkNameInput.addEventListener('input', validateBulkName);
    bulkNameInput.addEventListener('blur', validateBulkName);
  }

  function validateBulkName() {
    if (!bulkNameInput) return true;
    const val = bulkNameInput.value.trim();
    const nameRegex = /^[a-zA-Z\s]{2,}$/;
    if (!val || !nameRegex.test(val)) {
      bulkNameInput.classList.add('is-invalid');
      bulkNameInput.classList.remove('is-valid');
      return false;
    } else {
      bulkNameInput.classList.remove('is-invalid');
      bulkNameInput.classList.add('is-valid');
      return true;
    }
  }

  function validateBulkPhone() {
    if (!bulkPhoneInput) return true;
    const val = bulkPhoneInput.value.trim();
    const phoneRegex = /^[6-9]\d{9}$/;
    if (!val || !phoneRegex.test(val)) {
      bulkPhoneInput.classList.add('is-invalid');
      bulkPhoneInput.classList.remove('is-valid');
      return false;
    } else {
      bulkPhoneInput.classList.remove('is-invalid');
      bulkPhoneInput.classList.add('is-valid');
      return true;
    }
  }

  if (bulkForm) {
    bulkForm.addEventListener('submit', (e) => {
      e.preventDefault();

      const isNameValid = validateBulkName();
      const isPhoneValid = validateBulkPhone();
      const messageVal = bulkMessageInput ? bulkMessageInput.value.trim() : '';

      if (!isNameValid || !isPhoneValid || !messageVal) {
        showNotification('Please enter a valid Name, 10-digit Mobile Number and Requirement Details.');
        if (!isNameValid) bulkNameInput.focus();
        else if (!isPhoneValid) bulkPhoneInput.focus();
        else if (bulkMessageInput) bulkMessageInput.focus();
        return;
      }

      const name = bulkNameInput.value.trim();
      const phone = bulkPhoneInput.value.trim();
      const company = document.getElementById('bulkCompany')?.value || '';
      const compText = company ? ` (${company})` : '';

      alert(`Thank you, ${name}${compText}! Your bulk supply inquiry has been received. Our corporate team will contact you at ${phone}.`);
      
      bulkForm.reset();
      [bulkNameInput, bulkPhoneInput, bulkEmailInput].forEach(el => {
        if (el) el.classList.remove('is-valid', 'is-invalid');
      });
    });
  }

  // Auto Popup Quick Query / Contact Form after 3 seconds of opening website
  setTimeout(() => {
    const activeModal = document.querySelector('.modal-overlay.active');
    if (!activeModal && modalOverlay) {
      openModal();
    }
  }, 3000);

  // 2. Catalogue Download Handlers
  const downloadBtns = document.querySelectorAll('.download-btn');
  downloadBtns.forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      const catType = btn.textContent.trim();
      
      // Create temporary alert/toast
      showNotification(`Downloading ${catType}... Please wait a moment.`);
    });
  });

  // 3. Hero Carousel & Animated Text/Button Switcher
  const heroPrevBtn = document.querySelector('.slider-arrow.prev');
  const heroNextBtn = document.querySelector('.slider-arrow.next');
  const heroImg = document.getElementById('heroImg');
  const heroContent = document.querySelector('.hero-content');
  const heroTitle = document.querySelector('.hero-title');
  const heroSubtext = document.querySelector('.hero-subtext');
  const heroBtnPrimary = document.querySelector('.hero-cta-group .btn-primary');
  const heroBtnNavy = document.querySelector('.hero-cta-group .btn-navy');

  const heroSlides = [
    {
      image: 'https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=1400&q=80',
      title: 'Your Trusted Partner in <br><span class="hero-title-gradient shiny-text">Retail & B2B Supply</span> <br>Solutions',
      subtext: 'Delivering quality products, trusted brands and reliable service across our stores and business supply network.',
      btnPrimaryText: 'Explore Store Categories <i class="fa-solid fa-arrow-right"></i>',
      btnPrimaryLink: '#categories',
      btnNavyText: 'Explore B2B Catalogue <i class="fa-solid fa-arrow-right"></i>',
      btnNavyLink: '#b2b'
    },
    {
      image: 'https://images.unsplash.com/photo-1578916171728-46686eac8d58?auto=format&fit=crop&w=1400&q=80',
      title: 'Premium Supermarket <br><span class="hero-title-gradient shiny-text">Groceries & Daily Staples</span> <br>For Every Home',
      subtext: 'Discover 1000+ top-rated household items, fresh produce, and daily essential products at best wholesale prices.',
      btnPrimaryText: 'Shop Retail Items <i class="fa-solid fa-arrow-right"></i>',
      btnPrimaryLink: '#categories',
      btnNavyText: 'View Store Locations <i class="fa-solid fa-arrow-right"></i>',
      btnNavyLink: '#stores'
    },
    {
      image: 'https://images.unsplash.com/photo-1604719312566-8912e9227c6a?auto=format&fit=crop&w=1400&q=80',
      title: 'Bulk Commercial Supply <br><span class="hero-title-gradient shiny-text">Corporate & Business Solutions</span> <br>Direct To Facility',
      subtext: 'Streamline your enterprise procurement with dedicated B2B pricing, flexible credit terms, and fast express dispatch.',
      btnPrimaryText: 'Explore B2B Catalogue <i class="fa-solid fa-arrow-right"></i>',
      btnPrimaryLink: '#b2b',
      btnNavyText: 'Get Instant Quote <i class="fa-solid fa-headset"></i>',
      btnNavyLink: '#contact'
    },
    {
      image: 'https://images.unsplash.com/photo-1588964895597-cfccd6e2dbf9?auto=format&fit=crop&w=1400&q=80',
      title: 'Authorised Distributors <br><span class="hero-title-gradient shiny-text">Top National Brands</span> <br>& Direct Network',
      subtext: 'Partnering with Amul, Colgate, Dabur, Godrej, Parle and leading FMCG manufacturers across Surat & Gujarat.',
      btnPrimaryText: 'View Authorised Brands <i class="fa-solid fa-arrow-right"></i>',
      btnPrimaryLink: '#dealers',
      btnNavyText: 'Contact Sales Team <i class="fa-solid fa-arrow-right"></i>',
      btnNavyLink: '#contact'
    }
  ];

  let currentHeroIdx = 0;

  function switchHeroSlide(next = true) {
    if (!heroImg || !heroContent) return;

    if (next) {
      currentHeroIdx = (currentHeroIdx + 1) % heroSlides.length;
    } else {
      currentHeroIdx = (currentHeroIdx - 1 + heroSlides.length) % heroSlides.length;
    }

    const slide = heroSlides[currentHeroIdx];

    // Fade out image and text content smoothly
    heroImg.style.opacity = '0.2';
    heroContent.classList.add('changing');

    setTimeout(() => {
      // Update background image
      heroImg.src = slide.image;
      heroImg.style.opacity = '1';

      // Update text title with shiny-text class
      if (heroTitle) heroTitle.innerHTML = slide.title;
      if (heroSubtext) heroSubtext.textContent = slide.subtext;

      // Update action button texts & links
      if (heroBtnPrimary) {
        heroBtnPrimary.innerHTML = slide.btnPrimaryText;
        heroBtnPrimary.setAttribute('href', slide.btnPrimaryLink);
      }
      if (heroBtnNavy) {
        heroBtnNavy.innerHTML = slide.btnNavyText;
        heroBtnNavy.setAttribute('href', slide.btnNavyLink);
      }

      // Animate text back in smoothly
      heroContent.classList.remove('changing');
    }, 220);
  }

  if (heroNextBtn) {
    heroNextBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      switchHeroSlide(true);
    });
  }

  if (heroPrevBtn) {
    heroPrevBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      switchHeroSlide(false);
    });
  }

  // Auto slide hero every 5.5 seconds
  let heroTimer = setInterval(() => switchHeroSlide(true), 5500);

  [heroPrevBtn, heroNextBtn].forEach(btn => {
    if (btn) {
      btn.addEventListener('click', () => {
        clearInterval(heroTimer);
        heroTimer = setInterval(() => switchHeroSlide(true), 5500);
      });
    }
  });

  // 4. Category Click Interactive Preview
  const catItems = document.querySelectorAll('.cat-item');
  catItems.forEach(item => {
    item.addEventListener('click', () => {
      const title = item.querySelector('.cat-item-title').textContent;
      showNotification(`Exploring "${title}" items... Filter applied.`);
    });
  });

  // Helper notification toast
  function showNotification(msg) {
    const existing = document.querySelector('.toast-notification');
    if (existing) existing.remove();

    const toast = document.createElement('div');
    toast.className = 'toast-notification';
    toast.style.cssText = `
      position: fixed;
      bottom: 30px;
      right: 30px;
      background: #061427;
      color: #FFFFFF;
      padding: 14px 22px;
      border-radius: 12px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.25);
      border-left: 4px solid #FA5400;
      font-size: 14px;
      font-weight: 600;
      z-index: 3000;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      gap: 10px;
    `;
    toast.innerHTML = `<i class="fa-solid fa-circle-check" style="color:#FA5400;"></i> ${msg}`;
    document.body.appendChild(toast);

    setTimeout(() => {
      toast.style.opacity = '0';
      toast.style.transform = 'translateY(10px)';
      setTimeout(() => toast.remove(), 300);
    }, 3500);
  }

  // 5. Scroll Reveal Intersection Observer
  const revealElements = document.querySelectorAll('.reveal, .reveal-left, .reveal-right, .reveal-scale');
  
  if ('IntersectionObserver' in window) {
    const observer = new IntersectionObserver((entries, obs) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('active');
          obs.unobserve(entry.target);
        }
      });
    }, {
      threshold: 0.1,
      rootMargin: '0px 0px -40px 0px'
    });

    revealElements.forEach(el => observer.observe(el));
  } else {
    revealElements.forEach(el => el.classList.add('active'));
  }

  // 6. Number Counter Animation for Stats Banner
  function animateStatsCounters() {
    const statNumbers = document.querySelectorAll('.stat-number[data-target]');
    statNumbers.forEach(el => {
      if (el.classList.contains('counted')) return;
      el.classList.add('counted');

      const target = parseInt(el.getAttribute('data-target'), 10);
      const suffix = el.getAttribute('data-suffix') || '';
      const duration = 2000;
      const startTime = performance.now();

      function updateCount(currentTime) {
        const elapsedTime = currentTime - startTime;
        const progress = Math.min(elapsedTime / duration, 1);
        
        const easeProgress = progress === 1 ? 1 : 1 - Math.pow(2, -10 * progress);
        const currentVal = Math.floor(easeProgress * target);

        el.textContent = currentVal.toLocaleString() + suffix;

        if (progress < 1) {
          requestAnimationFrame(updateCount);
        } else {
          el.textContent = target.toLocaleString() + suffix;
        }
      }

      requestAnimationFrame(updateCount);
    });
  }

  const statsBanner = document.querySelector('.stats-banner');
  if (statsBanner) {
    if ('IntersectionObserver' in window) {
      const statsObserver = new IntersectionObserver((entries, obs) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            animateStatsCounters();
            obs.unobserve(entry.target);
          }
        });
      }, { threshold: 0.05 });
      statsObserver.observe(statsBanner);
    } else {
      animateStatsCounters();
    }
  }

  // 7. Certificate Zoom Modal Handler
  const certCards = document.querySelectorAll('.cert-card');
  const certModal = document.getElementById('certModal');
  const closeCertModalBtn = document.getElementById('closeCertModalBtn');
  const closeCertModalBtn2 = document.getElementById('closeCertModalBtn2');
  const certModalLogo = document.getElementById('certModalLogo');
  const certBrandName = document.getElementById('certBrandName');

  if (certCards.length > 0 && certModal) {
    certCards.forEach(card => {
      card.addEventListener('click', () => {
        const logoHTML = card.querySelector('.cert-logo').innerHTML;
        const brand = card.getAttribute('data-brand') || 'AUTHORISED BRAND';
        
        if (certModalLogo) certModalLogo.innerHTML = logoHTML;
        if (certBrandName) certBrandName.textContent = brand;
        
        certModal.classList.add('active');
        document.body.style.overflow = 'hidden';
      });
    });

    const closeCertModal = () => {
      certModal.classList.remove('active');
      document.body.style.overflow = '';
    };

    if (closeCertModalBtn) closeCertModalBtn.addEventListener('click', closeCertModal);
    if (closeCertModalBtn2) closeCertModalBtn2.addEventListener('click', closeCertModal);

    certModal.addEventListener('click', (e) => {
      if (e.target === certModal) closeCertModal();
    });

    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && certModal.classList.contains('active')) {
        closeCertModal();
      }
    });
  }
});
