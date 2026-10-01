export interface Book {
  id: number;
  isbn: string;
  title: string;
  author_id: number;
  publication_year: number | null;
  total_copies: number;
  available_copies: number;
}