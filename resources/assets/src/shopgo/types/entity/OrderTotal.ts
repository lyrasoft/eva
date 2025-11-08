export interface OrderTotal {
  id: number;
  orderId: number;
  discountId: number;
  discountType: string;
  type: string;
  title: string;
  code: string;
  value: number;
  ordering: number;
  protect: boolean;
  params: any;
  [prop: string]: any;
}
