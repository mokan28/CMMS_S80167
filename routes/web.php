<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WorkOrderController;

Route::get('/', fn() => view('dashboard'));
Route::resource('workorders', WorkOrderController::class)->only(['store','show']);
Route::post('workorders/{workorder}/start', [WorkOrderController::class,'start'])->name('workorders.start');
Route::post('workorders/{workorder}/submit', [WorkOrderController::class,'submit'])->name('workorders.submit');
Route::post('workorders/{workorder}/return', [WorkOrderController::class,'returnToProgress'])->name('workorders.return');
Route::post('workorders/{workorder}/close', [WorkOrderController::class,'close'])->name('workorders.close']);
