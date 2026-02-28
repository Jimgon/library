**Public Routes**
| Method | URI | Name | Controller Action | Notes |
| --- | --- | --- | --- | --- |
| GET | / | - | Closure redirect to login | `routes/web.php` |
| GET | /login | login | `Auth\LoginController@showLoginForm` | `routes/web.php` |
| POST | /login | login.submit | `Auth\LoginController@login` | `routes/web.php` |
| GET | /logout | logout | `Auth\LoginController@logout` | `routes/web.php` |

**Authenticated Routes**
These routes are explicitly wrapped in `Route::middleware(['auth'])`. Source: `routes/web.php`.

| Method | URI | Name | Controller Action | Notes |
| --- | --- | --- | --- | --- |
| GET | /dashboard | dashboard | `DashboardController@index` | `routes/web.php` |
| GET | /reports | reports | `DashboardController@reports` | `routes/web.php` |
| GET | /teachers | teachers.index | `TeacherController@index` | `routes/web.php` |
| GET | /teachers/create | teachers.create | `TeacherController@create` | `routes/web.php` |
| POST | /teachers | teachers.store | `TeacherController@store` | `routes/web.php` |
| GET | /teachers/import | teachers.import.form | `TeacherController@importForm` | `routes/web.php` |
| POST | /teachers/import | teachers.import | `TeacherController@import` | `routes/web.php` |
| GET | /teachers/{teacher}/edit | teachers.edit | `TeacherController@edit` | `routes/web.php` |
| PUT | /teachers/{teacher} | teachers.update | `TeacherController@update` | `routes/web.php` |
| PATCH | /teachers/{teacher}/remark | teachers.updateRemark | `TeacherController@updateRemark` | `routes/web.php` |
| DELETE | /teachers/{teacher} | teachers.destroy | `TeacherController@destroy` | `routes/web.php` |
| GET | /books/import | books.import | `BookController@showImportForm` | `routes/web.php` |
| POST | /books/import | books.import.post | `BookController@import` | `routes/web.php` |
| GET | /books/catalog | books.catalog | `BookController@catalog` | `routes/web.php` |
| GET | /books/distribute/{id} | books.distribute.show | `BookController@distributeShow` | `routes/web.php` |
| GET | /books/distribute/{id}/edit | books.distribute.edit | `BookController@distributeEdit` | `routes/web.php` |
| PUT | /books/distribute/{id} | books.distribute.update | `BookController@distributeUpdate` | `routes/web.php` |
| DELETE | /books/distribute/{id} | books.distribute.destroy | `BookController@distributeDestroy` | `routes/web.php` |
| GET | /books/print | books.print | `BookController@printAll` | `routes/web.php` |
| POST | /books/{book}/add-copies | books.addCopies | `BookController@addCopies` | `routes/web.php` |
| GET | /books/api/next-control-base | books.api.nextControlBase | `BookController@getNextControlBase` | `routes/web.php` |
| POST | /users/bulk-delete | users.bulkDelete | `UserController@bulkDelete` | `routes/web.php` |
| GET | /users/teachers | users.teachers | `UserController@teachers` | `routes/web.php` |
| GET | /users/students | users.students | `UserController@students` | `routes/web.php` |
| POST | /users/import | users.import | `UserController@import` | `routes/web.php` |
| PATCH | /users/{user}/remark | users.updateRemark | `UserController@updateRemark` | `routes/web.php` |
| GET | /utilities | utilities.index | `UtilitiesController@index` | `routes/web.php` |
| GET | /utilities/logs | utilities.logs | `UtilitiesController@logs` | `routes/web.php` |
| GET | /utilities/archive | utilities.archive | `UtilitiesController@archive` | `routes/web.php` |
| PATCH | /utilities/archive/restore/{model}/{id} | utilities.restore | `UtilitiesController@restore` | `routes/web.php` |
| PATCH | /utilities/archive/restore-all/{model} | utilities.restoreAll | `UtilitiesController@restoreAll` | `routes/web.php` |
| DELETE | /utilities/archive/delete/{model}/{id} | utilities.delete | `UtilitiesController@delete` | `routes/web.php` |
| DELETE | /utilities/archive/delete-all/{model} | utilities.deleteAll | `UtilitiesController@deleteAll` | `routes/web.php` |
| POST | /utilities/backup | utilities.backup | `UtilitiesController@backup` | `routes/web.php` |
| GET | /utilities/backups | utilities.backups | `UtilitiesController@listBackups` | `routes/web.php` |
| GET | /utilities/download-backup/{filename} | utilities.downloadBackup | `UtilitiesController@downloadBackup` | `routes/web.php` |
| GET | /borrow/create | borrow.create | `BorrowController@create` | `routes/web.php` |
| POST | /borrow | borrow.store | `BorrowController@store` | `routes/web.php` |
| GET | /borrow/distribute | borrow.distribute | `BorrowController@createForDistribute` | `routes/web.php` |
| POST | /borrow/distribute | borrow.distribute.store | `BorrowController@storeForDistribute` | `routes/web.php` |
| GET | /borrow/teacher/create | borrow.teacher.create | `TeacherBorrowController@create` | `routes/web.php` |
| POST | /borrow/teacher | borrow.teacher.store | `TeacherBorrowController@store` | `routes/web.php` |
| GET | /borrow/return | borrow.return.index | `BorrowController@returnIndex` | `routes/web.php` |
| POST | /borrow/return/{borrow} | borrow.return.process | `BorrowController@processReturn` | `routes/web.php` |
| GET | /borrow/{borrow}/receipt | borrow.receipt | `BorrowController@receipt` | `routes/web.php` |
| GET | /borrow/receipt/{borrow}/print | borrow.receipt.print | `BorrowController@printReceipt` | `routes/web.php` |
| GET | /staff | staff.index | `UserManagementController@index` | `routes/web.php` |
| GET | /staff/create | staff.create | `UserManagementController@create` | `routes/web.php` |
| POST | /staff | staff.store | `UserManagementController@store` | `routes/web.php` |
| GET | /staff/{user}/edit | staff.edit | `UserManagementController@edit` | `routes/web.php` |
| PUT | /staff/{user} | staff.update | `UserManagementController@update` | `routes/web.php` |
| DELETE | /staff/{user} | staff.destroy | `UserManagementController@destroy` | `routes/web.php` |

**Resource Routes**
The following resource routes are defined via `Route::resource`.

| Resource | Controller | Actions |
| --- | --- | --- |
| books | `BookController` | index, create, store, show, edit, update, destroy |
| users | `UserController` | index, create, store, show, edit, update, destroy |

Source: `routes/web.php`.

**Notes**
- The `web` middleware group includes `Authenticate`, which applies auth even to public routes. This may affect `/login`. Source: `app/Http/Kernel.php`.
- There is no `routes/api.php` file in this repository, and only `routes/web.php` is loaded by the framework. Source: `bootstrap/app.php`.
