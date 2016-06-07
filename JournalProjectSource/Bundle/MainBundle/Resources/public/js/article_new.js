// Initialisation des variables des mots clés
maxManualKeyword        = 20;
maxSuggeredKeyword      = 10;
nbManualKeyword         = 0;

// Au chargement de la page
// Sert pour les evenement
$(document).ready(function() {


    // Savoir si c'est une édition ou un nouvel article
    typeArticle = $('#article_id').val();
    isEdit = (typeArticle === null || typeArticle === "") ? false : true;

    // Pour l'édition
    if(isEdit) {
        nbManualKeyword = $('#multi-field-wrapper > *').length;
        maxManualKeyword    = maxManualKeyword + maxSuggeredKeyword;
        maxSuggeredKeyword  = ( nbManualKeyword >= maxManualKeyword) ? maxManualKeyword - nbManualKeyword : maxSuggeredKeyword;

        // Cacher des mots-clés suggérer si le nombre de mots-clés est supérieur au maximum de mots-clés manuels
        hideSuggeredKeyword(maxSuggeredKeyword, nbManualKeyword, maxManualKeyword, maxSuggeredKeyword);
        hideManuelKeywordAddButton();
    }

    $("#keywordaddtext").keypress(function(e){
        if (e.which == 13){
            $("#keywordadd").trigger( "click" );
        }
    });

    $("#keywordadd").click(function(e) {
        $('#multi-field-wrapper').find('input').each(function(){
            $(this).removeClass( 'keyword_error' );
        });
        var word = $("#keywordaddtext").val();
        var w = $('#multi-field-wrapper').html();
        var nbSuggeredKeyword = $('#keyword_suggestions input:checked').length;
        var nbManualKeyword = $('#multi-field-wrapper > *').length;
        var numberOfKeywords = nbSuggeredKeyword + nbManualKeyword;
        var quit = false;
        $('#multi-field-wrapper').find('input').each(function(){
            if($(this).val().toLowerCase() == word.toLowerCase()){
                $(this).addClass( 'keyword_error' );
                quit = true;
            }
        });

        if(quit)
            return;

        if(word !== "") {
            if(nbManualKeyword < maxManualKeyword) {
                $('#minkeywordalert').remove();		//On enlève le message d'erreur à l'ajout d'un mot-clé
                is_favorite = false;
                $.ajax({
                    type: "POST",
                    url: $("#pathIsFavorite").val(),
                    data: {name : word},
                    cache: false,
                    success: function(is_favorite){
                        if(is_favorite == 1){
                            var img_path = $('#is_favorite_img_path').val();
                        }
                        else{
                            var img_path = $('#is_not_favorite_img_path').val();
                        }
                        manual_keyword_input_html = 
                            '<div class="multi-fields input-append">' +
                                '<div class="right-input"><img id="is_favorite" onclick="toggle_is_favorite(this);" src="' + img_path + '" value="' + $('#keywordaddtext').val() + '"></div>' +
                                '<div class="left-input"><input type="text" value="' + $('#keywordaddtext').val() + '" name="extra_fields[]" /><button class="keywordremove btn btn-danger" type="button" onclick="keyword_remove(this)"><i class="icon icon-remove"></i></button></div>' +
                            '</div>';
                        $('#multi-field-wrapper').prepend(manual_keyword_input_html);
                        $('#keywordaddtext').val('');
                    }
                });
            }

            $('.multi-fields').each(function() {
                $('.keywordremove').click(function() {
                    $(this).parent('.multi-fields').remove();
                    var nbManualKeyword2 = $('#multi-field-wrapper > *').length;
                    if(nbManualKeyword2 < maxManualKeyword) {
                        $("#keywordaddtext").prop('disabled', false);
                        $("#keywordadd").prop('disabled', false);

                        // Afficher un mots suggéré
                        hideSuggeredKeyword(isEdit, nbManualKeyword2, maxManualKeyword, maxSuggeredKeyword);
                    }
                });
            });

            // Cacher un mots suggéré
            hideSuggeredKeyword();


            if(nbManualKeyword == maxManualKeyword - 1) {
                $("#keywordaddtext").prop('disabled', true);
                $("#keywordadd").prop('disabled', true);
            }
        } else {
            $('#multi-field-wrapper').prepend('<div id="maxkeywordalert" class="alert alert-danger">Vous devez ajouter un mot-clé avant de l\'ajouter.</div>');
        }

        hideManuelKeywordAddButton();
    });

    $('#keyword_suggestions').change(function() {
        hideSuggeredKeyword();
        hideManuelKeywordAddButton();
    });
});

// MOTS CLÉS PERSONNALISÉS

/**
 * Cache les mots clés suggérés
 * @param number nombre de mots clés à cacher
 */
function hideSuggeredKeyword(isEdit, nbManualKeyword, maxManualKeyword, maxSuggeredKeyword) {
    for(j=0; j<maxSuggeredKeyword; j++) {
        $('.checkbox:eq(' + j + ')').show();
    }
    if(isEdit && nbManualKeyword > (maxManualKeyword - maxSuggeredKeyword)) {
        for(i=maxSuggeredKeyword; i>=(maxManualKeyword - nbManualKeyword); i--) {
            $('.checkbox:eq(' + i + ')').hide();
        }
    }
}

/**
 * Cache le formulaire d'ajout d'un mot clé manuel
 */
function hideManuelKeywordAddButton() {
    var nbManualKeyword = $('#multi-field-wrapper > *').length;
    if(nbManualKeyword >= (maxManualKeyword - maxSuggeredKeyword)){
        //On cache le champ de saisie si le nombre de mots-clés manuel est atteint
        $('#keywordboxadd').hide();
    } else {
        $('#keywordboxadd').show();
    }

    if(isEdit) {
        if(nbManualKeyword > (maxManualKeyword - maxSuggeredKeyword)) {
            x = maxManualKeyword;
        } else {
            x = maxSuggeredKeyword;
        }
    } else {
        x = maxSuggeredKeyword;
    }
    var nbSuggeredKeyword = $('#keyword_suggestions input:checked').length;
    if((nbSuggeredKeyword + nbManualKeyword) == x) {
        $("#parse").hide();
    } else {
        $("#parse").show();
    }
}

/**
 * Supprimer un mot clé manuel
 * @param button id du mot clé à supprimer
 */
function keyword_remove(button) {
    $(button).parent().parent('.multi-fields').remove();
    var nbManualKeyword2 = $('#multi-field-wrapper > *').length;
    if(nbManualKeyword2 < maxManualKeyword) {
        $("#keywordaddtext").prop('disabled', false);
        $("#keywordadd").prop('disabled', false);
    }

    // Afficher un mots suggéré
    hideSuggeredKeyword(isEdit, nbManualKeyword2 , maxManualKeyword, maxSuggeredKeyword);
    hideManuelKeywordAddButton();
}

// BINDING DES ÉVENEMENTS

function saveAndContinue (){

    if(checkDoublon()){
        return;
    }

    $('#minkeywordalert').remove();

    if($('#multi-field-wrapper').html().trim() == "" && $('#keyword_suggestions').find("[name='extra_fields[]']:checked").length == 0){

        //On définit le css de l'éventuel message d'erreur
        $('#multi-field-wrapper').css('width', ($('#keywordboxadd').width() + 20) + 'px');
        $('#multi-field-wrapper').css('max-height', '200px');
        $('#multi-field-wrapper').css('overflow-y', 'auto');

        $('#multi-field-wrapper').prepend('<div id="minkeywordalert" class="alert alert-danger">Un mot-clé minimum doit être renseigné pour pouvoir enregistrer l\'article et continuer</div>');
    }else{
        var DATA = $('#form_article').serializeArray();
        $.ajax({
            type: "POST",
            url: $("#pathSaveContinue").val(),
            data: DATA,
            cache: false,
            success: function(data){
                $('#create_form').html(data);
                refreshArticleList();
                $('#keyword_suggestions').html('');
                $('#multi-field-wrapper').html('');
                $('#keywordbox').hide();
                $('#nextarticle').hide();
            }
        });
    }
    return false;
}


function saveArticle(){
    if(checkDoublon()){
        return;
    }
    var DATA = $('#form_article').serializeArray();
    $.ajax({
        type: "POST",
        url: $("#pathSave").val(),
        data: DATA,
        cache: false,
        success: function(data){
            $('#create_form').html(data);
            refreshArticleList();
            $.ajax({
                type: "POST",
                url: $("#pathKeywordList").val(),
                data: {
                    'article_id' : $('#article_id').val()
                },
                cache: false,
                success: function(data){
                    $('#multi-field-wrapper').html(data);
                    // refreshArticleList();
                    $('#keyword_suggestions').html('');
                }
            });
        }
    });
    return false;
}


function deleteArticle(){

    if(confirm("Voulez-vous réellement supprimer cet article ?")){
        var DATA = 	{
            'article_id' : $('#article_id').val(),
            'newspaper_id' : $('#newspaper_id').val()
        };
        $.ajax({
            type: "POST",
            url: $("#pathDelete").val(),
            data: DATA,
            cache: false,
            success: function(data){
                $('#create_form').html(data);
                refreshArticleList();
                $('#multi-field-wrapper').replaceWith("");
                $('#keyword_suggestions').html('');
                $('#bottom').hide();
                $('#nextarticle').hide();
            }
        })
    }

    //return false;
}

function checkDoublon(){
    var a_keywords = new Array();
    $('#multi-field-wrapper').find('input').each(function(){
        a_keywords.push($(this));
        $(this).removeClass('keyword_error');
    });

    for(var i = 0, len = a_keywords.length; i < len; i++){
        for(var j = i, len = a_keywords.length; j < len-1; j++){
            console.log(a_keywords[i].val() + ' vs ' + a_keywords[j+1].val());
            if(a_keywords[i].val().toLowerCase() == a_keywords[j+1].val().toLowerCase()){
                a_keywords[j+1].addClass('keyword_error');
                a_keywords[i].addClass('keyword_error');
                return true;
            }
        }
    }
    return false;
}

function refreshArticleList(){
    var DATA = {
        'newspaper_id' : $('#newspaper_id').val()
    }
    $.ajax({
        type: "POST",
        url: $("#pathList").val(),
        data: DATA,
        cache: false,
        success: function(data){
            $('#article-list').html(data);
        }
    });
}

$("#is_finished").click(function(){

    var isFinished = $('#is_finished_value').val();

    if(isFinished == 0 && $('#multi-field-wrapper').html().trim() == "" && $('#keyword_suggestions').find("[name='extra_fields[]']:checked").length == 0){

        alert('Un mot-clé minimum doit être renseigné pour pouvoir terminer le journal');

    }else{

        var DATA = {
            is_finished : isFinished,
            newspaper_id : $('#newspaper_id').val()
        }
        $.ajax({
            type: "POST",
            url: $("#pathToggleIsFinished").val(),
            data: DATA,
            cache: false,
            success: function(data){
                //Si retour -1, pas de modification de css ni de valeur
                if(data == '1'){
                    $( "#is_finished" ).attr('src', $('#img_path').val() + "iconeBoutonVert.png");
                    $( "#is_finished_value" ).val(1);
                }
                else{
                    if(data == '0'){
                        $( "#is_finished" ).attr('src', $('#img_path').val() + "iconeBoutonRouge.png");
                        $( "#is_finished_value" ).val(0);
                    }
                    else{
                        if(data == '-1'){
                            alert('L\'article en cours ou l\'un des autres articles du journal n\'a pas de mot-clé enregistré.');
                        }
                    }
                }
            }
        });
        return false;
    }
});

function analyze_article(){
    $('#error_content').remove();
    if($('#tribuca_bundle_mainbundle_article_content').val() != ""){
        $("#bottom").show();
        $("#keywordbox").show();
        $("#loader").show();
        $("#nextarticle").show();
        var DATA = {
            'content' : $('#tribuca_bundle_mainbundle_article_content').val(),
            'newspaper_id' : $('#newspaper_id').val(),
            'article_id' : $('#article_id').val()
        }
        $.ajax({
            type: "POST",
            url: $("#pathParse").val(),
            data: DATA,
            cache: false,
            success: function(data){
                $('#keyword_suggestions').html(data);
                $("#loader").hide();
                hideSuggeredKeyword(isEdit, nbManualKeyword, maxManualKeyword, maxSuggeredKeyword)
            }
        });
        return false;
    }
    else {
        $('#tribuca_bundle_mainbundle_article_content').parent().append("<div id='error_content' class='alert alert-danger'>Le contenu de l'article ne peut être vide</div>");
    }
}

function inputchange() {
    var maxSuggeredKeyword  = 10;
    var s2 = $('#keyword_suggestions input:checked').length;
    if(s == maxSuggeredKeyword) {
        $("#parse").prop('disabled', true);
    } else {
        $("#parse").prop('disabled', false);
    }

    if (s2 > s) {
        s = s2;
        m--;
    } else {
        s = s2;
        m++;
    }
}

function toggle_is_favorite(clicked_img){
        var favorite_button =  $(clicked_img);
        var is_favorite = false;
        if ( $(clicked_img).attr('src') ==  $('#is_favorite_img_path').val()) {
            is_favorite = true;
        }
        var DATA = {
            is_favorite : is_favorite,
            name : $(clicked_img).attr('value'),
        }
        $.ajax({
            type: "POST",
            url: $('#pathFav').val(),
            data: DATA,
            cache: false,
            success: function(data){
                //Si retour -1, pas de modification de css ni de valeur
                if(parseInt(data) == 0){
                    favorite_button.attr('src', $('#is_not_favorite_img_path').val());
                }
                if(parseInt(data) == 1){
                    favorite_button.attr('src', $('#is_favorite_img_path').val());
                }
            }               
        });    
        return false;
        
    }