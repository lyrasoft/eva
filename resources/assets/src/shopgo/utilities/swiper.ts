import { injectCssToDocument, selectOne } from '@windwalker-io/unicorn-next';
import { SwiperOptions } from 'swiper/types';

let cssImported: any = null;

export async function useSwiper(selector?: HTMLElement | string, options: SwiperOptions = {}) {
  const [{ default: Swiper }, { Navigation, Pagination }, { default: css }] = await Promise.all([
    import('swiper'),
    import('swiper/modules'),
    import('swiper/css/bundle?inline')
  ]);

  cssImported ??= injectCssToDocument(css);

  if (selector) {
    const el = selectOne<HTMLElement>(selector)!;

    options = Object.assign({}, {
      simulateTouch: false,
      allowTouchMove: false,
      autoHeight: true,
      modules: [Navigation, Pagination],
    }, options);
    
    return new Swiper(el, options);
  }
  
  return Swiper;
}

