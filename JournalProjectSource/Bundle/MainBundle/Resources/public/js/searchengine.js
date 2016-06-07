$(document).ready( function() {
    $ = jQuery;
    $( "#advanced_option" ).accordion({
        collapsible: true,
        active: false,
    });
    // $("#advanced_option").hide();
    $('#results_search').hide();

    // $("#showHideOptions").click(function() {
    //     var optionsIsVisible = $("#advanced_option").is(':visible');

    //     if(optionsIsVisible) {
    //         $("#advanced_option").hide();
    //         $("#showHideOptions").html('<i class="icon-chevron-down"></i>');
    //     } else {
    //         $("#advanced_option").show();
    //         $("#showHideOptions").html('<i class="icon-chevron-up"></i>');

    //     }
    // });

    $('#clearForm').click(function() {
        $('#appendedInputSearch').val('');
        $('#appendedInputMonth').val('');
        $('#appendedInputYear').val('');
        $('#appendedInputNum').val('');
        $('#appendedInputAuthor').val('');
        $('#orderBy').val('');
    });

    $("#appendedInputSearch").keypress(function(e){
        if (e.which == 13){
            $("#research").trigger( "click" );
        }
    });

    $("#research").click(function() {
        $('#result_table').html('<tr><th class="span2">Date</th><th class="span6">Titre</th><th class="span1">Page</th><th class="span2">Auteur</th><th class="span1">Numéro</th></tr>');


        var keywords = $('#appendedInputSearch').val();
        var getValues = "keywords=" + keywords;

        var mois     = $('#appendedInputMonth').val();
        var annee    = $('#appendedInputYear').val();
        var num = $('#appendedInputNum').val();
        var auteur = $('#appendedInputAuthor').val();
        var orderBy = $('#orderBy').val();

        if(mois.length > 0) getValues += "&month=" + mois;
        if(annee.length > 0) getValues += "&year=" + annee;
        if(num.length > 0) getValues += "&numero=" + num;
        if(auteur.length > 0) getValues += "&author=" + auteur;
        if(orderBy.length > 0) getValues += "&orderBy=" + orderBy;

        var path_search = $("#path_search").val();
        if(keywords.length > 0) {
            $.ajax({
                type: "GET",
                url: path_search,
                data: getValues,
                cache: false,
                success: function(data) {
                    $('#results_search').show();
                    console.log(data);
                    if(data.length > 0) {
                        for(var article in data) {

                            $('#result_table').append('<tr><td class="span2">' + data[article].date + '</td><td class="span6"><a target="blank" href="../' + data[article].link +'#page='+data[article].page+'">'+ data[article].title + '</a></td><td class="span1 textBolds">' + data[article].page + '</td><td class="span2">' + data[article].author + '</td><td class="span1">' + data[article].numero +'</td></tr>');
                        }

                    }
                }
            });
        }
    });
});