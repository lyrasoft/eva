
import '@main';

// Ripple Effects
(() => {
  if (window.Ribble) {
    u.directive('ripple', {
      mounted(el, { value }) {
        if (value) {
          value = JSON.parse(value);
        } else {
          value = undefined;
        }

        Ribble.attachEvent(el, value);
      }
    });
  }
})();

// Sidebar Menu
(() => {
  const menuButtons = document.querySelectorAll<HTMLDivElement>('.l-sidebar .dropdown-toggle');

  for (const menuButton of menuButtons) {
    const menu = menuButton.closest('.dropdown')?.querySelector('.dropdown-menu');

    if (!menu) {
      continue;
    }

    const menuCollapse = u.$ui.bootstrap.collapse(menu, { toggle: false });

    menuButton.addEventListener('click', () => {
      const show = menuButton.classList.toggle('show');

      if (show) {
        menuCollapse.show();
      } else {
        menuCollapse.hide();
      }
    });
  }
})();

// Fullscreen
(() => {
  const fullScreenButtons = document.querySelectorAll<HTMLAnchorElement|HTMLButtonElement>('[data-bs-toggle=fullscreen]');

  for (const fullScreenButton of fullScreenButtons) {
    fullScreenButton.addEventListener('click', () => {
      if (!document.fullscreenElement) {
        let el: HTMLElement | null = document.body;

        if (fullScreenButton.dataset.bsTarget) {
          el = document.querySelector(fullScreenButton.dataset.bsTarget);
        }

        if (el) {
          el.requestFullscreen?.()
            || el.mozRequestFullscreen?.()
            || el.webkitRequestFullscreen?.();
        }
      } else {
        document.cancelFullscreen?.()
          || document.mozCancelFullScreen?.()
          || document.webkitCancelFullScreen?.();
      }
    })
  }
})();

declare global {
  interface HTMLElement {
    mozRequestFullscreen?: Function;
    webkitRequestFullscreen?: Function;
  }
  interface Document {
    cancelFullscreen?: Function;
    mozCancelFullScreen?: Function;
    webkitCancelFullScreen?: Function;
  }
}
