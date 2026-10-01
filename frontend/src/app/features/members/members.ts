import { ChangeDetectorRef, Component, OnInit } from '@angular/core';
import { FormsModule, ReactiveFormsModule, FormBuilder, Validators } from '@angular/forms';

import { MemberService } from '../member.service';
import { Member } from '../member.model';

@Component({
  selector: 'app-members',
  standalone: true,
  imports: [FormsModule, ReactiveFormsModule],
  templateUrl: './members.html',
  styleUrl: './members.css'
})
export class Members implements OnInit {
  memberForm!: ReturnType<FormBuilder['group']>;
  members: Member[] = [];
  editingMemberId: number | null = null;

currentPage = 1;
pageSize = 10;
totalPages = 0;

  constructor(
  private memberService: MemberService,
  private cdr: ChangeDetectorRef,
  private fb: FormBuilder
) {}

  ngOnInit(): void {
    this.memberForm = this.fb.group({
  document_number: ['', Validators.required],
  full_name: ['', Validators.required],
  email: ['', [Validators.required, Validators.email]],
  phone: [''],
  is_active: [true]
});

    this.loadMembers();
  }

  loadMembers(): void {
  this.memberService.getAll(
    this.currentPage,
    this.pageSize
  ).subscribe({
    next: (data) => {
      this.members = data.items;
      this.totalPages = data.totalPages;

      this.cdr.markForCheck();
    },
    error: (error) => {
      console.error('Error al obtener miembros:', error);
    }
  });
}

  createMember(): void {
  if (this.memberForm.invalid) {
    this.memberForm.markAllAsTouched();
    return;
  }

  const member = this.memberForm.getRawValue();

  const memberData = {
    document_number: member.document_number ?? '',
    full_name: member.full_name ?? '',
    email: member.email ?? '',
    phone: member.phone ?? '',
    is_active: true
  };

  this.memberService.create(memberData).subscribe({
    next: () => {
      alert('Miembro registrado correctamente');

      this.resetMemberForm();
      this.loadMembers();
    },
    error: (error) => {
      console.error('Error al registrar miembro:', error);

      alert(
        error.error?.message ||
        'No se pudo registrar el miembro'
      );
    }
  });
}

  editMember(member: Member): void {
  this.editingMemberId = member.id;

  this.memberForm.patchValue({
    document_number: member.document_number,
    full_name: member.full_name,
    email: member.email,
    phone: member.phone || '',
    is_active: member.is_active
  });
}

  updateMember(): void {
  if (this.editingMemberId === null) {
    return;
  }

  if (this.memberForm.invalid) {
    this.memberForm.markAllAsTouched();
    return;
  }

  const member = this.memberForm.getRawValue();

  const memberData = {
    document_number: member.document_number ?? '',
    full_name: member.full_name ?? '',
    email: member.email ?? '',
    phone: member.phone ?? '',
    is_active: member.is_active ?? true
  };

  this.memberService.update(
    this.editingMemberId,
    memberData
  ).subscribe({
    next: () => {
      alert('Miembro actualizado correctamente');

      this.resetMemberForm();
      this.loadMembers();
    },
    error: (error) => {
      console.error('Error al actualizar miembro:', error);

      alert(
        error.error?.message ||
        'No se pudo actualizar el miembro'
      );
    }
  });
}

  cancelEdit(): void {
    this.resetMemberForm();
  }

  resetMemberForm(): void {
  this.editingMemberId = null;

  this.memberForm.reset({
    document_number: '',
    full_name: '',
    email: '',
    phone: '',
    is_active: true
  });
}

  deactivateMember(id: number): void {
  const confirmed = confirm(
    '¿Está seguro de desactivar este miembro?'
  );

  if (!confirmed) {
    return;
  }

  this.memberService.deactivate(id).subscribe({
    next: () => {
      alert('Miembro desactivado correctamente');
      this.loadMembers();
    },
    error: (error) => {
      console.error('Error al desactivar miembro:', error);

      alert(
        error.error?.message ||
        'No se pudo desactivar el miembro'
      );
    }
  });
}
}