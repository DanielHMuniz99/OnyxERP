<template>
  <footer class="dashboard-footer">
    <span>OnyxERP</span>
    <span>Ambiente inicial generico para operacao e acompanhamento.</span>
  </footer>
</template>

<style scoped>
.dashboard-footer {
  display: flex;
  justify-content: space-between;
  gap: 1rem;
  padding: 1rem 1.75rem;
  border-top: 1px solid #dde5ee;
  background: #ffffff;
  color: #5c6b7d;
  font-size: 0.9rem;
}

@media (max-width: 720px) {
  .dashboard-footer {
    flex-direction: column;
  }
}
</style>
