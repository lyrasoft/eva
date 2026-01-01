export interface Lesson {
  id: number;
  categoryId: number;
  title: string;
  alias: string;
  teacherId: number;
  description: string;
  acquired: string;
  image: string;
  state: any;
  startDate: string | null;
  endDate: string | null;
  isStepByStep: boolean;
  hasCertificate: boolean;
  price: number;
  specialPrice: number;
  isSpecial: boolean;
  isFree: boolean;
  passAverageScore: number;
  passMinScore: number;
  ordering: number;
  created: string | null;
  modified: string | null;
  createdBy: number;
  modifiedBy: number;
  params: any;
  [prop: string]: any;
}
