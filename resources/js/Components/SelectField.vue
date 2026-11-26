<template>
  <div class="sfield" ref="rootRef">
    <button
      ref="triggerRef"
      type="button"
      class="sfield-trigger"
      :class="{ 'is-open': open }"
      :id="id"
      role="combobox"
      :aria-expanded="open"
      aria-haspopup="listbox"
      :aria-controls="`${id}-listbox`"
      @click="toggle"
      @keydown="onTriggerKeydown"
    >
      <span class="sfield-value" :class="{ 'is-placeholder': !selected }">
        {{ selected ? selected.label : placeholder }}
      </span>
      <i class="fa-solid fa-chevron-down sfield-chev" :class="{ 'is-open': open }"></i>
    </button>

    <!-- Teleported to <body> so the panel is never clipped: the contact modal
         scrolls its own content, and an absolutely positioned child would be
         cut off at the card's edge — which is exactly what the native select
         was doing on a phone. -->
    <Teleport to="body">
      <div v-if="open && isSheet" class="sfield-scrim" @click="close"></div>

      <ul
        v-if="open"
        ref="panelRef"
        class="sfield-panel"
        :class="{ 'is-sheet': isSheet }"
        :style="isSheet ? null : panelStyle"
        :id="`${id}-listbox`"
        role="listbox"
        :aria-activedescendant="`${id}-opt-${activeIndex}`"
        tabindex="-1"
        data-lenis-prevent
        @keydown="onPanelKeydown"
      >
        <li v-if="isSheet" class="sfield-sheet-title">{{ sheetTitle }}</li>

        <li
          v-for="(option, index) in options"
          :key="option.value"
          :id="`${id}-opt-${index}`"
          class="sfield-opt"
          :class="{ 'is-active': index === activeIndex, 'is-selected': option.value === modelValue }"
          role="option"
          :aria-selected="option.value === modelValue"
          @click="choose(option.value)"
          @mousemove="activeIndex = index"
        >
          <span class="sfield-opt-text">{{ option.label }}</span>
          <i v-if="option.value === modelValue" class="fa-solid fa-check sfield-tick"></i>
        </li>
      </ul>
    </Teleport>
  </div>
</template>

<script setup>
import { computed, nextTick, onBeforeUnmount, onMounted, ref, watch } from 'vue';

/**
 * Single-select form field with a styled menu, replacing a native <select>.
 *
 * The native control could not be styled and, more importantly, rendered its
 * option list as an OS-level layer that escaped the contact modal on a phone.
 * This keeps the list inside the page: a bottom sheet on small screens, an
 * anchored popover everywhere else.
 */
const props = defineProps({
  modelValue: { type: [String, Number], default: '' },
  // [{ value, label }]
  options: { type: Array, default: () => [] },
  id: { type: String, required: true },
  placeholder: { type: String, default: 'Select an option' },
  // Heading shown above the options in the phone bottom sheet.
  sheetTitle: { type: String, default: 'Choose an option' },
});

const emit = defineEmits(['update:modelValue']);

const rootRef = ref(null);
const triggerRef = ref(null);
const panelRef = ref(null);
const open = ref(false);
const activeIndex = ref(0);
const panelStyle = ref({});
const isSheet = ref(false);

const selected = computed(() => props.options.find(option => option.value === props.modelValue) ?? null);

// Below this the popover becomes a bottom sheet — the same breakpoint the
// contact modal switches to its single-column layout at.
const sheetQuery = window.matchMedia('(max-width: 576px)');
const syncSheet = () => { isSheet.value = sheetQuery.matches; };

/** Anchors the fixed panel to the trigger, flipping above it when short of room. */
function position() {
  const trigger = triggerRef.value;
  if (!trigger || isSheet.value) return;

  const rect = trigger.getBoundingClientRect();
  const panelHeight = Math.min(props.options.length * 44 + 12, 280);
  const below = window.innerHeight - rect.bottom;
  const flip = below < panelHeight + 12 && rect.top > below;

  panelStyle.value = {
    left: `${rect.left}px`,
    width: `${rect.width}px`,
    ...(flip
      ? { bottom: `${window.innerHeight - rect.top + 6}px` }
      : { top: `${rect.bottom + 6}px` }),
  };
}

function toggle() {
  open.value ? close() : openMenu();
}

function openMenu() {
  activeIndex.value = Math.max(0, props.options.findIndex(option => option.value === props.modelValue));
  open.value = true;

  nextTick(() => {
    position();
    panelRef.value?.focus();
  });
}

function close({ refocus = false } = {}) {
  open.value = false;
  if (refocus) triggerRef.value?.focus();
}

function choose(value) {
  emit('update:modelValue', value);
  close({ refocus: true });
}

function move(step) {
  const count = props.options.length;
  if (!count) return;
  activeIndex.value = (activeIndex.value + step + count) % count;
}

function onTriggerKeydown(event) {
  if (['ArrowDown', 'ArrowUp', 'Enter', ' '].includes(event.key)) {
    event.preventDefault();
    openMenu();
  }
}

function onPanelKeydown(event) {
  switch (event.key) {
    case 'ArrowDown': event.preventDefault(); move(1); break;
    case 'ArrowUp': event.preventDefault(); move(-1); break;
    case 'Home': event.preventDefault(); activeIndex.value = 0; break;
    case 'End': event.preventDefault(); activeIndex.value = props.options.length - 1; break;
    case 'Enter':
    case ' ':
      event.preventDefault();
      if (props.options[activeIndex.value]) choose(props.options[activeIndex.value].value);
      break;
    case 'Escape':
    case 'Tab':
      close({ refocus: true });
      break;
  }
}

function onPointerDown(event) {
  if (rootRef.value?.contains(event.target)) return;
  if (panelRef.value?.contains(event.target)) return;
  close();
}

// `true` captures scrolling inside the modal card too, not just the window —
// without it the panel would hang in place while the form scrolled under it.
const reposition = () => (open.value ? position() : null);

watch(open, (isOpen) => {
  if (isOpen) {
    document.addEventListener('mousedown', onPointerDown);
    window.addEventListener('scroll', reposition, true);
    window.addEventListener('resize', reposition);
  } else {
    document.removeEventListener('mousedown', onPointerDown);
    window.removeEventListener('scroll', reposition, true);
    window.removeEventListener('resize', reposition);
  }
});

onMounted(() => {
  syncSheet();
  sheetQuery.addEventListener('change', syncSheet);
});

onBeforeUnmount(() => {
  sheetQuery.removeEventListener('change', syncSheet);
  document.removeEventListener('mousedown', onPointerDown);
  window.removeEventListener('scroll', reposition, true);
  window.removeEventListener('resize', reposition);
});

defineExpose({ close });
</script>

<style scoped>
.sfield {
  position: relative;
}

/* ------------------------------------------------------------- trigger */
/* Matches the sibling .form-control inputs so the row reads as one set. */
.sfield-trigger {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 10px;
  width: 100%;
  height: 38px;
  padding: 7px 11px;
  border: 1px solid var(--border-color);
  border-radius: 8px;
  background: #FFFFFF;
  font-family: var(--font-main);
  font-size: 13px;
  text-align: left;
  color: var(--text-dark);
  cursor: pointer;
  outline: none;
  transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.sfield-trigger:hover {
  border-color: #CBD5E1;
}

.sfield-trigger:focus-visible,
.sfield-trigger.is-open {
  border-color: var(--primary-orange);
  box-shadow: 0 0 0 3px rgba(228, 101, 46, 0.15);
}

.sfield-value {
  flex: 1;
  min-width: 0;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.sfield-value.is-placeholder {
  color: var(--text-light);
}

.sfield-chev {
  flex-shrink: 0;
  font-size: 10px;
  color: var(--text-muted);
  transition: transform 0.2s ease, color 0.2s ease;
}

.sfield-chev.is-open {
  transform: rotate(180deg);
  color: var(--primary-orange);
}

/* ------------------------------------------------------------- panel */
.sfield-panel {
  position: fixed;
  /* Above the modal card (2000) and the promo popup (3000). */
  z-index: 3200;
  margin: 0;
  padding: 6px;
  list-style: none;
  max-height: 280px;
  overflow-y: auto;
  overscroll-behavior: contain;
  background: #FFFFFF;
  border: 1px solid var(--border-color);
  border-radius: 12px;
  box-shadow: 0 18px 44px rgba(6, 20, 39, 0.18);
  outline: none;
  animation: sfield-in 0.16s var(--ease-entrance, cubic-bezier(0.16, 1, 0.3, 1)) both;
  scrollbar-width: thin;
  scrollbar-color: rgba(228, 101, 46, 0.35) transparent;
}

@keyframes sfield-in {
  from { opacity: 0; transform: translateY(-6px); }
  to { opacity: 1; transform: none; }
}

.sfield-opt {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 10px;
  padding: 10px 12px;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 500;
  color: var(--text-dark);
  cursor: pointer;
}

.sfield-opt.is-active {
  background: #F8FAFC;
}

.sfield-opt.is-selected {
  color: var(--primary-orange);
  font-weight: 700;
}

.sfield-opt.is-selected.is-active {
  background: var(--primary-orange-light);
}

.sfield-opt-text {
  flex: 1;
  min-width: 0;
}

.sfield-tick {
  flex-shrink: 0;
  font-size: 11px;
}

.sfield-sheet-title {
  padding: 4px 12px 10px;
  font-family: var(--font-heading);
  font-size: 14px;
  font-weight: 800;
  color: var(--dark-navy);
}

/* ------------------------------------------------------------- phone sheet */
.sfield-scrim {
  position: fixed;
  inset: 0;
  z-index: 3150;
  background: rgba(6, 20, 39, 0.45);
  animation: sfield-fade 0.16s ease both;
}

@keyframes sfield-fade {
  from { opacity: 0; }
  to { opacity: 1; }
}

/* A sheet rather than a popover: the field sits mid-form on a phone, and a
   list anchored to it would be half under the keyboard. */
.sfield-panel.is-sheet {
  left: 10px;
  right: 10px;
  bottom: 10px;
  top: auto;
  width: auto;
  padding: 14px 8px 8px;
  max-height: 56vh;
  border-radius: 18px;
  box-shadow: 0 -10px 44px rgba(6, 20, 39, 0.26);
  animation-name: sfield-sheet-in;
}

@keyframes sfield-sheet-in {
  from { opacity: 0; transform: translateY(16px); }
  to { opacity: 1; transform: none; }
}

@media (max-width: 576px) {
  /* Same 44px control height and 16px text as the inputs beside it. */
  .sfield-trigger {
    height: 44px;
    padding: 10px 13px;
    font-size: 16px;
    border-radius: 10px;
  }

  .sfield-chev {
    font-size: 12px;
  }

  .sfield-opt {
    padding: 14px 14px;
    font-size: 15px;
    border-radius: 12px;
  }

  .sfield-tick {
    font-size: 13px;
  }
}

@media (prefers-reduced-motion: reduce) {
  .sfield-panel,
  .sfield-scrim {
    animation: none;
  }

  .sfield-chev {
    transition: none;
  }
}
</style>
