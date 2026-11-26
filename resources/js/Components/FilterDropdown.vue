<template>
  <div ref="rootRef" class="fdrop">
    <button
      type="button"
      class="fdrop-chip"
      :class="{ 'is-open': open, 'is-active': badgeCount > 0 }"
      :aria-expanded="open"
      aria-haspopup="true"
      @click="open = !open"
    >
      <i v-if="icon" class="fa-solid" :class="icon"></i>
      <span class="fdrop-label">{{ label }}</span>
      <span v-if="badgeCount" class="fdrop-count">{{ badgeCount }}</span>
      <i class="fa-solid fa-chevron-down fdrop-chev" :class="{ 'is-open': open }"></i>
    </button>

    <!-- The panel lives on <body>, not inside the chip. The filter bar is a
         horizontal scroller on a phone, and a popover inside it is clipped —
         on iOS the scroller traps a `position: fixed` child as well, which is
         what made the Brand list appear squeezed into the bar and the
         Subcategory list not appear at all. Teleporting puts it out of reach
         of every ancestor's overflow. -->
    <Teleport to="body">
    <div v-if="open" class="fdrop-scrim" @click="open = false"></div>

    <div
      v-if="open"
      ref="panelRef"
      class="fdrop-panel"
      :style="panelStyle"
      data-lenis-prevent
    >
      <!-- A slot takes the whole body — that is how the price range reuses this
           chip and popover instead of growing a second set of them. -->
      <slot>
        <div v-if="showSearch" class="fdrop-search">
          <i class="fa-solid fa-magnifying-glass"></i>
          <input
            v-model="query"
            type="text"
            :placeholder="`Search ${label.toLowerCase()}`"
            class="fdrop-search-input"
          >
        </div>

        <div class="fdrop-list">
          <label
            v-for="option in visibleOptions"
            :key="option.value"
            class="fdrop-opt"
            :class="{ 'is-empty': option.count === 0 && !isChecked(option.value) }"
          >
            <input
              type="checkbox"
              class="fdrop-native"
              :checked="isChecked(option.value)"
              @change="toggleOption(option.value)"
            >
            <span class="fdrop-box"><i class="fa-solid fa-check"></i></span>
            <span class="fdrop-text">{{ option.label }}</span>
            <span v-if="option.count !== null" class="fdrop-num">{{ option.count }}</span>
          </label>

          <p v-if="!visibleOptions.length" class="fdrop-empty">Nothing matches that.</p>
        </div>
      </slot>

      <div class="fdrop-foot">
        <button type="button" class="fdrop-clear" :disabled="!badgeCount" @click="clear">
          Clear
        </button>
        <button type="button" class="fdrop-done" @click="open = false">Done</button>
      </div>
    </div>
    </Teleport>
  </div>
</template>

<script setup>
import { computed, nextTick, onUnmounted, ref, watch } from 'vue';

/** Below this the panel is a bottom sheet — matches the CSS breakpoint. */
const SHEET_WIDTH = 768;

/** Breathing room kept between the panel and the edge of the screen. */
const EDGE = 12;

const props = defineProps({
  label: { type: String, required: true },
  icon: { type: String, default: '' },
  // [{ value, label, count }] — `count` may be null to hide the number.
  options: { type: Array, default: () => [] },
  modelValue: { type: Array, default: () => [] },
  // Overrides the chip badge for slot-driven filters (e.g. the price range).
  badge: { type: [Number, String], default: null },
  panelWidth: { type: Number, default: 260 },
});

const emit = defineEmits(['update:modelValue', 'clear']);

const rootRef = ref(null);
const panelRef = ref(null);
const open = ref(false);
const query = ref('');

/**
 * Where the teleported panel sits. Empty below the sheet breakpoint — the CSS
 * pins it to the bottom of the screen there.
 */
const panelStyle = ref({});

/**
 * Put the panel under its own chip, kept inside the screen.
 *
 * Positions are read off the chip every time rather than stored, because the
 * chip moves: the filter bar scrolls sideways on a phone, and the page scrolls
 * under a fixed panel.
 */
function place() {
  const chip = rootRef.value?.querySelector('.fdrop-chip');
  const panel = panelRef.value;

  if (!chip || !panel) return;

  if (window.innerWidth <= SHEET_WIDTH) {
    panelStyle.value = {};

    return;
  }

  const rect = chip.getBoundingClientRect();
  const width = Math.min(props.panelWidth, window.innerWidth - EDGE * 2);
  const height = panel.offsetHeight;

  // Nudged left when the chip sits near the right edge, rather than letting
  // the panel run off the screen.
  const left = Math.min(Math.max(EDGE, rect.left), window.innerWidth - width - EDGE);
  const roomBelow = window.innerHeight - rect.bottom;
  // Opens upwards only when below genuinely does not fit and above is roomier.
  const flip = roomBelow < height + EDGE && rect.top > roomBelow;

  panelStyle.value = {
    width: `${width}px`,
    left: `${left}px`,
    top: flip ? 'auto' : `${rect.bottom + 8}px`,
    bottom: flip ? `${window.innerHeight - rect.top + 8}px` : 'auto',
  };
}

const badgeCount = computed(() => (props.badge !== null ? props.badge : props.modelValue.length));

// A short list is quicker to scan than to type into.
const showSearch = computed(() => props.options.length > 8);

const visibleOptions = computed(() => {
  const q = query.value.trim().toLowerCase();
  if (!q) return props.options;
  return props.options.filter(option => String(option.label).toLowerCase().includes(q));
});

function isChecked(value) {
  return props.modelValue.includes(value);
}

function toggleOption(value) {
  emit('update:modelValue', isChecked(value)
    ? props.modelValue.filter(item => item !== value)
    : [...props.modelValue, value]);
}

function clear() {
  emit('update:modelValue', []);
  emit('clear');
}

function onDocumentPointerDown(event) {
  // The panel is no longer inside the chip's element, so it has to be asked
  // separately — otherwise every click on an option would close the panel.
  const inside = rootRef.value?.contains(event.target) || panelRef.value?.contains(event.target);

  if (!inside) open.value = false;
}

function onKeydown(event) {
  if (event.key === 'Escape') open.value = false;
}

// Listeners only exist while a panel is actually open. `capture` on scroll
// catches the filter bar's own sideways scrolling as well as the page's.
function listen(add) {
  const method = add ? 'addEventListener' : 'removeEventListener';

  document[method]('mousedown', onDocumentPointerDown);
  document[method]('keydown', onKeydown);
  window[method]('scroll', place, true);
  window[method]('resize', place);
}

watch(open, async (isOpen) => {
  if (isOpen) {
    query.value = '';
    listen(true);
    // Placed once the panel exists, so its height is known and it can flip.
    await nextTick();
    place();
  } else {
    listen(false);
  }
});

// A list that grows or shrinks — a search typed into the panel — changes the
// height the flip decision was made on.
watch(visibleOptions, () => {
  if (open.value) nextTick(place);
});

onUnmounted(() => listen(false));
</script>

<style scoped>
.fdrop {
  position: relative;
}

/* ---- Chip ---- */
.fdrop-chip {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: #FFFFFF;
  border: 1.5px solid #E2E8F0;
  color: #475569;
  font-family: var(--font-main);
  font-size: 13px;
  font-weight: 700;
  padding: 10px 14px;
  border-radius: 12px;
  cursor: pointer;
  white-space: nowrap;
  transition: border-color 0.2s var(--ease-interact), color 0.2s var(--ease-interact), background-color 0.2s var(--ease-interact);
}

.fdrop-chip > i:first-child {
  font-size: 12px;
  color: #94A3B8;
  transition: color 0.2s var(--ease-interact);
}

.fdrop-chip:hover {
  border-color: #E4652E;
  color: #E4652E;
}

.fdrop-chip:hover > i:first-child,
.fdrop-chip.is-active > i:first-child,
.fdrop-chip.is-open > i:first-child {
  color: #E4652E;
}

.fdrop-chip.is-open,
.fdrop-chip.is-active {
  border-color: #E4652E;
  color: #E4652E;
  background: #FDF3ED;
}

.fdrop-count {
  background: #E4652E;
  color: #FFFFFF;
  font-size: 10.5px;
  font-weight: 800;
  min-width: 18px;
  height: 18px;
  padding: 0 5px;
  border-radius: 9px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
}

.fdrop-chev {
  font-size: 9px;
  transition: transform 0.2s var(--ease-interact);
}

.fdrop-chev.is-open {
  transform: rotate(180deg);
}

/* ---- Panel ---- */
/* Teleported to <body>, so these coordinates are the viewport's. The inline
   style from place() fills in top/left/width. */
.fdrop-panel {
  position: fixed;
  top: 0;
  left: 0;
  z-index: 1200;
  max-width: calc(100vw - 24px);
  background: #FFFFFF;
  border: 1px solid #E2E8F0;
  border-radius: 16px;
  box-shadow: 0 18px 44px rgba(6, 20, 39, 0.14);
  overflow: hidden;
  animation: fdrop-in 0.18s var(--ease-entrance) both;
}

@keyframes fdrop-in {
  from { opacity: 0; transform: translateY(-6px); }
  to { opacity: 1; transform: none; }
}

/* Dims the page behind the bottom sheet; does nothing on a desktop popover. */
.fdrop-scrim {
  display: none;
}

.fdrop-search {
  position: relative;
  padding: 12px 12px 8px;
}

.fdrop-search i {
  position: absolute;
  left: 24px;
  top: 50%;
  transform: translateY(-30%);
  font-size: 12px;
  color: #94A3B8;
}

.fdrop-search-input {
  width: 100%;
  padding: 9px 12px 9px 32px;
  border: 1.5px solid #E2E8F0;
  border-radius: 10px;
  font-family: var(--font-main);
  font-size: 12.5px;
  font-weight: 600;
  color: #0F172A;
  outline: none;
}

.fdrop-search-input:focus {
  border-color: #E4652E;
}

.fdrop-list {
  max-height: 262px;
  overflow-y: auto;
  padding: 6px;
}

.fdrop-opt {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 8px 10px;
  border-radius: 10px;
  cursor: pointer;
  transition: background-color 0.15s ease;
}

.fdrop-opt:hover {
  background: #F8FAFC;
}

/* Still selectable — it just cannot add anything to the current result set. */
.fdrop-opt.is-empty {
  opacity: 0.45;
}

.fdrop-native {
  position: absolute;
  opacity: 0;
  width: 0;
  height: 0;
}

.fdrop-box {
  flex-shrink: 0;
  width: 17px;
  height: 17px;
  border: 1.5px solid #CBD5E1;
  border-radius: 5px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #FFFFFF;
  font-size: 9px;
  transition: background-color 0.15s ease, border-color 0.15s ease;
}

.fdrop-box i {
  opacity: 0;
  transform: scale(0.6);
  transition: opacity 0.15s ease, transform 0.15s ease;
}

.fdrop-native:checked + .fdrop-box {
  background: #E4652E;
  border-color: #E4652E;
}

.fdrop-native:checked + .fdrop-box i {
  opacity: 1;
  transform: none;
}

.fdrop-native:focus-visible + .fdrop-box {
  outline: 2px solid #E4652E;
  outline-offset: 2px;
}

.fdrop-text {
  flex: 1;
  min-width: 0;
  font-size: 12.5px;
  font-weight: 600;
  color: #0F172A;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.fdrop-num {
  font-size: 11px;
  font-weight: 700;
  color: #94A3B8;
}

.fdrop-empty {
  font-size: 12.5px;
  color: #94A3B8;
  text-align: center;
  padding: 18px 10px;
  margin: 0;
}

.fdrop-foot {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 10px;
  padding: 10px 12px;
  border-top: 1px solid #F1F5F9;
  background: #FAFBFD;
}

.fdrop-clear {
  background: none;
  border: none;
  color: #64748B;
  font-family: var(--font-main);
  font-size: 12px;
  font-weight: 700;
  cursor: pointer;
  padding: 4px 2px;
}

.fdrop-clear:hover:not(:disabled) {
  color: #E4652E;
}

.fdrop-clear:disabled {
  opacity: 0.4;
  cursor: default;
}

.fdrop-done {
  background: #E4652E;
  border: none;
  color: #FFFFFF;
  font-family: var(--font-main);
  font-size: 12px;
  font-weight: 800;
  padding: 7px 16px;
  border-radius: 9px;
  cursor: pointer;
  transition: background-color 0.2s var(--ease-interact);
}

.fdrop-done:hover {
  background: #C9541F;
}

@media (max-width: 768px) {
  .fdrop-chip {
    font-size: 12px;
    padding: 9px 12px;
    border-radius: 10px;
  }

  .fdrop-scrim {
    display: block;
    position: fixed;
    inset: 0;
    z-index: 1190;
    background: rgba(6, 20, 39, 0.32);
    animation: fdrop-fade-in 0.18s var(--ease-entrance) both;
  }

  @keyframes fdrop-fade-in {
    from { opacity: 0; }
    to { opacity: 1; }
  }

  /* A phone gets a bottom sheet rather than a popover: there is no room beside
     a chip, and the sheet is reachable with a thumb. */
  .fdrop-panel {
    top: auto !important;
    left: 12px !important;
    right: 12px;
    bottom: 12px !important;
    width: auto !important;
    max-width: none;
    border-radius: 18px;
    box-shadow: 0 -8px 44px rgba(6, 20, 39, 0.22);
    animation-name: fdrop-sheet-in;
  }

  @keyframes fdrop-sheet-in {
    from { opacity: 0; transform: translateY(14px); }
    to { opacity: 1; transform: none; }
  }

  .fdrop-list {
    max-height: 44vh;
  }

  .fdrop-foot {
    padding: 12px;
  }

  .fdrop-done {
    padding: 9px 22px;
  }
}
</style>
