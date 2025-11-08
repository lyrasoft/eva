import { numberFormat } from '@lyrasoft/ts-toolkit/generic';
import { data, route, useFormAsync, useUniDirective } from '@windwalker-io/unicorn-next';
import { Currency } from '~shopgo/types';

interface CurrencyInfo {
  main: Currency;
  current: Currency;
}

export interface CurrencyFormatOptions {
  code?: boolean;
  sign?: boolean;
  signPosition?: 'start' | 'end';
}

export function useCurrency(currencyOptions: CurrencyFormatOptions = {}) {
  function getCurrentCurrency(): Currency {
    return data<CurrencyInfo>('currency')!.current;
  }

  function getMainCurrency(): Currency {
    return data<CurrencyInfo>('currency')!.main;
  }

  function isSubCurrency(): boolean {
    return getCurrentCurrency().code !== getMainCurrency().code;
  }

  function exchange(num: number, currency: any): number {
    return num * currency.exchangeRate;
  }

  function format(num: number | string, currency?: Currency, options: CurrencyFormatOptions = {}): string {
    // normalize number
    let n = typeof num === 'string' ? parseFloat(num) : (num as number);
    if (Number.isNaN(n)) {
      n = 0;
    }

    const currencyObj = currency || getCurrentCurrency();

    options = Object.assign({}, currencyOptions, options);

    const addCode = options?.code ?? false;
    const addSign = options?.sign ?? true;
    const signPosition = options?.signPosition ?? currencyObj.signPosition;

    const negative = n < 0;
    n = Math.abs(n);

    n = exchange(n, currencyObj);

    let formatted = numberFormat(n, currencyObj.decimalPlace, currencyObj.decimalPoint);

    const space = currencyObj.space ? ' ' : '';

    if (addSign) {
      if (signPosition === 'start') {
        formatted = currencyObj.sign + space + formatted;
      } else {
        formatted += space + currencyObj.sign;
      }
    }

    if (negative) {
      return '-' + formatted;
    }

    if (addCode) {
      formatted = currencyObj.code + " " + formatted;
    }

    return formatted;
  }

  function formatMainCurrency(num: number | string, options: CurrencyFormatOptions = {}): string {
    return format(num, getMainCurrency(), options);
  }

  return {
    isSubCurrency,
    getCurrentCurrency,
    getMainCurrency,
    format,
    formatMainCurrency,
    exchange,
  };
}

export function useCurrencySwitcher() {
  let listener: any = null;

  return useUniDirective<HTMLAnchorElement>('currency-switch', {
    mounted(el, { value }) {
      listener = async () => {
        const url = route('currency_switch');

        const form = await useFormAsync();
        form.post(url, { code: value });
      };

      el.addEventListener('click', listener);
    },
    unmounted(el) {
      if (listener) {
        el.removeEventListener('click', listener);
        listener = null;
      }
    },
  });
}
