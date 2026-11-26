import { computed, ref } from 'vue';
import { usePage } from '@inertiajs/vue3';

/**
 * The master catalogue PDFs uploaded in the admin, shared on every response by
 * HandleInertiaRequests. Each type is either an object with a downloadUrl or
 * null when nothing has been uploaded yet.
 *
 * Every download button on the site goes through downloadCatalogue(): it either
 * starts the download or opens the "coming soon" modal, so the two cases stay
 * consistent across pages.
 */
const LABELS = {
  retail: 'Retail Master Catalogue',
  b2b: 'B2B Master Catalogue',
};

export function useCatalogues() {
  const page = usePage();

  const catalogues = computed(() => ({
    retail: null,
    b2b: null,
    ...(page.props.catalogues || {}),
  }));

  // Holds { type, label } while the unavailable-catalogue modal is open.
  const missingCatalogue = ref(null);

  function catalogueFor(type) {
    return catalogues.value[type] || null;
  }

  function downloadCatalogue(type) {
    const catalogue = catalogueFor(type);

    if (catalogue?.downloadUrl) {
      // The route replies with Content-Disposition: attachment, so navigating
      // downloads the PDF without leaving the page.
      window.location.href = catalogue.downloadUrl;
      return;
    }

    missingCatalogue.value = { type, label: LABELS[type] || 'Catalogue' };
  }

  function closeCatalogueModal() {
    missingCatalogue.value = null;
  }

  return { catalogues, catalogueFor, downloadCatalogue, missingCatalogue, closeCatalogueModal };
}
