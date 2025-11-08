export interface PriceObject {
  name: string;
  label: string;
  price: string;
  params: Record<string, any>;
}

export type PriceSet = {
  origin: PriceObject;
  base: PriceObject;
  final: PriceObject;
  total: PriceObject;
  grand_total: PriceObject;
  shipping_fee: PriceObject;
  [name: string]: PriceObject;
};
