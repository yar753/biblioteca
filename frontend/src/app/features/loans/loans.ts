import { ChangeDetectorRef, Component, OnInit } from '@angular/core';
import { FormsModule, ReactiveFormsModule, FormBuilder, Validators } from '@angular/forms';
import { forkJoin } from 'rxjs';

import { LoanService } from '../loan.service';
import { CreateLoan, Loan } from '../loan.model';
import { BookService } from '../book.service';
import { Book } from '../book.model';
import { MemberService } from '../member.service';
import { Member } from '../member.model';

@Component({
  selector: 'app-loans',
  standalone: true,
  imports: [FormsModule, ReactiveFormsModule],
  templateUrl: './loans.html',
  styleUrl: './loans.css'
})

export class Loans implements OnInit {
  loanForm!: ReturnType<FormBuilder['group']>;
  loans: Loan[] = [];
  books: Book[] = [];
  availableBooks: Book[] = [];
  members: Member[] = [];
  activeMembers: Member[] = [];
  selectedMemberId: number | null = null;
  selectedStatus: string | null = null;

  showForm = false;

  
  constructor(
  private loanService: LoanService,
  private bookService: BookService,
  private memberService: MemberService,
  private cdr: ChangeDetectorRef,
  private fb: FormBuilder
) {}

  ngOnInit(): void {
  this.loanForm = this.fb.group({
  book_id: [0, Validators.min(1)],
  member_id: [0, Validators.min(1)],
  loan_date: ['', Validators.required]
});

  this.loadData();
}

  loadData(): void {
    forkJoin({
      loans: this.loanService.getAll(),
      books: this.bookService.getAll(),
      members: this.memberService.getAll()
    }).subscribe({
      next: (data) => {
        this.loans = data.loans;
        this.books = data.books.items;

        this.availableBooks = data.books.items.filter(
        book => book.available_copies > 0
);
        this.members = data.members.items;

this.activeMembers = data.members.items.filter(
  member => member.is_active
);

        this.cdr.markForCheck();
      },
      error: (error) => {
        console.error('Error al obtener los datos:', error);
      }
    });
  }

  filterLoans(): void {
  this.loanService.getFiltered(
    this.selectedMemberId,
    this.selectedStatus
  ).subscribe({
    next: (data) => {
      this.loans = data;
      this.cdr.markForCheck();
    },
    error: (error) => {
      console.error('Error al filtrar préstamos:', error);
    }
  });
}

  getBookTitle(bookId: number): string {
    const book = this.books.find(
      book => book.id === bookId
    );

    return book
      ? book.title
      : 'Libro no encontrado';
  }

  getMemberName(memberId: number): string {
    const member = this.members.find(
      member => member.id === memberId
    );

    return member
      ? member.full_name
      : 'Miembro no encontrado';
  }

  createLoan(): void {
  if (this.loanForm.invalid) {
    this.loanForm.markAllAsTouched();
    return;
  }

  const loan = this.loanForm.getRawValue();
  



  const loanData: CreateLoan = {
  book_id: Number(loan.book_id),
  member_id: Number(loan.member_id),
  loan_date: loan.loan_date ?? ''
};

  this.loanService.create(loanData).subscribe({
    next: () => {
      alert('Préstamo registrado correctamente');

      this.resetForm();
      this.showForm = false;

      this.loadData();
    },
    error: (error) => {
      console.error(
        'Error al crear el préstamo:',
        error
      );

      alert(
        error.error?.message ||
        'No se pudo registrar el préstamo'
      );
    }
  });
}

  returnLoan(id: number): void {
    const confirmed = confirm(
      '¿Está seguro de registrar la devolución de este préstamo?'
    );

    if (!confirmed) {
      return;
    }

    this.loanService.returnLoan(id).subscribe({
      next: () => {
        alert('Préstamo devuelto correctamente');
        this.loadData();
      },
      error: (error) => {
        console.error(
          'Error al devolver el préstamo:',
          error
        );

        alert(
          error.error?.message ||
          'No se pudo devolver el préstamo'
        );
      }
    });
  }

 resetForm(): void {
  this.loanForm.reset({
    book_id: 0,
    member_id: 0,
    loan_date: ''
  });
}
}