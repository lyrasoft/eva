export interface Currency {
  id: number;
  title: string;
  code: string;
  codeNum: number;
  sign: string;
  signPosition: string;
  decimalPlace: any;
  decimalPoint: string;
  numSeparator: string;
  exchangeRate: number;
  space: boolean;
  state: boolean;
  created: string | null;
  modified: string | null;
  createdBy: number;
  modifiedBy: number;
  params: any;
  [prop: string]: any;
}


