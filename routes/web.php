<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ImagesController;
use App\Http\Controllers\EbooksController;
use App\Http\Controllers\OrganizationsController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\UsersController;

use App\Http\Controllers\QuestionsController;

use App\Http\Controllers\TypeformPluginsController;
use App\Http\Controllers\ActiveCampaignPluginsController;
use App\Http\Controllers\PluginsController;
use App\Http\Controllers\FormsController;
use App\Http\Controllers\AutomationsController;

use App\Http\Controllers\DeliverablesController;

use Illuminate\Support\Facades\Route;

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

// Auth

Route::get('login', [AuthenticatedSessionController::class, 'create'])
    ->name('login')
    ->middleware('guest');

Route::post('login', [AuthenticatedSessionController::class, 'store'])
    ->name('login.store')
    ->middleware('guest');

Route::delete('logout', [AuthenticatedSessionController::class, 'destroy'])
    ->name('logout');

// Dashboard

// Route::get('/', [OrganizationsController::class, 'index'])
//     ->name('flows')
//     ->middleware('auth');

// Route::get('/', [DashboardController::class, 'index'])
//     ->name('dashboard')
//     ->middleware('auth');


// Forms

Route::get('forms', [FormsController::class, 'index'])
    ->name('forms')
    ->middleware('auth');

Route::get('forms/{selected_form}/view', [FormsController::class, 'view'])
    ->name('forms.view')
    ->middleware('auth');

Route::get('forms/{selected_form}/{question}/view', [QuestionsController::class, 'view'])
    ->name('question.view')
    ->middleware('auth');

    




// Questions

Route::get('questions', [QuestionsController::class, 'index'])
    ->name('questions')
    ->middleware('auth');

Route::get('questions/{question}/view', [QuestionsController::class, 'view'])
    ->name('questions.edit')
    ->middleware('auth');





// Settings

Route::get('settings/plugins', [PluginsController::class, 'index'])
    ->name('plugins')
    ->middleware('auth');

Route::post('settings/plugins/typeform', [TypeformPluginsController::class, 'store'])
    ->name('typeform.store')
    ->middleware('auth');

Route::get('settings/plugins/typeform/create', [TypeformPluginsController::class, 'create'])
    ->name('typeform.create')
    ->middleware('auth');

Route::get('settings/plugins/typeform/{plugin}/edit', [TypeformPluginsController::class, 'edit'])
    ->name('typeform.edit')
    ->middleware('auth');

Route::put('settings/plugins/typeform/{plugin}', [TypeformPluginsController::class, 'update'])
    ->name('typeform.update')
    ->middleware('auth');

Route::delete('settings/plugins/typeform/{plugin}', [TypeformPluginsController::class, 'destroy'])
    ->name('typeform.destroy')
    ->middleware('auth');

Route::put('settings/plugins/typeform/{plugin}/restore', [TypeformPluginsController::class, 'restore'])
    ->name('typeform.restore')
    ->middleware('auth');


Route::get('settings/plugins/typeform/{plugin}/sync', [TypeformPluginsController::class, 'sync'])
    ->name('typeform.sync')
    ->middleware('auth');


Route::post('settings/plugins/activecampaign', [ActiveCampaignPluginsController::class, 'store'])
    ->name('activecampaign.store')
    ->middleware('auth');

Route::get('settings/plugins/activecampaign/create', [ActiveCampaignPluginsController::class, 'create'])
    ->name('activecampaign.create')
    ->middleware('auth');

Route::get('settings/plugins/activecampaign/{plugin}/edit', [ActiveCampaignPluginsController::class, 'edit'])
    ->name('activecampaign.edit')
    ->middleware('auth');

Route::put('settings/plugins/activecampaign/{plugin}', [ActiveCampaignPluginsController::class, 'update'])
    ->name('activecampaign.update')
    ->middleware('auth');

Route::delete('settings/plugins/activecampaign/{plugin}', [ActiveCampaignPluginsController::class, 'destroy'])
    ->name('activecampaign.destroy')
    ->middleware('auth');
    




// Deliverables

Route::get('deliverables', [DeliverablesController::class, 'index'])
    ->name('deliverables')
    ->middleware('auth');

Route::get('deliverables/create', [DeliverablesController::class, 'create'])
    ->name('deliverables.create')
    ->middleware('auth');

Route::post('deliverables', [DeliverablesController::class, 'store'])
    ->name('deliverables.store')
    ->middleware('auth');

Route::get('deliverables/{deliverable}/edit', [DeliverablesController::class, 'edit'])
    ->name('deliverables.edit')
    ->middleware('auth');

Route::put('deliverables/{deliverable}', [DeliverablesController::class, 'update'])
    ->name('deliverables.update')
    ->middleware('auth');

Route::get('deliverables/{deliverable}/live_preview/{response}', [DeliverablesController::class, 'createPDF'])
    ->name('deliverables.createpdf');



// Flows

Route::get('/', [AutomationsController::class, 'index'])
    ->name('flows')
    ->middleware('auth');

Route::get('flows/create', [AutomationsController::class, 'create'])
    ->name('flows.create')
    ->middleware('auth');

Route::post('flows', [AutomationsController::class, 'store'])
    ->name('flows.store')
    ->middleware('auth');

Route::get('flows/{flow}/edit', [AutomationsController::class, 'edit'])
    ->name('flows.edit')
    ->middleware('auth');

Route::put('flows/{flow}', [AutomationsController::class, 'update'])
    ->name('flows.update')
    ->middleware('auth');

Route::get('flows/{flow}/execute', [AutomationsController::class, 'execute'])
    ->name('flows.execute')
    ->middleware('auth');

Route::get('flows/{flow}/execute/{response_id}', [AutomationsController::class, 'executeOnly'])
    ->name('flows.executeOnly')
    ->middleware('auth');


// Images

Route::get('/img/{path}', [ImagesController::class, 'show'])
    ->where('path', '.*')
    ->name('image');

Route::get('settings/images', [ImagesController::class, 'index'])
    ->name('images')
    ->middleware('auth');

Route::get('settings/images/create', [ImagesController::class, 'create'])
    ->name('image.create')
    ->middleware('auth');

Route::post('settings/images', [DeliverablesController::class, 'storeImage'])
    ->name('image.store')
    ->middleware('auth');


// Ebooks

Route::get('/ebooks/{path}', [EbooksController::class, 'show'])
    ->name('ebook');



// Users

Route::get('settings/users', [UsersController::class, 'index'])
    ->name('users')
    ->middleware('auth');

Route::get('settings/users/create', [UsersController::class, 'create'])
    ->name('users.create')
    ->middleware('auth');

Route::post('users', [UsersController::class, 'store'])
    ->name('users.store')
    ->middleware('auth');

Route::get('settings/users/{user}/edit', [UsersController::class, 'edit'])
    ->name('users.edit')
    ->middleware('auth');

Route::put('users/{user}', [UsersController::class, 'update'])
    ->name('users.update')
    ->middleware('auth');

Route::delete('users/{user}', [UsersController::class, 'destroy'])
    ->name('users.destroy')
    ->middleware('auth');

Route::put('users/{user}/restore', [UsersController::class, 'restore'])
    ->name('users.restore')
    ->middleware('auth');

// Organizations

Route::get('organizations', [OrganizationsController::class, 'index'])
    ->name('organizations')
    ->middleware('auth');

Route::get('organizations/create', [OrganizationsController::class, 'create'])
    ->name('organizations.create')
    ->middleware('auth');

Route::post('organizations', [OrganizationsController::class, 'store'])
    ->name('organizations.store')
    ->middleware('auth');

Route::get('organizations/{organization}/edit', [OrganizationsController::class, 'edit'])
    ->name('organizations.edit')
    ->middleware('auth');

Route::put('organizations/{organization}', [OrganizationsController::class, 'update'])
    ->name('organizations.update')
    ->middleware('auth');

Route::delete('organizations/{organization}', [OrganizationsController::class, 'destroy'])
    ->name('organizations.destroy')
    ->middleware('auth');

Route::put('organizations/{organization}/restore', [OrganizationsController::class, 'restore'])
    ->name('organizations.restore')
    ->middleware('auth');

// Contacts

Route::get('contacts', [ContactsController::class, 'index'])
    ->name('contacts')
    ->middleware('auth');

Route::get('contacts/create', [ContactsController::class, 'create'])
    ->name('contacts.create')
    ->middleware('auth');

Route::post('contacts', [ContactsController::class, 'store'])
    ->name('contacts.store')
    ->middleware('auth');

Route::get('contacts/{contact}/edit', [ContactsController::class, 'edit'])
    ->name('contacts.edit')
    ->middleware('auth');

Route::put('contacts/{contact}', [ContactsController::class, 'update'])
    ->name('contacts.update')
    ->middleware('auth');

Route::delete('contacts/{contact}', [ContactsController::class, 'destroy'])
    ->name('contacts.destroy')
    ->middleware('auth');

Route::put('contacts/{contact}/restore', [ContactsController::class, 'restore'])
    ->name('contacts.restore')
    ->middleware('auth');

// Reports

Route::get('reports', [ReportsController::class, 'index'])
    ->name('reports')
    ->middleware('auth');



