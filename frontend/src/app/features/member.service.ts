import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';

import { ApiService } from '../core/api.service';
import { Member } from './member.model';
import { PaginatedResponse } from '../shared/paginated-response.model';

@Injectable({
  providedIn: 'root'
})
export class MemberService {
  constructor(private api: ApiService) {}

  getAll(
  page: number = 1,
  size: number = 10
): Observable<PaginatedResponse<Member>> {
  return this.api.get<PaginatedResponse<Member>>(
    `/members?page=${page}&size=${size}`
  );
}

  getById(id: number): Observable<Member> {
    return this.api.get<Member>(`/members/${id}`);
  }

  create(data: Omit<Member, 'id'>): Observable<unknown> {
    return this.api.post('/members', data);
  }

  update(id: number, data: Omit<Member, 'id'>): Observable<unknown> {
    return this.api.put(`/members/${id}`, data);
  }
  
  deactivate(id: number): Observable<unknown> {
  return this.api.patch(`/members/${id}/deactivate`, {});
}

}