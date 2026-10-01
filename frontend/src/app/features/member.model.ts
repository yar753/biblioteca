export interface Member {
  id: number;
  document_number: string;
  full_name: string;
  email: string;
  phone: string | null;
  is_active: boolean;
}