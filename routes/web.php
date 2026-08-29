<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\{
    HomeController,
    AuthController,
    ShopController,
    CartController,
    CheckoutController,
    AccountController,
    AdminController,
    AdminSettingsController,
    AdminUserController,
    AdminRoleController,
    AdminAuditController,
    GalleryController,
    ServiceController,
    ContactController,
    CompanyProfileController,
    AdminServiceController,
    AdminContactMessageController
};


/*
|--------------------------------------------------------------------------
| Public Website
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])
    ->name('home');

Route::get('/shop', [ShopController::class, 'index'])
    ->name('shop');

Route::get('/search', [ShopController::class, 'searchSuggestions'])
    ->name('search');

Route::get('/product/{slug}', [ShopController::class, 'show'])
    ->name('product.show');

Route::get('/gallery', [GalleryController::class, 'index'])
    ->name('gallery');

Route::get('/profile', [CompanyProfileController::class, 'index'])
    ->name('profile');

Route::get('/hospital-services', [ServiceController::class, 'index'])
    ->defaults('type', 'hospital')
    ->name('hospital.services');

Route::get('/hospital-services/{slug}', [ServiceController::class, 'show'])
    ->defaults('type', 'hospital')
    ->name('hospital.services.show');

Route::get('/other-services', [ServiceController::class, 'index'])
    ->defaults('type', 'other')
    ->name('other.services');

Route::get('/other-services/{slug}', [ServiceController::class, 'show'])
    ->defaults('type', 'other')
    ->name('other.services.show');

Route::get('/contact-us', [ContactController::class, 'index'])
    ->name('contact');

Route::post('/contact-us', [ContactController::class, 'store'])
    ->name('contact.store');


/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/

Route::get('/login', [AuthController::class, 'login'])
    ->name('login');

Route::post('/login', [AuthController::class, 'authenticate']);

Route::get('/register', [AuthController::class, 'register']);

Route::post('/register', [AuthController::class, 'store']);

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth');


/*
|--------------------------------------------------------------------------
| Cart
|--------------------------------------------------------------------------
*/

Route::get('/cart', [CartController::class, 'index'])
    ->name('cart');

Route::post('/cart/add/{product}', [CartController::class, 'add'])
    ->name('cart.add');

Route::post('/cart/update', [CartController::class, 'update']);

Route::get('/cart/remove/{id}', [CartController::class, 'remove']);


/*
|--------------------------------------------------------------------------
| Customer Account / Checkout
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::get('/checkout', [CheckoutController::class, 'index'])
        ->name('checkout');

    Route::post('/checkout', [CheckoutController::class, 'place'])
        ->name('checkout.place');

    Route::get('/account', [AccountController::class, 'index'])
        ->name('account');

    Route::get('/account/orders/{order}', [AccountController::class, 'order'])
        ->name('account.order');

    Route::get('/account/password', [AccountController::class, 'passwordEdit'])
        ->name('account.password.edit');

    Route::put('/account/password', [AccountController::class, 'passwordUpdate'])
        ->name('account.password.update');
});


/*
|--------------------------------------------------------------------------
| ADMIN PANEL
|--------------------------------------------------------------------------
*/

Route::prefix('admin')
    ->middleware(['auth', 'admin'])
    ->group(function () {
        Route::get('/', [AdminController::class, 'dashboard'])
            ->middleware('permission:dashboard.view')->name('admin.dashboard');

        Route::get('/products', [AdminController::class, 'products'])
            ->middleware('permission:products.view')->name('admin.products');
        Route::get('/products/create', [AdminController::class, 'productCreate'])
            ->middleware('permission:products.create')->name('admin.products.create');
        Route::post('/products', [AdminController::class, 'productStore'])
            ->middleware('permission:products.create')->name('admin.products.store');
        Route::get('/products/{product}/edit', [AdminController::class, 'productEdit'])
            ->middleware('permission:products.edit')->name('admin.products.edit');
        Route::put('/products/{product}', [AdminController::class, 'productUpdate'])
            ->middleware('permission:products.edit')->name('admin.products.update');
        Route::delete('/products/{product}', [AdminController::class, 'productDelete'])
            ->middleware('permission:products.delete')->name('admin.products.delete');

        Route::get('/orders', [AdminController::class, 'orders'])
            ->middleware('permission:orders.view')->name('admin.orders');
        Route::get('/orders/{order}', [AdminController::class, 'orderShow'])
            ->middleware('permission:orders.show')->name('admin.orders.show');
        Route::patch('/orders/{id}', [AdminController::class, 'orderUpdate'])
            ->middleware('permission:orders.update')->name('admin.orders.update');

        Route::get('/catalog', [AdminController::class, 'catalog'])
            ->middleware('permission:catalog.view')->name('admin.catalog');
        Route::post('/catalog/categories', [AdminController::class, 'categoryStore'])
            ->middleware('permission:catalog.manage')->name('admin.catalog.categories.store');
        Route::post('/catalog/subcategories', [AdminController::class, 'subcategoryStore'])
            ->middleware('permission:catalog.manage')->name('admin.catalog.subcategories.store');
        Route::post('/catalog/brands', [AdminController::class, 'brandStore'])
            ->middleware('permission:catalog.manage')->name('admin.catalog.brands.store');
        Route::delete('/catalog/categories/{category}', [AdminController::class, 'categoryDelete'])
            ->middleware('permission:catalog.manage')->name('admin.catalog.categories.delete');
        Route::delete('/catalog/subcategories/{subcategory}', [AdminController::class, 'subcategoryDelete'])
            ->middleware('permission:catalog.manage')->name('admin.catalog.subcategories.delete');
        Route::delete('/catalog/brands/{brand}', [AdminController::class, 'brandDelete'])
            ->middleware('permission:catalog.manage')->name('admin.catalog.brands.delete');

        Route::get('/company', [AdminController::class, 'company'])
            ->middleware('permission:company.view')->name('admin.company');
        Route::post('/company', [AdminController::class, 'companyUpdate'])
            ->middleware('permission:company.edit')->name('admin.company.update');

        Route::get('/sales', [AdminController::class, 'sales'])
            ->middleware('permission:sales.view')->name('admin.sales');
        Route::post('/sales', [AdminController::class, 'saleStore'])
            ->middleware('permission:sales.create')->name('admin.sales.store');
        Route::get('/sales/history', [AdminController::class, 'saleHistory'])
            ->middleware('permission:sales.history')->name('admin.sales.history');
        Route::get('/sales/invoice/{order}', [AdminController::class, 'saleInvoice'])
            ->middleware('permission:sales.invoice')->name('admin.sales.invoice');
        Route::get('/tracking', [AdminController::class, 'tracking'])
            ->middleware('permission:tracking.view')->name('admin.tracking');

        Route::prefix('settings')->group(function () {
            Route::get('/', [AdminSettingsController::class, 'index'])
                ->middleware('permission:settings.general.view')->name('admin.settings');

            Route::get('/users', [AdminUserController::class, 'index'])
                ->middleware('permission:users.view')->name('admin.users.index');
            Route::get('/users/create', [AdminUserController::class, 'create'])
                ->middleware('permission:users.create')->name('admin.users.create');
            Route::post('/users', [AdminUserController::class, 'store'])
                ->middleware('permission:users.create')->name('admin.users.store');
            Route::get('/users/{user}/edit', [AdminUserController::class, 'edit'])
                ->middleware('permission:users.edit')->name('admin.users.edit');
            Route::put('/users/{user}', [AdminUserController::class, 'update'])
                ->middleware('permission:users.edit')->name('admin.users.update');
            Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])
                ->middleware('permission:users.delete')->name('admin.users.delete');
            Route::patch('/users/{user}/status', [AdminUserController::class, 'status'])
                ->middleware('permission:users.status')->name('admin.users.status');
            Route::put('/users/{user}/reset-password', [AdminUserController::class, 'resetPassword'])
                ->middleware('permission:users.password.reset')->name('admin.users.reset-password');

            Route::get('/roles', [AdminRoleController::class, 'index'])
                ->middleware('permission:roles.view')->name('admin.roles.index');
            Route::get('/roles/create', [AdminRoleController::class, 'create'])
                ->middleware('permission:roles.create')->name('admin.roles.create');
            Route::post('/roles', [AdminRoleController::class, 'store'])
                ->middleware('permission:roles.create')->name('admin.roles.store');
            Route::get('/roles/{role}/edit', [AdminRoleController::class, 'edit'])
                ->middleware('permission:roles.edit')->name('admin.roles.edit');
            Route::put('/roles/{role}', [AdminRoleController::class, 'update'])
                ->middleware('permission:roles.permissions')->name('admin.roles.update');
            Route::delete('/roles/{role}', [AdminRoleController::class, 'destroy'])
                ->middleware('permission:roles.delete')->name('admin.roles.delete');

            Route::get('/audit', [AdminAuditController::class, 'index'])
                ->middleware('permission:audit.view')->name('admin.audit.index');

            Route::get('/product-display', [AdminSettingsController::class, 'productDisplay'])
                ->middleware('permission:settings.product-display.view')->name('admin.settings.product-display');
            Route::post('/product-display', [AdminSettingsController::class, 'productDisplayUpdate'])
                ->middleware('permission:settings.product-display.edit')->name('admin.settings.product-display.update');
            Route::get('/layout', [AdminSettingsController::class, 'layout'])
                ->middleware('permission:settings.layout.view')->name('admin.settings.layout');
            Route::post('/layout', [AdminSettingsController::class, 'layoutUpdate'])
                ->middleware('permission:settings.layout.edit')->name('admin.settings.layout.update');
            Route::get('/general', [AdminSettingsController::class, 'general'])
                ->middleware('permission:settings.general.view')->name('admin.settings.general');
            Route::post('/general', [AdminSettingsController::class, 'generalUpdate'])
                ->middleware('permission:settings.general.edit')->name('admin.settings.general.update');

            Route::get('/services', [AdminServiceController::class, 'index'])
                ->middleware('permission:services.view')->name('admin.settings.services.index');
            Route::get('/services/create', [AdminServiceController::class, 'create'])
                ->middleware('permission:services.create')->name('admin.settings.services.create');
            Route::post('/services', [AdminServiceController::class, 'store'])
                ->middleware('permission:services.create')->name('admin.settings.services.store');
            Route::get('/services/{service}/edit', [AdminServiceController::class, 'edit'])
                ->middleware('permission:services.edit')->name('admin.settings.services.edit');
            Route::put('/services/{service}', [AdminServiceController::class, 'update'])
                ->middleware('permission:services.edit')->name('admin.settings.services.update');
            Route::patch('/services/{service}/status', [AdminServiceController::class, 'status'])
                ->middleware('permission:services.status')->name('admin.settings.services.status');
            Route::delete('/services/{service}', [AdminServiceController::class, 'destroy'])
                ->middleware('permission:services.delete')->name('admin.settings.services.delete');

            Route::get('/contact-messages', [AdminContactMessageController::class, 'index'])
                ->middleware('permission:contact-messages.view')->name('admin.settings.contact-messages');
            Route::get('/contact-messages/{contactMessage}', [AdminContactMessageController::class, 'show'])
                ->middleware('permission:contact-messages.view')->name('admin.settings.contact-messages.show');
            Route::patch('/contact-messages/{contactMessage}', [AdminContactMessageController::class, 'update'])
                ->middleware('permission:contact-messages.manage')->name('admin.settings.contact-messages.update');
            Route::delete('/contact-messages/{contactMessage}', [AdminContactMessageController::class, 'destroy'])
                ->middleware('permission:contact-messages.delete')->name('admin.settings.contact-messages.delete');

            Route::get('/gallery', [GalleryController::class, 'adminIndex'])
                ->middleware('permission:gallery.view')->name('admin.settings.gallery');
            Route::post('/gallery', [GalleryController::class, 'store'])
                ->middleware('permission:gallery.manage')->name('admin.settings.gallery.store');
            Route::put('/gallery/{galleryItem}', [GalleryController::class, 'update'])
                ->middleware('permission:gallery.manage')->name('admin.settings.gallery.update');
            Route::delete('/gallery/{galleryItem}', [GalleryController::class, 'destroy'])
                ->middleware('permission:gallery.manage')->name('admin.settings.gallery.delete');
            Route::post('/gallery/settings', [GalleryController::class, 'settingsUpdate'])
                ->middleware('permission:gallery.manage')->name('admin.settings.gallery.settings');
        });
    });
