import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

/**
 * Site-wide content shared by HandleInertiaRequests: contact details, socials
 * and the WhatsApp config. Every page gets these, so chrome components read
 * them here instead of taking props through each page.
 *
 * The fallbacks keep components rendering if a setting is ever deleted.
 */
export function useSite() {
  const page = usePage();

  const site = computed(() => ({
    phonePrimary: '',
    phoneSecondary: '',
    phoneDisplay: '',
    emailPrimary: '',
    emailSecondary: '',
    addressShort: '',
    addressFooter: '',
    addressFull: '',
    storeHours: '',
    footerTagline: '',
    ...(page.props.site || {}),
    // Null, not '#': a social icon with nowhere to go is not rendered at all.
    social: {
      facebook: null,
      instagram: null,
      linkedin: null,
      ...(page.props.site?.social || {}),
    },
  }));

  const whatsapp = computed(() => ({
    enabled: false,
    number: '',
    message: '',
    url: null,
    ...(page.props.whatsapp || {}),
  }));

  /** The footer's two category columns, straight from the catalogue. */
  const footerCategories = computed(() => ({
    store: page.props.footerCategories?.store || [],
    b2b: page.props.footerCategories?.b2b || [],
  }));

  return { site, whatsapp, footerCategories };
}
