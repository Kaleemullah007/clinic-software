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
    'middleware' => ['avoid-back-history', 'auth', 'check-device'],
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
    Route::post('appointments/{appointment}/toggle-payment', [AppointmentController::class, 'togglePayment'])->name('appointments.toggle-payment');
    // Doctor service management on appointment
    Route::get('appointments/{appointment}/services', [AppointmentController::class, 'getServices'])->name('appointments.services.get');
    Route::post('appointments/{appointment}/services', [AppointmentController::class, 'addService'])->name('appointments.services.add');
    Route::put('appointments/{appointment}/services/{service}', [AppointmentController::class, 'updateService'])->name('appointments.services.update');
    Route::delete('appointments/{appointment}/services/{service}', [AppointmentController::class, 'deleteService'])->name('appointments.services.delete');
    Route::patch('appointments/{appointment}/discount', [AppointmentController::class, 'updateDiscount'])->name('appointments.discount.update');

    // Services / Categories
    Route::get('get-price/{category}', [CategoryController::class, 'getPrice']);
    Route::resource('category', CategoryController::class);

    // Users
    Route::get('updateprofile/{user}/edit', [UserController::class, 'showProfile']);
    Route::put('updateprofile', [UserController::class, 'updateProfile'])->name('users.updateprofile');
    Route::get('users/stop-acting',          [UserController::class, 'stopActing'])->name('users.stop-acting');
    Route::resource('users', UserController::class);
    Route::get('users/{user}/act-as',        [UserController::class, 'actAs'])->name('users.act-as');
    Route::post('users/{user}/toggle-status',[UserController::class, 'toggleStatus'])->name('users.toggle-status');

    // Roles & Permissions (super-admin only via controller middleware)
    Route::resource('role', RoleController::class);
    Route::resource('permission', PermissionController::class);

    // Other resources
    Route::resource('pages', PageController::class);
    Route::get('add-new-row', [SettingController::class, 'addNewRow']);
    Route::resource('settings', SettingController::class);
    Route::resource('prescription', PrescriptionController::class);
    Route::get('prescriptions/patient/{userId}', [\App\Http\Controllers\PrescriptionController::class, 'getPatientRecords'])->name('prescription.patient-records');
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

    // ── Taxonomy / Service Migration ─────────────────────────────────────
    // Specific sub-routes MUST come before any resource to avoid wildcard capture
    Route::get('taxonomy/appointment-services', [\App\Http\Controllers\TaxonomyController::class, 'getAppointmentServices'])->name('taxonomy.appointment-services');
    Route::post('taxonomy/preview',  [\App\Http\Controllers\TaxonomyController::class, 'preview'])->name('taxonomy.preview');
    Route::post('taxonomy/migrate',  [\App\Http\Controllers\TaxonomyController::class, 'migrate'])->name('taxonomy.migrate');
    Route::get('taxonomy/progress',  [\App\Http\Controllers\TaxonomyController::class, 'progress'])->name('taxonomy.progress');
    Route::get('taxonomy',           [\App\Http\Controllers\TaxonomyController::class, 'index'])->name('taxonomy.index');

    // ── CSV Import ───────────────────────────────────────────────────────
    Route::prefix('imports')->name('imports.')->group(function () {
        Route::get('/',                                          [\App\Http\Controllers\ImportController::class, 'index'])->name('index');
        Route::post('/upload',                                   [\App\Http\Controllers\ImportController::class, 'upload'])->name('upload');
        Route::post('/start',                                    [\App\Http\Controllers\ImportController::class, 'start'])->name('start');
        Route::get('/progress',                                  [\App\Http\Controllers\ImportController::class, 'progress'])->name('progress');
        Route::get('/{importLog}/download-failed',               [\App\Http\Controllers\ImportController::class, 'downloadFailed'])->name('download-failed');
        Route::post('/{importLog}/rollback',                     [\App\Http\Controllers\ImportController::class, 'rollback'])->name('rollback');
        Route::delete('/{importLog}',                            [\App\Http\Controllers\ImportController::class, 'destroy'])->name('destroy');
    });

    // ── WhatsApp ─────────────────────────────────────────────────────────
    Route::post('appointments/{appointment}/send-whatsapp-receipt', [\App\Http\Controllers\WhatsappController::class, 'send'])->name('whatsapp.send');
    Route::get('whatsapp-logs', [\App\Http\Controllers\WhatsappController::class, 'index'])->name('whatsapp.logs');

    // ── WhatsApp Campaign ────────────────────────────────────────────────
    Route::prefix('whatsapp-campaign')->name('whatsapp-campaign.')->group(function () {
        // Campaigns
        Route::get('/',              [\App\Http\Controllers\WhatsappCampaignController::class, 'index'])->name('index');
        Route::get('/create',        [\App\Http\Controllers\WhatsappCampaignController::class, 'create'])->name('create');
        Route::post('/',             [\App\Http\Controllers\WhatsappCampaignController::class, 'store'])->name('store');
        Route::get('/{whatsappCampaign}',        [\App\Http\Controllers\WhatsappCampaignController::class, 'show'])->name('show');
        Route::delete('/{whatsappCampaign}',     [\App\Http\Controllers\WhatsappCampaignController::class, 'destroy'])->name('destroy');
        // Templates
        Route::get('/templates/list',   [\App\Http\Controllers\WhatsappCampaignController::class, 'templatesIndex'])->name('templates');
        Route::get('/templates/create', [\App\Http\Controllers\WhatsappCampaignController::class, 'templatesCreate'])->name('templates.create');
        Route::post('/templates',       [\App\Http\Controllers\WhatsappCampaignController::class, 'templatesStore'])->name('templates.store');
        Route::get('/templates/{template}/edit', [\App\Http\Controllers\WhatsappCampaignController::class, 'templatesEdit'])->name('templates.edit');
        Route::put('/templates/{template}',      [\App\Http\Controllers\WhatsappCampaignController::class, 'templatesUpdate'])->name('templates.update');
        Route::delete('/templates/{template}',   [\App\Http\Controllers\WhatsappCampaignController::class, 'templatesDestroy'])->name('templates.destroy');
    });

    // ── Staff ID Cards ───────────────────────────────────────────────────
    Route::get('staff-id-cards', [\App\Http\Controllers\StaffIdCardController::class, 'index'])->name('staff-id-cards.index');
    Route::get('staff-id-cards/users', [\App\Http\Controllers\StaffIdCardController::class, 'getUsers'])->name('staff-id-cards.users');

    // ── Call Manager ─────────────────────────────────────────────────────
    Route::get('call-manager', [\App\Http\Controllers\CallManagerController::class, 'index'])->name('call-manager.index');
    Route::get('call-manager/notes/{appointment}', [\App\Http\Controllers\CallManagerController::class, 'getNotes'])->name('call-manager.notes.get');
    Route::post('call-manager/notes', [\App\Http\Controllers\CallManagerController::class, 'saveNote'])->name('call-manager.notes.save');
    Route::put('call-manager/notes/{log}', [\App\Http\Controllers\CallManagerController::class, 'updateNote'])->name('call-manager.notes.update');

    // ── Point of Sale ────────────────────────────────────────────────
    Route::prefix('pos')->name('pos.')->group(function () {
        Route::get('/',                   [\App\Http\Controllers\PosController::class, 'index'])->name('index');
        Route::get('/create',             [\App\Http\Controllers\PosController::class, 'create'])->name('create');
        Route::post('/',                  [\App\Http\Controllers\PosController::class, 'store'])->name('store');
        Route::get('/report',             [\App\Http\Controllers\PosController::class, 'report'])->name('report');
        Route::get('/products/search',    [\App\Http\Controllers\PosController::class, 'getProducts'])->name('products');
        Route::get('/patients/search',    [\App\Http\Controllers\PosController::class, 'searchPatients'])->name('patients.search');
        Route::post('/patients',          [\App\Http\Controllers\PosController::class, 'quickCreatePatient'])->name('patients.store');
        Route::get('/load-order',         [\App\Http\Controllers\PosController::class, 'loadOrder'])->name('load-order');
        Route::get('/states',             [\App\Http\Controllers\PosController::class, 'getStates'])->name('states');
        Route::get('/cities',             [\App\Http\Controllers\PosController::class, 'getCities'])->name('cities');
        Route::get('/{pos}/edit',         [\App\Http\Controllers\PosController::class, 'edit'])->name('edit');
        Route::put('/{pos}',              [\App\Http\Controllers\PosController::class, 'update'])->name('update');
        Route::get('/{pos}',              [\App\Http\Controllers\PosController::class, 'show'])->name('show');
        Route::delete('/{pos}',           [\App\Http\Controllers\PosController::class, 'destroy'])->name('destroy');
        Route::post('/{pos}/toggle-payment', [\App\Http\Controllers\PosController::class, 'togglePayment'])->name('toggle-payment');
    });

    // ── Reports ──────────────────────────────────────────────────────────
    Route::get('reports', [\App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/revenue', [\App\Http\Controllers\ReportController::class, 'revenue'])->name('reports.revenue');
    Route::get('reports/inventory', [\App\Http\Controllers\ReportController::class, 'inventory'])->name('reports.inventory');
    Route::get('reports/expenses', [\App\Http\Controllers\ReportController::class, 'expenses'])->name('reports.expenses');
    Route::get('reports/salaries', [\App\Http\Controllers\ReportController::class, 'salaries'])->name('reports.salaries');
    Route::get('reports/doctor-performance', [\App\Http\Controllers\ReportController::class, 'doctorPerformance'])->name('reports.doctor-performance');
    Route::get('reports/appointments',   [\App\Http\Controllers\ReportController::class, 'appointmentsReport'])->name('reports.appointments');
    Route::get('reports/services',       [\App\Http\Controllers\ReportController::class, 'serviceRevenue'])->name('reports.services');
    Route::get('reports/patients',       [\App\Http\Controllers\ReportController::class, 'patients'])->name('reports.patients');
    Route::get('reports/products-sold',  [\App\Http\Controllers\ReportController::class, 'productsSold'])->name('reports.products-sold');
    Route::get('reports/summary',        [\App\Http\Controllers\ReportController::class, 'summary'])->name('reports.summary');
    Route::get('reports/service-gap',    [\App\Http\Controllers\ReportController::class, 'serviceGap'])->name('reports.service-gap');
    Route::get('reports/product-gap',    [\App\Http\Controllers\ReportController::class, 'productGap'])->name('reports.product-gap');

    // ── Device Approvals (superadmin only) ───────────────────────────────
    Route::prefix('device-approvals')->group(function () {
        Route::get('/',                [\App\Http\Controllers\DeviceApprovalController::class, 'index'])->name('device-approvals.index');
        Route::get('/data',            [\App\Http\Controllers\DeviceApprovalController::class, 'data'])->name('device-approvals.data');
        Route::post('/{id}/approve',   [\App\Http\Controllers\DeviceApprovalController::class, 'approve'])->name('device-approvals.approve');
        Route::post('/{id}/reject',    [\App\Http\Controllers\DeviceApprovalController::class, 'reject'])->name('device-approvals.reject');
        Route::post('/{id}/revoke',    [\App\Http\Controllers\DeviceApprovalController::class, 'revoke'])->name('device-approvals.revoke');
        Route::post('/toggle-setting', [\App\Http\Controllers\DeviceApprovalController::class, 'toggleSetting'])->name('device-approvals.toggle-setting');
    });

});

// ── Device pending page (no auth required) ────────────────────────────────
Route::get('/device/pending',  [\App\Http\Controllers\DeviceApprovalController::class, 'pendingPage'])->name('device.pending');
Route::get('/device/check',    [\App\Http\Controllers\DeviceApprovalController::class, 'checkStatus'])->name('device.check');






Route::get('/', function () {
    // return view('welcome');
    return to_route('login');
});

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
