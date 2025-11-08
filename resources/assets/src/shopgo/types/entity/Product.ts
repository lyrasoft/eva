export interface Product {
  id: number;
  categoryId: number;
  primaryVariantId: number;
  model: string;
  title: string;
  alias: string;
  originPrice: number;
  safeStock: number;
  intro: string;
  description: string;
  meta: any;
  canAttach: boolean;
  variants: number;
  ordering: number;
  hide: boolean;
  state: boolean;
  searchIndex: string;
  shippings: any;
  publishUp: string | null;
  publishDown: string | null;
  created: string | null;
  modified: string | null;
  createdBy: number;
  modifiedBy: number;
  hits: number;
  params: any;
  [prop: string]: any;
}
