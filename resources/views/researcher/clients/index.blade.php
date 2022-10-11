@extends('researcher.layouts.app')

@section('navigation')
    @include('researcher.navigation')
@endsection

@section('css')
<style>
 #filters-form {
        margin-bottom: 15px;
        margin-top: 15px;
    }
    .client-disabled  {
        background: #fefbb9!important;
    }
</style>
@endsection

@section('content')
            <div id="page-wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-xs-12">
                        <h1 class="page-header">Clients  
                            <a class="btn btn-success pull-right" href="{{ route('clients.create')}}"><i class="fa fa-plus"></i> Add Client</a>
                        </h1>
                       
                    </div>
                           <div class="col-xs-12" id="filters-form">
                            {!! Form::open(['route' => 'clients.setfilter', 'class'=>'form-inline'])!!}
                                  <div class="form-group">
                                    <label for="search">Search: </label>
                                    {!! Form::text('search',session('client_filter.search'),['class'=>'form-control','placeholder' => 'Search...'])!!}
                                  </div>
                                
                            <button class="btn btn-success" type="submit" ><i class="fa fa-search"></i> Search</button>
                             <a href="{{ route('clients.resetfilter') }}" class="btn btn-danger">Reset</a>
                            {!! Form::close() !!}
                           </div>
                        
                    
                         @if (Session::has('message'))
                            <div class="col-xs-12 message-box">
                            <div class="alert alert-info">{{ Session::get('message') }}</div>
                            </div>
                        @endif
                    
                        
                        @if(count($clients) > 0 )
                        <div class="col-xs-12">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Account Number</th>
                                    <th>Company Name</th>
                                    <th>Full Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th class="col-xs-1"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($clients as $client)
                                <tr class="{{ ($client->status == 3 ) ? 'client-disabled' : '' }}">
                                    <td> {{ $client->id }}</td>
                                    <td> {{ $client->company_name }}</td>
                                    <td> {{ $client->full_name }}</td>
                                    <td> <a href="mailto:{{ $client->email }}">{{ $client->email }}</a></td>
                                    <td> {{ $client->phone }}</td>
                                    <td>
                                    <div class="btn-group pull-right">
                                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-cogs"></i> Actions <span class="caret"></span>
                                    </button>
                                    
                                      @component('researcher.clients.components.deletemodal')
                                        @slot('id') 
                                            {{ $client->id }}
                                        @endslot
                                        @slot('client_name') 
                                            {{ $client->full_name }}
                                        @endslot
                                    @endcomponent
                                    <li role="separator" class="divider"></li>
                                    @if($client->status == 3)
                                        <li><a href="{{ route('clients.enable',$client->id)  }}?page={{$clients->currentPage()}}"><i class="fa  fa-check"></i> Enable</a></li>
                                    @else
                                        <li><a href="{{ route('clients.disable',$client->id)  }}?page={{$clients->currentPage()}}"><i class="fa  fa-ban"></i> Disable</a></li>
                                    @endif
                                    <li role="separator" class="divider"></li>
                                    <li><a href="{{ route('clients.edit',$client->id) }}"><i class="fa fa-pencil"></i> Edit</a></li>
                                    <li><a href="{{ route('contacts.index',$client->id)  }}"><i class="fa fa-address-card-o"></i> Manage Contacts</a></li>
                                    <li><a href="{{ route('clientusers.index',$client->id)  }}"><i class="fa fa-user"></i> Manage Users</a></li>
                                    <li><a href="{{ route('jobs.setfilter') .'?resetfilter=true&client_filter='.$client->id  }}"><i class="fa fa-eye"></i> View Jobs</a></li>
                                    <li><a href="{{ route('workorders.setfilter') . '?resetfilter=true&client_filter=' . $client->id }}"><i class="fa fa-eye"></i> View Work Orders</a></li>
                                    <li><a href="{{ route('client.templates.index',$client->id)  }}"><i class="fa  fa-bar-chart-o fa-fw"></i> Manage Client Billing Templates</a></li>
           
                                    </ul>
                                      </div>

                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        </div>
                        <div class="col-xs-12 text-center">
                            {{ $clients->links() }}
                        </div>
                        @else
                        <div class="col-xs-12">
                            <h5>No Clients found</h5>
                        </div>
                        @endif
                    
                    <!-- /.col-lg-12 -->
                </div>
                <!-- /.row -->
            </div>
            <!-- /.container-fluid -->
        </div>
@endsection

@section('scripts')
<script>
$(function () {
    $(".message-box").fadeTo(6000, 500).slideUp(500, function(){
        $(".message-box").slideUp(500).remove();
    });
});
</script>
@endsection