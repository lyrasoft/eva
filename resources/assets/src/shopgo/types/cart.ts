import { Address, Discount, OrderTotal, PriceSet, Product, ProductVariant, Shipping } from '~shopgo/types';

export interface CartData {
  items: CartItem[];
  totals: Record<string, OrderTotal>;
  coupons: Discount[];
  discounts: Discount[];
  location: any;
  params: Record<string, any>;
  shipping: Shipping | null;
}

export interface CartItem {
  mainVariant: ProductVariant;
  variant: ProductVariant;
  product: Product;
  priceSet: PriceSet;
  quantity: number;
  cover: string;
  link: string;
  key: string;
  uid: string;
  outOfStock: boolean;
  payload: Record<string, any>;
  options: {
    checked: boolean;
    [name: string]: any;
  };
  attachments: CartItem[];
  discounts: Discount[];
}

export type AddressFormData = Address & {
  // We save id as addressId to avoid confusion of shipping / payment data
  addressId?: string | number;
  locationPath: (number | string)[];
  formatted: string;
}
