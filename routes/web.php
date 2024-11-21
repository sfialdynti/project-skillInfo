`<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AssessorController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MajorController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\UserController;
use App\Models\Major;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('template.dashbo');
// });


//LOGIN
Route::get('/', [LoginController::class, 'login'])->name('login');
Route::get('/login', [LoginController::class, 'login']);
Route::post('/auth', [LoginController::class, 'auth']);
Route::get('/logout', [LoginController::class, 'logout']);

Route::group(['middleware' => ['auth','cekrole:Admin']], function () {
    Route::get('/dashboard', [DashboardController::class, 'show']);
    Route::get('/detail/profile/{id}', [DashboardController::class, 'profile']);
    Route::post('profile/update/{id}', [DashboardController::class, 'updtprofile']);
    Route::get('/table-user', [UserController::class, 'showuser']);
    Route::post('/table-user', [UserController::class, 'search']);
    Route::get('/user/create', [UserController::class, 'create']);
    Route::post('/user/create', [UserController::class, 'add']);
    Route::get('/user/edit/{id}', [UserController::class, 'edit']);
    Route::post('/user/update/{id}', [UserController::class, 'update']);
    Route::get('/user/delete/{id}', [UserController::class, 'delete']);

    Route::get('/table-major', [MajorController::class, 'showmajor']);
    Route::post('/table-major', [MajorController::class, 'search']);
    Route::get('/major/create', [MajorController::class, 'create']);
    Route::post('/major/create', [MajorController::class, 'add']);
    Route::get('/major/edit/{id}', [MajorController::class, 'edit']);
    Route::post('/major/update/{id}', [MajorController::class, 'update']);
    Route::get('/major/delete/{id}', [MajorController::class, 'delete']);

    Route::get('/table-student', [StudentController::class, 'showstud']);
    Route::post('/table-student', [StudentController::class, 'search']);
    Route::get('/student/create', [StudentController::class, 'create']);
    Route::post('/student/create', [StudentController::class, 'add']);
    Route::get('/student/edit/{id}', [StudentController::class, 'edit']);
    Route::post('/student/update/{id}', [StudentController::class, 'update']);
    Route::get('/student/delete/{id}', [StudentController::class, 'delete']);

    Route::get('/table-assessorint', [AssessorController::class, 'internal']);
    Route::post('/table-assessorint', [AssessorController::class, 'searchInternal']);
    Route::get('/assessor/create/internal', [AssessorController::class, 'createInternal']);
    Route::get('/assessorint/edit/{id}', [AssessorController::class, 'editInternal']);
    Route::post('/assessorint/update/{id}', [AssessorController::class, 'updateInternal'])->name('assessorint.update');
    Route::get('/assessorint/delete/{id}', [AssessorController::class, 'deleteint']);
    Route::post('/assessor/create/{type}', [AssessorController::class, 'add'])->name('assessor.create');

    // Route::post('/assessor/create/internal', [AssessorController::class, 'add']);

    Route::get('/table-assessorext', [AssessorController::class, 'external']);
    Route::post('/table-assessorext', [AssessorController::class, 'searchExternal']);
    Route::get('/assessor/create/external', [AssessorController::class, 'createExternal']);
    Route::get('/assessorext/edit/{id}', [AssessorController::class, 'editExternal']);
    Route::post('/assessorext/update/{id}', [AssessorController::class, 'updateExternal'])->name('assessorext.update');
    Route::get('/assessorint/delete/{id}', [AssessorController::class, 'deleteint']);

    Route::get('/assessorext/delete/{id}', [AssessorController::class, 'deleteext']);





});

Route::group(['middleware'=> ['auth', 'cekrole:Student']], function () {
    // Route::get('/dashboard', [DashboardController::class, 'show']);

});