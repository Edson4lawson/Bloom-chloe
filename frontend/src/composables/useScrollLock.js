import { onMounted, onUnmounted, watch } from 'vue';

let lockCount = 0;
let previousOverflow = '';

/**
 * Verrouille le défilement de la page en arrière-plan
 */
export function lockScroll() {
  if (typeof document === 'undefined') return;
  if (lockCount === 0) {
    previousOverflow = document.body.style.overflow;
    document.body.style.overflow = 'hidden';
    document.body.style.touchAction = 'none';
  }
  lockCount++;
}

/**
 * Déverrouille le défilement de la page en arrière-plan
 */
export function unlockScroll() {
  if (typeof document === 'undefined') return;
  lockCount = Math.max(0, lockCount - 1);
  if (lockCount === 0) {
    document.body.style.overflow = previousOverflow || '';
    document.body.style.touchAction = '';
  }
}

/**
 * Composable pour synchroniser le verrouillage du scroll avec l'état d'un modal/tiroir
 * @param {Ref<boolean>|Function|null} isOpenRef - Référence ou getter de l'état d'ouverture
 */
export function useScrollLock(isOpenRef = null) {
  if (isOpenRef !== null) {
    watch(
      () => (typeof isOpenRef === 'function' ? isOpenRef() : isOpenRef.value),
      (open, wasOpen) => {
        if (open && !wasOpen) {
          lockScroll();
        } else if (!open && wasOpen) {
          unlockScroll();
        }
      },
      { immediate: true }
    );

    onUnmounted(() => {
      const open = typeof isOpenRef === 'function' ? isOpenRef() : isOpenRef.value;
      if (open) {
        unlockScroll();
      }
    });
  } else {
    // Si aucun ref n'est fourni, verrouille au montage et déverrouille au démontage
    onMounted(() => {
      lockScroll();
    });
    onUnmounted(() => {
      unlockScroll();
    });
  }
}
