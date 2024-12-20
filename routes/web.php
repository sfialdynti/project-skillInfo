`<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\AssessorController;
use App\Http\Controllers\CompetencyElementController;
use App\Http\Controllers\CompetencyStandardController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExaminationController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MajorController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\UserController;
use App\Models\Competency_element;
use App\Models\Competency_standard;
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

Route::get('/serti', function () {
    return view('student.sertifikat');
});


//LOGIN
Route::get('/', [LoginController::class, 'login'])->name('login');
Route::get('/login', [LoginController::class, 'login']);
Route::post('/auth', [LoginController::class, 'auth']);
Route::get('/logout', [LoginController::class, 'logout']);

Route::middleware('statusLogin')->group(function () {

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

        Route::get('/table-assessorext', [AssessorController::class, 'external']);
        Route::post('/table-assessorext', [AssessorController::class, 'searchExternal']);
        Route::get('/assessor/create/external', [AssessorController::class, 'createExternal']);
        Route::get('/assessorext/edit/{id}', [AssessorController::class, 'editExternal']);
        Route::post('/assessorext/update/{id}', [AssessorController::class, 'updateExternal'])->name('assessorext.update');
        Route::get('/assessorext/delete/{id}', [AssessorController::class, 'deleteext']);

        Route::get('/table-competency_standard-adm', [CompetencyStandardController::class, 'showCS']);
        Route::post('/table-competency_standard-adm', [CompetencyStandardController::class, 'searchCS']);
        Route::get('/competency_standard-adm/create', [CompetencyStandardController::class, 'createCS']);
        Route::post('/competency_standard-adm/create', [CompetencyStandardController::class, 'addCS']);
        Route::get('/competency_standard-adm/edit/{id}', [CompetencyStandardController::class, 'editCS']);
        Route::post('/competency_standard-adm/update/{id}', [CompetencyStandardController::class, 'updateCS']);
        Route::get('/competency_standard-adm/delete/a{id}', [CompetencyStandardController::class, 'deleteCS']);

        Route::get('/competency_elements-adm/{id}', [CompetencyElementController::class, 'showCE'])->name('competency.elements-adm');
        Route::get('/competency_elements-adm/create/{competency_standards_id}', [CompetencyElementController::class, 'createCE'])->name('competency.elements-adm.create');
        Route::post('/competency_elements-adm/add', [CompetencyElementController::class, 'addCE'])->name('competency.elements-adm.add');
        Route::get('/competency_elements-adm/edit/{id}', [CompetencyElementController::class, 'editCE'])->name('competency.elements-adm.edit');
        Route::post('/competency_elements-adm/update/{id}', [CompetencyElementController ::class, 'updateCE']);
        Route::get('/competency_element/delete-adm/{id}', [CompetencyElementController::class, 'deleteCE'])->name('competency.elements-adm.delete');

        Route::get('/table-exam-adm', [ExaminationController::class, 'showExam']);
        Route::get('/exam-adm/create', [ExaminationController::class, 'createExam']);
        Route::post('/exam-adm/create', [ExaminationController::class, 'addExam']);
        Route::get('/exam-adm/edit/{id}', [ExaminationController::class, 'editExam']);
        Route::post('/exam-adm/update/{id}', [ExaminationController::class, 'updateExam']);
        Route::get('/exam-adm/delete/{id}', [ExaminationController::class, 'deleteExam']);


    });

    Route::group(['middleware'=> ['auth', 'cekrole:Assessor']], function () {
        Route::get('/dashboardAssessor', [DashboardController::class, 'showass']);
        Route::get('/detail/profile-ass/{id}', [DashboardController::class, 'profileass']);
        Route::post('profile-ass/update/{id}', [DashboardController::class, 'updtprofileass']);
        Route::get('/table-competency_standard', [CompetencyStandardController::class, 'show']);
        Route::post('/table-competency_standard', [CompetencyStandardController::class, 'search']);
        Route::get('/competency_standard/create', [CompetencyStandardController::class, 'create']);
        Route::post('/competency_standard/create', [CompetencyStandardController::class, 'add']);
        Route::get('/competency_standard/edit/{id}', [CompetencyStandardController::class, 'edit']);
        Route::post('/competency_standard/update/{id}', [CompetencyStandardController::class, 'update']);
        Route::get('/competency_standard/delete/{id}', [CompetencyStandardController::class, 'delete']);

        // Route::get('/competency_elements/create/{competency_standard_id}', [CompetencyElementController::class, 'create'])->name('competency.elements.create');


        // Route::get('/competency/elements/{id}', [CompetencyElementController::class, 'show'])->name('competency.elements');

        Route::get('/competency_elements/{id}', [CompetencyElementController::class, 'show'])->name('competency.elements');   
        Route::get('/competency_elements/create/{competency_standards_id}', [CompetencyElementController::class, 'create'])->name('competency.elements.create');
        Route::post('/competency_elements/add', [CompetencyElementController::class, 'add'])->name('competency.elements.add');
        Route::get('/competency_elements/edit/{id}', [CompetencyElementController::class, 'edit'])->name('competency.elements.edit');
        Route::post('/competency_elements/update/{id}', [CompetencyElementController ::class, 'update']);
        Route::get('/competency_element/delete/{id}', [CompetencyElementController::class, 'delete'])->name('competency.elements.delete');

        // Route::get('/competency-standard/{id}/elements', [CompetencyElementController::class, 'show'])->name('competency.elements');
    
        Route::get('/table-exam', [ExaminationController::class, 'show']);
        Route::get('/exam/create', [ExaminationController::class, 'create']);
        Route::post('/exam/create', [ExaminationController::class, 'add']);
        Route::get('/exam/edit/{id}', [ExaminationController::class, 'edit']);
        Route::post('/exam/update/{id}', [ExaminationController::class, 'update']);
        Route::get('/exam/delete/{id}', [ExaminationController::class, 'delete']);

        Route::get('/liststudent', [AssessmentController::class, 'listStudent'])->name('assessor.table-assessment');
        Route::get('/assess-student/{id}', [AssessmentController::class, 'assessStudent'])->name('assess-student');
        Route::post('/students/{studentId}/submit-assessment', [AssessmentController::class, 'submitAssessment'])->name('submit-assessment');

        // Route::post('/submit-assessment/{id}', [AssessmentController::class, 'submitAssessment'])->name('submit-assessment');

        
    });

    Route::group(['middleware'=> ['auth', 'cekrole:Student']], function () {
        Route::get('/dashboardStudent', [DashboardController::class, 'showstud']);
        Route::get('/detail/profile-stud/{id}', [DashboardController::class, 'profilestud']);
        Route::post('profile-stud/update/{id}', [DashboardController::class, 'updtprofilestud']);
        Route::get('/result', [ExaminationController::class, 'result']);
        Route::get('/exam_results/pdf', [ExaminationController::class, 'print_pdf']);
        
    });

    

});
