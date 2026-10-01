import { Routes } from '@angular/router';
import { Authors } from './features/authors/authors';
import { Books } from './features/books/books';
import { Members } from './features/members/members';
import { Loans } from './features/loans/loans';

export const routes: Routes = [
  {
    path: 'authors',
    component: Authors
  },
  {
    path: 'books',
    component: Books
  },
  {
    path: 'members',
    component: Members
  },
  {
    path: 'loans',
    component: Loans
  },
  {
    path: '',
    redirectTo: 'authors',
    pathMatch: 'full'
  },
  {
    path: '**',
    redirectTo: 'authors'
  }
];