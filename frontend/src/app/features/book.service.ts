import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';

import { ApiService } from '../core/api.service';
import { Book } from './book.model';
import { PaginatedResponse } from '../shared/paginated-response.model';

@Injectable({
  providedIn: 'root'
})
export class BookService {

  constructor(private api: ApiService) {}

 getAll(
  page: number = 1,
  size: number = 10
): Observable<PaginatedResponse<Book>> {
  return this.api.get<PaginatedResponse<Book>>(
    `/books?page=${page}&size=${size}`
  );
}

  getById(id: number): Observable<Book> {
    return this.api.get<Book>(`/books/${id}`);
  }

  create(data: Omit<Book, 'id'>): Observable<unknown> {
    return this.api.post('/books', data);
  }

  update(id: number, data: Omit<Book, 'id'>): Observable<unknown> {
    return this.api.put(`/books/${id}`, data);
  }

  delete(id: number): Observable<unknown> {
    return this.api.delete(`/books/${id}`);
  }

  searchByTitle(title: string): Observable<PaginatedResponse<Book>> {
    return this.api.get<PaginatedResponse<Book>>(
      `/books?title=${encodeURIComponent(title)}`
    );
  }

  searchByFilters(endpoint: string): Observable<PaginatedResponse<Book>> {
  return this.api.get<PaginatedResponse<Book>>(endpoint);
}

  getAvailable(
  page: number = 1,
  size: number = 10
): Observable<PaginatedResponse<Book>> {
  return this.api.get<PaginatedResponse<Book>>(
    `/books?available=true&page=${page}&size=${size}`
  );
}
}