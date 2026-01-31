<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AllocationController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Models\Category;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
;

// Route::middleware('auth')->group(function () {
    Route::get('/dashboard',[AdminController::class, 'index'])->name('admin');
    Route::get('/all-categories', [CategoryController::class, 'allcategories'])->name('all-categories');
    Route::get('/create-category', [CategoryController::class, 'createCategories'])->name('createCategories');
    Route::post('/store-category', [CategoryController::class, 'storeCategory'])->name('store-category');
    Route::get('/edit-category/{id}', [CategoryController::class, 'editCategory'])->name('editCategory');
    Route::put('/update-category/{id}', [CategoryController::class, 'updateCategory'])->name('update-category');
    Route::get('/all-users', [UserController::class, 'allUsers'])->name('all-users');
    Route::middleware(['auth', 'role:admin'])->group(function () {
        Route::get('/create-user', [UserController::class, 'createUser'])->name('create_user');
        Route::post('/store-user', [UserController::class, 'storeUser'])->name('store-user');
        Route::get('/edit-user/{id}', [UserController::class, 'editUser'])->name('edit-user');
        Route::put('/update-user/{id}', [UserController::class, 'updateUser'])->name('update-user');
        Route::put('/users/{user}/change-role',[UserController::class, 'changeRole'])->name('users.changeRole') ->middleware('role:admin');
    });



    //َAllocations
    Route::get('homee', [AllocationController::class, 'homee'])->name('allocations.homee');
    Route::get('allocations', [AllocationController::class,'index'])->name('allocations.index');
    Route::get('allocations/create', [AllocationController::class,'create'])->name('allocations.create');
    Route::post('allocations', [AllocationController::class,'store'])->name('allocations.store');
    
    Route::get('allocations/{id}/data', [AllocationController::class, 'show'])->name('allocations.show'); // برای fetch JSON
    // Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('allocations/{allocation}/edit', [AllocationController::class,'edit'])->name('allocations.edit');
    Route::put('allocations/{id}', [AllocationController::class, 'update'])->name('allocations.update'); // آپدیت (AJAX)
    Route::delete('/allocations/{id}', [AllocationController::class, 'destroy'])->name('allocations.destroy');
    // });
    Route::put('/allocations/{allocation}/approve',[AllocationController::class, 'approve'])->name('allocations.approve') ->middleware('auth');

    Route::get('allocations/filter-options', [AllocationController::class,'filterOptions'])->name('allocations.filterOptions');
    Route::post('/allocations/compute-sum',[AllocationController::class, 'computeSum'])->name('allocations.computeSum');
    Route::post('allocations/compute-edit-sum', [AllocationController::class, 'computeEditSum'])->name('allocations.computeEditSum');
    Route::post('allocations/compute-t', [AllocationController::class, 'computeTMosavvab'])->name('allocations.computeTMosavvab');
    Route::get('allocations/next-row', [AllocationController::class, 'nextRow'])->name('allocations.nextRow');


    // import/export & reports
    Route::post('/allocations/import', [AllocationController::class, 'import'])->name('allocations.import');
    Route::get('allocations/export', [AllocationController::class, 'export'])->name('allocations.export');
    Route::get('allocations/report', [AllocationController::class,'report'])->name('allocations.report');
    Route::post('allocations/compute-sum', [AllocationController::class, 'computeSum'])->name('allocations.computeSum');
    


    //ِDashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    //Report
    Route::get('/reports/allocations', [ReportController::class, 'index'])->name('reports.allocations');
    Route::get('/reports/allocations/codes', [ReportController::class, 'codesForGroup'])->name('reports.allocations.codes'); // ajax: codes for a Takhsis_group
    Route::get('/reports/file-categories/{parent}', function ($parentId) {
    return \App\Models\FileCategory::where('parent_id', $parentId)->get();
    })->name('reports.fileCategories.children');
    //Route::get('/reports/file-categories/{parent}/children',[\App\Http\Controllers\ReportController::class, 'fileCategoryChildren'])->name('reports.fileCategories.children');
    Route::get(
    '/reports/file-categories/{id}/leaf-ids',
    [ReportController::class, 'leafCategoryIds']
)->name('reports.fileCategories.leafIds');


    
// });

// require __DIR__.'/../auth.php';

