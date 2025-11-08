
import { delegate, useHttpClient, simpleAlert, route, useUnicorn, useUniDirective } from '@windwalker-io/unicorn-next';
import swal from 'sweetalert';

export async function useProductCartButtons() {
  delegate(document.body, '[data-task=add-to-cart]', 'click', (e) => {
    addToCart(e.currentTarget as HTMLElement);
  });

  delegate(document.body, '[data-task=buy]', 'click', (e) => {
    buy(e.currentTarget as HTMLElement);
  });

  await Promise.all([
    useAddToCartDirective(),
    useInstantBuyDirective()
  ]);
}

function useAddToCartDirective() {
  const addCartListener = (e: MouseEvent) => addToCart(e.currentTarget as HTMLElement);

  return useUniDirective<HTMLElement>('add-to-cart', {
    mounted(el) {
      el.addEventListener('click', addCartListener);
    },
    unmounted(el) {
      el.removeEventListener('click', addCartListener);
    }
  });
}

function useInstantBuyDirective() {
  const instantBuy = (e: MouseEvent) => buy(e.currentTarget as HTMLElement);

  return useUniDirective<HTMLElement>('buy', {
    mounted(el) {
      el.addEventListener('click', instantBuy);
    },
    unmounted(el) {
      el.removeEventListener('click', instantBuy);
    }
  });
}

async function sendAddAction(el: HTMLElement) {
  const productId = el.dataset.id;

  if (!productId) {
    throw new Error('No product ID');
  }

  const variantId = el.dataset.variantId;

  if (!variantId) {
    throw new Error('No variant ID');
  }

  const qtyInput = document.querySelector<HTMLInputElement>('[data-role=quantity]');
  const quantity = Number(qtyInput?.value || 1);

  // Find additional purchases
  const attachments = findAttachments();

  const { post } = await useHttpClient();

  try {
    const res = await post(
      '@cart_ajax/addToCart',
      {
        product_id: productId,
        variant_id: variantId,
        quantity,
        attachments
      }
    );

    updateCartButton(res.data.data);

    return res.data;
  } catch (e) {
    console.error(e);
    throw e;
  }
}

async function addToCart(el: HTMLElement) {
  const { isAxiosError } = await useHttpClient();

  try {
    await sendAddAction(el);
  } catch (e) {
    if (isAxiosError(e)) {
      simpleAlert(e.message, '', 'warning');
    }
    return;
  }

  // Todo: Translate it
  const v = await swal({
    title: '已加入購物車',
    buttons: [
      '繼續購物',
      '前往結帳'
    ]
  });

  if (!v) {
    return;
  }

  toCartPage();
}

async function buy(el: HTMLElement) {
  const { isAxiosError } = await useHttpClient();

  try {
    await sendAddAction(el);
  } catch (e) {
    if (isAxiosError(e)) {
      simpleAlert(e.message, '', 'warning');
    }
    return;
  }

  toCartPage();
}

function toCartPage() {
  location.href = route('cart');
}

function updateCartButton(data: any) {
  const count = data.length;

  const u = useUnicorn();

  u.trigger('cart.update', data, count);

  document.dispatchEvent(
    new CustomEvent('cart.update', {
      detail: {
        data,
        count
      }
    })
  );

  const $cartButtons = document.querySelectorAll<HTMLButtonElement>('[data-role=cart-button]');

  for (const $cartButton of $cartButtons) {
    const $cartQuantity = $cartButton.querySelector<HTMLDivElement>('[data-role=cart-quantity]');

    $cartButton.classList.toggle('h-has-items', count > 0);

    if ($cartQuantity) {
      $cartQuantity.textContent = count;
    }

    $cartButton.dispatchEvent(
      new CustomEvent('cart.update', {
        detail: {
          data,
          count
        }
      })
    );
  }
}

function findAttachments() {
  const attachments = document.querySelectorAll('[data-role=attachment]');
  const attachItems: Record<string, number> = {};

  for (const attachment of attachments) {
    const idInput = attachment.querySelector<HTMLInputElement>('[data-role=attachment_id]')!;
    const qtyInput = attachment.querySelector<HTMLInputElement>('[data-role=attachment_quantity]')!;

    if (idInput.checked) {
      attachItems[idInput.value] = Number(qtyInput.value);
    }
  }

  return attachItems;
}
