<?php

use App\Livewire\CreateTestPost;
use App\Livewire\SurveyForm;
use Illuminate\Support\Facades\Route;
$form_segment = env('APP_FORM_SEGMENT');

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::get("$form_segment/{uri}", function ($uri) {
    $data = \App\Models\Survey::where('url',$uri)->firstOrFail();
    dd($data);
});

Route::get("$form_segment/test/{survey}", CreateTestPost::class);
// If survery have different question and get them when it mounts and use normal form to display there is no problem.
// Just add a segment with question number then thats it. We also can show progress for this.
// I will only use livewire in front page and use filament for backend.
// Route::get("$form_segment/test/{survey}?q=1", CreateTestPost::class);

Route::get("/survey/{survey}", SurveyForm::class)->name('survey.form');
