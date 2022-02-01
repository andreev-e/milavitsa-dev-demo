<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->namespace('Api')->group(function () {
	Route::post('/mailing_list/copy', 'MailingListController@copy');
	Route::get('/mailing_list/{id}/statistic', 'MailingListController@statistic');
	Route::resource('/mailing_list', MailingListController::class);
	Route::resource('/mailing_segment', MailingSegmentController::class);
	Route::resource('/mailing_emailtemplate', MailingTemplateController::class);
	Route::resource('/mailing_whatsapptemplate', WhatsappTemplateController::class);

});

Route::namespace('Api')->group(function () {
	Route::get('/idgtl/callback', 'IdgtlController@callback');
});
