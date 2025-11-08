export interface Payment {
  id: number;
  locationCategoryId: number;
  locationId: number;
  orderStateId: number;
  classname: string;
  type: string;
  title: string;
  subtitle: string;
  alias: string;
  description: string;
  note: string;
  image: string;
  state: boolean;
  ordering: number;
  created: string | null;
  modified: string | null;
  createdBy: number;
  modifiedBy: number;
  params: any;
  [prop: string]: any;
}
