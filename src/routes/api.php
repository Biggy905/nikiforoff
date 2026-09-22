<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReferralsController;

/*
|--------------------------------------------------------------------------
| API
|--------------------------------------------------------------------------
|
| Текущий мастер приходит в заголовке X-Master-Id и уже разложен
| в атрибуты запроса middleware'ом ResolveCurrentMaster:
|
|     $master = $request->attributes->get('current_master');
|
| Здесь нужно написать три роута — см. README.md.
|
*/

Route::get('/ping', fn () => ['ok' => true]);

Route::post('/referrals/attach', [ReferralsController::class, 'attach']);
Route::get('/referrals/my', [ReferralsController::class, 'my']);
Route::get('/referrals/earnings', [ReferralsController::class, 'earnings']);
