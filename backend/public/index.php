<?php

header('Access-Control-Allow-Origin: http://localhost:4200');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');


if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

require __DIR__ . '/../vendor/autoload.php';

use App\Infrastructure\Config\Database;
use App\Infrastructure\Persistence\PdoAuthorRepository;
use App\Application\UseCase\CreateAuthor;
use App\Application\UseCase\UpdateAuthor;
use App\Application\UseCase\DeleteAuthor;
use App\Application\UseCase\CreateBook;
use App\Infrastructure\Persistence\PdoBookRepository;
use App\Application\UseCase\UpdateBook;
use App\Application\UseCase\DeleteBook;
use App\Infrastructure\Persistence\PdoMemberRepository;
use App\Application\UseCase\CreateMember;
use App\Application\UseCase\UpdateMember;
use App\Application\UseCase\DeleteMember;
use App\Application\UseCase\GetMembers;
use App\Application\UseCase\GetMemberById;
use App\Infrastructure\Persistence\PdoLoanRepository;
use App\Application\UseCase\CreateLoan;
use App\Application\UseCase\UpdateLoan;
use App\Application\UseCase\DeleteLoan;
use App\Application\UseCase\GetLoans;
use App\Application\UseCase\GetLoanById;
use App\Application\UseCase\ReturnLoan;
use App\Application\UseCase\DeactivateMember;
use App\Infrastructure\Persistence\PdoTransactionManager;
use App\Application\Exception\DuplicateResourceException;

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = preg_replace('#^/api/v1#', '', $path);

try {
    $connection = Database::connection();

    $authorRepository = new PdoAuthorRepository($connection);
    $memberRepository = new PdoMemberRepository($connection);
    $loanRepository = new PdoLoanRepository($connection);
    $bookRepository = new PdoBookRepository($connection);

  if ($method === 'PATCH' && preg_match('#^/loans/(\d+)/return$#', $path, $matches)) {

    $id = (int) $matches[1];

    $data = json_decode(
        file_get_contents('php://input'),
        true
    );

    $existingLoan = $loanRepository->findById($id);

    if ($existingLoan === null) {
        http_response_code(404);

        echo json_encode([
            'message' => 'Préstamo no encontrado'
        ]);

        exit;
    }

    $returnDate = $data['return_date'] ?? date('Y-m-d');

    $transactionManager = new PdoTransactionManager($connection);

    $useCase = new ReturnLoan(
        $loanRepository,
        $bookRepository,
        $transactionManager
    );

    $useCase->execute(
        $id,
        $returnDate
    );

    http_response_code(200);

    echo json_encode([
        'message' => 'Préstamo devuelto correctamente'
    ]);

    exit;
}


    if ($method === 'GET' && $path === '/loans') {

    $memberId = isset($_GET['memberId'])
        ? (int) $_GET['memberId']
        : null;

    $status = $_GET['status'] ?? null;

    $useCase = new GetLoans($loanRepository);

    $loans = $useCase->execute($memberId, $status);

    echo json_encode($loans);

    exit;
}

if ($method === 'GET' && preg_match('#^/loans/(\d+)$#', $path, $matches)) {

    $id = (int) $matches[1];

    $useCase = new GetLoanById($loanRepository);

    $loan = $useCase->execute($id);

    if ($loan === null) {
        http_response_code(404);

        echo json_encode([
            'message' => 'Préstamo no encontrado'
        ]);

        exit;
    }

    echo json_encode($loan);
    exit;
}

if ($method === 'POST' && $path === '/loans') {

    $data = json_decode(
        file_get_contents('php://input'),
        true
    );

    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode([
            'message' => 'El JSON enviado no es válido'
        ]);
        exit;
    }

    if (
        !isset($data['book_id']) ||
        !isset($data['member_id']) ||
        !isset($data['loan_date'])
    ) {
        http_response_code(400);
        echo json_encode([
            'message' => 'book_id, member_id y loan_date son obligatorios'
        ]);
        exit;
    }

    $transactionManager = new PdoTransactionManager($connection);

    $useCase = new CreateLoan(
        $loanRepository,
        $bookRepository,
        $memberRepository,
        $transactionManager
    );

    $useCase->execute(
        (int) $data['book_id'],
        (int) $data['member_id'],
        $data['loan_date']
    );

    http_response_code(201);

    echo json_encode([
        'message' => 'Préstamo creado correctamente'
    ]);

    exit;
}


if ($method === 'PUT' && preg_match('#^/loans/(\d+)$#', $path, $matches)) {

    $id = (int) $matches[1];

    $data = json_decode(
        file_get_contents('php://input'),
        true
    );

    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);

        echo json_encode([
            'message' => 'El JSON enviado no es válido'
        ]);

        exit;
    }

    $existingLoan = $loanRepository->findById($id);

    if ($existingLoan === null) {
        http_response_code(404);

        echo json_encode([
            'message' => 'Préstamo no encontrado'
        ]);

        exit;
    }

    if (
        !isset($data['book_id']) ||
        !isset($data['member_id']) ||
        !isset($data['loan_date']) ||
        !isset($data['due_date']) ||
        !isset($data['status'])
    ) {
        http_response_code(400);

        echo json_encode([
            'message' => 'book_id, member_id, loan_date, due_date y status son obligatorios'
        ]);

        exit;
    }

    $useCase = new UpdateLoan($loanRepository);

    $useCase->execute(
        $id,
        (int) $data['book_id'],
        (int) $data['member_id'],
        $data['loan_date'],
        $data['due_date'],
        $data['return_date'] ?? null,
        $data['status']
    );

    http_response_code(200);

    echo json_encode([
        'message' => 'Préstamo actualizado correctamente'
    ]);

    exit;
}


if ($method === 'DELETE' && preg_match('#^/loans/(\d+)$#', $path, $matches)) {

    $id = (int) $matches[1];

    $existingLoan = $loanRepository->findById($id);

    if ($existingLoan === null) {
        http_response_code(404);

        echo json_encode([
            'message' => 'Préstamo no encontrado'
        ]);

        exit;
    }

    $useCase = new DeleteLoan($loanRepository);

    $useCase->execute($id);

    echo json_encode([
        'message' => 'Préstamo eliminado correctamente'
    ]);

    exit;
}

   if ($method === 'GET' && $path === '/members') {

    $page = max(1, (int) ($_GET['page'] ?? 1));
    $size = max(1, (int) ($_GET['size'] ?? 10));

    $useCase = new GetMembers($memberRepository);

    $members = $useCase->execute($page, $size);
    $totalItems = $memberRepository->countAll();

    echo json_encode([
        'items' => $members,
        'page' => $page,
        'size' => $size,
        'totalItems' => $totalItems,
        'totalPages' => (int) ceil($totalItems / $size),
    ]);

    exit;
}
    
if ($method === 'GET' && preg_match('#^/members/(\d+)$#', $path, $matches)) {

    $id = (int) $matches[1];

    $useCase = new GetMemberById($memberRepository);

    $member = $useCase->execute($id);

    if ($member === null) {
        http_response_code(404);

        echo json_encode([
            'message' => 'Miembro no encontrado'
        ]);

        exit;
    }

    echo json_encode($member);
    exit;
}


if ($method === 'POST' && $path === '/members') {

    $data = json_decode(
        file_get_contents('php://input'),
        true
    );

    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode([
            'message' => 'El JSON enviado no es válido'
        ]);
        exit;
    }

    if (
        !isset($data['document_number']) ||
        !isset($data['full_name']) ||
        !isset($data['email'])
    ) {
        http_response_code(400);
        echo json_encode([
            'message' => 'document_number, full_name y email son obligatorios'
        ]);
        exit;
    }

    $useCase = new CreateMember(
        $memberRepository
    );

    $useCase->execute(
        $data['document_number'],
        $data['full_name'],
        $data['email'],
        $data['phone'] ?? null
    );

    http_response_code(201);

    echo json_encode([
        'message' => 'Miembro creado correctamente'
    ]);

    exit;
}

if ($method === 'PUT' && preg_match('#^/members/(\d+)$#', $path, $matches)) {

    $id = (int) $matches[1];

    $data = json_decode(
        file_get_contents('php://input'),
        true
    );

    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode([
            'message' => 'El JSON enviado no es válido'
        ]);
        exit;
    }

    $existingMember = $memberRepository->findById($id);

    if ($existingMember === null) {
        http_response_code(404);

        echo json_encode([
            'message' => 'Miembro no encontrado'
        ]);

        exit;
    }

    if (
        !isset($data['document_number']) ||
        !isset($data['full_name']) ||
        !isset($data['email'])
    ) {
        http_response_code(400);
        echo json_encode([
            'message' => 'document_number, full_name y email son obligatorios'
        ]);
        exit;
    }

    $useCase = new UpdateMember($memberRepository);

    $useCase->execute(
        $id,
        $data['document_number'],
        $data['full_name'],
        $data['email'],
        $data['phone'] ?? null,
        isset($data['is_active'])
            ? (bool) $data['is_active']
            : true
    );

    http_response_code(200);

    echo json_encode([
        'message' => 'Miembro actualizado correctamente'
    ]);

    exit;
}

if ($method === 'PATCH' && preg_match('#^/members/(\d+)/deactivate$#', $path, $matches)) {

    $id = (int) $matches[1];

    $existingMember = $memberRepository->findById($id);

    if ($existingMember === null) {
        http_response_code(404);
        echo json_encode([
            'message' => 'Miembro no encontrado'
        ]);
        exit;
    }

    $useCase = new DeactivateMember($memberRepository);

    $useCase->execute($id);

    http_response_code(204);

    exit;
}

if ($method === 'DELETE' && preg_match('#^/members/(\d+)$#', $path, $matches)) {

    $id = (int) $matches[1];

    $existingMember = $memberRepository->findById($id);

    if ($existingMember === null) {
        http_response_code(404);

        echo json_encode([
            'message' => 'Miembro no encontrado'
        ]);

        exit;
    }

    $useCase = new DeleteMember($memberRepository);

    $useCase->execute($id);

    echo json_encode([
        'message' => 'Miembro eliminado correctamente'
    ]);

    exit;
}

if ($method === 'GET' && $path === '/authors') {

    $page = max(1, (int) ($_GET['page'] ?? 1));
    $size = max(1, (int) ($_GET['size'] ?? 10));

    $authors = $authorRepository->findAll($page, $size);
    $totalItems = $authorRepository->countAll();

    echo json_encode([
        'items' => $authors,
        'page' => $page,
        'size' => $size,
        'totalItems' => $totalItems,
        'totalPages' => (int) ceil($totalItems / $size),
    ]);

    exit;
}
    


   if ($method === 'GET' && $path === '/books') {

    $title = $_GET['title'] ?? null;
    $authorId = isset($_GET['authorId'])
        ? (int) $_GET['authorId']
        : null;

    $available = null;

    if (isset($_GET['available'])) {
        $available = filter_var(
            $_GET['available'],
            FILTER_VALIDATE_BOOLEAN,
            FILTER_NULL_ON_FAILURE
        );
    }

    $page = max(1, (int) ($_GET['page'] ?? 1));
    $size = max(1, (int) ($_GET['size'] ?? 10));

    $bookRepository = new PdoBookRepository($connection);

    $books = $bookRepository->findFiltered(
        $title,
        $authorId,
        $available,
        $page,
        $size
    );

    $totalItems = $bookRepository->countFiltered(
        $title,
        $authorId,
        $available
    );

    echo json_encode([
        'items' => $books,
        'page' => $page,
        'size' => $size,
        'totalItems' => $totalItems,
        'totalPages' => (int) ceil($totalItems / $size),
    ]);

    exit;
}

if ($method === 'GET' && preg_match('#^/books/(\d+)$#', $path, $matches)) {

    $bookRepository = new PdoBookRepository($connection);

    $book = $bookRepository->findById((int) $matches[1]);

    if ($book === null) {
        http_response_code(404);

        echo json_encode([
            'message' => 'Libro no encontrado'
        ]);

        exit;
    }

    echo json_encode($book);
    exit;
}

if ($method === 'DELETE' && preg_match('#^/books/(\d+)$#', $path, $matches)) {

    $id = (int) $matches[1];

    $bookRepository = new PdoBookRepository($connection);

    $existingBook = $bookRepository->findById($id);

    if ($existingBook === null) {
        http_response_code(404);

        echo json_encode([
            'message' => 'Libro no encontrado'
        ]);

        exit;
    }

    $useCase = new DeleteBook(
    $bookRepository,
    $loanRepository
);

    $useCase->execute($id);

    http_response_code(204);

exit;
}

if ($method === 'PUT' && preg_match('#^/books/(\d+)$#', $path, $matches)) {

    $id = (int) $matches[1];

    $data = json_decode(
        file_get_contents('php://input'),
        true
    );

    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode([
            'message' => 'El JSON enviado no es válido'
        ]);
        exit;
    }

    $bookRepository = new PdoBookRepository($connection);

    $existingBook = $bookRepository->findById($id);

    if ($existingBook === null) {
        http_response_code(404);

        echo json_encode([
            'message' => 'Libro no encontrado'
        ]);

        exit;
    }

    if (
        !isset($data['isbn']) ||
        !isset($data['title']) ||
        !isset($data['author_id']) ||
        !isset($data['total_copies']) ||
        !isset($data['available_copies'])
    ) {
        http_response_code(400);

        echo json_encode([
            'message' => 'isbn, title, author_id, total_copies y available_copies son obligatorios'
        ]);

        exit;
    }

    $useCase = new UpdateBook($bookRepository);

    $useCase->execute(
        $id,
        $data['isbn'],
        $data['title'],
        (int) $data['author_id'],
        isset($data['publication_year'])
            ? (int) $data['publication_year']
            : null,
        (int) $data['total_copies'],
        (int) $data['available_copies']
    );

    http_response_code(200);

    echo json_encode([
        'message' => 'Libro actualizado correctamente'
    ]);

    exit;
}

   if ($method === 'POST' && $path === '/books') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode([
            'message' => 'El JSON enviado no es válido'
        ]);
        exit;
    }

    if (
        !isset($data['isbn']) ||
        !isset($data['title']) ||
        !isset($data['author_id'])
    ) {
        http_response_code(400);
        echo json_encode([
            'message' => 'isbn, title y author_id son obligatorios'
        ]);
        exit;
    }

    $useCase = new CreateBook(
        new PdoBookRepository(Database::connection())
    );

    $useCase->execute(
        $data['isbn'],
        $data['title'],
        (int) $data['author_id'],
        isset($data['publication_year'])
            ? (int) $data['publication_year']
            : null,
        isset($data['total_copies'])
            ? (int) $data['total_copies']
            : 1
    );

    http_response_code(201);

    echo json_encode([
        'message' => 'Libro creado correctamente'
    ]);

    exit;
}

   if ($method === 'DELETE' && preg_match('#^/authors/(\d+)$#', $path, $matches)) {

    $id = (int) $matches[1];

    $existingAuthor = $authorRepository->findById($id);

    if ($existingAuthor === null) {
        http_response_code(404);
        echo json_encode([
            'message' => 'Autor no encontrado'
        ]);
        exit;
    }

    $useCase = new DeleteAuthor(
        $authorRepository,
        $bookRepository
    );

    $useCase->execute($id);

    http_response_code(204);

    exit;
}

    if ($method === 'GET' && preg_match('#^/authors/(\d+)$#', $path, $matches)) {

    $id = (int) $matches[1];

    $author = $authorRepository->findById($id);

    if ($author === null) {
        http_response_code(404);

        echo json_encode([
            'message' => 'Autor no encontrado'
        ]);

        exit;
    }

    echo json_encode($author);
    exit;
}

if ($method === 'PUT' && preg_match('#^/authors/(\d+)$#', $path, $matches)) {

    $id = (int) $matches[1];

    $existingAuthor = $authorRepository->findById($id);

    if ($existingAuthor === null) {
        http_response_code(404);
        echo json_encode([
            'message' => 'Autor no encontrado'
        ]);
        exit;
    }

    $data = json_decode(
        file_get_contents('php://input'),
        true
    );

    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode([
            'message' => 'El JSON enviado no es válido'
        ]);
        exit;
    }

    if (
        !isset($data['first_name']) ||
        !isset($data['last_name'])
    ) {
        http_response_code(400);
        echo json_encode([
            'message' => 'first_name y last_name son obligatorios'
        ]);
        exit;
    }

    $updateAuthor = new UpdateAuthor(
        $authorRepository
    );

    $updateAuthor->execute(
        $id,
        $data['first_name'],
        $data['last_name'],
        $data['nationality'] ?? null,
        $data['birth_date'] ?? null
    );

    http_response_code(200);

    echo json_encode([
        'message' => 'Autor actualizado correctamente'
    ]);

    exit;
}

   if ($method === 'POST' && $path === '/authors') {

    $data = json_decode(
        file_get_contents('php://input'),
        true
    );

    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode([
            'message' => 'El JSON enviado no es válido'
        ]);
        exit;
    }

    if (
        !isset($data['first_name']) ||
        !isset($data['last_name'])
    ) {
        http_response_code(400);
        echo json_encode([
            'message' => 'first_name y last_name son obligatorios'
        ]);
        exit;
    }

    $createAuthor = new CreateAuthor(
        $authorRepository
    );

    $createAuthor->execute(
        $data['first_name'],
        $data['last_name'],
        $data['nationality'] ?? null,
        $data['birth_date'] ?? null
    );

    http_response_code(201);

    echo json_encode([
        'message' => 'Autor creado correctamente'
    ]);

    exit;
}

    http_response_code(404);

    echo json_encode([
        'message' => 'Ruta no encontrada'
    ]);

} 

catch (DuplicateResourceException $e) {
    http_response_code(409);
    echo json_encode([
        'message' => $e->getMessage()
    ]);
}


catch (\InvalidArgumentException $e) {

    http_response_code(422);

    echo json_encode([
        'message' => $e->getMessage()
    ]);

} catch (\PDOException $e) {

    if ($e->errorInfo[1] === 1062) {

        http_response_code(409);

        echo json_encode([
            'message' => 'El ISBN ya existe'
        ]);

    } elseif ($e->errorInfo[1] === 1452) {

        http_response_code(422);

        echo json_encode([
            'message' => 'El autor no existe'
        ]);

    } else {

        http_response_code(500);

        echo json_encode([
            'message' => 'Error interno del servidor'
        ]);
    }
    

} catch (\Exception $e) {

    http_response_code(500);

    echo json_encode([
        'message' => 'Error interno del servidor'
    ]);
}

