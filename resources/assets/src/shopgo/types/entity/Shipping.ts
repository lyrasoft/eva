export interface Shipping<Params = any> {
  id: number;
  locationCategoryId: number;
  locationId: number;
  classname: string;
  type: string;
  title: string;
  subtitle: string;
  alias: string;
  description: string;
  note: string;
  image: string;
  payments: any;
  allowTags: any;
  unallowTags: any;
  pricing: any;
  state: boolean;
  ordering: number;
  created: string | null;
  modified: string | null;
  createdBy: number;
  modifiedBy: number;
  params: Params;
  [prop: string]: any;
}
