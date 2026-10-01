import { ChangeDetectorRef, Component, OnInit } from '@angular/core';
import { ReactiveFormsModule, FormBuilder, Validators } from '@angular/forms';

import { AuthorService } from '../author.service';
import { Author } from '../author.model';

@Component({
  selector: 'app-authors',
  standalone: true,
  imports: [ReactiveFormsModule],
  templateUrl: './authors.html',
  styleUrl: './authors.css'
})


export class Authors implements OnInit {

  authorForm!: ReturnType<FormBuilder['group']>;

  authors: Author[] = [];

  editingAuthorId: number | null = null;

  currentPage = 1;
  pageSize = 10;
  totalPages = 0;

  constructor(
    private authorService: AuthorService,
    private cdr: ChangeDetectorRef,
    private fb: FormBuilder
  ) {}

  ngOnInit(): void {
    this.authorForm = this.fb.group({
      first_name: ['', Validators.required],
      last_name: ['', Validators.required],
      nationality: ['', Validators.required],
      birth_date: ['', Validators.required]
    });

    this.loadAuthors();
  }

  loadAuthors(): void {
  this.authorService.getAll(
    this.currentPage,
    this.pageSize
  ).subscribe({
    next: (data) => {
      this.authors = data.items;
      this.totalPages = data.totalPages;

      this.cdr.markForCheck();
    },
    error: (error) => {
      console.error('Error al obtener autores:', error);
    }
  });
}

  createAuthor(): void {
  if (this.authorForm.invalid) {
    this.authorForm.markAllAsTouched();
    return;
  }

  const author = this.authorForm.getRawValue();

  this.authorService.create({
    first_name: author.first_name ?? '',
    last_name: author.last_name ?? '',
    nationality: author.nationality ?? '',
    birth_date: author.birth_date ?? ''
  }).subscribe({
    next: () => {
      alert('Autor registrado correctamente');

      this.authorForm.reset();

      this.loadAuthors();
    },
    error: (error) => {
      console.error('Error al crear autor:', error);

      alert(
        error.error?.message ||
        'No se pudo registrar el autor'
      );
    }
  });
}

editAuthor(author: Author): void {
  this.editingAuthorId = author.id;

  this.authorForm.patchValue({
    first_name: author.first_name,
    last_name: author.last_name,
    nationality: author.nationality,
    birth_date: author.birth_date
  });
}

cancelEdit(): void {
  this.editingAuthorId = null;

  this.authorForm.reset();
}

  updateAuthor(): void {
  if (this.editingAuthorId === null) {
    return;
  }

  if (this.authorForm.invalid) {
    this.authorForm.markAllAsTouched();
    return;
  }

  const author = this.authorForm.getRawValue();

  this.authorService.update(
    this.editingAuthorId,
    {
      first_name: author.first_name ?? '',
      last_name: author.last_name ?? '',
      nationality: author.nationality ?? '',
      birth_date: author.birth_date ?? ''
    }
  ).subscribe({
    next: () => {
      alert('Autor actualizado correctamente');

      this.cancelEdit();
      this.loadAuthors();
    },
    error: (error) => {
      console.error('Error al actualizar autor:', error);

      alert(
        error.error?.message ||
        'No se pudo actualizar el autor'
      );
    }
  });
}

  deleteAuthor(id: number): void {
    const confirmed = confirm(
      '¿Está seguro de eliminar este autor?'
    );

    if (!confirmed) {
      return;
    }

    this.authorService.delete(id).subscribe({
      next: () => {
        alert('Autor eliminado correctamente');
        this.loadAuthors();
      },
      error: (error) => {
        console.error('Error al eliminar autor:', error);
        alert(
          error.error?.message ||
          'No se pudo eliminar el autor'
        );
      }
    });
  }
}