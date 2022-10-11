
 <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
    
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="{{ route('admin')}}"> {{ config('app.name', 'Laravel') }}</a>
            </div>
            <!-- /.navbar-header -->

            <ul class="nav navbar-top-links navbar-right">
                @if(Auth::user()->restricted && Auth::user()->hasRole('researcher'))
                @else
                <li class="dropdown" id='app_notifications'>
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-envelope fa-fw"></i><span class="label label-danger label-as-badge menu-badge" id='messages-count'>{{ $count_notification == 0 ? '' : $count_notification }}</span> <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-messages" >

                        @foreach($notifications as $xn)
                                
                             <li id="{{$xn->id}}">
                         
                                <a class="meesage-link" data-id="{{$xn->id}}" data-url="{{  ( array_key_exists('url_admin',$xn->data)) ? $xn->data['url_admin'] : '#'  }}" href="#">
                                    <div>
                                        @if($xn->type =='App\Notifications\NewWorkNote')
                                        <strong>New Work Order Note</strong>
                                        @endif
                                        @if($xn->type =='App\Notifications\PastDueClientEnteredWorkOrder')
                                        <strong>Past Due Client Entered Work Order</strong>
                                        @endif
                                        @if($xn->type =='App\Notifications\NewJobNote')
                                        <strong>New Job Note</strong>
                                        @endif
                                        @if($xn->type =='App\Notifications\NewWorkOrder')
                                        <strong>New Work Order</strong>
                                        @endif
                                        @if($xn->type =='App\Notifications\NewAttachment')
                                        <strong>New Attachment</strong>
                                        @endif
                                        @if($xn->type =='App\Notifications\NewRelease')
                                        <strong>New Release</strong>
                                        @endif
                                        @if($xn->type =='App\Notifications\NewClientUser')
                                        <strong>New User Signup</strong>
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
                @endif
                <!-- /.dropdown -->
    
        
                <!-- /.dropdown -->
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-user fa-fw"></i> {{ Auth::user()->full_name}} <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-user">
                       <!-- <li><a href="#"><i class="fa fa-user fa-fw"></i> User Profile</a>
                        </li>
                        <li><a href="#"><i class="fa fa-gear fa-fw"></i> Settings</a>
                        </li>
                        <li class="divider"></li>-->
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

            <div class="navbar-yellow sidebar" role="navigation">
                <div class="sidebar-nav navbar-collapse">
                    <ul class="nav" id="side-menu">
                        @if(Auth::user()->restricted && Auth::user()->hasRole('researcher'))
                        @else
                        <li class="sidebar-search">
                            <div class="input-group custom-search-form">
                                <input id="searchbox" type="text" class="form-control" placeholder="Search...">
                                <span class="input-group-btn">
                                    <button class="btn btn-default" type="button" id="searchbutton">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </span>
                            </div>
                            <!-- /input-group -->
                        </li>
                        @endif
                        @if(!Auth::user()->restricted || Session::get('user_role')=='admin')
                        <li>
                            <a href="{{ route("admin")}}" class="{{ ends_with( Request::url(),route("admin")) ? 'active' : ''}}"><i class="fa fa-dashboard fa-fw"></i>  Dashboard</a>
                        </li>
                        <li>
                            <a href="{{ route("hotcontacts.index")}}" class="{{ ends_with( Request::url(),route("hotcontacts.index")) || str_contains( Request::url(),'hot') ? 'active' : ''}}"><i class="fa fa-fire fa-fw"></i>  HotList</a>
                        </li>
                        @endif
                        @if (Session::get('user_role')=='admin')
                        <li>
                            <a href="{{ route("clients.index") }}" class="{{ starts_with( Request::url(),route("clients.index")) ? 'active' : ''}}"><i class="fa fa-users fa-fw"></i> Clients</a>
                        </li>
                        @endif
                        @if(!Auth::user()->restricted || Session::get('user_role')=='admin')
                        <li>
                            <a href="{{ route("contract_trackers.index") }}" class="{{ starts_with( Request::url(),route("contract_trackers.index")) ? 'active' : ''}}"><i class="fa fa-book fa-fw"></i> Contract Tracker</a>
                        </li>
                        <li>
                            <a href="{{ route("jobs.index") }}" class="{{ starts_with( Request::url(),route("jobs.index")) ? 'active' : ''}}"><i class="fa fa-briefcase fa-fw"></i> Jobs</a>
                        </li>
                        <li>
                            <a href="{{ route("jobs_shared.index") }}" class="{{ starts_with( Request::url(),route("jobs_shared.index")) ? 'active' : ''}}"><i class="fa fa-share fa-fw"></i> Shared Jobs</a>
                        </li>
                        <li>
                            <a href="{{ route("workorders.index") }}" class="{{ starts_with( Request::url(),route("workorders.index")) ? 'active' : ''}}"><i class="fa fa-gears  fa-fw"></i> Work Orders(Full-Service) </a>
                            <a href="{{ route("workorders.index2") }}" class="{{ starts_with( Request::url(),route("workorders.index2")) ? 'active' : ''}}"><i class="fa fa-gears  fa-fw"></i> Work Orders(Self-Service) </a>
                        </li>
                        @endif
                        @if(Auth::user()->restricted)
                        <li>
                            <a href="{{ route("research.index") }}" class="{{ starts_with( Request::url(),route("research.index")) ? 'active' : ''}}"><i class="fa  fa-book fa-fw"></i> Research Queue</a>
                        </li>
                        @endif
                        @if (Session::get('user_role')=='admin')
                        @if (starts_with( Request::url(),route("invoicesbatches.index")))
                        <li>
                            <a href="{{ route("invoices.index") }}" class=""><i class="fa  fa-bar-chart-o fa-fw"></i> Invoices</a>
                        </li>
                        @else
                        <li>
                            <a href="{{ route("invoices.index") }}" class="{{ starts_with( Request::url(),route("invoices.index")) ? 'active' : ''}}"><i class="fa  fa-bar-chart-o fa-fw"></i> Invoices</a>
                        </li>
                        @endif
                        <li>
                            <a href="{{ route("invoicesbatches.index") }}" class="{{ starts_with( Request::url(),route("invoicesbatches.index")) ? 'active' : ''}}"><i class="fa  fa-pie-chart fa-fw"></i> Invoice Batches</a>
                        </li>    

                        <li>
                            @if (starts_with( Request::url(),route("mailinghistory.index")))
                            <a href="{{ route("mailing.index") }}" ><i class="fa  fa-envelope fa-fw"></i> Mailing</a>
                            @else
                            <a href="{{ route("mailing.index") }}" class="{{ starts_with( Request::url(),route("mailing.index")) ? 'active' : ''}}"><i class="fa  fa-envelope fa-fw"></i> Mailing</a>
                            @endif
                        </li>
                        
                        <li>
                            <a href="{{ route("mailinghistory.index2") }}" class="{{ starts_with( Request::url(),route("mailinghistory.index2")) ? 'active' : ''}}"><i class="fa  fa-repeat fa-fw"></i> Mailing History</a>
                            
                        </li>  

                        <li>
                            <a href="{{ route("mailinghistory.index") }}" class="{{ starts_with( Request::url(),route("mailinghistory.index")) ? 'active' : ''}}"><i class="fa  fa-repeat fa-fw"></i> Resend Queue</a>
                        </li>
                        <li class="{{ starts_with( Request::url(),route ('company.index')) ? 'active' : ''}} {{ starts_with( Request::url(),route ('workorderfields.index')) ? 'active' : ''}}  {{ starts_with( Request::url(),route ('ftp.index')) ? 'active' : ''}} {{ starts_with( Request::url(),route ('pricelist.index')) ? 'active' : ''}} {{ starts_with( Request::url(),route ('templates.index')) ? 'active' : ''}}{{ starts_with( Request::url(),route ('roles.index')) ? 'active' : ''}}{{ starts_with( Request::url(),route ('workordertypes.index')) ? 'active' : ''}}{{ starts_with( Request::url(),route ('attachmenttypes.index')) ? 'active' : ''}}{{ starts_with( Request::url(),route ('users.index')) ? 'active' : ''}} {{ starts_with( Request::url(),route ('serversftp.index')) ? 'active' : ''}} {{ starts_with( Request::url(),route ('mailingtype.index')) ? 'active' : ''}} {{ starts_with( Request::url(),route ('reminders.index')) ? 'active' : ''}} {{ starts_with( Request::url(),route ('fromemails.index')) ? 'active' : ''}} {{ starts_with( Request::url(),route ('adminemails.index')) ? 'active' : ''}} {{ starts_with( Request::url(),route ('sites.index')) ? 'active' : ''}} {{ starts_with( Request::url(),route ('subscriptionrate.edit')) ? 'active' : ''}}">
                            <a href="#"  aria-expanded="true"><i class="fa fa-sliders fa-fw"></i> Management<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level" aria-expanded="false">
                                <li>
                                    <a href="{{ route ('company.index')}}" class="{{ starts_with( Request::url(),route ('company.index')) ? 'active' : ''}}">Company Settings</a>
                                </li>
                                <li>
                                    <a href="{{ route ('workorderfields.index')}}" class="{{ starts_with( Request::url(),route ('workorderfields.index')) ? 'active' : ''}}">Work Order Fields</a>
                                </li>
                                <li>
                                    <a href="{{ route ('mailingtype.index')}}" class="{{ starts_with( Request::url(),route ('mailingtype.index')) ? 'active' : ''}}">Mailing Type Definitions</a>
                                </li>
                                <li>
                                    <a href="{{ route ('serversftp.index')}}" class="{{ starts_with( Request::url(),route ('serversftp.index')) ? 'active' : ''}}">FTP Servers</a>
                                </li>
                                <li>
                                    <a href="{{ route ('ftp.index')}}" class="{{ starts_with( Request::url(),route ('ftp.index')) ? 'active' : ''}}">FTP Locations</a>
                                </li>
                                 <li>
                                    <a href="{{ route ('templates.index')}}" class="{{ starts_with( Request::url(),route ('templates.index')) ? 'active' : ''}}">Default Billing Templates</a>
                                </li>
                                <li>
                                    <a href="{{ route ('pricelist.index')}}" class="{{ starts_with( Request::url(),route ('pricelist.index')) ? 'active' : ''}}">Price List</a>
                                </li>
                                 <li>
                                    <a href="{{ route ('roles.index')}}" class="{{ starts_with( Request::url(),route ('roles.index')) ? 'active' : ''}}">User Roles</a>
                                </li>
                                <li>
                                    <a href="{{ route ('users.index')}}" class="{{ starts_with( Request::url(),route ('users.index')) ? 'active' : ''}}">Admin Users</a>
                                </li>
                                 <li>
                                    <a href="{{ route ('workordertypes.index')}}" class="{{ starts_with( Request::url(),route ('workordertypes.index')) ? 'active' : ''}}">Work Order Types</a>
                                </li>
                                <li>
                                    <a href="{{ route ('attachmenttypes.index')}}" class="{{ starts_with( Request::url(),route ('attachmenttypes.index')) ? 'active' : ''}}">Attachments Types</a>
                                </li>
                                <li>
                                    <a href="{{ route ('reminders.index')}}" class="{{ starts_with( Request::url(),route ('reminders.index')) ? 'active' : ''}}">Reminders</a>
                                </li>
                                <li>
                                    <a href="{{ route ('fromemails.index')}}" class="{{ starts_with( Request::url(),route ('fromemails.index')) ? 'active' : ''}}">From Emails Setting</a>
                                </li>
                                <li>
                                    <a href="{{ route ('adminemails.index')}}" class="{{ starts_with( Request::url(),route ('adminemails.index')) ? 'active' : ''}}">Emails To Admin/Researcher</a>
                                </li>
                                <li>
                                    <a href="{{ route ('sites.index')}}" class="{{ starts_with( Request::url(),route ('sites.index')) ? 'active' : ''}}">Research Sites Setting</a>
                                </li>
                                <li>
                                    <a href="{{ route ('subscriptionrate.edit')}}" class="{{ starts_with( Request::url(),route ('subscriptionrate.edit')) ? 'active' : ''}}">Subscription Rate</a>
                                </li>
                                <li>
                                    <a href="{{ route ('folders.index')}}" class="{{ starts_with( Request::url(),route ('folders.index')) ? 'active' : ''}}">Reporting</a>
                                </li>
                                <!-- <li>
                                    <a href="route ('returnadresstpyes.index')}}" class=" starts_with( Request::url(),route ('returnadresstpyes.index')) ? 'active' : ''}}">Return Address Types</a>
                                </li> -->
                                <br>
                            </ul>
                        </li>
                        @endif
                  
                    </ul>
                </div>
                <!-- /.sidebar-collapse -->
            </div>
            <!-- /.navbar-static-side -->
        </nav>
