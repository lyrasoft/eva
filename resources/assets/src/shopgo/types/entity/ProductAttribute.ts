import { ListOption } from '~shopgo/types';

export interface ProductAttribute {
  id: number;
  categoryId: number;
  type: string;
  title: string;
  key: string;
  display: boolean;
  ordering: number;
  state: boolean;
  options: ListOption[];
  created: string | null;
  modified: string | null;
  createdBy: number;
  modifiedBy: number;
  params: any;
  [prop: string]: any;
}
