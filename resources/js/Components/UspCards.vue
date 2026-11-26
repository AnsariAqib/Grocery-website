<template>
  <section v-if="cards.length" class="section-padding usp-section section-flush-bottom">
    <div class="container">
      <div class="usp-grid stagger">
        <div v-for="card in cards" :key="card.id" class="usp-card reveal">
          <span class="usp-icon"><i class="fa-solid" :class="card.icon || 'fa-shield-halved'"></i></span>
          <div class="usp-copy">
            <h3>{{ card.title }}</h3>
            <p v-if="card.description">{{ card.description }}</p>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
defineProps({
  cards: { type: Array, default: () => [] },
});
</script>

<style scoped>
.usp-grid {
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 18px;
}

/* Four is the design target; three or fewer cards spread evenly instead of
   leaving a hole at the end of the row. */
.usp-grid:has(> :last-child:nth-child(3)) {
  grid-template-columns: repeat(3, minmax(0, 1fr));
}

.usp-grid:has(> :last-child:nth-child(2)) {
  grid-template-columns: repeat(2, minmax(0, 1fr));
}

.usp-card {
  display: flex;
  align-items: flex-start;
  gap: 14px;
  padding: 22px 20px;
  background: #FFFFFF;
  border: 1px solid var(--border-color);
  border-radius: var(--radius-lg);
  box-shadow: var(--shadow-sm);
  transition: transform 0.3s var(--ease-interact), box-shadow 0.3s var(--ease-interact), border-color 0.3s var(--ease-interact);
}

.usp-card:hover {
  transform: translateY(var(--lift-tile));
  border-color: rgba(228, 101, 46, 0.4);
  box-shadow: var(--shadow-hover-orange);
}

.usp-icon {
  width: 46px;
  height: 46px;
  flex-shrink: 0;
  border-radius: 14px;
  background: var(--primary-orange-light);
  color: var(--primary-orange);
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 19px;
}

.usp-copy h3 {
  font-family: var(--font-heading);
  font-size: 15px;
  font-weight: 800;
  color: var(--dark-navy);
  margin: 2px 0 5px 0;
  line-height: 1.25;
}

.usp-copy p {
  font-size: 12.5px;
  color: var(--text-muted);
  line-height: 1.55;
  margin: 0;
}

@media (max-width: 1024px) {
  .usp-grid,
  .usp-grid:has(> :last-child:nth-child(3)) {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}

@media (max-width: 768px) {
  .usp-grid,
  .usp-grid:has(> :last-child:nth-child(3)),
  .usp-grid:has(> :last-child:nth-child(2)) {
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 12px;
  }

  .usp-card {
    flex-direction: column;
    gap: 10px;
    padding: 15px 13px;
    border-radius: 16px;
  }

  .usp-icon {
    width: 36px;
    height: 36px;
    font-size: 15px;
    border-radius: 11px;
  }

  .usp-copy h3 {
    font-size: 13px;
  }

  .usp-copy p {
    font-size: 11.5px;
  }
}
</style>
