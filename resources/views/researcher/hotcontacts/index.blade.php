@extends('researcher.layouts.app')


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
    
    .hotassociate-disabled  {
        background: #fefbb9!important;
    }
   
</style>

@endsection


@section('navigation')
    @include('researcher.navigation')
@endsection



@section('content')
            <div id="page-wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-xs-12">
                        <h1 class="page-header">Contacts HotList
                            <div class="pull-right">
                            <a class="btn btn-success " href="{{ route('hotcontacts.create')}}"><i class="fa fa-plus"></i> Add Contact</a> 
                            </div>
                        </h1>
                       
                    </div>
                         @if (Session::has('message'))
                            <div class="col-xs-12 message-box">
                            <div class="alert alert-info">{{ Session::get('message') }}</div>
                            </div>
                        @endif
                    
                        
                        @if(count($entities) > 0 )
                            <div class="col-xs-12">
                                <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                                @foreach($entities as $entity)   
                                    <div class="panel panel-default">
                                      <div class="panel-heading" role="tab" id="heading{{$entity->id}}">





                                        <div class="panel-title ">
                                          <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse{{$entity->id}}" aria-expanded="true" aria-controls="collapse{{$entity->id}}">
                                              <i class="fa {{ ($loop->first) ? 'fa-minus-square': 'fa-plus-square'}}"></i> {{ $entity->firm_name}}
                                          </a>
                                            <div class="pull-right">
                                                @component('researcher.hotcontacts.components.deletemodal')
                                                    @slot('id') 
                                                        {{ $entity->id }}
                                                    @endslot
                                                    @slot('firm_name') 
                                                        {{ $entity->firm_name }}
                                                    @endslot

                                                @endcomponent 
                                                <a href="{{ route ('hotcontacts.edit',[$entity->id])}}" class="btn btn-success btn-xs"><i class="fa fa-pencil"></i> Edit</a>
                                                
                                          </div>

                                        </div>


                                      </div>
                                      <div id="collapse{{$entity->id}}" class="panel-collapse collapse {{ ($loop->first) ? 'in': ''}}" role="tabpanel" aria-labelledby="heading{{$entity->id}}">
                                        <div class="panel-body">
                                            <div class="row same-height" >
                                                @foreach($entity->contacts()->orderBy('primary','desc')->orderBy('last_name','asc')->get() as $contact) 

                                                    @include('researcher.hotcontacts.components.contacticon')


                                                @endforeach

                                                <div class="col-xs-12 col-sm-6 col-md-2" >
                                                    <div class="thumbnail text-center"  >
                                                        <a href="{{ route('hotassociates.create',$entity->id) }}">
                                                        <i class="fa fa-user-plus fa-5x thumbnail-icon"></i>
                                                        <h4>New Associate</h4>
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


