<?php

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
Route::post('/payeezy/hook/token', 'Clients\CreditCardController@webhook');

Route::group(['middleware' => ['auth','role:client|client-secondary'], 'namespace' => 'Clients'], function () {
   
    Route::get('/{user_id}/workorder_provide/{id}/contactwith_attachment', 'WorkOrdersController@provideInfo')->name('workorder_provide.provideInfo');
    Route::post('/{user_id}/workorder_provide/job/{id}/addattachments', 'WorkOrdersController@upload_addattachments')->name('workorder_provide.addattachments');

    Route::get('jobparties/additionalform/{party_type}','WizardController@additionalForm')->name('wizard.additionalform');
    Route::get('jobparties/newcontactform/{client_id}','WizardController@newContactForm')->name('wizard.newcontactform');
    Route::get('jobparties/additionalform2/{party_type}','Wizard2Controller@additionalForm')->name('wizard2.additionalform');
    Route::get('jobparties/newcontactform2/{client_id}','Wizard2Controller@newContactForm')->name('wizard2.newcontactform');
    Route::post('/api/authorize-session', 'CreditCardController@authorizeSession');
    Route::get('/api/verify-tokenize-response', 'CreditCardController@verifyTokenizeResponse');

    Route::group(['prefix' => 'client'],function(){
        Route::get('/validate/resend', 'ClientController@resendValidate')->name('client.validate.resend');
        Route::get('/reminderEmail/{id}/notAllow', 'ClientController@notAllow')->name('client.reminderEmail.notAllow');
        Route::get('/attachment/{id}/print', 'ClientController@printAttachment')->name('client.attachment.print');
        Route::get('/attachment/{id}/printnotice', 'ClientController@printNotice')->name('client.attachment.printnotice');
        Route::get('/attachment/printSelected', 'ClientController@printSelected')->name('client.attachment.printSelected');
        Route::get('/attachment/{id}/view', 'ClientController@viewAttachment')->name('client.attachment.view');
        Route::get('/{client_id}/cancel-subscription', 'ClientController@cancelSubscription')->name('client.subscription.cancel');
        
        Route::resource('clientusers', 'ManageClientUsersController',['as'=>'client']);
        
        Route::match(['get', 'post'],'contacts/setfilter','ContactsController@setfilter')->name('client.contacts.setfilter');
        Route::get('contacts/resetfilter','ContactsController@resetfilter')->name('client.contacts.resetfilter');

        Route::resource('contacts', 'ContactsController',['as'=>'client']);
        Route::post('creditcard/tokenize', 'CreditCardController@tokenize');
        Route::get('creditcard/tokenize', 'CreditCardController@tokenize');
        Route::post('creditcard/remove', 'CreditCardController@remove')->name('client.creditcard.remove');;
        Route::get('creditcard/remove_card', 'CreditCardController@remove_card')->name('client.creditcard.remove_card');;
        Route::get('creditcard/active_card', 'CreditCardController@active_card')->name('client.creditcard.active_card');;



        Route::resource('creditcard', 'CreditCardController');

        Route::resource('folders', 'FoldersController',['as'=>'client']);
        Route::post('/reports/{id}/run', 'ReportsController@run')->name('client.reports.run');
        Route::post('/reports/{id}/subscribe', 'ReportsController@subscribe')->name('client.reports.subscribe');
        Route::get('/reports/{id}/unsubscribe', 'ReportsController@unsubscribe')->name('client.reports.unsubscribe');
        Route::resource('reports', 'ReportsController',['as'=>'client']);
        
        Route::group(['prefix' => 'contacts'], function(){
            Route::group(['prefix' => '{contact_id}'], function($contact_id){
                Route::resource('associates', 'AssociatesController',['as'=>'client']);
                Route::group(['prefix' => '/associate/{associate_id}'], function($associate_id){
                    Route::get('/enable', 'AssociatesController@enable')->name('client.associates.enable');
                    Route::get('/disable', 'AssociatesController@disable')->name('client.associates.disable');
                });
             });
        });
        
        Route::match(['get', 'post'],'invoices/setfilter','InvoicesController@setfilter')->name('client.invoices.setfilter');
        Route::get('invoices/resetfilter','InvoicesController@resetfilter')->name('client.invoices.resetfilter');
        Route::post('invoices/payment','InvoicesController@payment')->name('client.invoices.payment');
        
        Route::get('notice/{wo_id}/attachament/{attach_id}/paytodownload','InvoicesController@paytodownload')->name('client.invoices.paytodownload');
        Route::post('invoices/submitpayment','InvoicesController@submitpayment')->name('client.invoices.submitpayment');
        Route::get('invoices/invoiceforbatch','InvoicesController@invoiceforbatch')->name('client.invoices.invoiceforbatch');
        Route::post('invoices/tobatch','InvoicesController@tobatch')->name('client.invoices.tobatch');
        Route::resource('invoices', 'InvoicesController',['as'=> 'client']);

        Route::post('invoicesbatches/submitpayment','InvoicesbatchesController@submitpayment')->name('client.invoicesbatches.submitpayment');
        Route::get('invoicesbatches/payment/{batch_id}','InvoicesbatchesController@payment')->name('client.invoicesbatches.payment');
        Route::post('invoicesbatches/setfilter','InvoicesbatchesController@setfilter')->name('client.invoicesbatches.setfilter');
        Route::get('invoicesbatches/resetfilter','InvoicesbatchesController@resetfilter')->name('client.invoicesbatches.resetfilter');        

        Route::get('invoicesbatches/delete/{batch_id}','InvoicesbatchesController@delete')->name('client.invoicesbatches.delete');
        Route::get('invoicesbatches/printview/{batch_id}','InvoicesbatchesController@printview')->name('client.invoicesbatches.printview');
        Route::resource('invoicesbatches', 'InvoicesbatchesController',['as'=> 'client']);
        
        Route::post('notification/{id}/delete','NotesController@removenotification',['as'=>'client']);
        Route::get('notification/{id}/delete','NotesController@removenotification',['as'=>'client']);
        Route::resource('{type}/{id}/notes', 'NotesController',['as'=>'client']);
        
       
        
        Route::get('jobparties/additionalform/{party_type}','JobPartiesController@additionalForm')->name('client.additionalform.parties');
        Route::get('jobparties/newcontactform/{client_id}','JobPartiesController@newContactForm')->name('client.newcontactform.parties');
        
        Route::post('{client_id}/interestrate', 'ClientController@interestrate');
        Route::post('{client_id}/defaultmaterials', 'ClientController@defaultmaterials');
        Route::get('{client_id}/interestrate', 'ClientController@interestrate');
        Route::get('{client_id}/defaultmaterials', 'ClientController@defaultmaterials');

        Route::group(['prefix' => 'jobs'], function(){
            Route::get('/joblist', 'JobsController@joblist');
            Route::delete('/attachment/{id}/destroy', 'JobsController@destroy_attachment')->name('client.jobs.attachment.destroy');
            Route::group(['prefix' => '{job_id}'], function($job_id){
                Route::get('/summary', 'JobsController@summary')->name('client.jobs.summary');
                Route::post('/close', 'JobsController@closeJob')->name('client.jobs.close');
                Route::get('/closelink', 'JobsController@closelink')->name('client.jobs.closelink');

                Route::get('/jobpayments/showattachment', 'JobPaymentsController@showattachment')->name('client.jobpayments.showattachment');
                Route::resource('jobpayments', 'JobPaymentsController',['as'=>'client']);
                Route::get('/jobchanges/showattachment', 'JobChangesController@showattachment')->name('client.jobchanges.showattachment');
                Route::resource('jobchanges', 'JobChangesController',['as'=>'client']);
                Route::get('/jobnocs/{id}/set_current', 'JobNocsController@setCurrent')->name('client.jobnocs.setcurrent');
                Route::get('/jobnocs/{id}/download', 'JobNocsController@downloadNOC')->name('client.jobnocs.download');
                Route::resource('jobnocs', 'JobNocsController',['as'=>'client']);
                Route::resource('jobreminders', 'JobRemindersController',['as'=>'client']);
                Route::post('/save_property', 'JobsController@save_property')->name('client.jobs.save_property');
                Route::get('/copy', 'JobsController@copy')->name('client.jobs.copy');
                Route::get('/contacts', 'JobsController@listcontacts');
                Route::post('/attachment', 'JobsController@uploadattachment')->name('client.jobs.addattachment');
                Route::get('/attachment/{id}/show', 'JobsController@showattachment')->name('client.jobs.showattachment');
                Route::get('/attachment/{id}/preview', 'JobsController@showthumbnail')->name('client.jobs.showthumbnail');
               
                Route::get('/jobparty/bond/{id}/download','JobPartiesController@downloadbond')->name('client.parties.downloadbond');
                Route::post('/parties/{id}/copy','JobPartiesController@copyParty')->name('client.parties.copy');
                Route::resource('parties', 'JobPartiesController',['as' => 'client' ]);
                Route::post('/share', 'JobsController@share')->name('client.jobs.share');
                Route::post('/shareTeam', 'JobsController@shareTeam')->name('client.jobs.shareTeam');
                Route::get('/share_to/{user_id}', 'JobsController@shareToUser')->name('client.jobs.shareto');
             });
        });
        Route::match(['get', 'post'],'jobs/setfilter','JobsController@setfilter')->name('client.jobs.setfilter');
        Route::get('jobs/resetfilter','JobsController@resetfilter')->name('client.jobs.resetfilter');
        Route::get('jobs/getaddress','JobsController@getaddress')->name('client.jobs.getaddress');
        Route::resource('jobs', 'JobsController', ['as' => 'client' ]);

        Route::match(['get', 'post'],'contract_trackers/setfilter','ContractTrackerController@setfilter')->name('client.contract_trackers.setfilter');
        Route::get('contract_trackers/resetfilter','ContractTrackerController@resetfilter')->name('client.contract_trackers.resetfilter');
        Route::get('contract_trackers/{id}/download', 'ContractTrackerController@download')->name('client.contract_trackers.download');
        Route::resource('contract_trackers', 'ContractTrackerController', ['as' => 'client' ]);

        Route::group(['prefix' => 'jobs_shared'], function(){
            Route::group(['prefix' => '{job_id}'], function($job_id){
                Route::get('/summary', 'JobsSharedController@summary')->name('client.jobs_shared.summary');
                Route::post('/link_to', 'JobsSharedController@linkTo')->name('client.jobs_shared.link_to');
                Route::get('/unlink', 'JobsSharedController@unlink')->name('client.jobs_shared.unlink');
            });
            Route::post('/request/from_notice', 'JobsSharedController@shareRequestFromNotice')->name('client.jobs_shared.request.fromNotice');
        });
        Route::match(['get', 'post'],'jobs_shared/setfilter','JobsSharedController@setfilter')->name('client.jobs_shared.setfilter');
        Route::get('jobs_shared/resetfilter','JobsSharedController@resetfilter')->name('client.jobs_shared.resetfilter');
        Route::resource('jobs_shared', 'JobsSharedController', ['as' => 'client' ]);
        Route::resource('jobs_monitor', 'JobsMonitorController', ['as' => 'client' ]);

        Route::group(['prefix' => 'research'], function(){
            Route::delete('/attachment/{id}/destroy', 'ResearchController@destroy_attachment')->name('client.research.attachment.destroy');
            Route::group(['prefix' => '{job_id}'], function($job_id){
                Route::get('/start', 'ResearchController@start')->name('client.research.start');
                Route::get('/runsearch', 'ResearchController@runsearch')->name('client.research.runsearch');
                Route::get('/select_property', 'ResearchController@select_property')->name('client.research.select_property');
                Route::post('/select_property', 'ResearchController@select_property')->name('client.research.select_property');
                Route::post('/save_property', 'ResearchController@save_property')->name('client.research.save_property');
                Route::group(['prefix' => 'wizard'], function(){
                    Route::get('/step1', 'ResearchController@wizardStep1')->name('client.research.wizard.step1');
                    Route::get('/step2', 'ResearchController@wizardStep2')->name('client.research.wizard.step2');
                    Route::get('/step3', 'ResearchController@wizardStep3')->name('client.research.wizard.step3');
                    Route::get('/step4', 'ResearchController@wizardStep4')->name('client.research.wizard.step4');
                    Route::get('/step5', 'ResearchController@wizardStep5')->name('client.research.wizard.step5');
                    Route::get('/step6', 'ResearchController@wizardStep6')->name('client.research.wizard.step6');
                    Route::get('/step7', 'ResearchController@wizardStep7')->name('client.research.wizard.step7');
                    Route::get('/step8', 'ResearchController@wizardStep8')->name('client.research.wizard.step8');
                    Route::get('/step9', 'ResearchController@wizardStep9')->name('client.research.wizard.step9');
    
                    Route::put('/step1/update', 'ResearchController@wizardStep1Update')->name('client.research.wizard.step1.update');
                    Route::put('/step2/update', 'ResearchController@wizardStep2Update')->name('client.research.wizard.step2.update');
                    Route::put('/step3/update', 'ResearchController@wizardStep3Update')->name('client.research.wizard.step3.update');
                    Route::put('/step4/update', 'ResearchController@wizardStep4Update')->name('client.research.wizard.step4.update');
                    Route::put('/step5/update', 'ResearchController@wizardStep5Update')->name('client.research.wizard.step5.update');
                    Route::put('/step6/update', 'ResearchController@wizardStep6Update')->name('client.research.wizard.step6.update');
                    Route::put('/step7/update', 'ResearchController@wizardStep7Update')->name('client.research.wizard.step7.update');
                    Route::put('/step8/update', 'ResearchController@wizardStep8Update')->name('client.research.wizard.step8.update');
                    Route::put('/step9/update', 'ResearchController@wizardStep9Update')->name('client.research.wizard.step9.update');
                    Route::post('/step9/update/number', 'ResearchController@wizardStep9UpdateNumber')->name('client.research.wizard.step9.number');
                    Route::put('/finish', 'ResearchController@finishWizard')->name('client.research.wizard.finish');
    
                    Route::post('/parties/store', 'ResearchController@storeParty')->name('client.research.wizard.parties.store');
                    Route::delete('/parties/{id}/destroy', 'ResearchController@destroyParty')->name('client.research.wizard.parties.destroy');
                    Route::put('/parties/{id}/update', 'ResearchController@updateParty')->name('client.research.wizard.parties.update');
                    Route::get('/parties/{id}/edit', 'ResearchController@editParty')->name('client.research.wizard.parties.edit');
                    Route::post('/parties/{id}/copy', 'ResearchController@copyParty')->name('client.research.wizard.parties.copy');
                    Route::get('/contacts', 'ResearchController@listcontacts');
                });
                Route::post('/attachment', 'ResearchController@uploadattachment')->name('client.research.addattachment');
                Route::get('/attachment/{id}/show', 'ResearchController@showattachment')->name('client.research.showattachment');
                Route::get('/attachment/{id}/preview', 'ResearchController@showthumbnail')->name('client.research.showthumbnail');
                
                Route::post('/mark_completed', 'ResearchController@markCompleted')->name('client.research.mark_completed');
                Route::post('/close', 'ResearchController@closeJob')->name('client.research.close');
                Route::get('/number', 'ResearchController@getNumber');
                Route::get('/type/{type}', 'ResearchController@checkType');
                Route::get('/contractamount', 'ResearchController@getContractAmount');
                Route::get('/startedat', 'ResearchController@getStartedAt');
                Route::get('/lastday', 'ResearchController@getLastDay');
                Route::get('/users', 'ResearchController@getUsers');
                Route::get('/jobparty/bond/{id}/download','JobPartiesController@downloadbond')->name('client.parties.downloadbond');
                Route::get('/copy', 'ResearchController@listjobs');
                Route::get('/copyform', 'ResearchController@jobform');
                Route::post('/copyselected', 'ResearchController@docopy')->name('client.research.docopy');
                Route::post('/parties/{id}/copy','JobPartiesController@copyParty')->name('client.parties.copy');
             });
        });
        Route::match(['get', 'post'],'research/setfilter','ResearchController@setfilter')->name('client.research.setfilter');
        Route::get('research/resetfilter','ResearchController@resetfilter')->name('client.research.resetfilter');
        Route::get('research/getaddress','ResearchController@getaddress')->name('client.research.getaddress');
        Route::get('research/search_address','ResearchController@search_address')->name('client.research.search_address');
        Route::get('research/search_jobs','ResearchController@search_jobs')->name('client.research.search_jobs');
        Route::resource('research', 'ResearchController', ['as' => 'client' ]);
        
        Route::group(['prefix' => 'wizard'], function(){
            Route::get('/purchase', 'WizardController@paymentPurchase')->name('wizard.payments.purchase');
            Route::get('/job', 'WizardController@getJob')->name('wizard.getjob');
            Route::get('/job/create', 'WizardController@createJob')->name('wizard.createjob');
            Route::post('/job', 'WizardController@postJob')->name('wizard.setjob');
            Route::get('/job/{job_id}/form', 'WizardController@getJobForm')->name('wizard.getjobform');
            Route::get('/job/pullnotice', 'WizardController@pullNotice')->name('wizard.pullnotice');
            
            Route::get('/job/{job_id}/attachment/{id}/show', 'WizardController@showattachment')->name('wizard.jobs.showattachment');
            Route::get('/attachment/{att_id}/showthumbnail', 'WizardController@showthumbnail')->name('wizard.showthumbnail');

            Route::get('/invoice/{id}', 'WizardController@capturecc')->name('wizard.capturecc');
            Route::post('/invoice/{id}', 'WizardController@capturecc')->name('wizard.capturecc');

            Route::get('/payment/{id}/paid', 'WizardController@paid')->name('wizard.invoice.paid');
            Route::get('/payment/{id}/unpaid', 'WizardController@unpaid')->name('wizard.invoice.unpaid');
             Route::group(['prefix' => 'job/{id}'], function($id){
                Route::get('/created', 'WizardController@jobcreated')->name('wizard.jobcreated');
                Route::post('/parties/{party_id}/copy','WizardController@copyParty')->name('wizard.parties.copy');
                Route::get('/contacts', 'WizardController@listcontacts');
                
                Route::get('/employer', 'WizardController@getEmployer')->name('wizard.getemployer');
                Route::post('/employer/store', 'WizardController@storeEmployer')->name('wizard.employer.store');
                
                Route::get('/gc', 'WizardController@getGc')->name('wizard.getgc');
                Route::post('/gc/store', 'WizardController@storeGc')->name('wizard.gc.store');

                Route::get('/landowner', 'WizardController@getLandowner')->name('wizard.getlandowner');
                Route::post('/landowner/store', 'WizardController@storeLandowner')->name('wizard.landowner.store');
                
                Route::get('/leaseholder', 'WizardController@getLeaseholder')->name('wizard.getleaseholder');
                Route::post('/leaseholder/store', 'WizardController@storeLeaseholder')->name('wizard.leaseholder.store');
                
                Route::get('/bond', 'WizardController@getBond')->name('wizard.getbond');
                Route::post('/bond/store', 'WizardController@storeBond')->name('wizard.bond.store');
                
                
                 Route::get('/other', 'WizardController@getOther')->name('wizard.getother');
                Route::post('/other/store', 'WizardController@storeOther')->name('wizard.other.store');
                
                Route::get('/parties', 'WizardController@getParties')->name('wizard.getparties');
                Route::post('/party/store', 'WizardController@storeParty')->name('wizard.parties.store');
                Route::get('/party/{party_id}/edit', 'WizardController@editParty')->name('wizard.parties.edit');
                Route::put('/party/{party_id}/update', 'WizardController@updateParty')->name('wizard.parties.update');
                Route::delete('/party/{party_id}/delete', 'WizardController@destroyParty')->name('wizard.parties.destroy');
                Route::get('/workorder/create', 'WizardController@createWorkOrder')->name('wizard.workorder.create');
                Route::post('/workorder/store', 'WizardController@storeWorkOrder')->name('wizard.workorder.store');
                Route::post('/addattachments', 'WizardController@addattachments')->name('wizard.addattachments');
                Route::get('/attachments', 'WizardController@attachments')->name('wizard.attachments');
                Route::delete('/attachment/{att_id}', 'WizardController@deleteattachment')->name('wizard.attachment.destroy');
                Route::get('/workorder/{wo_id}/payments', 'WizardController@payments')->name('wizard.payments');
             });
        });

        Route::group(['prefix' => 'wizard2'], function(){
            Route::get('/get-existing-notice', 'Wizard2Controller@getExistingNotice');
            Route::get('/get-unpaid-notice', 'Wizard2Controller@getUnpaidNotice');
            Route::get('/purchase', 'Wizard2Controller@paymentPurchase')->name('wizard2.payments.purchase');
            Route::get('/job_workorder', 'Wizard2Controller@getJobWorkorder')->name('wizard2.getjobworkorder');
            Route::post('/job_workorder', 'Wizard2Controller@postJobWorkorder')->name('wizard2.setjobworkorder');
            Route::get('/job/{job_id}/form', 'Wizard2Controller@getJobForm')->name('wizard2.getjobform');
            Route::get('/job/pullnotice', 'Wizard2Controller@pullNotice')->name('wizard2.pullnotice');
            
            Route::get('/job/{job_id}/attachment/{id}/show', 'Wizard2Controller@showattachment')->name('wizard2.jobs.showattachment');
            Route::get('/attachment/{att_id}/showthumbnail', 'Wizard2Controller@showthumbnail')->name('wizard2.showthumbnail');

            Route::get('/invoice/{id}', 'Wizard2Controller@capturecc')->name('wizard2.capturecc');
            Route::post('/invoice/{id}', 'Wizard2Controller@capturecc')->name('wizard2.capturecc');

            Route::get('/payment/{id}/paid', 'Wizard2Controller@paid')->name('wizard2.invoice.paid');
            Route::get('/payment/{id}/unpaid', 'Wizard2Controller@unpaid')->name('wizard2.invoice.unpaid');
             Route::group(['prefix' => 'job/{id}'], function($id){
                Route::post('/parties/{party_id}/copy','Wizard2Controller@copyParty')->name('wizard2.parties.copy');
                Route::get('/contacts', 'Wizard2Controller@listcontacts');
                Route::get('/contactsall', 'Wizard2Controller@listcontactsall');
                
                Route::get('/employer', 'Wizard2Controller@getEmployer')->name('wizard2.getemployer');
                Route::post('/employer/store', 'Wizard2Controller@storeEmployer')->name('wizard2.employer.store');
                
                Route::get('/gc', 'Wizard2Controller@getGc')->name('wizard2.getgc');
                Route::post('/gc/store', 'Wizard2Controller@storeGc')->name('wizard2.gc.store');

                Route::get('/landowner', 'Wizard2Controller@getLandowner')->name('wizard2.getlandowner');
                Route::post('/landowner/store', 'Wizard2Controller@storeLandowner')->name('wizard2.landowner.store');
                
                Route::get('/leaseholder', 'Wizard2Controller@getLeaseholder')->name('wizard2.getleaseholder');
                Route::post('/leaseholder/store', 'Wizard2Controller@storeLeaseholder')->name('wizard2.leaseholder.store');
                
                Route::get('/bond', 'Wizard2Controller@getBond')->name('wizard2.getbond');
                Route::post('/bond/store', 'Wizard2Controller@storeBond')->name('wizard2.bond.store');
                
                
                 Route::get('/other', 'Wizard2Controller@getOther')->name('wizard2.getother');
                Route::post('/other/store', 'Wizard2Controller@storeOther')->name('wizard2.other.store');
                
                Route::get('/parties', 'Wizard2Controller@getParties')->name('wizard2.getparties');
                Route::post('/party/store', 'Wizard2Controller@storeParty')->name('wizard2.parties.store');
                Route::get('/party/{party_id}/edit', 'Wizard2Controller@editParty')->name('wizard2.parties.edit');
                Route::put('/party/{party_id}/update', 'Wizard2Controller@updateParty')->name('wizard2.parties.update');
                Route::delete('/party/{party_id}/delete', 'Wizard2Controller@destroyParty')->name('wizard2.parties.destroy');
                Route::get('/workorder/create', 'Wizard2Controller@createWorkOrder')->name('wizard2.workorder.create');
                Route::post('/workorder/store', 'Wizard2Controller@storeWorkOrder')->name('wizard2.workorder.store');
                Route::post('/addattachments', 'Wizard2Controller@addattachments')->name('wizard2.addattachments');
                Route::get('/attachments', 'Wizard2Controller@attachments')->name('wizard2.attachments');
                Route::delete('/attachment/{att_id}', 'Wizard2Controller@deleteattachment')->name('wizard2.attachment.destroy');
                Route::get('/workorder/{wo_id}/payments', 'Wizard2Controller@payments')->name('wizard2.payments');
                Route::get('/workorder/{wo_id}/choicenext', 'Wizard2Controller@choicenext')->name('wizard2.choicenext');
             });
        });
        
        Route::group(['prefix' => 'release'], function(){
            Route::get('/', 'ReleaseController@newrelease')->name('client.release.new');
            Route::post('/pdf', 'ReleaseController@pdf')->name('client.release.pdf');
            Route::post('/generate', 'ReleaseController@generate')->name('client.release.generate');
            Route::post('/update', 'ReleaseController@update')->name('client.release.update');
           
            Route::post('/close', 'ReleaseController@doClose')->name('client.release.close');
            Route::get('/job/{id}/parties', 'ReleaseController@getparties');
        });

        Route::group(['prefix' => 'coordinates'], function(){
            Route::get('/', 'CoordinateController@index')->name('client.coordinates.index');

            Route::get('/resetfilter','CoordinateController@resetfilter')->name('client.coordinates.resetfilter');
            Route::post('/setfilter','CoordinateController@setfilter')->name('client.coordinates.setfilter');
            Route::post('/store', 'CoordinateController@store')->name('client.coordinates.store');
            Route::put('/update/{id}', 'CoordinateController@update')->name('client.coordinates.update');
            Route::delete('/delete/{id}', 'CoordinateController@delete')->name('client.coordinates.delete');
        });
        
        Route::group(['prefix' => 'notices'], function(){
            Route::get('/existing-unpaid', 'WorkOrdersController@existingWorkorderUnpaid');
            Route::get('/kickout', 'PdfPagesController@kickout');
            Route::delete('/attachment/{id}/destroy', 'WorkOrdersController@destroy_attachment')->name('client.notices.attachment.destroy');
            Route::group(['prefix' => '{work_id}'], function($work_id){
                Route::get('/document', 'PdfPagesController@document')->name('client.notices.document');
                Route::get('/document/reset', 'PdfPagesController@askreset')->name('client.notices.reset.ask');
                Route::post('/document/reset', 'PdfPagesController@doreset')->name('client.notices.reset.do');
                Route::post('/document/cancel', 'PdfPagesController@docancel')->name('client.notices.document.cancel');
                Route::get('/delete-document', 'PdfPagesController@deletedocument')->name('client.notices.deletedocument');
                Route::get('/delete-regenerate', 'PdfPagesController@deleteRegenerate')->name('client.notices.deleteregenerate');

                Route::get('/request-additional-service', 'WorkOrdersController@requestService')->name('client.notices.requestService');
                Route::post('/purchase-additional-service', 'WorkOrdersController@purchaseService')->name('client.notices.purchaseService');
                Route::get('/service/pay', 'WorkOrdersController@payService')->name('client.notices.payService');
                Route::get('/service/payment/{payment_id}/paid', 'WorkOrdersController@paid')->name('client.notices.service.paid');
                Route::get('/service/payment/{payment_id}/unpaid', 'WorkOrdersController@unpaid')->name('client.notices.service.unpaid');

               Route::get('/cancel', 'WorkOrdersController@cancel')->name('client.notices.cancel');
               //Route::get('/delete-document', 'Admin\PdfPagesController@deletedocument')->name('client.notices.deletedocument');
               Route::post('/attachment', 'WorkOrdersController@uploadattachment')->name('client.notices.addattachment');
               Route::get('/attachment/{id}/show', 'WorkOrdersController@showattachment')->name('client.notices.showattachment');
               Route::get('/attachment/{id}/preview', 'WorkOrdersController@showthumbnail')->name('client.notices.showthumbnail');
               Route::get('/attachment/{id}/download', 'WorkOrdersController@downloadattachment')->name('client.notices.downloadattachment');
               Route::get('/attachment/{id}/pdfpreview','WorkOrdersController@view')->name('client.notices.view');
               Route::get('invoices/new','WorkOrdersController@createinvoice')->name('client.notices.createinvoice');

               Route::get('/todo/{id}/edit', 'WorkOrdersController@todoEdit')->name('client.notices.todo.edit');
               Route::get('/todo/{id}/complete', 'WorkOrdersController@todoComplete')->name('client.notices.todo.complete');
            });
            Route::post('/todo/{id}/upload', 'WorkOrdersController@todoUpload')->name('client.notices.todo.upload');
            Route::delete('/todo/{id}/document/destroy', 'WorkOrdersController@destroyTodoDocument')->name('client.notices.todo.document.destroy');
            Route::get('/todo/{id}/document/download', 'WorkOrdersController@downloadTodoDocument')->name('client.notices.todo.document.download');
            Route::get('/todo/{id}/document/showthumbnail', 'WorkOrdersController@showTodoThumbnail')->name('client.notices.todo.document.showTodoThumbnail');

            Route::post('/todo/{id}/instruction', 'WorkOrdersController@todoInstruction')->name('client.notices.todo.instruction');
            Route::delete('/todo/{id}/instruction/destroy', 'WorkOrdersController@destroyTodoInstruction')->name('client.notices.todo.instruction.destroy');
        });
        Route::match(['get', 'post'],'notices/setfilter','WorkOrdersController@setfilter')->name('client.notices.setfilter');
        Route::get('notices/resetfilter','WorkOrdersController@resetfilter')->name('client.notices.resetfilter');
        Route::get('notices/getfields','WorkOrdersController@getfields')->name('notices.getfields');
        Route::resource('notices', 'WorkOrdersController',['as'=>'client']);
        Route::resource('mailing', 'MailingController',['as'=>'client']);
        
        Route::group(['prefix' => 'pdfpage'], function(){
            Route::post('/complete', 'PdfPagesController@complete')->name('client.pdfpage.complete');
            Route::group(['prefix' => '{pdf_id}'], function($pdf_id){
                Route::post('/update', 'PdfPagesController@update')->name('client.pdfpage.update');
                Route::post('/preview', 'PdfPagesController@preview')->name('client.pdfpage.preview');
                Route::post('/AttachPDF', 'PdfPagesController@AttachPDF')->name('client.pdfpage.AttachPDF');
                Route::match(['get', 'post'],'/generate', 'PdfPagesController@generate')->name('client.pdfpage.generate');
                Route::post('/save', 'PdfPagesController@save')->name('client.pdfpage.save');
            });
            Route::get('/paypdf', 'PdfPagesController@payPDF')->name('client.pdfpage.pay');
            Route::get('/payment/{payment_id}/paid', 'PdfPagesController@paid')->name('client.pdfpage.paid');
            Route::get('/payment/{payment_id}/unpaid', 'PdfPagesController@unpaid')->name('client.pdfpage.unpaid');
        });

         Route::group(['prefix' => 'mailinghistory','as'=>'client.'], function(){
         Route::get('/', 'MailingHistoryController@index')->name('mailinghistory.index');
         Route::get('/resetfilter', 'MailingHistoryController@resetfilter')->name('mailinghistory.resetfilter');
         Route::post('/setfilter', 'MailingHistoryController@setfilter')->name('mailinghistory.setfilter');
         Route::post('/resend/{id}', 'MailingHistoryController@resend')->name('mailinghistory.resend');
         Route::post('/savepdf/{id}', 'MailingHistoryController@savepdf')->name('mailinghistory.savepdf');
     });
        
        
    });
    Route::get('/client/service/renews', 'ClientController@renews')->name('client.renews');
    Route::put('/client/service/renews', 'ClientController@renews')->name('client.renews');
    Route::get('/client/dashboard', 'ClientController@index')->name('client');
    Route::resource('/client', 'ClientController');
    
     
   
});

