export interface Address {
  id?: number | null;
  userId: number;
  locationId: number;
  firstname: string;
  lastname: string;
  name: string;
  email: string;
  phone: string;
  mobile: string;
  company: string;
  country: string;
  state: string;
  city: string;
  postcode: string;
  address1: string;
  address2: string;
  vat: string;
  formatted: string;
  details: any;
  enabled: boolean;
  created: string | null;
  modified: string | null;
  [prop: string]: any;
}
