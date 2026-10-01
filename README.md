# Sistema de Gestión de Biblioteca

Aplicación web para la gestión de una biblioteca, desarrollada como prueba técnica.

El sistema permite administrar autores, libros, miembros y préstamos, incluyendo el control de disponibilidad de ejemplares y las principales reglas de negocio asociadas al proceso de préstamo y devolución.

---

## Tecnologías utilizadas

### Backend

* PHP 8.2
* PDO
* Composer
* MySQL
* API REST
* Arquitectura hexagonal

### Frontend

* Angular
* Angular Standalone Components
* TypeScript
* HTML
* CSS
* Angular Reactive Forms

### Herramientas

* XAMPP
* MySQL
* Postman
* Visual Studio Code

---

## Funcionalidades

### Autores

* Registrar autores.
* Consultar autores.
* Consultar un autor por ID.
* Editar autores.
* Eliminar autores.
* Listar autores con paginación.

### Libros

* Registrar libros.
* Asociar libros con autores.
* Consultar libros.
* Consultar un libro por ID.
* Editar libros.
* Eliminar libros.
* Buscar libros por título.
* Consultar libros disponibles.
* Filtrar libros.
* Listar libros con paginación.
* Controlar cantidad total de ejemplares.
* Controlar cantidad disponible de ejemplares.
* Validar formato del ISBN.
* Evitar ISBN duplicados.

### Miembros

* Registrar miembros.
* Consultar miembros.
* Consultar un miembro por ID.
* Editar miembros.
* Desactivar miembros mediante desactivación lógica.
* Listar miembros con paginación.
* Validar formato del correo electrónico.
* Impedir que miembros inactivos soliciten préstamos.

### Préstamos

* Registrar préstamos.
* Consultar préstamos.
* Consultar un préstamo por ID.
* Registrar devolución de libros.
* Consultar préstamos por miembro.
* Consultar préstamos por estado.
* Consultar préstamos activos y devueltos.
* Controlar automáticamente la disponibilidad de ejemplares.
* Validar que el libro tenga ejemplares disponibles.
* Validar que el miembro esté activo.
* Limitar a un máximo de 3 préstamos activos por miembro.
* Establecer un plazo de devolución de 14 días.
* Actualizar el stock disponible al realizar un préstamo.
* Restaurar el stock disponible al realizar una devolución.

---

## Reglas de negocio principales

El sistema implementa las siguientes reglas:

### ISBN

El ISBN de un libro debe tener una longitud válida de 10 o 13 dígitos.

No se permite registrar dos libros con el mismo ISBN.

### Miembros

Los miembros pueden ser desactivados mediante el campo `is_active`.

Un miembro inactivo no puede realizar nuevos préstamos.

La eliminación física de miembros no forma parte del flujo de gestión de miembros.

### Préstamos

Un miembro puede tener como máximo 3 préstamos activos simultáneamente.

No se puede realizar un préstamo cuando el libro no tiene ejemplares disponibles.

El préstamo establece automáticamente una fecha de vencimiento de 14 días.

Al registrar un préstamo, disminuye la cantidad de ejemplares disponibles.

Al registrar una devolución, aumenta nuevamente la cantidad de ejemplares disponibles.

No se permite devolver nuevamente un préstamo que ya fue marcado como devuelto.

### Correo electrónico

El correo electrónico de los miembros debe cumplir con un formato válido de email.

---

## API

La API utiliza el prefijo:

```text
/api/v1
```

La URL base utilizada durante el desarrollo es:

```text
http://localhost:8000/api/v1
```

### Principales recursos

```text
/authors
/books
/members
/loans
```

### Ejemplos de endpoints

#### Autores

```text
GET    /api/v1/authors
GET    /api/v1/authors/{id}
POST   /api/v1/authors
PUT    /api/v1/authors/{id}
DELETE /api/v1/authors/{id}
```

#### Libros

```text
GET    /api/v1/books
GET    /api/v1/books/{id}
POST   /api/v1/books
PUT    /api/v1/books/{id}
DELETE /api/v1/books/{id}
```

También se dispone de operaciones para búsqueda por título y consulta de libros disponibles.

#### Miembros

```text
GET    /api/v1/members
GET    /api/v1/members/{id}
POST   /api/v1/members
PUT    /api/v1/members/{id}
```

La gestión de miembros utiliza desactivación lógica mediante `is_active`.

#### Préstamos

```text
GET    /api/v1/loans
GET    /api/v1/loans/{id}
POST   /api/v1/loans
```

También se dispone de operaciones para registrar devoluciones y filtrar préstamos por miembro y estado.

---

## Paginación y filtros

El sistema incorpora paginación en:

* Autores.
* Libros.
* Miembros.

Los libros cuentan además con filtros y búsqueda por título.

Los préstamos cuentan con filtros por:

* Miembro.
* Estado del préstamo.

Los préstamos no utilizan paginación, de acuerdo con los requerimientos definidos para el proyecto.

---

## Arquitectura

El backend está organizado siguiendo una arquitectura hexagonal, separando las responsabilidades principales de la aplicación.

```text
backend/

├── src/

│   ├── Domain/
│   │   ├── Author/
│   │   ├── Book/
│   │   ├── Member/
│   │   └── Loan/
│   │
│   ├── Application/
│   │   ├── Port/
│   │   └── UseCase/
│   │
│   └── Infrastructure/
│       ├── Config/
│       ├── Http/
│       └── Persistence/
│
├── database/
│   └── biblioteca.sql
│
├── public/
│   └── index.php
│
├── composer.json
└── composer.lock
```

### Domain

Contiene las entidades y reglas propias del dominio de la biblioteca.

Ejemplos:

* Author
* Book
* Member
* Loan

Las reglas de negocio principales se mantienen separadas de la infraestructura.

### Application

Contiene los casos de uso de la aplicación y los puertos utilizados para comunicarse con otras capas.

Ejemplos de casos de uso:

* Crear autor.
* Actualizar autor.
* Eliminar autor.
* Crear libro.
* Actualizar libro.
* Eliminar libro.
* Crear préstamo.
* Registrar devolución.
* Buscar libros.

### Infrastructure

Contiene las implementaciones concretas relacionadas con la infraestructura.

Por ejemplo:

* Conexión a MySQL mediante PDO.
* Repositorios PDO.
* Implementación de transacciones.
* Entrada HTTP de la aplicación.

Esta separación permite mantener las reglas de negocio independientes de la tecnología utilizada para persistencia o comunicación.

---

## Base de datos

El proyecto utiliza MySQL como sistema gestor de base de datos.

La estructura y datos de la base de datos se encuentran en:

```text
backend/database/biblioteca.sql
```

El proyecto utiliza las siguientes entidades principales:

```text
authors
books
members
loans
```

Las relaciones principales permiten:

* Asociar libros con autores.
* Asociar préstamos con libros.
* Asociar préstamos con miembros.
* Controlar la disponibilidad de ejemplares.

---

## Requisitos previos

Antes de ejecutar el proyecto se debe tener instalado:

* PHP 8.2 o superior.
* Composer.
* Node.js y npm.
* Angular CLI.
* MySQL.
* XAMPP o un entorno equivalente.
* Git, si se desea clonar el proyecto desde GitHub.

Se recomienda verificar las versiones instaladas con:

```bash
php -v
composer -V
node -v
npm -v
ng version
```

---

## Instalación del backend

Ingresar a la carpeta del backend:

```bash
cd backend
```

Instalar las dependencias de Composer:

```bash
composer install
```

---

## Configuración de la base de datos

Crear una base de datos llamada:

```text
biblioteca
```

Posteriormente importar el archivo:

```text
backend/database/biblioteca.sql
```

En XAMPP se puede utilizar phpMyAdmin o la consola de MySQL para realizar la importación.

La configuración de conexión utiliza MySQL y PDO.

La conexión local utilizada durante el desarrollo es:

```text
Host: 127.0.0.1
Base de datos: biblioteca
```

---

## Ejecutar el backend

Desde la carpeta:

```text
backend
```

ejecutar:

```bash
php -S localhost:8000 -t public
```

La API estará disponible en:

```text
http://localhost:8000
```

y los endpoints bajo:

```text
http://localhost:8000/api/v1
```

---

## Instalación del frontend

El proyecto Angular se encuentra en:

```text
frontend-app
```

Ingresar a la carpeta:

```bash
cd frontend-app
```

Instalar las dependencias:

```bash
npm install
```

---

## Configuración del frontend

La URL base de la API se encuentra configurada mediante el archivo de environment:

```text
src/environments/environment.ts
```

La configuración utilizada durante el desarrollo es:

```typescript
export const environment = {
  apiUrl: 'http://localhost:8000/api/v1'
};
```

El servicio `ApiService` utiliza esta configuración para realizar las peticiones HTTP al backend.

---

## Ejecutar el frontend

Desde la carpeta:

```text
frontend-app
```

ejecutar:

```bash
ng serve
```

La aplicación estará disponible normalmente en:

```text
http://localhost:4200
```

---

## Flujo recomendado para ejecutar el proyecto

### 1. Iniciar MySQL

Desde XAMPP iniciar:

```text
MySQL
```

### 2. Iniciar el backend

Desde:

```text
backend
```

ejecutar:

```bash
php -S localhost:8000 -t public
```

### 3. Iniciar el frontend

Desde:

```text
frontend-app
```

ejecutar:

```bash
ng serve
```

### 4. Abrir la aplicación

Ingresar desde el navegador a:

```text
http://localhost:4200
```

---

## Pruebas de la API

Durante el desarrollo se utilizó Postman para probar los endpoints del backend.

Las pruebas contemplan:

* Operaciones CRUD de autores.
* Operaciones CRUD de libros.
* Gestión y desactivación de miembros.
* Creación de préstamos.
* Devolución de libros.
* Validación de disponibilidad.
* Validación de miembros activos.
* Límite de préstamos activos.
* Validación de ISBN.
* Validación de email.
* Validación de datos enviados.
* Respuestas HTTP correspondientes a cada operación.

---

## Manejo de errores HTTP

La API utiliza códigos HTTP para representar el resultado de las operaciones.

Algunos ejemplos:

```text
200 OK
201 Created
400 Bad Request
404 Not Found
409 Conflict
500 Internal Server Error
```

Los errores de validación y reglas de negocio devuelven mensajes descriptivos en formato JSON.

Ejemplo:

```json
{
  "message": "El ISBN ya existe"
}
```

---

## Transacciones

Las operaciones que modifican información relacionada con préstamos utilizan transacciones para mantener la consistencia de los datos.

Por ejemplo, al crear un préstamo se debe actualizar el estado del préstamo y la cantidad disponible del libro como parte de una misma operación.

De esta manera, si ocurre un error durante el proceso, los cambios pueden revertirse para evitar inconsistencias entre las tablas relacionadas.

---

## Decisiones de diseño

### PDO

Se utiliza PDO para la comunicación con MySQL debido a que permite trabajar con consultas preparadas y manejar las excepciones de base de datos.

### Repositorios

La persistencia se encuentra abstraída mediante repositorios.

Los casos de uso no dependen directamente de las consultas SQL, sino de los contratos definidos en la capa de aplicación.

### Casos de uso

Las operaciones principales se organizan mediante casos de uso independientes.

Esto permite separar las reglas de negocio de la entrada HTTP y de la persistencia.

### Desactivación de miembros

Los miembros no se eliminan físicamente.

Se utiliza el campo:

```text
is_active
```

para representar si un miembro se encuentra activo o inactivo.

### Control de ejemplares

Los libros mantienen:

```text
total_copies
available_copies
```

Esto permite controlar cuántos ejemplares existen y cuántos están disponibles para nuevos préstamos.

### Transacciones

Las operaciones de préstamo y devolución utilizan transacciones para mantener sincronizados los préstamos y el inventario disponible.

---

## Supuestos

Para el desarrollo del proyecto se consideran los siguientes supuestos:

* La aplicación se ejecuta en un entorno local durante el desarrollo.
* MySQL se encuentra disponible en `127.0.0.1`.
* La base de datos utilizada se denomina `biblioteca`.
* El backend se ejecuta mediante el servidor integrado de PHP.
* El frontend se ejecuta mediante Angular CLI.
* La autenticación y autorización de usuarios no forman parte del alcance definido para esta prueba técnica.
* Los miembros se desactivan mediante desactivación lógica.
* Los préstamos activos se controlan mediante el estado `ACTIVE`.
* Las devoluciones cambian el estado del préstamo a `RETURNED`.
* La fecha de vencimiento de un préstamo se establece a 14 días.
* El límite máximo de préstamos activos por miembro es de 3.

---

## Estructura general del proyecto

```text
biblioteca/

├── backend/
│   ├── src/
│   ├── database/
│   │   └── biblioteca.sql
│   ├── public/
│   │   └── index.php
│   ├── composer.json
│   └── composer.lock
│
├── frontend-app/
│   ├── src/
│   ├── angular.json
│   ├── package.json
│   └── tsconfig.json
│
└── README.md
```

---

## Estado del proyecto

El proyecto cuenta con:

* Backend PHP funcionando.
* API REST funcionando.
* Persistencia MySQL mediante PDO.
* Arquitectura hexagonal.
* Gestión de autores.
* Gestión de libros.
* Gestión y desactivación de miembros.
* Gestión de préstamos y devoluciones.
* Control de disponibilidad de libros.
* Validaciones de reglas de negocio.
* Paginación.
* Filtros.
* Transacciones.
* Frontend Angular integrado con la API.
* Configuración de API mediante environment.
* Pruebas de endpoints mediante Postman.

---

## Autor

Proyecto desarrollado como prueba técnica de desarrollo de software.
