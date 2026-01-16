<template>
  <Transition name="sweep">
    <slot></slot>
  </Transition>
</template>

<style scoped>
.sweep-enter-active,
.sweep-leave-active {
  transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
  position: relative;
}

.sweep-enter-from {
  opacity: 0;
  transform: translateX(-20px);
}

.sweep-enter-to {
  opacity: 1;
  transform: translateX(0);
}

.sweep-leave-from {
  opacity: 1;
  transform: translateX(0);
}

.sweep-leave-to {
  opacity: 0;
  transform: translateX(20px);
}
</style>
