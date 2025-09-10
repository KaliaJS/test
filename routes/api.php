<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthsController;
use App\Http\Controllers\HighlightsController;
use App\Http\Controllers\IngredientsController;
use App\Http\Controllers\LiveController;
use App\Http\Controllers\OrderPaymentController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\PasskeysController;
use App\Http\Controllers\PaymentsController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\RewardsController;
use App\Http\Controllers\SchedulePlacesController;
use App\Http\Controllers\SchedulesController;
use App\Http\Controllers\SensorsController;
use App\Http\Controllers\TerminalsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WebhooksController;

Route::post('webhooks/endpoint', [WebhooksController::class, 'endpoint']);

Route::middleware(['sanctum.optional'])->group(function()
{
  Route::get('auths/check-if-user-exist', [AuthsController::class, 'checkIfUserExist']);
  Route::post('auths/register', [AuthsController::class, 'register']);
  Route::post('auths/login', [AuthsController::class, 'login']);
  Route::post('auths/reset-request', [AuthsController::class, 'resetRequest']);
  Route::post('auths/reset-password', [AuthsController::class, 'resetPassword']);

  Route::get('passkeys/register/options', [PasskeysController::class, 'registerOptions']);
  Route::post('passkeys/store', [PasskeysController::class, 'store']);
  Route::get('passkeys/authenticate/options', [PasskeysController::class, 'authenticateOptions']);
  Route::post('passkeys/authenticate', [PasskeysController::class, 'authenticate']);
  Route::delete('passkeys/${passkey}', [PasskeysController::class, 'destroy']);

  Route::get('products', [ProductsController::class, 'index']);
  Route::post('products/add-ingredients', [ProductsController::class, 'addIngredients']);
  Route::post('products/remove-ingredient', [ProductsController::class, 'removeIngredient']);

  Route::get('highlights', [HighlightsController::class, 'index']);

  Route::get('ingredients', [IngredientsController::class, 'index']);

  Route::get('schedules', [SchedulesController::class, 'index']);
  Route::post('schedules', [SchedulesController::class, 'create']);

  Route::get('schedule-places', [SchedulePlacesController::class, 'index']);

  Route::get('orders', [OrdersController::class, 'index']);
  Route::get('order', [OrdersController::class, 'show']);
  Route::get('orders/total-manufacturing-time', [OrdersController::class, 'totalManufacturingTime']);
  Route::post('orders', [OrdersController::class, 'create']);

  Route::post('payments', [PaymentsController::class, 'create']);
  Route::post('payments/process', [PaymentsController::class, 'process']);
  Route::post('payments/retrieve-secret', [PaymentsController::class, 'retrieveSecret']);
  Route::patch('payments/{id}', [PaymentsController::class, 'update']);
});

/**
 * Users
 */
Route::middleware(['auth:sanctum'])->group(function()
{
  Route::post('auths/update-password', [AuthsController::class, 'updatePassword']);
  Route::get('user', [UserController::class, 'fetch']);
  Route::get('rewards', [RewardsController::class, 'fetchs']);
  Route::get('rewards/history', [RewardsController::class, 'getHistory']);
  Route::post('rewards/add', [RewardsController::class, 'addPoints']);
  Route::post('rewards/deduct', [RewardsController::class, 'deductPoints']);
});

/**
 * Terminals
 */
Route::middleware(['auth:sanctum', 'can:admin-or-terminal'])->group(function()
{
  Route::get('terminals', [TerminalsController::class, 'fetchs']);
  Route::get('terminal/fetch-orders', [TerminalsController::class, 'getOrders']);
  Route::get('terminal/retrieve-order-by-slug', [TerminalsController::class, 'retrieveOrderBySlug']);
  Route::post('terminal/process-payment', [OrderPaymentController::class, 'processPaymentFromTerminal']);

  Route::get('admin/get-users', [AdminController::class, 'getUsers']);
  Route::get('admin/get-orders', [AdminController::class, 'getOrders']);
  Route::get('admin/get-user-orders', [AdminController::class, 'getUserOrders']);
  Route::get('admin/get-schedules', [AdminController::class, 'getSchedules']);
  Route::get('admin/get-trucks', [AdminController::class, 'getTrucks']);
});

/**
 * Admin
 */
Route::middleware(['auth:sanctum', 'can:admin'])->group(function()
{
  Route::post('admin/highlights', [HighlightsController::class, 'create']);
  Route::patch('admin/highlights', [HighlightsController::class, 'update']);
  Route::delete('admin/highlights', [HighlightsController::class, 'delete']);

  Route::get('admin/users', [AdminController::class, 'fetchUsers']);
  Route::get('admin/user-orders', [AdminController::class, 'getUserOrders']);

  Route::post('admin/orders/refund', [OrderPaymentController::class, 'refund']);
  Route::post('admin/orders/start', [LiveController::class, 'startPreparingOrder']);
  Route::post('admin/orders/starts', [LiveController::class, 'startPreparingAllOrders']);
  Route::post('admin/orders/finish', [LiveController::class, 'finishPreparingOrder']);
  Route::patch('admin/orders/set-product-done', [LiveController::class, 'setProductOfOrderDone']);

  Route::get('sensors', [SensorsController::class, 'fetchs']);
  Route::get('sensors/measurements', [SensorsController::class, 'fetchMeasurements']);

  Route::post('admin/add-truck', [AdminController::class, 'addTruck']);
  Route::delete('admin/delete-truck', [AdminController::class, 'deleteTruck']);
});

/**
 * Sensors
 */
Route::middleware('auth.sensor')->group(function()
{
  Route::post('sensors/measurement', [SensorsController::class, 'storeMeasurement']);
});
