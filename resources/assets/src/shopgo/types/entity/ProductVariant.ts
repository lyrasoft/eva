export interface ProductVariant {
  id: number;
  productId: number;
  title: string;
  hash: string;
  primary: boolean;
  sku: string;
  upc: string;
  ean: string;
  jan: string;
  isbn: string;
  mpn: string;
  stockQuantity: number;
  subtract: boolean;
  price: number;
  dimension: any;
  stockBuyable: boolean;
  outOfStockText: string;
  cover: string;
  images: any;
  options: any;
  state: boolean;
  created: string | null;
  modified: string | null;
  createdBy: number;
  modifiedBy: number;
  params: any;
  [prop: string]: any;
}
