import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';

import { ApiService } from '../core/api.service';
import { Author } from './author.model';
import { PaginatedResponse } from '../shared/paginated-response.model';

@Injectable({
  providedIn: 'root'
})
export class AuthorService {

  constructor(private api: ApiService) {}

  getAll(
  page: number = 1,
  size: number = 10
): Observable<PaginatedResponse<Author>> {
  return this.api.get<PaginatedResponse<Author>>(
    `/authors?page=${page}&size=${size}`
  );
}

  getById(id: number): Observable<Author> {
    return this.api.get<Author>(`/authors/${id}`);
  }

  create(data: Omit<Author, 'id'>): Observable<unknown> {
    return this.api.post('/authors', data);
  }

  update(id: number, data: Omit<Author, 'id'>): Observable<unknown> {
    return this.api.put(`/authors/${id}`, data);
  }

  delete(id: number): Observable<unknown> {
    return this.api.delete(`/authors/${id}`);
  }
}