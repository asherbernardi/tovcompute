<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CharityController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\MatchController;
use App\Http\Controllers\CompanyGroupingController;
use App\Http\Controllers\ManualController;
use App\Http\Controllers\CharityRecommendController;
use App\Http\Controllers\CharityGivingController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\ScoreController;
use App\Http\Controllers\ResearchController;
use Illuminate\Support\Facades\Route;

/*
Route::get('/', function () {
    return view('welcome');
});
*/

Route::redirect('/', '/companies', 301);

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

#move this to wrap all of the functions below to enable the Laravel login / authenticator portion.
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::get('/charities', [CharityController::class, 'index'])->name("charities.index");
Route::get('/charities/{id}', [CharityController::class, 'show'])->name("charities.show")
    ->missing(function (Request $request) {
        return Redirect::route('charity.index');
    });
Route::get('/charitySearch', [CharityController::class, 'search'])->name("charities.search")
->missing(function (Request $request) {
    return Redirect::route('charity.index');
    });
Route::get('/charities/{charity}/edit', [CharityController::class, 'edit'])->name('charities.edit');
Route::put('/charities/{charity}', [CharityController::class, 'update'])->name('charities.update');
Route::post('/charities/{id}/export-history', [CharityController::class, 'exportHistory'])->name('charities.export.history');
Route::post('/charities/{charity}/associate-company', [CharityController::class, 'associateCompany'])->name('charities.associate-company');
Route::delete('/charities/{charity}/remove-company', [CharityController::class, 'removeCompany'])->name('charities.remove-company');

Route::get('/companies/fetch', [CompanyController::class, 'fetchCompanies'])->name('companies.fetch');

Route::resource('companies', CompanyController::class);

// Custom routes for charity search and association
Route::post('/companies/{company}/search-charity', [CompanyController::class, 'searchCharity'])
    ->name('companies.search-charity');
Route::post('/companies/{company}/associate-charity', [CompanyController::class, 'associateCharity'])
    ->name('companies.associate-charity');


Route::get('/addCharity/{id}', [CompanyController::class, 'addCharity'])->name("company.addCharity");

Route::get('/runMatches', [MatchController::class, 'runMatches'])->name("match.run");
Route::get('/runMatches/status', [MatchController::class, 'status'])->name("match.status");
Route::post('/runMatches/start', [MatchController::class, 'startMatches'])->name("match.start");
Route::post('/runMatches/stop', [MatchController::class, 'stopMatches'])->name("match.stop");
Route::post('/runMatches/deduplicate', [MatchController::class, 'deduplicate'])->name("match.deduplicate");
Route::get('/runMatches/stream', [MatchController::class, 'streamMatches'])->name("match.stream");

Route::post('/companies/{id}/export-history', [CompanyController::class, 'exportHistory'])->name('companies.export.history');
Route::post('/companies/export-charity-history', [CompanyController::class, 'exportCharityHistory'])->name('companies.export.charity-history');


Route::resource('company-groupings', CompanyGroupingController::class);
Route::post('/company-groupings/{companyGrouping}/add-companies', [CompanyGroupingController::class, 'addCompanies'])->name('company-groupings.add-companies');
Route::delete('/company-groupings/{companyGrouping}/remove-company/{companyId}', [CompanyGroupingController::class, 'removeCompany'])->name('company-groupings.remove-company');

Route::get('/manual', [ManualController::class, 'index'])->name('manual.index');
Route::get('/setup', [ManualController::class, 'setup'])->name('manual.setup');
Route::get('/process', [ManualController::class, 'process'])->name('manual.process');
Route::post('/charities/import', [ManualController::class, 'importCharities'])->name('charities.import');
Route::post('/giving/import', [ManualController::class, 'importGiving'])->name('giving.import');
Route::post('/giving/extract-xml', [ManualController::class, 'extractXmlGiving'])->name('giving.extract-xml');

Route::get('/recommendations', [CharityRecommendController::class, 'index'])->name('recommendations.index');
Route::post('/recommendations/{recommendation}/approve', [CharityRecommendController::class, 'approve'])->name('recommendations.approve');
Route::post('/recommendations/{recommendation}/disapprove', [CharityRecommendController::class, 'disapprove'])->name('recommendations.disapprove');

Route::post('/charity-giving/sync', [CharityGivingController::class, 'syncCharityIds'])->name('charity-giving.sync');

Route::post('/backup/run', [BackupController::class, 'run'])->name('backup.run');

Route::redirect('/scores', '/research/scores', 301);

Route::prefix('research')->name('research.')->group(function () {
    Route::get('/scores',  [ResearchController::class, 'scores'])->name('scores');
    Route::get('/prompts', [ResearchController::class, 'prompts'])->name('prompts');
    Route::get('/runs',    [ResearchController::class, 'runs'])->name('runs');
});
