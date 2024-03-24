<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('send-email', function (\Illuminate\Http\Request $request) {
    \Illuminate\Support\Facades\Mail::to('vodinh2000ht@gmail.com')->send(new \App\Mail\NotifyContactEmail(
        $request->companyNameInp,
        $request->emailInp,
        $request->phoneInp,
        $request->requestInp
    ));

    return response()->json([
        'success' => true
    ]);
});
