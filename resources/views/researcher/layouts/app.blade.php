<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>
    
    <!--  <link href="{{ asset('css/app.css') }}" rel="stylesheet">-->
    
    <link href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/metisMenu/metisMenu.min.css') }}" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="{{ asset('vendor/sb-admin/css/sb-admin-2.css') }}" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="{{ asset('vendor/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('/vendor/typeahead/css/typeaheadjs.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('/vendor/checkbox-b-flat/css/checkbox-b-flat.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('/css/search.css')}}" rel="stylesheet" type="text/css"/>
    
    <style>
        .label-as-badge {
            border-radius: 1em;
        }
        .menu-badge {
            position: absolute;
            top: 6px;
            left: 29px;
        }
       
    </style>
    @yield('css')

</head>
<body>
    <div id="wrapper">
    @yield('navigation')
    
   
    <div id="right-container">
    @yield('content')
    </div>
    
    
    <!-- Scripts -->
    </div>
    
    @yield('sidebar')
        <!--<script src="{{ asset('js/app.js') }}"></script>-->
        <script src="{{ asset('/vendor/jquery/jquery.min.js') }}"></script>
       
        <script src="{{ asset('/js/uppercase.js') }}"></script>
        <script src="{{ asset('/vendor/bootstrap/js/bootstrap.min.js') }}"></script>
        <script src="{{ asset('/vendor/metisMenu/metisMenu.min.js') }}"></script>
        <script src="{{ asset('/vendor/sb-admin/js/sb-admin-2.js') }}"></script>
        <script src="{{ asset('/vendor/typeahead/js/typeahead.bundle.min.js') }}" type="text/javascript"></script>
        <script src="{{ asset('/vendor/socket-io/js/socket.io.js') }}"></script>
        <script src="{{ asset('/vendor/laravelecho/echo.js') }}"></script>
        <script src="{{ asset('/vendor/moment/js/moment.min.js') }}"></script>
        <script src="{{ asset('/vendor/moment/js/moment-timezone-with-data.min.js') }}"></script>
        <script src="{{ asset('/vendor/caret/caret.js') }}"></script>
        <script src="{{ asset('/js/processnotifications.js') }}"></script>
        <script src="{{ asset('/js/sitesearch.js') }}"></script>
        
        
        <script>
            var search_url_loading = '{{route('search.loading')}}';
            var search_url_clients = '{{route('search.clients')}}';
            var search_url_associates = '{{route('search.associates')}}';
            var search_url_contacts = '{{route('search.contacts')}}';
            var search_url_jobs = '{{route('search.jobs')}}';
            var search_url_notes = '{{route('search.notes')}}';
            var search_url_attachments = '{{route('search.attachments')}}';
            var search_url_parties = '{{route('search.parties')}}';
            var module = { };
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            window.Echo = new Echo({
                broadcaster: 'socket.io',
                host: '{{env("ECHO_PROTOCOL","https")}}://{{env("ECHO_SERVER")}}:6001'
            });
           window.Echo.private('App.User.1')
                   .notification((notification) => {
                       ProcessNotification(notification);
                    });
           var remove_notification_url = '{{ url ('researcher/notification')}}'
           
            $(function () {
                
                
                $('input').each(function(){
                            var xname = $(this).attr("name");
                            var timeStampInMs = window.performance && window.performance.now && window.performance.timing && window.performance.timing.navigationStart ? window.performance.now() + window.performance.timing.navigationStart : Date.now();
                            $(this).attr("autocomplete","new-" + xname + timeStampInMs);
                          //$(this).attr("autocomplete","off");
                       });
           
            });

           
           
        </script>
    @yield('scripts')
    
</body>
</html>
