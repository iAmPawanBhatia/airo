<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\QuotationController;
use App\Http\Middleware\ValidateQuotationJwt;
use Illuminate\Support\Facades\Route;

Route::post('/auth/token', [AuthController::class, 'token']);

Route::post('/quotation', [QuotationController::class, 'store'])
    ->middleware(ValidateQuotationJwt::class);
