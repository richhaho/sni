/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
moment.tz.setDefault('America/New_York');

function ProcessNotification(notification){
    if($("ul.dropdown-messages li#" + notification.id).length == 0) {
        var  notification_type;
        var xurl;
        if (notification.type == "App\\Notifications\\NewJobNote") {
             notification_type = "New Job Note";
             
             
        }
        if (notification.type == "App\\Notifications\\NewWorkNote") {
             notification_type = "New Work Order Note";
        }
        
        if (notification.type == "App\\Notifications\\NewWorkOrder") {
             notification_type = "New Work Order";
        }
        
        if (notification.type == "App\\Notifications\\NewAttachment") {
             notification_type = "New Attachment";
        }

        if (notification.type == "App\\Notifications\\NewRelease") {
            notification_type = "New Release";
       }
        
        if (window.location.href.indexOf("/client/") > -1) {
            xurl= notification.url_client;
        } else {
            xurl= notification.url_admin;
        }

        var content = '<li>'
            content += '    <a class="meesage-link" data-id="' + notification.id + '" data-url="' + xurl + '"' + notification.id + '" href="#">'
            content += '    <div>'
            content += '        <strong>' +  notification_type + '</strong>'
            content += '        <span class="pull-right text-muted">'
            content += '           <em>' + moment(notification.message.entered_at).fromNow()  +'</em>'
            content += '        </span>'
            content += '    </div>'
            content += '    <div>' + notification.message.note + '</div>'
            content += '    </a>'
            content += '</li>';
        var separator = '<li class="divider"></li>';
        
        if ($("ul.dropdown-messages li").length >= 10 ) {
            $('ul.dropdown-messages li:last-child').remove();
        } else {
             $('#messages-count').html(TryParseInt($('#messages-count').html(),0) + 1);
        }
        if ($("ul.dropdown-messages li").length == 0 ) {
            $('ul.dropdown-messages').prepend(content);
        } else {
            $('ul.dropdown-messages').prepend(content+ separator);
        }
        $('#messages-count').fadeOut(300).fadeIn(300).fadeOut(300).fadeIn(300).fadeOut(300).fadeIn(300);
    }
}


function TryParseInt(str,defaultValue) {
     var retValue = defaultValue;
     if(str !== null) {
         if(str.length > 0) {
             if (!isNaN(str)) {
                 retValue = parseInt(str);
             }
         }
     }
     return retValue;
}

$( document ).ready(function() {
            
    $('ul.dropdown-messages').on('click','a', function(){
        
        var xid = $(this).data('id');
        var url = $(this).data('url');
        $.get( remove_notification_url + "/" + xid + "/delete", function( data) {
            if(data == "DELETED") {
                console.log(xid);
                if ( $('li#' + xid).is(':last-child')) {
                    $('li#' + xid).prev('li').remove();
                    $('li#' + xid).remove();
                } else {
                    $('li#' + xid).next('li').remove();
                    $('li#' + xid).remove();
                }
                var xcount = TryParseInt($('#messages-count').html(),0) - 1;
                if (xcount ==0 ) {
                    $('#messages-count').html('');
                } else {
                    $('#messages-count').html(xcount);
                }
            } else {
                console.log('error deleting notification');
            }
            
            window.location.replace(url);
            
        });
        
        
    });
    
});
 