 <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="{{ route('researcher')}}"> {{ config('app.name', 'Laravel') }}</a>
            </div>
            <!-- /.navbar-header -->

            <ul class="nav navbar-top-links navbar-right">
                <li class="dropdown" id='app_notifications'>
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-envelope fa-fw"></i><span class="label label-danger label-as-badge menu-badge" id='messages-count'>{{ $count_notification == 0 ? '' : $count_notification }}</span> <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-messages" >

                        @foreach($notifications as $xn)
                                
                             <li id="{{$xn->id}}">
                         
                                <a class="meesage-link" data-id="{{$xn->id}}" data-url="{{  ( array_key_exists('url_researcher',$xn->data)) ? $xn->data['url_researcher'] : '#'  }}" href="#">
                                    <div>
                                        @if($xn->type =='App\Notifications\NewWorkNote')
                                        <strong>New Work Order Note</strong>
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
                        
                        <li>
                            <a href="{{ route("researcher")}}" class="{{ ends_with( Request::url(),route("researcher")) ? 'active' : ''}}"><i class="fa fa-dashboard fa-fw"></i>  Dashboard</a>
                        </li>
                        <li>
                            <a href="{{ route("hotcontacts.index")}}" class="{{ ends_with( Request::url(),route("hotcontacts.index")) || str_contains( Request::url(),'hot') ? 'active' : ''}}"><i class="fa fa-fire fa-fw"></i>  HotList</a>
                        </li>
                        
                        <li>
                            <a href="{{ route("jobs.index") }}" class="{{ starts_with( Request::url(),route("jobs.index")) ? 'active' : ''}}"><i class="fa fa-briefcase fa-fw"></i> Jobs</a>
                        </li>
                        <li>
                            <a href="{{ route("workorders.index") }}" class="{{ starts_with( Request::url(),route("workorders.index")) ? 'active' : ''}}"><i class="fa fa-gears  fa-fw"></i> Work Orders</a>
                        </li>
                        
                          
                        
                        
                  
                    </ul>
                </div>
                <!-- /.sidebar-collapse -->
            </div>
            <!-- /.navbar-static-side -->
        </nav>
