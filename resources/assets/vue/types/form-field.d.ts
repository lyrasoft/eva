export interface FormType {
  id: string;
  name: string;
  icon: string;
  params: any;
  description: string;
}

export interface FormTypeParams {
  type: string;
  [name: string]: any;
}
