<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Bank\TitleOneController;
use App\Http\Controllers\Bank\TitleTwoController;
use App\Http\Controllers\bank\EnterpriseController;
use App\Http\Controllers\bank\ProjectsController;
use App\Http\Controllers\bank\CityController;
use App\Http\Controllers\bank\ProjectsCityController;
use App\Http\Controllers\bank\IncomeController;
use App\Http\Controllers\bank\ExpenseController;
use App\Http\Controllers\bank\BanksController;
use App\Http\Controllers\bank\BankslineController;
use App\Http\Controllers\bank\BanksdetailController;
use App\Http\Controllers\bank\ReportbankController;
use App\Http\Controllers\Bank\CampaignsController;
use App\Http\Controllers\Bank\ReportProjectController;
use App\Http\Controllers\Bank\DonateworthController;
    /*middleware
    |--------------------------------------------------------------------------
    | Web Routes
    |--------------------------------------------------------------------------
    |
    | Here is where you can register web routes for your application. These
    | routes are loaded by the RouteServiceProvider within a group which
    | contains the "web"  group. Now create something great!
    |
    */
/**
Route::get('/', function () {
    return view('welcome');
});
**/
/**
Route::group(['prefix' => 'inventory/color', 'namespace' => 'Inventory', 'middleware' => ['web']], function () {
Route::get('show', 'ColorController@getColors')->name('color.show');
Route::post('store.ajax', 'ColorController@storeAjax')->name('color.store.ajax');
Route::put('update.ajax', 'ColorController@updateAjax')->name('color.update.ajax');
Route::delete('delete.ajax', 'ColorController@deleteAjax')->name('color.delete.ajax');
});

 */

Route::get('/', function () {
    return view('demoangle');
})->name('home');

Route::get('/test', function () {
     return 'test';
    //TitleOneController->show
});


//Start project

Route::group(['prefix' => 'managetable/title', 'namespace' => 'Bank', 'middleware' => ['web']], function () {
    //כותרת ראשית ותת כותרת ראשית
    //managetable/title/show
    Route::get('show', [TitleOneController::class ,'showTable'])->name('table.title.show');
    Route::post('store', [TitleOneController::class, 'store'])->name('table.title.store');
    Route::post('storetitletwo', [TitleTwoController::class, 'store'])->name('table.titletwo.store');
});

//enterprise

Route::group(['prefix' => 'managetable/enterprise', 'namespace' => 'Bank', 'middleware' => ['web']], function () {
    //قائمة المؤسسات والمشاريع
    //managetable/enterprise/show
    Route::get('show', [EnterpriseController::class ,'showTable'])->name('table.enterprise.show');
    Route::post('store', [EnterpriseController::class, 'store'])->name('table.enterprise.store');
    Route::post('storeproject', [ProjectsController::class, 'store'])->name('table.project.store');
});

Route::group(['prefix' => 'managetable/city', 'namespace' => 'Bank', 'middleware' => ['web']], function () {
    //جدول البلدان
    //managetable/enterprise/show
    Route::get('show', [CityController::class ,'showTable'])->name('table.city.show');
    Route::post('store', [CityController::class, 'store'])->name('table.city.store');
});

/**
Route::group(['prefix' => 'managetable/connect_projects_city', 'namespace' => 'Bank', 'middleware' => ['web']], function () {
    //ربط المشاريع مع البلدان
    //managetable/connect_projects_city/show
    Route::get('show', [ProjectsCityController::class ,'showTableWithProjectAndCity'])->name('table.connect_projects_city.show');
    //הצגה
    Route::get('show/{id}', [ProjectsCityController::class ,'showTableByProject'])->name('table.connect_projects_city.edit');
    //הוספת
    Route::post('show/{id}', [ProjectsCityController::class ,'store'])->name('table.connect_projects_city.store');
});
**/
Route::group(['prefix' => 'managetable/expense_income', 'namespace' => 'Bank', 'middleware' => ['web']], function () {
    //جدول المصروفات والمدخولات
    //managetable/enterprise/show
    Route::get('show', [ExpenseController::class ,'showExpenseAndIncome'])->name('table.expense_income.show');
    Route::post('storeexpense', [ExpenseController::class ,'store'])->name('table.expense.store');
    Route::post('storeincome', [IncomeController::class ,'store'])->name('table.income.store');
});

Route::group(['prefix' => 'managetable/campaigns', 'namespace' => 'Bank', 'middleware' => ['web']], function () {
    //جدول الحملات للمشاريع
    Route::get('show/{id_projc}', [CampaignsController::class,'showTableById'])->name('table.campaigns.show');
    Route::post('store/{id_projc}', [CampaignsController::class ,'store'])->name('table.campaigns.store');

    //מחיקת שורה
    Route::delete('delete/{id_projc}/', [CampaignsController::class, 'delete'])->name('table.campaigns.delete');

});
/**
Route::group(['prefix' => 'managetable/income', 'namespace' => 'Bank', 'middleware' => ['web']], function () {
    //הוצאות
    //managetable/income/show
    Route::get('show', [IncomeController::class ,'show'])->name('table.income.show');
    //הצגה
    Route::get('show/{id}', [IncomeController::class ,'showById'])->name('table.income.edit');
    //הוספת
    Route::post('show/{id}', [IncomeController::class ,'store'])->name('table.income.store');
});

Route::group(['prefix' => 'managetable/expense', 'namespace' => 'Bank', 'middleware' => ['web']], function () {
    //הכנסות
    //managetable/income/show
    Route::get('show', [ExpenseController::class ,'show'])->name('table.expense.show');
    //הצגה
    Route::get('show/{id}', [ExpenseController::class ,'showById'])->name('table.expense.edit');
    //הוספת
    Route::post('show/{id}', [ExpenseController::class ,'store'])->name('table.expense.store');
});
**/
Route::group(['prefix' => 'reports/structure_association', 'namespace' => 'Bank', 'middleware' => ['web']], function () {
    //מבנה העמותה
    Route::get('show', [EnterpriseController::class ,'showTableStructure'])->name('reports.structure');

    //הצגת עיר מקושרת לפרויקט
    Route::get('showProjectCity/{id}', [ProjectsController::class ,'showCityByProject'])->name('table.connect_projects_city.edit');
    //שמירה קישור בין עיר לפרויקט
    Route::post('showProjectCity/{id}', [ProjectsController::class ,'storeProjectCity'])->name('table.connect_projects_city.store');

    //הצגת הכנסה מקושרת לפרויקט
    Route::get('showProjectExpense/{id}', [ProjectsController::class ,'showExpenseByProject'])->name('table.connect_projects_expense.edit');
    //שמירה קישור בין הכנסה לפרויקט
    Route::post('showProjectExpense/{id}', [ProjectsController::class ,'storeProjectExpense'])->name('table.connect_projects_expense.store');

    //הצגת הוצאות מקושרת לפרויקט
    Route::get('showProjectIncome/{id}', [ProjectsController::class ,'showIncomeByProject'])->name('table.connect_projects_income.edit');
    //שמירה קישור בין הוצאה לפרויקט
    Route::post('showProjectIncome/{id}', [ProjectsController::class ,'storeProjectIncome'])->name('table.connect_projects_income.store');

});


Route::group(['prefix' => 'managebanks/listbanks', 'namespace' => 'Bank', 'middleware' => ['web']], function () {
    //جدول البنوك
    //managebanks/listbanks/show
    Route::get('show/{id_bank?}', [BanksController::class ,'showTable'])->name('banks.show');
    Route::post('store/{id_bank}', [BanksController::class, 'store'])->name('banks.store');
});

Route::group(['prefix' => 'managebanks/csvbanks', 'namespace' => 'Bank', 'middleware' => ['web']], function () {
    //מסך ראשי להעלאה קובץ CSV לבנק
    Route::get('storecsv', [BanksController::class, 'mainLoadCsv'])->name('banks.mainLoadCsv');

    //העלאת קובץ CSV
    Route::post('storecsv', [BanksController::class, 'storeFileCsv'])->name('banks.storeFileCsv');
});

Route::group(['prefix' => 'donate', 'namespace' => 'Bank', 'middleware' => ['web']], function () {
    //מסך ראשי - תרומה בשווה
    Route::get('maindonate', [DonateworthController::class,'mainDonate'])->name('banks.mainDonate');
});


Route::group(['prefix' => 'managebanks/linebanks', 'namespace' => 'Bank', 'middleware' => ['web']], function () {
    //תנועות בחשבון - שורות בנק
    //managebanks/linebanks/show
    ///{from_date?}/{to_date?}
    Route::get('show/{id_bank}', [BankslineController::class ,'showTable'])->name('linebanks.show');
    Route::post('store/{id_bank}', [BankslineController::class, 'storeAjax'])->name('linebanks.storeajax');

    Route::get('store/{id_bank}/{id_line?}', [BankslineController::class, 'editAjax'])->name('linebanks.editajax');
    Route::put('store/{id_bank}/{id_line?}', [BankslineController::class, 'updateAjax'])->name('linebanks.updateajax');
    //מחיקת שורה
    Route::delete('delete/{id_bank}/{id_line?}', [BankslineController::class, 'deleteAjax'])->name('linebanks.deleteajax');
    //סימון שורה לא כפולה
    Route::put('noduplicate/{id_bank}/{id_line?}', [BankslineController::class, 'noduplicateAjax'])->name('linebanks.noduplicateajax');

    //צריך להוריד אם עוברים לשיטיה החדשה - דף ספיציפי להעלאה מסמך CSV
    //העלאת קובץ CSV
    //Route::post('storecsv/{id_bank}', [BankslineController::class, 'storeFileCsv'])->name('linebanks.storeFileCsv');

    //עדכון גורף לסוג תנועה או עמותה
    Route::post('updateselect/{id_bank}', [BankslineController::class, 'storeSelect'])->name('linebanks.storeselecttitle');

    //מחזיר שורה HTML - לחלוקת שורות
    Route::get('showrowdetils/{id_bank}/{id_line?}', [BankslineController::class, 'showrowdetilshtml'])->name('linebanks.showrowdetils');

    Route::post('story/{id_bank}/{id_line?}', [BanksdetailController::class ,'storeMultiRowAjax'])->name('linedetail.storemultirowajax');
});

Route::group(['prefix' => 'managebanks/linedetail', 'namespace' => 'Bank', 'middleware' => ['web']], function () {
    //פירוט שורות בנק
    Route::get('show/{id_line}', [BanksdetailController::class ,'showTable'])->name('linedetail.show');
    Route::post('story/{id_line}', [BanksdetailController::class ,'storeAjax'])->name('linedetail.storeajax');

    Route::get('store/{id_line}/{id_detail?}', [BanksdetailController::class, 'editAjax'])->name('linedetail.editajax');
    Route::put('store/{id_line}/{id_detail?}', [BanksdetailController::class, 'updateAjax'])->name('linedetail.updateajax');

    //מחיקת שורה
    Route::delete('delete/{id_line}/{id_detail?}', [BanksdetailController::class, 'deleteAjax'])->name('linedetail.deleteajax');

    Route::post('show/autocomplate/{id_proj?}', [ProjectsController::class ,'getCityIcomeExpenseByProject'])->name('autocomplate.city.income.expense');

    //שמירת חלוקת השורות
    Route::post('storedivline/{id_line}', [BanksdetailController::class ,'storeDivDetail'])->name('linedetail.storedivline');

});


Route::group(['prefix' => 'reports/reportbank', 'namespace' => 'Bank', 'middleware' => ['web']], function () {
    Route::get('search', [ReportbankController::class ,'mainPage'])->name('reports.banksearch');
    Route::get('searchNew', [ReportbankController::class ,'mainPageNew'])->name('reports.banksearch.new');

    Route::get('searchProj', [ReportProjectController::class ,'mainPageProj'])->name('reports.projsearch');


});
