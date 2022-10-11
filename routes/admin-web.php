<?php
use Illuminate\Support\Facades\Redis;

use App\Notifications\NewNote;
Use App\Jobs\WeeklyOutstanding;

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

Route::group(['prefix' => 'admin','middleware' => ['auth','role:admin|researcher'], 'namespace' => 'Admin'], function () {
    
    Route::get('fire', function () {
    // this fires the event
        dispatch(new WeeklyOutstanding());
        return "Weekly email";
    });
    
    Route::get('/xpdf/certified', function () {
        $data = [];
        //return view('admin.mailing.files.certified',$data);
        $xpdf = PDF::loadView('admin.mailing.files.certified',$data);
        return  $xpdf->download();
    });
    
    
    Route::get('/xpdf/{view}', function ($view) {
    // this fires the event
        if ($view == 'true') {
            $view = true;
        }else {
            $view = false;
        }
        $job = \App\Job::find(2);
        $landowner =  $job->parties->where('type', 'landowner')->first();
        $customer = $job->parties->where('type', 'customer')->first();
        $client = $job->parties->where('type', 'client')->first();
        $leaseholders = $job->parties->where('type', 'leaseholder');
        switch(count($leaseholders)) {
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
            'view' =>  $view,
            'leaseholders' => $leaseholders,
            'cols' => $cols,
            'client' => $client
        ];
        if ($view) {
            return view('admin.pdf.nto',$data);
        } else {
            $pdf = PDF::loadView('admin.pdf.nto', $data);
            return $pdf->download('nto.pdf');
        }
        
    });
    
    
    Route::match(['get', 'post'],'hotcontacts/setfilter','HotContactsController@setfilter')->name('hotcontacts.setfilter');
    Route::get('hotcontacts/resetfilter','HotContactsController@resetfilter')->name('hotcontacts.resetfilter');
    Route::resource('hotcontacts', 'HotContactsController');
    Route::post('notification/{id}/delete','NotesController@removenotification');
    Route::get('notification/{id}/delete','NotesController@removenotification');
    Route::resource('{type}/{id}/notes', 'NotesController');
    Route::group(['prefix' => 'clients'], function(){
        Route::group(['prefix' => '{client_id}'], function($client_id){
            Route::resource('templates', 'ClientTemplatesController',['as'=>'client']);
            Route::resource('clientusers', 'ManageClientUsersController');
            Route::get('/enable', 'ClientsController@enable')->name('clients.enable');
            Route::get('/disable', 'ClientsController@disable')->name('clients.disable');
            Route::post('/interestrate', 'ClientsController@interestrate');
            Route::post('/defaultmaterials', 'ClientsController@defaultmaterials');
            Route::get('/workorders', 'ClientsController@workorders');
            Route::resource('contacts', 'ContactsController');
            Route::get('/cancel-subscription', 'ClientsController@cancelSubscription')->name('clients.subscription.cancel');
            Route::get('/joblist', 'JobsController@joblist');
         });
    });
    Route::get('/customerlist', 'JobsController@customerlist');

    Route::group(['prefix' => 'contacts'], function(){
        Route::group(['prefix' => '{contact_id}'], function($contact_id){
            Route::resource('associates', 'AssociatesController');
            Route::resource('hotassociates', 'HotAssociatesController');
            Route::group(['prefix' => '/hotassociate/{associate_id}'], function($associate_id){
                Route::get('/enable', 'HotAssociatesController@enable')->name('hotassociates.enable');
                Route::get('/disable', 'HotAssociatesController@disable')->name('hotassociates.disable');
            });
            
            Route::group(['prefix' => '/associate/{associate_id}'], function($associate_id){
                Route::get('/enable', 'AssociatesController@enable')->name('associates.enable');
                Route::get('/disable', 'AssociatesController@disable')->name('associates.disable');
            });
         });
    });
    
    
    
    Route::match(['get', 'post'],'clients/setfilter','ClientsController@setfilter')->name('clients.setfilter');
    Route::get('clients/resetfilter','ClientsController@resetfilter')->name('clients.resetfilter');
    Route::resource('clients', 'ClientsController');
    
    
    Route::match(['get', 'post'],'invoices/setfilter','InvoicesController@setfilter')->name('invoices.setfilter');
    Route::get('invoices/resetfilter','InvoicesController@resetfilter')->name('invoices.resetfilter');
    Route::post('invoices/payment','InvoicesController@payment')->name('invoices.payment');
     Route::post('invoices/payment/bycheck','InvoicesController@paymentbycheck')->name('invoices.payment.bycheck');
   Route::post('invoices/submit/check','InvoicesController@submitcheck')->name('admin.invoices.submitcheck');

    Route::resource('invoices', 'InvoicesController');
    Route::post('invoices/submitpayment','InvoicesController@submitpayment')->name('admin.invoices.submitpayment');

    Route::post('invoicesbatches/submitpayment','InvoicesbatchesController@submitpayment')->name('invoicesbatches.submitpayment');
    Route::get('invoicesbatches/submitcheck','InvoicesbatchesController@submitcheck')->name('invoicesbatches.submitcheck');
    Route::get('invoicesbatches/payment/{batch_id}','InvoicesbatchesController@payment')->name('invoicesbatches.payment');

    Route::post('invoicesbatches/setfilter','InvoicesbatchesController@setfilter')->name('invoicesbatches.setfilter');
    Route::get('invoicesbatches/resetfilter','InvoicesbatchesController@resetfilter')->name('invoicesbatches.resetfilter');
    Route::get('invoicesbatches/delete/{batch_id}','InvoicesbatchesController@delete')->name('invoicesbatches.delete');
    Route::get('invoicesbatches/printview/{batch_id}','InvoicesbatchesController@printview')->name('invoicesbatches.printview');
    Route::resource('invoicesbatches', 'InvoicesbatchesController');

      
    
    Route::match(['get', 'post'],'mailing/setfilter','MailingController@setfilter')->name('mailing.setfilter');
    Route::get('mailing/resetfilter','MailingController@resetfilter')->name('mailing.resetfilter');
    Route::get('mailing/{id}/completeworkorder','MailingController@completeworkorder')->name('mailing.completeworkorder');
    Route::get('mailing/{id}/preview','MailingController@view')->name('mailing.view');
    Route::get('mailing/{id}/print','MailingController@mailingprint')->name('mailing.print');
    Route::post('mailing/{id}/process','MailingController@process')->name('mailing.process');
    Route::post('mailing/{id}/uploadmanifest','MailingController@uploadmanifest')->name('mailing.uploadmanifest');
    Route::get('mailing/{id}/downloadmanifest','MailingController@downloadmanifest')->name('mailing.downloadmanifest');
    Route::get('mailing/{id}/deletemanifest','MailingController@deletemanifest')->name('mailing.deletemanifest');
    Route::post('mailing/add-tracking-number','MailingController@addTrackingNumber')->name('mailing.addtrackingnumber');
    
    Route::resource('mailing', 'MailingController');
   
    
    Route::resource('attachmenttypes', 'AttachmentTypesController');
    //Route::resource('returnadresstpyes', 'ReturnAdressTpyesController');
    Route::resource('workordertypes', 'WorkOrderTypesController');
    Route::resource('roles', 'RolesController');
    Route::get('pricelist/donwload', 'PriceListController@download')->name('pricelist.download');
    Route::post('pricelist/upload', 'PriceListController@upload')->name('pricelist.upload');
    Route::resource('pricelist', 'PriceListController');
    Route::get('templates/donwload', 'TemplatesController@download')->name('templates.download');
    Route::post('templates/upload', 'TemplatesController@upload')->name('templates.upload');
    Route::resource('templates', 'TemplatesController');
    Route::resource('templates/lines', 'TemplateLinesController');
    Route::resource('invoices/lines', 'InvoiceLinesController');
    Route::resource('users', 'ManageUsersController');

    Route::get('/attachment/{id}/print', 'AdminController@printAttachment')->name('attachment.print');
    Route::get('/attachment/{id}/view', 'AdminController@viewAttachment')->name('attachment.view');

    Route::get('jobparties/additionalform/{party_type}','JobPartiesController@additionalForm')->name('additionalform.parties');
    Route::get('jobparties/newcontactform/{client_id}','JobPartiesController@newContactForm')->name('newcontactform.parties');
    Route::group(['prefix' => 'jobs'], function(){
        
        Route::delete('/attachment/{id}/destroy', 'JobsController@destroy_attachment')->name('jobs.attachment.destroy');
        Route::group(['prefix' => '{job_id}'], function($job_id){
            Route::get('/summary', 'JobsController@summary')->name('jobs.summary');
            Route::resource('jobreminders', 'JobRemindersController');
            Route::get('/jobnocs/{id}/set_current', 'JobNocsController@setCurrent')->name('jobnocs.setcurrent');
            Route::get('/jobnocs/{id}/download', 'JobNocsController@downloadNOC')->name('jobnocs.download');
            Route::resource('jobnocs', 'JobNocsController');
            Route::get('/jobpayments/showattachment', 'JobPaymentsController@showattachment')->name('jobpayments.showattachment');
            Route::resource('jobpayments', 'JobPaymentsController');
            Route::get('/jobchanges/showattachment', 'JobChangesController@showattachment')->name('jobchanges.showattachment');
            Route::resource('jobchanges', 'JobChangesController');
            Route::get('/runsearch', 'JobsController@runsearch')->name('jobs.runsearch');
            Route::get('/select_property', 'JobsController@select_property')->name('jobs.select_property');
            Route::post('/select_property', 'JobsController@select_property')->name('jobs.select_property');
            Route::post('/save_property', 'JobsController@save_property')->name('jobs.save_property');
            Route::post('/close', 'JobsController@closeJob')->name('jobs.close');
            Route::get('/number', 'JobsController@getNumber');
            Route::get('/type/{type}', 'WorkOrdersController@checkType');
            Route::get('/contractamount', 'JobsController@getContractAmount');
            Route::get('/startedat', 'JobsController@getStartedAt');
            Route::get('/lastday', 'JobsController@getLastDay');
            Route::get('/users', 'JobsController@getUsers');
            Route::get('/jobparty/bond/{id}/download','JobPartiesController@downloadbond')->name('parties.downloadbond');
            Route::get('/contacts', 'JobsController@listcontacts');
            Route::get('/copy', 'JobsController@listjobs');
            Route::get('/copyform', 'JobsController@jobform');
            Route::post('/copyselected', 'JobsController@docopy')->name('jobs.docopy');
            Route::post('/attachment', 'JobsController@uploadattachment')->name('jobs.addattachment');
            Route::get('/attachment/{id}/show', 'JobsController@showattachment')->name('jobs.showattachment');
            Route::get('/attachment/{id}/preview', 'JobsController@showthumbnail')->name('jobs.showthumbnail');
            Route::post('/parties/{id}/copy','JobPartiesController@copyParty')->name('parties.copy');
            Route::resource('parties', 'JobPartiesController');
            Route::get('/logs', 'JobsController@viewLog')->name('jobs.logs');
            Route::get('/submit/research', 'JobsController@submiteToResearch')->name('jobs.submit.research');
            Route::match(['get', 'post'],'logs/setfilter','JobsController@setfilterLog')->name('joblogs.setfilter');
            Route::get('logs/resetfilter','JobsController@resetfilterLog')->name('joblogs.resetfilter');
            Route::get('/mark_completed', 'JobsController@markCompleted')->name('jobs.mark_completed');
            Route::match(['delete', 'post'],'logs/delete','JobsController@deleteLog')->name('jobs.deletelog');
            Route::post('/share', 'JobsController@share')->name('jobs.share');
        });
    });
    Route::match(['get', 'post'],'jobs/setfilter','JobsController@setfilter')->name('jobs.setfilter');
    Route::get('jobs/resetfilter','JobsController@resetfilter')->name('jobs.resetfilter');
    Route::get('jobs/getaddress','JobsController@getaddress')->name('jobs.getaddress');
    Route::get('jobs/search_address','JobsController@search_address')->name('jobs.search_address');
    Route::get('jobs/search_jobs','JobsController@search_jobs')->name('jobs.search_jobs');
    Route::resource('jobs', 'JobsController');

    Route::match(['get', 'post'],'contract_trackers/setfilter','ContractTrackerController@setfilter')->name('contract_trackers.setfilter');
    Route::get('contract_trackers/resetfilter','ContractTrackerController@resetfilter')->name('contract_trackers.resetfilter');
    Route::get('contract_trackers/{id}/download', 'ContractTrackerController@download')->name('contract_trackers.download');
    Route::resource('contract_trackers', 'ContractTrackerController');

    Route::group(['prefix' => 'jobs_shared'], function(){
        Route::group(['prefix' => '{job_id}'], function($job_id){
            Route::get('/summary', 'JobsSharedController@summary')->name('jobs_shared.summary');
            Route::post('/link_to', 'JobsSharedController@linkTo')->name('jobs_shared.link_to');
            Route::get('/unlink', 'JobsSharedController@unlink')->name('jobs_shared.unlink');
            Route::get('/unshare', 'JobsSharedController@unshare')->name('jobs_shared.unshare');
        });
        Route::post('/request/from_notice', 'JobsSharedController@shareRequestFromNotice')->name('jobs_shared.request.fromNotice');
    });
    Route::match(['get', 'post'],'jobs_shared/setfilter','JobsSharedController@setfilter')->name('jobs_shared.setfilter');
    Route::get('jobs_shared/resetfilter','JobsSharedController@resetfilter')->name('jobs_shared.resetfilter');
    Route::resource('jobs_shared', 'JobsSharedController');
    Route::resource('jobs_monitor', 'JobsMonitorController');


    Route::group(['prefix' => 'research'], function(){
        Route::delete('/attachment/{id}/destroy', 'ResearchController@destroy_attachment')->name('research.attachment.destroy');
        Route::group(['prefix' => '{job_id}'], function($job_id){
            Route::get('/start', 'ResearchController@start')->name('research.start');
            Route::get('/runsearch', 'ResearchController@runsearch')->name('research.runsearch');
            Route::get('/select_property', 'ResearchController@select_property')->name('research.select_property');
            Route::post('/select_property', 'ResearchController@select_property')->name('research.select_property');
            Route::post('/save_property', 'ResearchController@save_property')->name('research.save_property');
            Route::group(['prefix' => 'wizard'], function(){
                Route::get('/step1', 'ResearchController@wizardStep1')->name('research.wizard.step1');
                Route::get('/step2', 'ResearchController@wizardStep2')->name('research.wizard.step2');
                Route::get('/step3', 'ResearchController@wizardStep3')->name('research.wizard.step3');
                Route::get('/step4', 'ResearchController@wizardStep4')->name('research.wizard.step4');
                Route::get('/step5', 'ResearchController@wizardStep5')->name('research.wizard.step5');
                Route::get('/step6', 'ResearchController@wizardStep6')->name('research.wizard.step6');
                Route::get('/step7', 'ResearchController@wizardStep7')->name('research.wizard.step7');
                Route::get('/step8', 'ResearchController@wizardStep8')->name('research.wizard.step8');
                Route::get('/step9', 'ResearchController@wizardStep9')->name('research.wizard.step9');

                Route::put('/step1/update', 'ResearchController@wizardStep1Update')->name('research.wizard.step1.update');
                Route::put('/step2/update', 'ResearchController@wizardStep2Update')->name('research.wizard.step2.update');
                Route::put('/step3/update', 'ResearchController@wizardStep3Update')->name('research.wizard.step3.update');
                Route::put('/step4/update', 'ResearchController@wizardStep4Update')->name('research.wizard.step4.update');
                Route::put('/step5/update', 'ResearchController@wizardStep5Update')->name('research.wizard.step5.update');
                Route::put('/step6/update', 'ResearchController@wizardStep6Update')->name('research.wizard.step6.update');
                Route::put('/step7/update', 'ResearchController@wizardStep7Update')->name('research.wizard.step7.update');
                Route::put('/step8/update', 'ResearchController@wizardStep8Update')->name('research.wizard.step8.update');
                Route::put('/step9/update', 'ResearchController@wizardStep9Update')->name('research.wizard.step9.update');
                Route::post('/step9/update/number', 'ResearchController@wizardStep9UpdateNumber')->name('research.wizard.step9.number');
                Route::put('/finish', 'ResearchController@finishWizard')->name('research.wizard.finish');

                Route::post('/parties/store', 'ResearchController@storeParty')->name('research.wizard.parties.store');
                Route::delete('/parties/{id}/destroy', 'ResearchController@destroyParty')->name('research.wizard.parties.destroy');
                Route::put('/parties/{id}/update', 'ResearchController@updateParty')->name('research.wizard.parties.update');
                Route::get('/parties/{id}/edit', 'ResearchController@editParty')->name('research.wizard.parties.edit');
                Route::post('/parties/{id}/copy', 'ResearchController@copyParty')->name('research.wizard.parties.copy');
                Route::get('/contacts', 'ResearchController@listcontacts');
            });
            Route::post('/attachment', 'ResearchController@uploadattachment')->name('research.addattachment');
            Route::get('/attachment/{id}/show', 'ResearchController@showattachment')->name('research.showattachment');
            Route::get('/attachment/{id}/preview', 'ResearchController@showthumbnail')->name('research.showthumbnail');
            
            Route::post('/mark_completed', 'ResearchController@markCompleted')->name('research.mark_completed');
            Route::post('/close', 'ResearchController@closeJob')->name('research.close');
            Route::get('/number', 'ResearchController@getNumber');
            Route::get('/type/{type}', 'ResearchController@checkType');
            Route::get('/contractamount', 'ResearchController@getContractAmount');
            Route::get('/startedat', 'ResearchController@getStartedAt');
            Route::get('/lastday', 'ResearchController@getLastDay');
            Route::get('/users', 'ResearchController@getUsers');
            Route::get('/jobparty/bond/{id}/download','JobPartiesController@downloadbond')->name('parties.downloadbond');
            Route::get('/copy', 'ResearchController@listjobs');
            Route::get('/copyform', 'ResearchController@jobform');
            Route::post('/copyselected', 'ResearchController@docopy')->name('research.docopy');
            Route::post('/parties/{id}/copy','JobPartiesController@copyParty')->name('parties.copy');
         });
    });
    Route::match(['get', 'post'],'research/setfilter','ResearchController@setfilter')->name('research.setfilter');
    Route::get('research/resetfilter','ResearchController@resetfilter')->name('research.resetfilter');
    Route::get('research/getaddress','ResearchController@getaddress')->name('research.getaddress');
    Route::get('research/search_address','ResearchController@search_address')->name('research.search_address');
    Route::get('research/search_jobs','ResearchController@search_jobs')->name('research.search_jobs');
    Route::resource('research', 'ResearchController');
    
    
    Route::group(['prefix' => 'workorders'], function(){
        Route::get('/kickout', 'PdfPagesController@kickout');
        Route::delete('/attachment/{id}/destroy', 'WorkOrdersController@destroy_attachment')->name('workorders.attachment.destroy');
        Route::group(['prefix' => '{work_id}'], function($work_id){
            Route::get('/document', 'PdfPagesController@document')->name('workorders.document');
            Route::get('/document/reset', 'PdfPagesController@askreset')->name('workorders.reset.ask');
            Route::post('/document/reset', 'PdfPagesController@doreset')->name('workorders.reset.do');
            Route::post('/document/cancel', 'PdfPagesController@docancel')->name('workorders.cancel');
            Route::get('/delete-regenerate', 'PdfPagesController@deleteRegenerate')->name('workorders.deleteregenerate');
            
            Route::get('/delete-document', 'PdfPagesController@deletedocument')->name('workorders.deletedocument');
            Route::post('/attachment', 'WorkOrdersController@uploadattachment')->name('workorders.addattachment');
            Route::get('/attachment/{id}/show', 'WorkOrdersController@showattachment')->name('workorders.showattachment');
            Route::get('/attachment/{id}/preview', 'WorkOrdersController@showthumbnail')->name('workorders.showthumbnail');
            Route::get('/attachment/{id}/pdfpreview','WorkOrdersController@view')->name('workorders.view');
            Route::get('invoices/new','WorkOrdersController@createinvoice')->name('workorders.createinvoice');
            Route::get('invoices/newinvoice','WorkOrdersController@newinvoice')->name('workorders.newinvoice');

            Route::get('/todo/{id}/edit', 'WorkOrdersController@todoEdit')->name('workorders.todo.edit');
            Route::get('/todo/{id}/complete', 'WorkOrdersController@todoComplete')->name('workorders.todo.complete');

            Route::get('/sendlink', 'WorkOrdersController@sendlink')->name('workorders.sendlink');
        });

        Route::post('/todo/{id}/upload', 'WorkOrdersController@todoUpload')->name('workorders.todo.upload');
        Route::delete('/todo/{id}/document/destroy', 'WorkOrdersController@destroyTodoDocument')->name('workorders.todo.document.destroy');
        Route::get('/todo/{id}/document/download', 'WorkOrdersController@downloadTodoDocument')->name('workorders.todo.document.download');
        Route::get('/todo/{id}/document/showthumbnail', 'WorkOrdersController@showTodoThumbnail')->name('workorders.todo.document.showTodoThumbnail');

        Route::post('/todo/{id}/instruction', 'WorkOrdersController@todoInstruction')->name('workorders.todo.instruction');
        Route::delete('/todo/{id}/instruction/destroy', 'WorkOrdersController@destroyTodoInstruction')->name('workorders.todo.instruction.destroy');
    });
    Route::match(['get', 'post'],'workorders/setfilter','WorkOrdersController@setfilter')->name('workorders.setfilter');
    Route::get('workorders/resetfilter','WorkOrdersController@resetfilter')->name('workorders.resetfilter');
    Route::match(['get', 'post'],'workorders/setfilter2','WorkOrdersController@setfilter2')->name('workorders.setfilter2');
    Route::get('workorders/resetfilter2','WorkOrdersController@resetfilter2')->name('workorders.resetfilter2');
    Route::get('workorders/getfields','WorkOrdersController@getfields')->name('workorders.getfields');

    Route::get('workorders/self','WorkOrdersController@index2')->name('workorders.index2');
    Route::resource('workorders', 'WorkOrdersController');

    Route::resource('workorderfields', 'WorkOrderFieldsController');
    Route::get('workorderfields/resetfilter','WorkOrderFieldsController@resetfilter')->name('workorderfields.resetfilter');
    Route::match(['get', 'post'],'workorderfields/setfilter','WorkOrderFieldsController@setfilter')->name('workorderfields.setfilter');
    Route::post('/workorderfields/{id}/update', 'WorkOrderFieldsController@update')->name('workorderfields.update');

    Route::resource('reminders', 'RemindersController');
    Route::post('/reminders/{id}/update', 'RemindersController@update')->name('reminders.update');

    Route::resource('fromemails', 'FromEmailsController');
    Route::post('/fromemails/{id}/update', 'FromEmailsController@update')->name('fromemails.update');

    Route::resource('adminemails', 'AdminEmailsController');
    Route::post('/adminmails/{id}/update', 'AdminEmailsController@update')->name('adminemails.update');

    Route::get('/sites/resetfilter', 'SitesController@resetfilter')->name('sites.resetfilter');
    Route::resource('sites', 'SitesController');
    Route::resource('folders', 'FoldersController');
    Route::post('/reports/{id}/run', 'ReportsController@run')->name('reports.run');
    Route::resource('reports', 'ReportsController');
    Route::post('/sites/{id}/update', 'SitesController@update')->name('sites.update');
    Route::get('/sites/{id}/destroy', 'SitesController@destroy')->name('sites.destroy');
    Route::post('/sites/setfilter', 'SitesController@setfilter')->name('sites.setfilter');

    Route::resource('company', 'CompanyController');
    Route::get('typedefinitions', 'MailingDefinitionController@index')->name('mailingtype.index');
    Route::post('typedefinitions', 'MailingDefinitionController@update')->name('mailingtype.update');
    Route::get('typedefinitions/donwload', 'MailingDefinitionController@download')->name('mailingtype.download');
    Route::post('typedefinitions/upload', 'MailingDefinitionController@upload')->name('mailingtype.upload');

    Route::resource('ftp', 'FtpController');
    Route::resource('serversftp', 'FtpServerController');
    Route::group(['prefix' => 'pdfpage'], function(){
        Route::post('/complete', 'PdfPagesController@complete')->name('pdfpage.complete');
        Route::group(['prefix' => '{pdf_id}'], function($pdf_id){
            Route::post('/update', 'PdfPagesController@update')->name('pdfpage.update');
            Route::post('/preview', 'PdfPagesController@preview')->name('pdfpage.preview');
            Route::post('/AttachPDF', 'PdfPagesController@AttachPDF')->name('pdfpage.AttachPDF');
            Route::match(['get', 'post'],'/generate', 'PdfPagesController@generate')->name('pdfpage.generate');
            Route::post('/save', 'PdfPagesController@save')->name('pdfpage.save');
        });
    });
    

    Route::get('items', 'PriceListController@itemlist')->name('list.items');
   
    Route::get('/', 'AdminController@index')->name('admin');
    Route::get('/subscriptionrate/edit', 'AdminController@editSubscriptionRate')->name('subscriptionrate.edit');
    Route::post('/subscriptionrate/update', 'AdminController@updateSubscriptionRate')->name('subscriptionrate.update');
    Route::get('subscriptionrate/donwload', 'AdminController@download')->name('subscriptionrate.download');
    Route::post('subscriptionrate/upload', 'AdminController@upload')->name('subscriptionrate.upload');
    
    Route::group(['prefix' => 'search'], function(){
        Route::get('loading', 'SearchController@loading')->name('search.loading');
        Route::post('clients', 'SearchController@clients')->name('search.clients');
        Route::post('contacts', 'SearchController@contacts')->name('search.contacts');
        Route::post('associates', 'SearchController@associates')->name('search.associates');
        Route::post('jobs', 'SearchController@jobs')->name('search.jobs');
        Route::post('notes', 'SearchController@notes')->name('search.notes');
        Route::post('attachments', 'SearchController@attachments')->name('search.attachments');
        Route::post('parties', 'SearchController@parties')->name('search.parties');
    });
    
    
     Route::group(['prefix' => 'mailinghistory'], function(){
         Route::get('/', 'MailingHistoryController@index')->name('mailinghistory.index');
         Route::get('/resetfilter', 'MailingHistoryController@resetfilter')->name('mailinghistory.resetfilter');
         Route::post('/setfilter', 'MailingHistoryController@setfilter')->name('mailinghistory.setfilter');
         Route::post('/resend/{id}', 'MailingHistoryController@resend')->name('mailinghistory.resend');
         Route::post('/savepdf/{id}', 'MailingHistoryController@savepdf')->name('mailinghistory.savepdf');
         
     });
     
     
     Route::group(['prefix' => 'resenthistory'], function(){ 
         Route::get('/', 'MailingHistoryController@sent')->name('mailinghistory.index2');
          Route::get('/resetfilter', 'MailingHistoryController@resetfilter2')->name('mailinghistory.resetfilter2');
         Route::post('/setfilter', 'MailingHistoryController@setfilter2')->name('mailinghistory.setfilter2');
          Route::post('/resend/{id}', 'MailingHistoryController@resend2')->name('mailinghistory.resend2');
     });
});

