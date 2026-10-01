export interface Loan {
  id: number;
  book_id: number;
  member_id: number;
  loan_date: string;
  due_date: string;
  return_date: string | null;
  status: 'ACTIVE' | 'RETURNED';
}

export interface CreateLoan {
  book_id: number;
  member_id: number;
  loan_date: string;
}