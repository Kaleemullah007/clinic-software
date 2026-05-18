<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\BusinessHourController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\PlaceholderController;
use App\Http\Controllers\ClinicController;
use App\Http\Controllers\EmailTemplateController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\ScrappingController;

use App\Http\Controllers\FormController;
use App\Http\Controllers\HomeController;
use App\Models\Module;
use App\Models\Permission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

use function Ramsey\Uuid\v1;

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

// dd("dd");
// Route::get('generate-css', function(){
//     $color =  '#072e75'; //bg-orange
//     $color1 =  '#00d6ab'; //bg-theme g1
//     $color2 =  '#19e198'; //bg-thmeme g2
//     $color3 =  '#c8ffe4'; //light-theme
//     $color4 =  '#012e2e'; // text color
//     $color5 =  '#dbf8ff74';

//     $cssString = file_get_contents(resource_path('views/base.css'));
//     $cssString = str_replace('{{color}}', $color,$cssString);
//     $cssString = str_replace('{{color1}}', $color1,$cssString);
//     $cssString = str_replace('{{color2}}', $color2,$cssString);
//     $cssString = str_replace('{{color3}}', $color3,$cssString);
//     $cssString = str_replace('{{color4}}', $color4,$cssString);
//     $cssString = str_replace('{{color5}}', $color5,$cssString);
//     file_put_contents(resource_path('css/layout.css'),$cssString);

// dd("Done");
// });

//  Admin Panel

// Artisan::call('optimize');
// Artisan::call('up'); //need to down replace with up
// die();

Route::get('missing-enteries',[ScrappingController::class,'index'])->name('missing-enteries');
Route::get('new-enteries',[ScrappingController::class,'addSales'])->name('new-enteries');

// Route::get('download-csv',[ScrappingController::class,'exportUsersToCsv'])->name('exportUsersToCsv');


Route::group([
    'middleware' => ['avoid-back-history', 'auth'],
], function () {

    // Dashboard
    Route::get('dashboard', [HomeController::class, 'index'])->name('dashboard');

    // Appointments
    Route::get('time-slots', [AppointmentController::class, 'time_slots']);
    Route::get('add-new-service', [AppointmentController::class, 'addNewRow']);
    Route::post('update-services', [AppointmentController::class, 'UpdateService']);
    Route::post('update-patient-history', [AppointmentController::class, 'UpdatePatientHistory']);
    Route::get('get-detail', [AppointmentController::class, 'getcategory']);
    Route::get('generate-pdf/{id}', [AppointmentController::class, 'generatePDF'])->name('generate-pdf');
    Route::resource('appointments', AppointmentController::class);

    // Services / Categories
    Route::get('get-price/{category}', [CategoryController::class, 'getPrice']);
    Route::resource('category', CategoryController::class);

    // Users
    Route::get('updateprofile/{user}/edit', [UserController::class, 'showProfile']);
    Route::put('updateprofile', [UserController::class, 'updateProfile'])->name('users.updateprofile');
    Route::resource('users', UserController::class);

    // Roles & Permissions (super-admin only via controller middleware)
    Route::resource('role', RoleController::class);
    Route::resource('permission', PermissionController::class);

    // Other resources
    Route::resource('pages', PageController::class);
    Route::get('add-new-row', [SettingController::class, 'addNewRow']);
    Route::resource('settings', SettingController::class);
    Route::resource('prescription', PrescriptionController::class);
    Route::resource('businesshour', BusinessHourController::class);
    Route::resource('contacts', ContactController::class);
    Route::resource('blogger', BlogController::class);
    Route::resource('media', MediaController::class);
    Route::resource('email', EmailTemplateController::class)->parameters(['email' => 'id']);
    Route::resource('placeholder', PlaceholderController::class);
    Route::resource('clinic', ClinicController::class);
    Route::resource('module', ModuleController::class);

    // ── Vendors ─────────────────────────────────────────────────────────
    Route::get('vendors/list', [\App\Http\Controllers\VendorController::class, 'list'])->name('vendor.list');
    Route::resource('vendor', \App\Http\Controllers\VendorController::class);

    // ── Products & Variations ────────────────────────────────────────────
    Route::get('products/search', [\App\Http\Controllers\ProductController::class, 'search'])->name('product.search');
    Route::resource('products', \App\Http\Controllers\ProductController::class);
    Route::post('products/{product}/variations', [\App\Http\Controllers\ProductController::class, 'storeVariation'])->name('product.variation.store');
    Route::delete('products/variations/{variation}', [\App\Http\Controllers\ProductController::class, 'destroyVariation'])->name('product.variation.destroy');

    // ── Inventory ────────────────────────────────────────────────────────
    // Specific named routes MUST come before the resource so {inventory} wildcard doesn't swallow them
    Route::get('inventory/movements', [\App\Http\Controllers\InventoryController::class, 'movements'])->name('inventory.movements');
    Route::post('inventory/adjust', [\App\Http\Controllers\InventoryController::class, 'adjust'])->name('inventory.adjust');
    Route::resource('inventory', \App\Http\Controllers\InventoryController::class)->only(['index', 'show']);

    // ── Purchase Requests ────────────────────────────────────────────────
    Route::post('purchase-requests/{purchaseRequest}/approve', [\App\Http\Controllers\PurchaseRequestController::class, 'approve'])->name('purchase-request.approve');
    Route::post('purchase-requests/{purchaseRequest}/reject', [\App\Http\Controllers\PurchaseRequestController::class, 'reject'])->name('purchase-request.reject');
    Route::resource('purchase-requests', \App\Http\Controllers\PurchaseRequestController::class);

    // ── Purchases ────────────────────────────────────────────────────────
    Route::resource('purchases', \App\Http\Controllers\PurchaseController::class);

    // ── Doctor Agreements ────────────────────────────────────────────────
    Route::resource('doctor-agreements', \App\Http\Controllers\DoctorAgreementController::class);

    // ── Appointment Products ─────────────────────────────────────────────
    Route::get('appointment-products/by-code', [\App\Http\Controllers\AppointmentProductController::class, 'lookupByCode'])->name('appointment-product.lookup');
    Route::get('appointments/{appointment}/receipt', [\App\Http\Controllers\AppointmentProductController::class, 'receipt'])->name('appointment.receipt');
    Route::resource('appointment-products', \App\Http\Controllers\AppointmentProductController::class);

    // ── Returns & Damaged ────────────────────────────────────────────────
    Route::resource('returns', \App\Http\Controllers\ReturnController::class)->only(['index', 'create', 'store', 'show', 'destroy']);
    Route::resource('damaged-products', \App\Http\Controllers\DamagedProductController::class)->only(['index', 'create', 'store', 'destroy']);

    // ── Expenses ─────────────────────────────────────────────────────────
    Route::resource('expenses', \App\Http\Controllers\ExpenseController::class);

    // ── Salaries ─────────────────────────────────────────────────────────
    Route::get('salaries/{salary}/slip', [\App\Http\Controllers\SalaryController::class, 'slip'])->name('salary.slip');
    Route::post('salaries/{salary}/pay', [\App\Http\Controllers\SalaryController::class, 'markPaid'])->name('salary.pay');
    Route::resource('salaries', \App\Http\Controllers\SalaryController::class);

    // ── Call Logs ────────────────────────────────────────────────────────
    Route::resource('call-logs', \App\Http\Controllers\CallLogController::class);

    // ── Consent Forms ────────────────────────────────────────────────────
    Route::get('consent-forms/{consentForm}/sign', [\App\Http\Controllers\ConsentFormController::class, 'signView'])->name('consent-form.sign');
    Route::post('consent-forms/{consentForm}/sign', [\App\Http\Controllers\ConsentFormController::class, 'saveSignature'])->name('consent-form.save-signature');
    Route::resource('consent-forms', \App\Http\Controllers\ConsentFormController::class);

    // ── Before/After Photos ──────────────────────────────────────────────
    Route::resource('before-after-photos', \App\Http\Controllers\BeforeAfterPhotoController::class)->only(['index', 'store', 'destroy']);

    // ── Reports ──────────────────────────────────────────────────────────
    Route::get('reports', [\App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/revenue', [\App\Http\Controllers\ReportController::class, 'revenue'])->name('reports.revenue');
    Route::get('reports/inventory', [\App\Http\Controllers\ReportController::class, 'inventory'])->name('reports.inventory');
    Route::get('reports/expenses', [\App\Http\Controllers\ReportController::class, 'expenses'])->name('reports.expenses');
    Route::get('reports/salaries', [\App\Http\Controllers\ReportController::class, 'salaries'])->name('reports.salaries');
    Route::get('reports/doctor-performance', [\App\Http\Controllers\ReportController::class, 'doctorPerformance'])->name('reports.doctor-performance');

});






Route::get('/', function () {
    // return view('welcome');
    return to_route('login');
})->name('home');

Route::get('/image', function () {
    return view('image');
})->name('image');



Route::get('/services', function () {
    return view('services');
});


Route::get('/about', function () {
    return view('about');
})->name('about');

Route::get('/pricing', function () {
    return view('pricing');
})->name('pricing');

Route::get('/gallery', function () {
    return view('gallery');
})->name('gallery');

Route::get('/blogs', function () {
    return view('blogs');
})->name('blogs');

Route::get('/blog-post', function () {
    return view('blog-post');
})->name('blog-post');

Route::get('/contact', function () {
    return view('contact');
})->name('contact');

Route::get('/appointment', function () {
    return view('appointment');
})->name('appointment');



Route::get('hair-treatment', function () {
    return view('hair-treatment');
})->name('hair-treatment');

Route::get('hair-treatment/prp-for-hair-regrowth', function () {
    return view('prp-for-hair-regrowth');
})->name('prp-for-hair-regrowth');

Route::get('/hair-treatment/hair-transplant', function () {
    return view('hair-transplant');
})->name('hair-transplant');

Route::get('/hair-treatment/laser-hair-removal', function () {
    return view('laser-hair-removal');
})->name('laser-hair-removal');


Route::get('/skin-care-treatment/acne-scar', function () {
    return view('acne-scar');
})->name('acne-scar');

Route::get('/skin-care-treatment/co2-fractional-laser', function () {
    return view('co2-fractional-laser');
})->name('co2-fractional-laser');

Route::get('/skin-care-treatment/face-prp-micro-needlingandmesotherapy', function () {
    return view('face-prp-micro-needlingandmesotherapy');
})->name('face-prp-micro-needlingandmesotherapy');


Route::get('/skin-care-treatment/melasma-pigmentation-skin-glow', function () {
    return view('melasma-pigmentation-skin-glow');
})->name('melasma-pigmentation-skin-glow');

Route::get('/skin-care-treatment/q-switch-laser', function () {
    return view('q-switch-laser');
})->name('q-switch-laser');

Route::get('/skin-care-treatment/glutathione-cocktail', function () {
    return view('glutathione-cocktail');
})->name('glutathione-cocktail');

Route::get('/skin-care-treatment/prp-micro-needling-and-mesotherapy', function () {
    return view('prp-micro-needling-and-mesotherapy');
})->name('prp-micro-needling-and-mesotherapy');


Route::get('/skin-care-treatment/hydra-facial', function () {
    return view('hydra-facial');
})->name('hydra-facial');

Route::get('/skin-care-treatment/basic-simple-hydrafacial', function () {
    return view('basic-simple-hydrafacial');
})->name('basic-simple-hydrafacial');

Route::get('/skin-care-treatment/oxygeno-facial', function () {
    return view('oxygeno-facial');
})->name('oxygeno-facial');

Route::get('/skin-care-treatment/photo-facial', function () {
    return view('photo-facial');
})->name('photo-facial');

Route::get('/skin-care-treatment/carbon-peel-laser', function () {
    return view('carbon-peel-laser');
})->name('carbon-peel-laser');


Route::get('/skin-care-treatment/fillers', function () {
    return view('fillers');
})->name('fillers');

Route::get('/skin-care-treatment/lip-filler', function () {
    return view('lip-filler');
})->name('lip-filler');

Route::get('/skin-care-treatment/laugh-nasolabial-lines', function () {
    return view('laugh-nasolabial-lines');
})->name('laugh-nasolabial-lines');

Route::get('/skin-care-treatment/under-eye-filler', function () {
    return view('under-eye-filler');
})->name('under-eye-filler');


Route::get('/skin-care-treatment/botox', function () {
    return view('botox');
})->name('botox');

Route::get('/skin-care-treatment/forhead-lines', function () {
    return view('forhead-lines');
})->name('forhead-lines');

Route::get('/skin-care-treatment/crows-feet', function () {
    return view('crows-feet');
})->name('crows-feet');


Route::get('/skin-care-treatment/non-surgical-face-lift', function () {
    return view('non-surgical-face-lift');
})->name('non-surgical-face-lift');

Route::get('/skin-care-treatment/thread-face-lift', function () {
    return view('thread-face-lift');
})->name('thread-face-lift');

Route::get('/skin-care-treatment/high-intensity-focused-ultrasound', function () {
    return view('high-intensity-focused-ultrasound');
})->name('high-intensity-focused-ultrasound');

Route::get('/skin-care-treatment/non-surgical-breast-lift', function () {
    return view('non-surgical-breast-lift');
})->name('non-surgical-breast-lift');

Route::get('/skin-care-treatment/fat-reduction-fat-freezing-cryolipolysis', function () {
    return view('fat-reduction-fat-freezing-cryolipolysis');
})->name('fat-reduction-fat-freezing-cryolipolysis');

Route::get('/skin-care-treatment/mole-removal', function () {
    return view('mole-removal');
})->name('mole-removal');

Route::get('/skin-care-treatment/tattoo-removal', function () {
    return view('tattoo-removal');
})->name('tattoo-removal');




Route::get('/sign-up', function () {
    return view('sign-up');
});

Route::get('/forgot-password', function () {
    return view('forgot-password');
});

Route::get('/log-in', function () {
    return view('log-in');
});

Route::get('/sign-up', function () {
    return view('sign-up');
});




Auth::routes();
// Auth::routes([
//   'register' => false // Registration Routes...
// //   'reset' => false, // Password Reset Routes...
// //   'verify' => false, // Email Verification Routes...
// ]);

Route::controller(HomeController::class)->middleware(['auth'])->group(function(){
    Route::get('/home', 'index')->name('home');
});
Route::post('/form/store', [FormController::class, 'store'])->name('contactus');

Route::fallback(function () {
  return redirect('/');

});
