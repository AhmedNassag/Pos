<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\GeneralController;
use App\Http\Controllers\Dashboard\RoleController;
use App\Http\Controllers\Dashboard\UserController;
use App\Http\Controllers\Dashboard\CategoryController;
use App\Http\Controllers\Dashboard\SettingController;

use App\Http\Controllers\Dashboard\Admin\SettingsController;
use App\Http\Controllers\Dashboard\Admin\ReportController;
use App\Http\Controllers\Dashboard\Admin\ClientController;
use App\Http\Controllers\Dashboard\Admin\ProvidersController;
use App\Http\Controllers\Dashboard\Admin\PosController;
use App\Http\Controllers\Dashboard\Admin\ProductsController;
use App\Http\Controllers\Dashboard\Admin\CategorieController;
use App\Http\Controllers\Dashboard\Admin\UnitsController;
use App\Http\Controllers\Dashboard\Admin\BrandsController;
use App\Http\Controllers\Dashboard\Admin\CurrencyController;
use App\Http\Controllers\Dashboard\Admin\WarehouseController;
use App\Http\Controllers\Dashboard\Admin\PurchasesController;
use App\Http\Controllers\Dashboard\Admin\PaymentPurchasesController;
use App\Http\Controllers\Dashboard\Admin\SalesController;
use App\Http\Controllers\Dashboard\Admin\PaymentSalesController;
use App\Http\Controllers\Dashboard\Admin\ExpensesController;
use App\Http\Controllers\Dashboard\Admin\CategoryExpenseController;
use App\Http\Controllers\Dashboard\Admin\QuotationsController;
use App\Http\Controllers\Dashboard\Admin\SalesReturnController;
use App\Http\Controllers\Dashboard\Admin\PurchasesReturnController;
use App\Http\Controllers\Dashboard\Admin\PaymentSaleReturnsController;
use App\Http\Controllers\Dashboard\Admin\PaymentPurchaseReturnsController;
use App\Http\Controllers\Dashboard\Admin\AdjustmentController;
use App\Http\Controllers\Dashboard\Admin\TransferController;
// use App\Http\Controllers\Dashboard\Admin\UserController;
// use App\Http\Controllers\Dashboard\Admin\SettingsController;
// use App\Http\Controllers\Dashboard\Admin\ReportController;
// use App\Http\Controllers\Dashboard\Admin\SalesController;
// use App\Http\Controllers\Dashboard\Admin\QuotationsController;
// use App\Http\Controllers\Dashboard\Admin\PurchasesController;
// use App\Http\Controllers\Dashboard\Admin\SalesReturnController;
// use App\Http\Controllers\Dashboard\Admin\PurchasesReturnController;
// use App\Http\Controllers\Dashboard\Admin\PaymentPurchasesController;
// use App\Http\Controllers\Dashboard\Admin\PaymentSaleReturnsController;
// use App\Http\Controllers\Dashboard\Admin\PaymentPurchaseReturnsController;
// use App\Http\Controllers\Dashboard\Admin\PaymentSalesController;
// use App\Http\Controllers\Dashboard\Admin\SalesController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Auth::routes();
Auth::routes(['register' => false]);

Route::get('/', function () {
    return view('auth.login');
});




Route::group(['middleware' => ['auth']], function() {
    
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    
    
    
    //routes related with javascripts events
    Route::get('warehouseProducts/{warehouse_id}',[GeneralController::class, 'warehouseProducts'])->name('warehouseProducts');
    



    //roles
    Route::resource('roles', RoleController::class);
    Route::post('rolesDelete', [RoleController::class, 'delete'])->name('roles.delete');
    Route::get('rolesShowNotification/{id}/{notification_id}', [RoleController::class, 'showNotification'])->name('roles.showNotification');



    //users
    Route::resource('users', UserController::class);
    Route::get('usersShowNotification/{id}/{notification_id}', [UserController::class, 'showNotification'])->name('users.showNotification');   
    


    //setting
    Route::get('settings/profile', [SettingController::class, 'profile'])->name('settings.profile');
    Route::get('settings/editProfile', [SettingController::class, 'editProfile'])->name('settings.editProfile');
    Route::post('settings/updateProfile', [SettingController::class, 'updateProfile'])->name('settings.updateProfile');
    Route::get('settings/changePassword', [SettingController::class, 'changePassword'])->name('settings.changePassword');
    Route::post('settings/updatePassword', [SettingController::class, 'updatePassword'])->name('settings.updatePassword');


    //category
    Route::get('category/{name_ar?}/{name_en?}/{photo?}', [CategoryController::class, 'index'])->name('category.index');
    Route::get('categoryShow/{id}', [CategoryController::class, 'show'])->name('category.show');
    Route::post('category', [CategoryController::class, 'store'])->name('category.store');
    Route::patch('category', [CategoryController::class, 'update'])->name('category.update');
    Route::delete('category', [CategoryController::class, 'destroy'])->name('category.destroy');
    Route::post('categoryDeleteSelected', [CategoryController::class, 'deleteSelected'])->name('category.deleteSelected');
    Route::post('categoryForceDelete', [CategoryController::class, 'forceDelete'])->name('category.forceDelete');
    Route::post('categoryRestore', [CategoryController::class, 'restore'])->name('category.restore');
    Route::get('categoryArchived', [CategoryController::class, 'archived'])->name('category.archived');
    Route::get('categoryShowNotification/{id}/{notification_id}', [CategoryController::class, 'showNotification'])->name('category.showNotification');



    //clients
    Route::resource('clients', ClientController::class);
    Route::post('clientsDeleteSelected', [ClientController::class, 'deleteSelected'])->name('clients.deleteSelected');
    Route::post('clientsForceDelete', [ClientController::class, 'forceDelete'])->name('clients.forceDelete');
    Route::post('clientsRestore', [ClientController::class, 'restore'])->name('clients.restore');
    Route::get('clientsArchived', [ClientController::class, 'archived'])->name('clients.archived');
    Route::get('clientsShowNotification/{id}/{notification_id}', [ClientController::class, 'showNotification'])->name('clients.showNotification');

    Route::get('clients/export/Excel', [ClientController::class, 'exportExcel']);
    Route::post('clients/import/csv', [ClientController::class, 'import_clients']);


    //providers
    Route::resource('providers', ProvidersController::class);
    Route::post('providersDeleteSelected', [ProvidersController::class, 'deleteSelected'])->name('providers.deleteSelected');
    Route::post('providersForceDelete', [ProvidersController::class, 'forceDelete'])->name('providers.forceDelete');
    Route::post('providersRestore', [ProvidersController::class, 'restore'])->name('providers.restore');
    Route::get('providersArchived', [ProvidersController::class, 'archived'])->name('providers.archived');
    Route::get('providersShowNotification/{id}/{notification_id}', [ProvidersController::class, 'showNotification'])->name('providers.showNotification');



    //brands
    Route::resource('brands', BrandsController::class);
    Route::post('brandsDeleteSelected', [BrandsController::class, 'deleteSelected'])->name('brands.deleteSelected');
    Route::post('brandsForceDelete', [BrandsController::class, 'forceDelete'])->name('brands.forceDelete');
    Route::post('brandsRestore', [BrandsController::class, 'restore'])->name('brands.restore');
    Route::get('brandsArchived', [BrandsController::class, 'archived'])->name('brands.archived');
    Route::get('brandsShowNotification/{id}/{notification_id}', [BrandsController::class, 'showNotification'])->name('brands.showNotification');



    //currencies
    Route::resource('currencies', CurrencyController::class);
    Route::post('currenciesDeleteSelected', [CurrencyController::class, 'deleteSelected'])->name('currencies.deleteSelected');
    Route::post('currenciesForceDelete', [CurrencyController::class, 'forceDelete'])->name('currencies.forceDelete');
    Route::post('currenciesRestore', [CurrencyController::class, 'restore'])->name('currencies.restore');
    Route::get('currenciesArchived', [CurrencyController::class, 'archived'])->name('currencies.archived');
    Route::get('currenciesShowNotification/{id}/{notification_id}', [CurrencyController::class, 'showNotification'])->name('currencies.showNotification');



    //units
    Route::resource('units', UnitsController::class);
    Route::post('unitsDeleteSelected', [UnitsController::class, 'deleteSelected'])->name('units.deleteSelected');
    Route::post('unitsForceDelete', [UnitsController::class, 'forceDelete'])->name('units.forceDelete');
    Route::post('unitsRestore', [UnitsController::class, 'restore'])->name('units.restore');
    Route::get('unitsArchived', [UnitsController::class, 'archived'])->name('units.archived');
    Route::get('unitsShowNotification/{id}/{notification_id}', [UnitsController::class, 'showNotification'])->name('units.showNotification');



    //products
    Route::resource('products', ProductsController::class);
    Route::post('productsDeleteSelected', [ProductsController::class, 'deleteSelected'])->name('products.deleteSelected');
    Route::post('productsForceDelete', [ProductsController::class, 'forceDelete'])->name('products.forceDelete');
    Route::post('productsRestore', [ProductsController::class, 'restore'])->name('products.restore');
    Route::get('productsArchived', [ProductsController::class, 'archived'])->name('products.archived');
    Route::get('productsShowNotification/{id}/{notification_id}', [ProductsController::class, 'showNotification'])->name('products.showNotification');



    //purchases
    Route::resource('purchases', PurchasesController::class);
    Route::post('purchasesDeleteSelected', [PurchasesController::class, 'deleteSelected'])->name('purchases.deleteSelected');
    Route::post('purchasesForceDelete', [PurchasesController::class, 'forceDelete'])->name('purchases.forceDelete');
    Route::post('purchasesRestore', [PurchasesController::class, 'restore'])->name('purchases.restore');
    Route::get('purchasesArchived', [PurchasesController::class, 'archived'])->name('purchases.archived');
    Route::get('purchasesShowNotification/{id}/{notification_id}', [PurchasesController::class, 'showNotification'])->name('purchases.showNotification');

    //paymentPurchase
    Route::resource('paymentPurchase', PaymentPurchasesController::class);


    
    //purchasesReturns
    Route::resource('purchasesReturns', PurchasesReturnController::class);
    Route::post('purchasesReturnsDeleteSelected', [PurchasesReturnController::class, 'deleteSelected'])->name('purchasesReturns.deleteSelected');
    Route::post('purchasesReturnsForceDelete', [PurchasesReturnController::class, 'forceDelete'])->name('purchasesReturns.forceDelete');
    Route::post('purchasesReturnsRestore', [PurchasesReturnController::class, 'restore'])->name('purchasesReturns.restore');
    Route::get('purchasesReturnsArchived', [PurchasesReturnController::class, 'archived'])->name('purchasesReturns.archived');
    Route::get('purchasesReturnsShowNotification/{id}/{notification_id}', [PurchasesReturnController::class, 'showNotification'])->name('purchasesReturns.showNotification');

    //paymentPurchaseReturn
    Route::resource('paymentPurchaseReturn', PaymentPurchaseReturnsController::class);



    //sales
    Route::resource('sales', SalesController::class);
    Route::post('salesDeleteSelected', [SalesController::class, 'deleteSelected'])->name('sales.deleteSelected');
    Route::post('salesForceDelete', [SalesController::class, 'forceDelete'])->name('sales.forceDelete');
    Route::post('salesRestore', [SalesController::class, 'restore'])->name('sales.restore');
    Route::get('salesArchived', [SalesController::class, 'archived'])->name('sales.archived');
    Route::get('salesShowNotification/{id}/{notification_id}', [SalesController::class, 'showNotification'])->name('sales.showNotification');
    Route::get('stockAlertSNotification/{notification_id}', [SalesController::class, 'stockAlert'])->name('adjustments.stockAlert');

    //paymentSale
    Route::resource('paymentSale', PaymentSalesController::class);



    //salesReturns
    Route::resource('salesReturns', SalesReturnController::class);
    Route::post('salesReturnsDeleteSelected', [SalesReturnController::class, 'deleteSelected'])->name('salesReturns.deleteSelected');
    Route::post('salesReturnsForceDelete', [SalesReturnController::class, 'forceDelete'])->name('salesReturns.forceDelete');
    Route::post('salesReturnsRestore', [SalesReturnController::class, 'restore'])->name('salesReturns.restore');
    Route::get('salesReturnsArchived', [SalesReturnController::class, 'archived'])->name('salesReturns.archived');
    Route::get('salesReturnsShowNotification/{id}/{notification_id}', [SalesReturnController::class, 'showNotification'])->name('salesReturns.showNotification');

    //paymentSaleReturn
    Route::resource('paymentSaleReturn', PaymentSaleReturnsController::class);



    //warehouses
    Route::resource('warehouses', WarehouseController::class);
    Route::post('warehousesDeleteSelected', [WarehouseController::class, 'deleteSelected'])->name('warehouses.deleteSelected');
    Route::post('warehousesForceDelete', [WarehouseController::class, 'forceDelete'])->name('warehouses.forceDelete');
    Route::post('warehousesRestore', [WarehouseController::class, 'restore'])->name('warehouses.restore');
    Route::get('warehousesArchived', [WarehouseController::class, 'archived'])->name('warehouses.archived');
    Route::get('warehousesShowNotification/{id}/{notification_id}', [WarehouseController::class, 'showNotification'])->name('warehouses.showNotification');



    //adjustments
    Route::resource('adjustments', AdjustmentController::class);
    Route::post('adjustmentsDeleteSelected', [AdjustmentController::class, 'deleteSelected'])->name('adjustments.deleteSelected');
    Route::post('adjustmentsForceDelete', [AdjustmentController::class, 'forceDelete'])->name('adjustments.forceDelete');
    Route::post('adjustmentsRestore', [AdjustmentController::class, 'restore'])->name('adjustments.restore');
    Route::get('adjustmentsArchived', [AdjustmentController::class, 'archived'])->name('adjustments.archived');
    Route::get('adjustmentsShowNotification/{id}/{notification_id}', [AdjustmentController::class, 'showNotification'])->name('adjustments.showNotification');
    Route::get('adjustmentStock', [AdjustmentController::class, 'stock'])->name('adjustments.stock');


    //expensescategory
    Route::resource('expensescategory', CategoryExpenseController::class);
    Route::post('expensescategoryDeleteSelected', [CategoryExpenseController::class, 'deleteSelected'])->name('expensescategory.deleteSelected');
    Route::post('expensescategoryForceDelete', [CategoryExpenseController::class, 'forceDelete'])->name('expensescategory.forceDelete');
    Route::post('expensescategoryRestore', [CategoryExpenseController::class, 'restore'])->name('expensescategory.restore');
    Route::get('expensescategoryArchived', [CategoryExpenseController::class, 'archived'])->name('expensescategory.archived');
    Route::get('expensescategoryShowNotification/{id}/{notification_id}', [CategoryExpenseController::class, 'showNotification'])->name('expensescategory.showNotification');



    //expenses
    Route::resource('expenses', ExpensesController::class);
    Route::post('expensesDeleteSelected', [ExpensesController::class, 'deleteSelected'])->name('expenses.deleteSelected');
    Route::post('expensesForceDelete', [ExpensesController::class, 'forceDelete'])->name('expenses.forceDelete');
    Route::post('expensesRestore', [ExpensesController::class, 'restore'])->name('expenses.restore');
    Route::get('expensesArchived', [ExpensesController::class, 'archived'])->name('expenses.archived');
    Route::get('expensesShowNotification/{id}/{notification_id}', [ExpensesController::class, 'showNotification'])->name('expenses.showNotification');



    //reports
    Route::get('reports/salesPayments', [ReportController::class, 'salesPayments'])->name('reports.salesPayments');
    Route::get('reports/purchasesPayments', [ReportController::class, 'purchasesPayments'])->name('reports.purchasesPayments');
    Route::get('reports/salesReturnsPayments', [ReportController::class, 'salesReturnsPayments'])->name('reports.salesReturnsPayments');
    Route::get('reports/purchasesReturnsPayments', [ReportController::class, 'purchasesReturnsPayments'])->name('reports.purchasesReturnsPayments');
    Route::get("reports/statistics", [ReportController::class, 'statistics'])->name('reports.statistics');
    Route::get("reports/stockAlert", [ReportController::class, 'stockAlert'])->name('reports.stockAlert');
    Route::get("reports/warehouses", [ReportController::class, 'warehouses'])->name('reports.warehouses');
    Route::get("reports/sales", [ReportController::class, 'sales'])->name('reports.sales');
    Route::get("reports/purchases", [ReportController::class, 'purchases'])->name('reports.purchases');
    Route::get("reports/clients", [ReportController::class, 'clients'])->name('reports.clients');
    Route::get("reports/clientDetails/{id}", [ReportController::class, 'clientDetails'])->name('reports.clientDetails');
    Route::get("reports/providers", [ReportController::class, 'providers'])->name('reports.providers');
    Route::get("reports/providerDetails/{id}", [ReportController::class, 'providerDetails'])->name('reports.providerDetails');


    //setting
    Route::get("up", [SettingsController::class, 'up']);
    Route::get("down", [SettingsController::class, 'down']);
    Route::get("down_render", [SettingsController::class, 'down_render']);
    Route::get("Clear_Cache", [SettingsController::class, 'Clear_Cache']);

    //general routes
    Route::get('show_file/{folder_name}/{photo_name}', [GeneralController::class, 'show_file'])->name('show_file');
    Route::get('download_file/{folder_name}/{photo_name}', [GeneralController::class, 'download_file'])->name('download_file');
    Route::get('allNotifications', [GeneralController::class, 'allNotifications'])->name('allNotifications');
    Route::get('markAllAsRead', [GeneralController::class, 'markAllAsRead'])->name('markAllAsRead');

});













//------------------------------- Users --------------------------\\
//------------------------------------------------------------------\\
Route::resource('users', UserController::class);
Route::get('GetUserAuth', [UserController::class, 'GetUserAuth']);
Route::get("/GetPermissions", [UserController::class, 'GetPermissions']);
Route::put('users/Activated/{id}', [UserController::class, 'IsActivated']);
Route::get('users/export/Excel', [UserController::class, 'exportExcel']);
Route::get('users/Get_Info/Profile', [UserController::class, 'GetInfoProfile']);
Route::put('updateProfile/{id}', [UserController::class, 'updateProfile']);


//------------------------------- Backup --------------------------\\
//------------------------------------------------------------------\\
Route::get("GetBackup", [ReportController::class, 'GetBackup']);
Route::get("GenerateBackup", [ReportController::class, 'GenerateBackup']);
Route::delete("DeleteBackup/{name}", [ReportController::class, 'DeleteBackup']);







Route::get('/{page}', [GeneralController::class, 'index']);