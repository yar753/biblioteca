import { ChangeDetectorRef, Component, OnInit } from '@angular/core';
import { FormsModule, ReactiveFormsModule, FormBuilder, Validators } from '@angular/forms';

import { BookService } from '../book.service';
import { Book } from '../book.model';
import { AuthorService } from '../author.service';
import { Author } from '../author.model';

@Component({
  selector: 'app-books',
  standalone: true,
  imports: [FormsModule, ReactiveFormsModule],
  templateUrl: './books.html',
  styleUrl: './books.css'
})

export class Books implements OnInit {
  bookForm!: ReturnType<FormBuilder['group']>;
  books: Book[] = [];
  authors: Author[] = [];
  searchTitle = '';
  selectedAuthorId: number | null = null;
  editingBookId: number | null = null;

currentPage = 1;
pageSize = 10;
totalPages = 0;


  constructor(
  private bookService: BookService,
  private authorService: AuthorService,
  private cdr: ChangeDetectorRef,
  private fb: FormBuilder
) {}

  ngOnInit(): void {
    this.bookForm = this.fb.group({
  isbn: ['', Validators.required],
  title: ['', Validators.required],
  author_id: [0, Validators.min(1)],
  publication_year: [null as number | null],
  total_copies: [1, [Validators.required, Validators.min(1)]],
  available_copies: [1, [Validators.required, Validators.min(0)]]
});

    this.loadBooks();
    this.loadAuthors();
  }

  loadBooks(): void {
  this.bookService.getAll(
    this.currentPage,
    this.pageSize
  ).subscribe({
    next: (data) => {
      this.books = data.items;
      this.totalPages = data.totalPages;

      this.cdr.markForCheck();
    },
    error: (error) => {
      console.error('Error al obtener libros:', error);
    }
  });
}

  loadAuthors(): void {
    this.authorService.getAll().subscribe({
      next: (data) => {
        this.authors = data.items;
        this.cdr.markForCheck();
      },
      error: (error) => {
        console.error('Error al obtener autores:', error);
      }
    });
  }

  searchBooks(): void {
  const title = this.searchTitle.trim();

  if (!title && this.selectedAuthorId === null) {
    this.loadBooks();
    return;
  }

  let url = '/books?';

  if (title) {
    url += `title=${encodeURIComponent(title)}&`;
  }

  if (this.selectedAuthorId !== null) {
    url += `authorId=${this.selectedAuthorId}&`;
  }

  this.bookService.searchByFilters(url).subscribe({
    next: (data) => {
      this.books = data.items;
      this.cdr.markForCheck();
    },
    error: (error) => {
      console.error('Error al buscar libros:', error);
    }
  });
}

 showAvailableBooks(): void {
  this.bookService.getAvailable(
    this.currentPage,
    this.pageSize
  ).subscribe({
    next: (data) => {
      this.books = data.items;
      this.totalPages = data.totalPages;

      this.cdr.markForCheck();
    },
    error: (error) => {
      console.error(
        'Error al obtener libros disponibles:',
        error
      );
    }
  });
}

  createBook(): void {
  if (this.bookForm.invalid) {
    this.bookForm.markAllAsTouched();
    return;
  }

  const book = this.bookForm.getRawValue();

  const bookData = {
    isbn: book.isbn ?? '',
    title: book.title ?? '',
    author_id: Number(book.author_id),
    publication_year: book.publication_year
      ? Number(book.publication_year)
      : null,
    total_copies: Number(book.total_copies),
    available_copies: Number(book.total_copies)
  };

  this.bookService.create(bookData).subscribe({
    next: () => {
      alert('Libro registrado correctamente');

      this.resetBookForm();
      this.loadBooks();
    },
    error: (error) => {
      console.error('Error al registrar el libro:', error);

      alert(
        error.error?.message ||
        'No se pudo registrar el libro'
      );
    }
  });
}

  editBook(book: Book): void {
  this.editingBookId = book.id;

  this.bookForm.patchValue({
    isbn: book.isbn,
    title: book.title,
    author_id: book.author_id,
    publication_year: book.publication_year,
    total_copies: book.total_copies,
    available_copies: book.available_copies
  });
}

 updateBook(): void {
  if (this.editingBookId === null) {
    return;
  }

  if (this.bookForm.invalid) {
    this.bookForm.markAllAsTouched();
    return;
  }

  const book = this.bookForm.getRawValue();

  const bookData = {
    isbn: book.isbn ?? '',
    title: book.title ?? '',
    author_id: Number(book.author_id),
    publication_year: book.publication_year
      ? Number(book.publication_year)
      : null,
    total_copies: Number(book.total_copies),
    available_copies: Number(book.available_copies)
  };

  if (bookData.available_copies > bookData.total_copies) {
    alert('Las copias disponibles no pueden superar el total');
    return;
  }

  this.bookService.update(this.editingBookId, bookData).subscribe({
    next: () => {
      alert('Libro actualizado correctamente');

      this.resetBookForm();
      this.loadBooks();
    },
    error: (error) => {
      console.error('Error al actualizar el libro:', error);

      alert(
        error.error?.message ||
        'No se pudo actualizar el libro'
      );
    }
  });
}

  cancelEdit(): void {
    this.resetBookForm();
  }

  resetBookForm(): void {
  this.editingBookId = null;

  this.bookForm.reset({
    isbn: '',
    title: '',
    author_id: 0,
    publication_year: null,
    total_copies: 1,
    available_copies: 1
  });
}

  deleteBook(id: number): void {
    const confirmed = confirm(
      '¿Está seguro de eliminar este libro?'
    );

    if (!confirmed) {
      return;
    }

    this.bookService.delete(id).subscribe({
      next: () => {
        alert('Libro eliminado correctamente');
        this.loadBooks();
      },
      error: (error) => {
        console.error('Error al eliminar el libro:', error);
        alert(
          error.error?.message ||
          'No se pudo eliminar el libro'
        );
      }
    });
  }
}