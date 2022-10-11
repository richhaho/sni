
 <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">

            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="{{ route('client')}}"> {{ config('app.name', 'Laravel') }}</a>
            </div>
            <!-- /.navbar-header -->

            <ul class="nav navbar-top-links navbar-right">
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-envelope fa-fw"></i><span class="label label-danger label-as-badge menu-badge" id='messages-count'>{{ $count_notification == 0 ? '' : $count_notification }}</span> <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-messages">
                       @foreach($notifications as $xn)
                             
                             <li id="{{$xn->id}}">
               
                                <a class="meesage-link" data-id="{{$xn->id}}" data-url="{{ array_key_exists('url_client',$xn->data) ? $xn->data['url_client']: '#' }}" href="#">
                                    <div>
                                        @if($xn->type =='App\Notifications\NewWorkNote')
                                        <strong>New Work Order Note</strong>
                                        @endif
                                        @if($xn->type =='App\Notifications\NewJobNote')
                                        <strong>New Job Note</strong>
                                        @endif
                                        @if($xn->type =='App\Notifications\NewAttachment')
                                        <strong>New Attachment</strong>
                                        @endif
                                        <span class="pull-right text-muted">
                                            <em>{{ \Carbon\Carbon::createFromTimeStamp(strtotime($xn->data['message']['entered_at']))->diffForHumans() }}</em>
                                            
                                        </span>
                                    </div>
                                    <div>{{ $xn->data['message']['note'] }}</div>
                                </a>
                            </li>
                            @if(!$loop->last)
                             <li class="divider"></li>
                            @endif
                        @endforeach
                    </ul>
                    <!-- /.dropdown-messages -->
                </li>
                <!-- /.dropdown -->
            
            
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-user fa-fw"></i> {{ Auth::user()->full_name}} <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-user">
         
                        <li>
                             <a href="{{ route('logout') }}"
                                onclick="event.preventDefault();
                                         document.getElementById('logout-form').submit();">
                                <i class="fa fa-sign-out fa-fw"></i> Logout
                            </a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                {{ csrf_field() }}
                            </form>        
                        </li>
                    </ul>
                    <!-- /.dropdown-user -->
                </li>
                <!-- /.dropdown -->
            </ul>
            <!-- /.navbar-top-links -->

 
            @if(!Auth::user()->verified)
            <div class="navbar-default sidebar" role="navigation" style="pointer-events:none;">
            @else
            <div class="navbar-default sidebar" role="navigation">    
            @endif
                <div class="sidebar-nav navbar-collapse">
                    <ul class="nav" id="side-menu">
                        <!--<li class="sidebar-search">
                            <div class="input-group custom-search-form">
                                <input type="text" class="form-control" placeholder="Search...">
                                <span class="input-group-btn">
                                    <button class="btn btn-default" type="button">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </span>
                            </div>
                           
                        </li> -->
                        <li>
                            <a href="{{ route('client')}}"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a>
                        </li>
                        @if(Auth::user()->client->gps_tracking)
                        <li>
                            <a href="{{ route('client.coordinates.index')}}" class="{{ starts_with(Request::url(),url('client/coordinates')) ? 'active' : ''}}"><i class="fa fa-map-marker fa-fw"></i> Track Location</a>
                        </li>
                        @endif
                        <li class="{{ Auth::user()->verified ? '' : 'disabled'}}">
                            <a href="{{ Auth::user()->verified ? route('wizard2.getjobworkorder') : '' }}" class="{{ starts_with(Request::url(),url('client/wizard2/job_workorder')) ? 'active' : ''}} {{ Auth::user()->verified ? '' : 'disabled'}}"><i class="fa fa-edit fa-fw"></i> New Work Order </a>
                        </li>
                        <li>
                            <a href="{{ route('client.release.new')}}" class="{{ starts_with(Request::url(),url('client/release')) ? 'active' : ''}}"><i class="fa fa-external-link fa-fw"></i> New Release</a>
                        </li>

                        <li>
                            <a href="{{ route('client.folders.index')}}" class="{{ starts_with(Request::url(),url('client/folders')) ? 'active' : ''}}"><i class="fa fa-database fa-fw"></i> Reporting</a>
                        </li>
                        @if(Auth::user()->client->has_contract_tracker)
                        <li>
                            <a href="{{ route('client.contract_trackers.index')}}" class="{{ starts_with(Request::url(),url('client/contract_trackers')) ? 'active' : ''}}"><i class="fa fa-book fa-fw"></i> Contract Tracker</a>
                        </li>
                        @endif
                        <li class="{{ starts_with( Request::url(),route('client.jobs_monitor.index')) ? 'active' : ''}} {{ starts_with( Request::url(),route('client.jobs_shared.index')) ? 'active' : ''}} {{ starts_with( Request::url(),route('client.jobs.index')) ? 'active' : ''}} {{ starts_with( Request::url(),route('client.notices.index')) ? 'active' : ''}} {{ starts_with( Request::url(),route('client.mailinghistory.index')) ? 'active' : ''}} ">
                        
                            <a href="#"  aria-expanded="true"><i class="fa fa-briefcase fa-fw"></i> My Orders<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level" aria-expanded="false">
                                 <li>
                                    <a href="{{ route('client.jobs.index')}}" class="{{ starts_with(Request::url(),url('client/jobs')) ? 'active' : ''}}"><i class="fa fa-briefcase fa-fw"></i> My Job/Contract</a>
                                </li>
                                @if(Auth::user()->client->is_monitoring_user)
                                <li>
                                    <a href="{{ route('client.jobs_shared.index')}}" class="{{ starts_with(Request::url(),url('client/jobs_shared')) ? 'active' : ''}}"><i class="fa fa-share fa-fw"></i> My Shared Jobs</a>
                                </li>
                                @endif
                                 <li>
                                    <a href="{{ route('client.notices.index')}}" class="{{ starts_with(Request::url(),url('client/notices')) ? 'active' : ''}}"><i class="fa fa-cogs fa-fw"></i> My Work Orders</a>
                                </li>
                                <li>
                                    <a href="{{ route("client.mailinghistory.index") }}" class="{{ starts_with( Request::url(),route("client.mailinghistory.index")) ? 'active' : ''}}"><i class="fa  fa-repeat fa-fw"></i> Mailing History</a>
                                </li>
                            </ul>
                        </li>

                        <li class="{{ starts_with( Request::url(),route('client.invoices.index')) ? 'active' : ''}} {{ starts_with( Request::url(),route('client.invoices.index')) ? 'active' : ''}} ">
                            <a href="#"  aria-expanded="true"><i class="fa  fa-bar-chart-o fa-fw"></i> Bills and Payment<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level" aria-expanded="false">


                                @if (starts_with( Request::url(),route("client.invoicesbatches.index")))
                                <li>
                                    <a href="{{ route("client.invoices.index") }}" class=""><i class="fa  fa-bar-chart-o fa-fw"></i> Invoices</a>
                                </li>
                                @else
                                <li>
                                    <a href="{{ route("client.invoices.index") }}" class="{{ starts_with( Request::url(),route("client.invoices.index")) ? 'active' : ''}}"><i class="fa  fa-bar-chart-o fa-fw"></i> Invoices</a>
                                </li>
                                @endif

                                <li>
                                    <a href="{{ route("client.invoicesbatches.index") }}" class="{{ starts_with( Request::url(),route("client.invoicesbatches.index")) ? 'active' : ''}}"><i class="fa  fa-pie-chart fa-fw"></i> Batch Invoices</a>
                                </li> 

                            </ul>
                        </li>

                        <!--
                        <li>
                            <a href="{{ route("client.mailing.index") }}" class="{{ starts_with( Request::url(),route("client.mailing.index")) ? 'active' : ''}}"><i class="fa  fa-envelope fa-fw"></i> Mailing History</a>
                        </li>-->
                         <li class="{{ starts_with( Request::url(),route('client.clientusers.index')) ? 'active' : ''}} {{ starts_with( Request::url(),route('creditcard.index')) ? 'active' : ''}} {{ starts_with( Request::url(),route('client.create')) ? 'active' : ''}} {{ starts_with( Request::url(),route('client.contacts.index')) ? 'active' : ''}}">
                            <a href="#"  aria-expanded="true"><i class="fa fa-sliders fa-fw"></i> Management<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level" aria-expanded="false">
                                <li>
                                    <a href="{{ route('client.create') }}" class="{{ starts_with(Request::url(),url('client/create')) ? 'active' : ''}}"> Company Info</a>
                                </li>
                                <li>
                                    <a href="{{ route('client.contacts.index') }}" class="{{ starts_with(Request::url(),url('client/contacts')) ? 'active' : ''}}">Contacts</a>
                                </li>
                               
                                <li>
                                    <a href="{{ route ('client.clientusers.index')}}" class="{{ starts_with( Request::url(),route('client.clientusers.index')) ? 'active' : ''}}">Users</a>
                                </li>
                                 <li>
                                    <a href="{{ route ('creditcard.index')}}" class="{{ starts_with( Request::url(),route('creditcard.index')) ? 'active' : ''}}">Credit Card</a>
                                </li>
                                 
                            </ul>
                        </li>
                    </ul>
                </div>
                <!-- /.sidebar-collapse -->
                @if(!Auth::user()->verified)
                <div>&nbsp;</div>
                <div class="col-xs-12 ">
                    <div class="alert alert-danger alert-dismissible" role="alert"> You must validate your email before continuing forward.  Please check your inbox for an email validation link. <br><a href="{{route('client.validate.resend')}}" style="pointer-events:auto;">Resend email</a></div>
                </div>
                @endif
            </div>
            <!-- /.navbar-static-side -->
        </nav>
        @if(1===2)
        @if(!Auth::user()->client->service || (Auth::user()->client->service && Auth::user()->client->subscriptionRate && (!Auth::user()->client->expiration || Auth::user()->client->expiration < date('Y-m-d H:i:s'))))
            <div class="modal show" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Choose Self-Service/Full-service
                                <a href="{{ route('logout') }}" class="pull-right"
                                    onclick="event.preventDefault();
                                            document.getElementById('logout-form').submit();">
                                    <i class="fa fa-sign-out fa-fw"></i> Logout
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                    {{ csrf_field() }}
                                </form>
                            </h5>
                        </div>
                        <div class="modal-body">
                            <div class="row service-data-group">
                                <div class="col-xs-12 col-md-4 form-group">
                                    <label>Service Type:</label>
                                    {!!  Form::select('service', ['' => '', 'full' => 'Full-Service', 'self' => 'Self-Service'], Auth::user()->client->service, ['class' => 'form-control service_type', 'required' => 'true']) !!}
                                </div>
                                <div class="col-xs-12 col-md-4 form-group">
                                    <label>Subscription:</label>
                                    {!!  Form::select('subscription', ['' => '', '30' => '30-day rate', '365' => '365-day rate'], Auth::user()->client->subscription, ['class' => 'form-control subscription_type', 'required' => 'true']) !!}
                                </div>
                                <div class="col-xs-12 col-md-4 form-group">
                                    <label>Subscription Rate:</label>
                                    <input type="text" name='subscription_rate' class="subscription_rate form-control" readonly value="{{Auth::user()->client->subscriptionRate}}">
                                </div>
                                <div class="col-xs-12 col-md-12 form-group">
                                    <label>Description:</label>
                                    <input type="hidden" name='description'>
                                    <div class="description">
                                        <ul class="full-service-description {{Auth::user()->client->service=='full' ? '':'hidden'}}">
                                            <?php 
                                                $template = \App\Template::where('type_slug', 'notice-to-owner')->where('client_id', Auth::user()->client->id)->first();
                                                if (!$template) {
                                                    $template = \App\Template::where('type_slug', 'notice-to-owner')->where('client_id', 0)->first();
                                                }
                                                $nto= $template ? $template->lines()->where('type', 'apply-always')->sum('price') : 0;
                                            ?>
                                            <li>Sunshine Notices performs all of your research.</li>
                                            <li>Sunshine Notices will generate and gather any of your required documents.</li>
                                            <li>We print everything for you and deliver it to the post office.</li>
                                            <li>You can track your mail.</li>
                                            <li>Sunshine Notices will receive your return mail for processing.</li>
                                            <li>Cost per Notice to Owner is ${{$nto}}</li>
                                        </ul>
                                        <ul class="self-service-description {{Auth::user()->client->service=='self' ? '':'hidden'}}">
                                            <li>You perform all of your research.</li>
                                            <li>You generate and gather any documents needed.</li>
                                            <li>You choose how you would like Sunshine Notices to mail your documents.</li>
                                            <li>Sunshine Notices will print everything for you and deliver it to the post office.</li>
                                            <li>You can track your mail.</li>
                                            <li>You will receive your return mail.</li>
                                            <li>At any time you may request that Sunshine Notices perform any tasks required for your work order.</li>
                                            <li>Monthly Subscription Fee of ${{Auth::user()->client->self_30day_rate}} per month or ${{Auth::user()->client->self_365day_rate}} annually.</li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-md-12 form-group">
                                    <button type="button" class="btn btn-primary btn-service-save pull-right"><i class="fa fa-save"> Save</i></button>
                                </div>
                            </div>








                            <div class="row renews-group hidden" style="margin-top: 50px">
                                <?php
                                    $client = Auth::user()->client;
                                    $company =  \App\CompanySetting::first();
                                    $api_key = $company->apikey;
                                    $api_secret = $company->apisecret;
                                    $js_security_key = $company->js_security_key;
                                    $ta_token = $company->ta_token;
                                    $payeezy_url = $company->apiurl;
                                ?>
                                @if(strlen($client->payeezy_value) == 0)
                                <div class ="col-xs-12 col-md-4">
                                    <h4><i class="fa fa-lock"></i> You are safe. We take privacy very seriously<br> <small>We accept:</small></h4>
                                        <img  class="img-responsive col-xs-12 " src="{{ asset('/images/cclogos.jpg') }}" alt=""/>
                                        <p>&nbsp;</p>
                                        <p>Your credit card information is never stored on our servers. 
                                            This form will be processed through a secure channel to Payeezy 
                                            for payment processing.</p>
                                        <p>&nbsp;</p>
                                        
                                        <img  class="img-responsive col-xs-12 " src="{{ asset('/images/payeezylogo.png') }}" alt=""/>
                                </div> 
                                <div class="col-xs-12 col-md-8">
                                    <form id="renews-form-card">
                                    
                                    {{ Form::hidden('donottokenize',true) }}
                                    {{ Form::hidden('apikey', $api_key, ['id'=>'apikey']) }}
                                    {{ Form::hidden('apisecret', $api_secret, ['id'=>'apisecret']) }}
                                    
                                    {{ Form::hidden('currency', 'USD',['id'=>'currency','payeezy-data'=>'currency'])}}
                                        <div id="payment-errors" class="alert alert-danger hidden" >
                                            <span></span>
                                        </div>
                                        <div id="payment-success" class="alert alert-success hidden">
                                            <span></span>
                                        </div>
                                        <div id="response_msg" class="alert alert-success hidden">
                                            <span></span>
                                        </div>
                                        <div id="response_note" class="alert alert-info hidden">
                                            <span></span>
                                        </div>

                                        <div class="row">
                                            <div class="col-xs-12 form-group">
                                                <label for="cc-name" class="control-label">Card Holder Name:</label>
                                                <div class="form-control payment-fields disabled" id="cc-name" data-cc-name></div>
                                            </div>
                                        </div>
                                    
                                        <div class="row">
                                            <div class="col-xs-12 form-group">
                                                <label for="cc-card" class="control-label">Card Number:</label>
                                                <div class="form-control payment-fields disabled empty" id="cc-card" data-cc-card></div>
                                            </div>
                                        </div>
                                    
                                        <div class="row">
                                            <div class="col-xs-12 form-group">
                                                <label for="cc-cvv" class="control-label">CVV Code:</label>
                                                <div class="form-control payment-fields disabled empty" id="cc-cvv" data-cc-cvv></div>
                                            </div>
                                        </div>
                                    
                                        <div class="row">
                                            <div class="col-xs-12 form-group">
                                                <label for="cc-exp" class="control-label">Expiry Date:</label>
                                                <div class="form-control payment-fields disabled empty" id="cc-exp" data-cc-exp></div>
                                            </div>
                                        </div>
                                    
                                        <div class="row">
                                        <div class="col-xs-12 ">
                                            <div class="col-xs-4 pull-right">
                                                <button id="submit" class="btn btn-success form-control btn--primary disabled-bkg" data-submit-btn disabled>
                                                    <span class="btn__loader" style="display:none;">loading...</span>Pay <span data-card-type></span>
                                                </button>
                                            </div>
                                            <div class="col-xs-4 pull-right">
                                                <button type="button" class="btn btn-danger btn-service-back  form-control">Back</button>
                                            </div>
                                        </div>
                                        </div>

                                    </form>
                                </div>
                                @else
                                <div class ="col-xs-12 col-md-4">
                                    <h4><i class="fa fa-lock"></i> Payment Processing</h4>
                                        <p>We will process your payment using a token retrieved 
                                            from our payment gateway. Credit Card information is 
                                            never stored on our servers, we use Payeezy for payment processing.</p>
                                        <p>&nbsp;</p>
                                    
                                        <img  class="img-responsive col-xs-12 " src="{{ asset('/images/payeezylogo.png') }}" alt=""/>
                                </div> 
                                <div class="col-xs-12 col-md-8">
                                    <form method="POST" name="renews-form" id="renews-form">
                                        <div id="payment-errors" class="alert alert-danger hidden" >
                                            <span></span>
                                        </div>
                                        <div id="response_msg" class="alert alert-success hidden">
                                            <span></span>
                                        </div>
                                        <div id="response_note" class="alert alert-info hidden">
                                            <span></span>
                                        </div>
                                    {{ Form::hidden('donottokenize',true) }}
                                    {{ Form::hidden('apikey', $api_key, ['id'=>'apikey']) }}
                                    {{ Form::hidden('apisecret', $api_secret, ['id'=>'apisecret']) }}
                                    
                                    {{ Form::hidden('currency', 'USD',['id'=>'currency','payeezy-data'=>'currency'])}}

                                    <p class="text-right"><strong>There may be additional costs for postage at notice completion.</strong></p>
                                    <div class="col-xs-4 pull-right">
                                        <div class="">
                                        <button type="submit"  id="pay-button" class=" btn btn-success btn-block  form-control">Pay</button>
                                        </div>
                                    </div>
                                    <div class="col-xs-4 pull-right">
                                        <button type="button" class="btn btn-danger btn-service-back  form-control">Back</button>
                                    </div>
                                    </form>
                                </div>
                                @endif
                            </div>













                        </div>
                    </div>
                </div>
            </div>
        @endif
        @endif
