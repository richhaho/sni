<?php

use App\Jobs\WeeklyOutstanding;

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

Route::group(['prefix' => 'researcher', 'middleware' => ['auth', 'role:researcher'], 'namespace' => 'Researcher'], function () {
    Route::get('fire', function () {
        // this fires the event
        dispatch(new WeeklyOutstanding());

        return 'Weekly email';
    });

    Route::get('/xpdf/certified', function () {
        $data = [];
        //return view('admin.mailing.files.certified',$data);
        $xpdf = PDF::loadView('researcher.mailing.files.certified', $data);

        return  $xpdf->download();
    });

    Route::get('/xpdf/{view}', function ($view) {
        // this fires the event
        if ($view == 'true') {
            $view = true;
        } else {
            $view = false;
        }
        $job = \App\Job::find(2);
        $landowner = $job->parties->where('type', 'landowner')->first();
        $customer = $job->parties->where('type', 'customer')->first();
        $client = $job->parties->where('type', 'client')->first();
        $leaseholders = $job->parties->where('type', 'leaseholder');
        switch (count($leaseholders)) {
            case 1:
                $cols = 12;
                break;
            case 2:
                $cols = 6;
                break;
            default:
                $cols = 4;
        }
        $data = [
            'landowner' => $landowner,
            'job' => $job,
            'customer' => $customer,
            'view' => $view,
            'leaseholders' => $leaseholders,
            'cols' => $cols,
            'client' => $client,
        ];
        if ($view) {
            return view('researcher.pdf.nto', $data);
        } else {
            $pdf = PDF::loadView('researcher.pdf.nto', $data);

            return $pdf->download('nto.pdf');
        }
    });

    Route::resource('hotcontacts', 'HotContactsController');
    Route::post('notification/{id}/delete', 'NotesController@removenotification');
    Route::resource('{type}/{id}/notes', 'NotesController');
    Route::group(['prefix' => 'clients'], function () {
        Route::group(['prefix' => '{client_id}'], function ($client_id) {
            Route::resource('templates', 'ClientTemplatesController', ['as' => 'client']);
            Route::resource('clientusers', 'ManageClientUsersController');
            Route::get('/enable', 'ClientsController@enable')->name('clients.enable');
            Route::get('/disable', 'ClientsController@disable')->name('clients.disable');
            Route::post('/interestrate', 'ClientsController@interestrate');
            Route::post('/defaultmaterials', 'ClientsController@defaultmaterials');
            Route::get('/workorders', 'ClientsController@workorders');
            Route::resource('contacts', 'ContactsController');
        });
    });
    Route::group(['prefix' => 'contacts'], function () {
        Route::group(['prefix' => '{contact_id}'], function ($contact_id) {
            Route::resource('associates', 'AssociatesController');
            Route::resource('hotassociates', 'HotAssociatesController');
            Route::group(['prefix' => '/hotassociate/{associate_id}'], function ($associate_id) {
                Route::get('/enable', 'HotAssociatesController@enable')->name('hotassociates.enable');
                Route::get('/disable', 'HotAssociatesController@disable')->name('hotassociates.disable');
            });

            Route::group(['prefix' => '/associate/{associate_id}'], function ($associate_id) {
                Route::get('/enable', 'AssociatesController@enable')->name('associates.enable');
                Route::get('/disable', 'AssociatesController@disable')->name('associates.disable');
            });
        });
    });

    Route::match(['get', 'post'], 'clients/setfilter', 'ClientsController@setfilter')->name('clients.setfilter');
    Route::get('clients/resetfilter', 'ClientsController@resetfilter')->name('clients.resetfilter');
    Route::resource('clients', 'ClientsController');

    Route::match(['get', 'post'], 'invoices/setfilter', 'InvoicesController@setfilter')->name('invoices.setfilter');
    Route::get('invoices/resetfilter', 'InvoicesController@resetfilter')->name('invoices.resetfilter');
    Route::post('invoices/payment', 'InvoicesController@payment')->name('invoices.payment');
    Route::post('invoices/payment/bycheck', 'InvoicesController@paymentbycheck')->name('invoices.payment.bycheck');
    Route::post('invoices/submit/check', 'InvoicesController@submitcheck')->name('researcher.invoices.submitcheck');

    Route::resource('invoices', 'InvoicesController');
    Route::post('invoices/submitpayment', 'InvoicesController@submitpayment')->name('researcher.invoices.submitpayment');

    Route::match(['get', 'post'], 'mailing/setfilter', 'MailingController@setfilter')->name('mailing.setfilter');
    Route::get('mailing/resetfilter', 'MailingController@resetfilter')->name('mailing.resetfilter');
    Route::get('mailing/{id}/preview', 'MailingController@view')->name('mailing.view');
    Route::get('mailing/{id}/print', 'MailingController@mailingprint')->name('mailing.print');
    Route::post('mailing/{id}/process', 'MailingController@process')->name('mailing.process');
    Route::resource('mailing', 'MailingController');

    Route::resource('attachmenttypes', 'AttachmentTypesController');
    Route::resource('workordertypes', 'WorkOrderTypesController');
    Route::resource('roles', 'RolesController');
    Route::resource('pricelist', 'PriceListController');
    Route::resource('templates', 'TemplatesController');
    Route::resource('templates/lines', 'TemplateLinesController');
    Route::resource('invoices/lines', 'InvoiceLinesController');
    Route::resource('users', 'ManageUsersController');

    Route::get('/attachment/{id}/print', 'ResearcherController@printAttachment')->name('attachment.print');
    Route::get('/attachment/{id}/view', 'ResearcherController@viewAttachment')->name('attachment.view');

    Route::get('jobparties/additionalform/{party_type}', 'JobPartiesController@additionalForm')->name('additionalform.parties');
    Route::get('jobparties/newcontactform/{client_id}', 'JobPartiesController@newContactForm')->name('newcontactform.parties');
    Route::group(['prefix' => 'jobs'], function () {
        Route::delete('/attachment/{id}/destroy', 'JobsController@destroy_attachment')->name('jobs.attachment.destroy');
        Route::group(['prefix' => '{job_id}'], function ($job_id) {
            Route::resource('jobpayments', 'JobPaymentsController');
            Route::resource('jobchanges', 'JobChangesController');
            Route::post('/close', 'JobsController@closeJob')->name('jobs.close');
            Route::get('/number', 'JobsController@getNumber');
            Route::get('/type/{type}', 'WorkOrdersController@checkType');
            Route::get('/contractamount', 'JobsController@getContractAmount');
            Route::get('/startedat', 'JobsController@getStartedAt');
            Route::get('/lastday', 'JobsController@getLastDay');
            Route::get('/jobparty/bond/{id}/download', 'JobPartiesController@downloadbond')->name('parties.downloadbond');
            Route::get('/contacts', 'JobsController@listcontacts');
            Route::get('/copy', 'JobsController@listjobs');
            Route::get('/copyform', 'JobsController@jobform');
            Route::post('/copyselected', 'JobsController@docopy')->name('jobs.docopy');
            Route::post('/attachment', 'JobsController@uploadattachment')->name('jobs.addattachment');
            Route::get('/attachment/{id}/show', 'JobsController@showattachment')->name('jobs.showattachment');
            Route::get('/attachment/{id}/preview', 'JobsController@showthumbnail')->name('jobs.showthumbnail');
            Route::post('/parties/{id}/copy', 'JobPartiesController@copyParty')->name('parties.copy');
            Route::resource('parties', 'JobPartiesController');
        });
    });
    Route::match(['get', 'post'], 'jobs/setfilter', 'JobsController@setfilter')->name('jobs.setfilter');
    Route::get('jobs/resetfilter', 'JobsController@resetfilter')->name('jobs.resetfilter');
    Route::resource('jobs', 'JobsController');

    Route::group(['prefix' => 'workorders'], function () {
        Route::get('/kickout', 'PdfPagesController@kickout');
        Route::delete('/attachment/{id}/destroy', 'WorkOrdersController@destroy_attachment')->name('workorders.attachment.destroy');
        Route::group(['prefix' => '{work_id}'], function ($work_id) {
            Route::get('/document', 'PdfPagesController@document')->name('workorders.document');
            Route::get('/document/reset', 'PdfPagesController@askreset')->name('workorders.reset.ask');
            Route::post('/document/reset', 'PdfPagesController@doreset')->name('workorders.reset.do');
            Route::post('/document/cancel', 'PdfPagesController@docancel')->name('workorders.cancel');

            Route::get('/delete-document', 'PdfPagesController@deletedocument')->name('workorders.deletedocument');
            Route::post('/attachment', 'WorkOrdersController@uploadattachment')->name('workorders.addattachment');
            Route::get('/attachment/{id}/show', 'WorkOrdersController@showattachment')->name('workorders.showattachment');
            Route::get('/attachment/{id}/preview', 'WorkOrdersController@showthumbnail')->name('workorders.showthumbnail');
            Route::get('/attachment/{id}/pdfpreview', 'WorkOrdersController@view')->name('workorders.view');
            Route::get('invoices/new', 'WorkOrdersController@createinvoice')->name('workorders.createinvoice');
        });
    });
    Route::match(['get', 'post'], 'workorders/setfilter', 'WorkOrdersController@setfilter')->name('workorders.setfilter');
    Route::get('workorders/resetfilter', 'WorkOrdersController@resetfilter')->name('workorders.resetfilter');

    Route::resource('workorders', 'WorkOrdersController');

    Route::resource('company', 'CompanyController');
    Route::get('typedefinitions', 'MailingDefinitionController@index')->name('mailingtype.index');
    Route::post('typedefinitions', 'MailingDefinitionController@update')->name('mailingtype.update');
    Route::resource('ftp', 'FtpController');
    Route::resource('serversftp', 'FtpServerController');
    Route::group(['prefix' => 'pdfpage'], function () {
        Route::post('/complete', 'PdfPagesController@complete')->name('pdfpage.complete');
        Route::group(['prefix' => '{pdf_id}'], function ($pdf_id) {
            Route::post('/update', 'PdfPagesController@update')->name('pdfpage.update');
            Route::post('/preview', 'PdfPagesController@preview')->name('pdfpage.preview');
            Route::match(['get', 'post'], '/generate', 'PdfPagesController@generate')->name('pdfpage.generate');
            Route::post('/save', 'PdfPagesController@save')->name('pdfpage.save');
        });
    });

    Route::get('items', 'PriceListController@itemlist')->name('list.items');

    Route::get('/', 'ResearcherController@index')->name('researcher');

    Route::group(['prefix' => 'search'], function () {
        Route::get('loading', 'SearchController@loading')->name('search.loading');
        Route::post('clients', 'SearchController@clients')->name('search.clients');
        Route::post('contacts', 'SearchController@contacts')->name('search.contacts');
        Route::post('associates', 'SearchController@associates')->name('search.associates');
        Route::post('jobs', 'SearchController@jobs')->name('search.jobs');
        Route::post('notes', 'SearchController@notes')->name('search.notes');
        Route::post('attachments', 'SearchController@attachments')->name('search.attachments');
        Route::post('parties', 'SearchController@parties')->name('search.parties');
    });

    Route::group(['prefix' => 'mailinghistory'], function () {
        Route::get('/', 'MailingHistoryController@index')->name('mailinghistory.index');
        Route::get('/resetfilter', 'MailingHistoryController@resetfilter')->name('mailinghistory.resetfilter');
        Route::post('/setfilter', 'MailingHistoryController@setfilter')->name('mailinghistory.setfilter');
        Route::post('/resend/{id}', 'MailingHistoryController@resend')->name('mailinghistory.resend');
        Route::post('/savepdf/{id}', 'MailingHistoryController@savepdf')->name('mailinghistory.savepdf');
    });

    Route::group(['prefix' => 'resenthistory'], function () {
        Route::get('/', 'MailingHistoryController@sent')->name('mailinghistory.index2');
        Route::get('/resetfilter', 'MailingHistoryController@resetfilter2')->name('mailinghistory.resetfilter2');
        Route::post('/setfilter', 'MailingHistoryController@setfilter2')->name('mailinghistory.setfilter2');
        Route::post('/resend/{id}', 'MailingHistoryController@resend2')->name('mailinghistory.resend2');
    });
});
