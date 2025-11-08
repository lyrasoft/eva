import { ListOption } from '../options';

export interface ProductFeature {
  id: number;
  type: string;
  title: string;
  default: string;
  note: string;
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
