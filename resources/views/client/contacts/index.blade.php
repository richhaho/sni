@extends('client.layouts.app')


@section('css')
 <link href="{{ asset('/vendor/tooltipster/css/tooltipster.bundle.min.css') }}" rel="stylesheet" type="text/css">

 
<style>
    .row.same-height {
        display: flex; 
        flex-wrap: wrap;
    }
    
    .row.same-height .thumbnail {
        height: 100%;
        margin-bottom: 0px;
        line-height: 1;
    }
    
    .row.same-height .thumbnail .controls {
        margin-top: 4px;
    }
    .row.same-height .thumbnail i.thumbnail-icon {
        color:gray;
    }
    
    .tooltip_templates { display: none; } 
    
   .associate-disabled  {
        background: #fefbb9!important;
    }
   .thumbnail p a{
     word-break: break-all;
   }
</style>

@endsection


@section('navigation')
    @include('client.navigation')
@endsection



@section('content')
       <div id="page-wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-xs-12">
                        <h1 class="page-header">My Contacts 
                            <div class="pull-right">
                            
                            <a class="btn btn-success" href="{{ route('client.contacts.create')}}"><i class="fa fa-plus"></i> Add Contact</a> 
                            </div>
                        </h1>
                        <br>
                        <div class="col-xs-12" id="filters-form">
                            {!! Form::open(['route' => 'client.contacts.setfilter', 'class'=>'form-inline'])!!}
                                  <div class="form-group">
                                    <label for="search">Search: </label>
                                    {!! Form::text('search',session('contact_filter.search'),['class'=>'form-control','placeholder' => 'Search...'])!!}
                                  </div>
                                  
                            <button class="btn btn-success" type="submit" ><i class="fa fa-search"></i> Search</button>
                            <a href="{{ route('client.contacts.resetfilter') }}" class="btn btn-danger">Clear</a>  
                            {!! Form::close() !!}
                        <br>
                        </div>
                       
                    </div>
                    @if (Session::has('message'))
                       <div class="col-xs-12 message-box">
                       <div class="alert alert-info">{{ Session::get('message') }}</div>
                       </div>
                   @endif
                  

                   @if(count($entities) > 0 || count($centity) > 0 ||count($new_entity) > 0)
                   <div class="col-xs-12">
                       <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                            @if(count($centity) > 0 )

                            @foreach($centity as $entity)   
                                <div class="panel panel-default">
                                  <div class="panel-heading" role="tab" id="heading{{$entity->id}}">
        
                                    <div class="panel-title ">
                                      <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse{{$entity->id}}" aria-expanded="true" aria-controls="collapse{{$entity->id}}">
                                          <i class="fa {{ ($loop->first) ? 'fa-minus-square': 'fa-plus-square'}}"></i> {{ $entity->firm_name}}
                                      </a>
                                        <div class="pull-right">
                                            @component('client.contacts.components.deletemodal')
                                                @slot('id') 
                                                    {{ $entity->id }}
                                                @endslot
                                                @slot('firm_name') 
                                                    {{ $entity->firm_name }}
                                                @endslot
                                                @slot('client_id') 
                                                    {{ $client_id }}
                                                @endslot
                                            @endcomponent 
                                            
                                            <?php
                                                $primary=0;
                                                foreach($entity->contacts as $contact){
                                                     
                                                    if ($contact->primary=='2'){
                                                        $primary=2;break;
                                                    }
                                                }
                                            ?>
                                            @if ($entity->hot_id==0)
                                              @if ($primary==2)
                                              <a class="btn btn-success btn-xs" disabled><i class="fa fa-pencil"></i> Edit </a>
                                              @else
                                              <a href="{{ route ('client.contacts.edit',$entity->id)}}" class="btn btn-success btn-xs"><i class="fa fa-pencil"></i> Edit </a>
                                              @endif
                                            @endif
                                            
                                      </div>
        
                                    </div>
        
        
                                  </div>
                                  <div id="collapse{{$entity->id}}" class="panel-collapse collapse {{ ($loop->first) ? 'in': ''}}" role="tabpanel" aria-labelledby="heading{{$entity->id}}">
                                    <div class="panel-body">
                                        <div class="row same-height" >
                                            @foreach($entity->contacts ()->orderBy('primary','desc')->orderBy('last_name','asc')->get() as $contact) 
        
                                                @include('client.contacts.components.contacticon')
        
        
                                            @endforeach
        
                                            <div class="col-xs-12 col-sm-6 col-md-2" >
                                                <div class="thumbnail text-center"  >
                                                    <a href="{{ route('client.associates.create',$entity->id) }}">
                                                    <i class="fa fa-user-plus fa-5x thumbnail-icon"></i>
                                                    </a>
                                                </div>
                                            </div>
        
                                        </div>
                                    </div>
                                  </div>
                                </div>
                            @endforeach
                           
                           @endif
                           @if(count($new_entity) > 0 )

                            @foreach($new_entity as $entity)   
                                <div class="panel panel-default">
                                  <div class="panel-heading" role="tab" id="heading{{$entity->id}}">
        
                                    <div class="panel-title ">
                                      <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse{{$entity->id}}" aria-expanded="true" aria-controls="collapse{{$entity->id}}">
                                          <i class="fa {{ ($loop->first) ? 'fa-minus-square': 'fa-plus-square'}}"></i> {{ $entity->firm_name}}
                                      </a>
                                        <div class="pull-right">
                                            @component('client.contacts.components.deletemodal')
                                                @slot('id') 
                                                    {{ $entity->id }}
                                                @endslot
                                                @slot('firm_name') 
                                                    {{ $entity->firm_name }}
                                                @endslot
                                                @slot('client_id') 
                                                    {{ $client_id }}
                                                @endslot
                                            @endcomponent 
                                            
                                            <?php
                                                $primary=0;
                                                foreach($entity->contacts as $contact){
                                                     
                                                    if ($contact->primary=='2'){
                                                        $primary=2;break;
                                                    }
                                                }
                                            ?>
                                            @if ($entity->hot_id==0)
                                              @if ($primary==2)
                                              <a class="btn btn-success btn-xs" disabled><i class="fa fa-pencil"></i> Edit </a>
                                              @else
                                              <a href="{{ route ('client.contacts.edit',$entity->id)}}" class="btn btn-success btn-xs"><i class="fa fa-pencil"></i> Edit </a>
                                              @endif
                                            @endif
                                            
                                      </div>
        
                                    </div>
        
        
                                  </div>
                                  <div id="collapse{{$entity->id}}" class="panel-collapse collapse {{ ($loop->first) ? 'in': ''}}" role="tabpanel" aria-labelledby="heading{{$entity->id}}">
                                    <div class="panel-body">
                                        <div class="row same-height" >
                                            @foreach($entity->contacts ()->orderBy('primary','desc')->orderBy('last_name','asc')->get() as $contact) 
        
                                                @include('client.contacts.components.contacticon')
        
        
                                            @endforeach
        
                                            <div class="col-xs-12 col-sm-6 col-md-2" >
                                                <div class="thumbnail text-center"  >
                                                    <a href="{{ route('client.associates.create',$entity->id) }}">
                                                    <i class="fa fa-user-plus fa-5x thumbnail-icon"></i>
                                                    </a>
                                                </div>
                                            </div>
        
                                        </div>
                                    </div>
                                  </div>
                                </div>
                            @endforeach
                           
                           @endif
                       @foreach($entities as $entity)   
                           <div class="panel panel-default">
                             <div class="panel-heading" role="tab" id="heading{{$entity->id}}">

                               <div class="panel-title ">
                                 <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse{{$entity->id}}" aria-expanded="true" aria-controls="collapse{{$entity->id}}">
                                     <i class="fa {{ ($loop->first) ? 'fa-minus-square': 'fa-plus-square'}}"></i> {{ $entity->firm_name}}
                                 </a>
                                   <div class="pull-right">
                                       @component('client.contacts.components.deletemodal')
                                           @slot('id') 
                                               {{ $entity->id }}
                                           @endslot
                                           @slot('firm_name') 
                                               {{ $entity->firm_name }}
                                           @endslot
                                           @slot('client_id') 
                                               {{ $client_id }}
                                           @endslot
                                       @endcomponent 
                                       @if ($entity->hot_id==0)
                                       <a href="{{ route ('client.contacts.edit',$entity->id)}}" class="btn btn-success btn-xs"><i class="fa fa-pencil"></i> Edit </a>
                                       @endif
                                 </div>

                               </div>


                             </div>
                             <div id="collapse{{$entity->id}}" class="panel-collapse collapse {{ ($loop->first) ? 'in': ''}}" role="tabpanel" aria-labelledby="heading{{$entity->id}}">
                               <div class="panel-body">
                                   <div class="row same-height" >
                                       @foreach($entity->contacts ()->orderBy('primary','desc')->orderBy('last_name','asc')->get() as $contact) 

                                           @include('client.contacts.components.contacticon')


                                       @endforeach

                                       <div class="col-xs-12 col-sm-6 col-md-2" >
                                           <div class="thumbnail text-center"  >
                                               <a href="{{ route('client.associates.create',$entity->id) }}">
                                               <i class="fa fa-user-plus fa-5x thumbnail-icon"></i>
                                               </a>
                                           </div>
                                       </div>

                                   </div>
                               </div>
                             </div>
                           </div>
                       @endforeach
                       </div>
                   </div>
                   <div class="col-xs-12 text-center">
                            {{ $entities->links() }}
                   </div>
                   @else
                   <div class="col-xs-12">
                       <h5>No Contacts found</h5>
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
<script src="{{ asset('/vendor/tooltipster/js/tooltipster.bundle.min.js') }}" type="text/javascript"></script>


<script>
$(function () {
    $(".message-box").fadeTo(6000, 500).slideUp(500, function(){
        $(".message-box").slideUp(500).remove();
    });
    
    $('[data-toggle="tooltip"]').tooltip();
    $('.tooltipster').tooltipster();
    
    var anchor = window.location.hash;
    //console.log(anchor);
    if (anchor.length >0 ) {
        $(".collapse").collapse('hide');
        $(anchor).collapse('show'); 
    }


    $('.collapse').on('shown.bs.collapse', function(){
        $(this).parent().find("i.fa-plus-square").removeClass("fa-plus-square").addClass("fa-minus-square");
    }).on('hidden.bs.collapse', function(){
        $(this).parent().find(".fa-minus-square").removeClass("fa-minus-square").addClass("fa-plus-square");
    });
  
  
});
</script>
    
@endsection


