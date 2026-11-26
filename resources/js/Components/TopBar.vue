<template>
  <div class="fl-top-bar">
    <div class="container fl-top-bar-inner">
      <i class="fa-solid fa-leaf fl-top-leaf"></i>
      <span>Fresh from Local Farmers</span>
      <span class="fl-top-sep">|</span>
      <span>Naturally Grown</span>
      <span class="fl-top-sep">|</span>
      <span>Farm to Home</span>
    </div>
  </div>
</template>

<script setup>
</script>

<style scoped>
.fl-top-bar {
  background-color: #14532D;
  color: #FFFFFF;
  height: 34px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-family: 'Plus Jakarta Sans', var(--font-main), sans-serif;
  font-size: 12.5px;
  font-weight: 500;
}

.fl-top-bar-inner {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 12px;
  white-space: nowrap;
}

.fl-top-leaf {
  color: #4ADE80;
  font-size: 12px;
}

.fl-top-sep {
  opacity: 0.45;
}

@media (max-width: 640px) {
  .fl-top-bar {
    font-size: 11px;
    height: 30px;
  }
  .fl-top-bar-inner {
    gap: 6px;
  }
}
</style>
