var start_typing = false;
var clients_results = ""
var contacts_results = ""
var associates_results = ""
var jobs_results = ""
var notes_results = ""
var attachments_results = ""
var parties_results = ""
$( document ).ready(function() {
    $('#searchbox').on('keypress', function(e) {
        
        var content = $(this).val();
        if(e.which == 13) {
            if(start_typing) {
                doSearch(content);
            } else {
                if(content.length > 0) {
                    prepareSearchEngine();
                    doSearch(content);
                }
            }
        } else {
            if(content.length > 0) {
                if(start_typing) {

                } else {
                    start_typing = true;
                    prepareSearchEngine();
                }
            } else {
                if(content.length == 0) {
                    start_typing = false;
                }
            }
        }
    });

    $('#searchbutton').on('click', function() {
        var content = $('#searchbox').val();
        if(start_typing) {
            doSearch(content);
        } else {
            if(content.length > 0) {
                prepareSearchEngine();
                doSearch(content);
            }
        }
    });
});

function prepareSearchEngine() {
   
   $('#right-container').load(search_url_loading);
}


function doSearch(search_term) {
    $('.loading-messages').removeClass('hidden');
    $('#firstline').html('Searching...')
    $('#secondline').html('Looking into the Clients records')
    $.post(search_url_clients,{ search: search_term}, function(data) {
        clients_results = data;
        $('#client-results').html(clients_results);
        $('#clients-count').html($('#client_result_count').val());
        $('#secondline').html('Looking into the contacts records');
        $.post(search_url_contacts,{ search: search_term}, function(data) {
            contacts_results = data;
            $('#contacts-results').html(contacts_results);
            $('#contacts-count').html($('#contacts_result_count').val());
             $('#secondline').html('Looking into the associates records');
            $.post(search_url_associates,{ search: search_term}, function(data) {
                associates_results = data;
                $('#associates-results').html(associates_results);
                $('#associates-count').html($('#associates_result_count').val());
                $('#secondline').html('Looking into the associates records');
                $.post(search_url_jobs,{ search: search_term}, function(data) {
                    jobs_results = data;
                    $('#jobs-results').html(jobs_results);
                    $('#jobs-count').html($('#jobs_result_count').val());
                    $('#secondline').html('Looking into the notes records');
                    $.post(search_url_notes,{ search: search_term}, function(data) {
                        notes_results = data;
                        $('#notes-results').html(notes_results);
                        $('#notes-count').html($('#notes_result_count').val());
                         $('#secondline').html('Looking into the attachments records');
                        $.post(search_url_attachments,{ search: search_term}, function(data) {
                            attachments_results = data;
                            $('#attachments-results').html(attachments_results);
                            $('#attachments-count').html($('#attachments_result_count').val());
                             $('#secondline').html('Looking into the job parties records');
                             $.post(search_url_parties,{ search: search_term}, function(data) {
                                parties_results = data;
                                $('#parties-results').html(parties_results);
                                $('#parties-count').html($('#parties_result_count').val());
                                $('.search-results-conatiner').removeClass('hidden');
                                $('.loading-messages').addClass('hidden');
                            });
                        });
                    });
                });
            });
        });
        
        
    });
}