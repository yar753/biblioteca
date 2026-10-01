import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { CreateLoan, Loan } from './loan.model';
import { ApiService } from '../core/api.service';

@Injectable({
  providedIn: 'root'
})
export class LoanService {
  constructor(private api: ApiService) {}

  getAll(): Observable<Loan[]> {
    return this.api.get<Loan[]>('/loans');
  }

  getFiltered(
  memberId: number | null,
  status: string | null
): Observable<Loan[]> {
  let url = '/loans?';

  if (memberId !== null) {
    url += `memberId=${memberId}&`;
  }

  if (status !== null) {
    url += `status=${encodeURIComponent(status)}&`;
  }

  return this.api.get<Loan[]>(url);
}


  getById(id: number): Observable<Loan> {
    return this.api.get<Loan>(`/loans/${id}`);
  }

  create(data: CreateLoan): Observable<unknown> {
  return this.api.post('/loans', data);
}

  update(id: number, data: Omit<Loan, 'id'>): Observable<unknown> {
    return this.api.put(`/loans/${id}`, data);
  }

  delete(id: number): Observable<unknown> {
    return this.api.delete(`/loans/${id}`);
  }

  returnLoan(id: number, returnDate?: string): Observable<unknown> {
  return this.api.patch(`/loans/${id}/return`, {
    return_date: returnDate
  });
}

}