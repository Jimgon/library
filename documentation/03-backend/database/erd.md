**ERD (MySQL)**

```mermaid
erDiagram
    USERS {
        BIGINT id PK
        VARCHAR first_name
        VARCHAR last_name
        VARCHAR name
        VARCHAR email
        VARCHAR gender
        VARCHAR grade_section
        VARCHAR lrn
        VARCHAR phone_number
        TEXT address
        INT borrowed
        VARCHAR role
        VARCHAR remark
        TIMESTAMP created_at
        TIMESTAMP updated_at
        TIMESTAMP deleted_at
    }

    SYSTEM_USERS {
        BIGINT id PK
        VARCHAR email
        VARCHAR password
        VARCHAR role
        TIMESTAMP created_at
        TIMESTAMP updated_at
        TIMESTAMP deleted_at
    }

    TEACHERS {
        BIGINT id PK
        VARCHAR name
        VARCHAR first_name
        VARCHAR last_name
        VARCHAR gender
        VARCHAR address
        VARCHAR phone_number
        VARCHAR email
        VARCHAR remark
        TIMESTAMP created_at
        TIMESTAMP updated_at
        TIMESTAMP deleted_at
    }

    BOOKS {
        BIGINT id PK
        VARCHAR title
        VARCHAR author
        VARCHAR publisher
        VARCHAR isbn
        VARCHAR category
        INT copies
        INT available_copies
        VARCHAR status
        VARCHAR edition
        INT pages
        VARCHAR source_of_funds
        DECIMAL cost_price
        INT published_year
        DECIMAL purchase_price
        VARCHAR acquisition_type
        VARCHAR condition
        VARCHAR copy_status
        VARCHAR call_number
        VARCHAR dewey_decimal
        VARCHAR cutter_number
        JSON control_numbers
        TIMESTAMP created_at
        TIMESTAMP updated_at
        TIMESTAMP deleted_at
    }

    DISTRIBUTED_BOOKS {
        BIGINT id PK
        VARCHAR title
        VARCHAR author
        VARCHAR publisher
        VARCHAR isbn
        VARCHAR category
        INT copies
        INT available_copies
        VARCHAR status
        VARCHAR edition
        INT pages
        VARCHAR source_of_funds
        DECIMAL cost_price
        INT year
        VARCHAR condition
        TIMESTAMP created_at
        TIMESTAMP updated_at
        TIMESTAMP deleted_at
    }

    BORROWS {
        BIGINT id PK
        BIGINT user_id
        BIGINT book_id
        DATE borrowed_at
        DATE due_date
        TIMESTAMP returned_at
        VARCHAR remark
        TEXT notes
        VARCHAR role
        TIMESTAMP created_at
        TIMESTAMP updated_at
    }

    PENALTY_SETTINGS {
        BIGINT id PK
        INT borrow_days_allowed
        DECIMAL penalty_per_day
        TIMESTAMP created_at
        TIMESTAMP updated_at
    }

    ACTIVITY_LOGS {
        BIGINT id PK
        BIGINT user_id FK
        VARCHAR action
        VARCHAR target_type
        BIGINT target_id
        TEXT details
        TIMESTAMP created_at
        TIMESTAMP updated_at
    }

    SYSTEM_USERS ||--o{ ACTIVITY_LOGS : "actor"
    USERS ||--o{ BORROWS : "borrows"
    BOOKS ||--o{ BORROWS : "book"

    %% Note: Teachers can also be referenced in BORROWS.user_id by role logic
    %% Note: Distributed books are referenced by book_id in some flows
```

**Relationship Notes**
- `borrows.user_id` is used for both students (`users`) and teachers (`teachers`) based on role logic in controllers. The schema does not enforce a strict FK for this field.
- `borrows.book_id` points to `books` in primary flows; some distributed flows may reference `distributed_books` by ID.
- `activity_logs.user_id` references `system_users.id` via FK.

Sources: `app/Models/*`, `database/migrations/*`, `app/Http/Controllers/BorrowController.php`, `app/Http/Controllers/BookController.php`, `app/Http/Controllers/TeacherBorrowController.php`.
