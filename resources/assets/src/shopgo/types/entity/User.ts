export interface User {
  id: number | string;
  email: string;
  name: string;
  [name: string]: any;
}

