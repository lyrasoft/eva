export interface FormType {
  id: string;
  type: string;
  title: string;
  group: string;
  icon: string;
  params: any;
  description: string;
  componentName: string;
  componentModuleUrl: string | null;
}

export interface FormTypeParams {
  type: string;
  [name: string]: any;
}
