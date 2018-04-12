var correcoes;

$(function(){

    $('.modal').modal({
        ready:function(){
            $('#texto').focus();
        },
        complete:function(){
            $('#idcorrecao, #linha, #texto').val("");
        }
    });

    startCorrectionMode();

    atualizarCorrecoes();


    $("#formCorrecoes").validate({
        rules: {
            texto: {
				required: true
            }
        },
        //For custom messages
        messages: {
            texto: {
				required: "Digite alguma observação."
            }
        },
        errorElement : 'div',
        errorPlacement: function(error, element) {
          var placement = $(element).data('error');
          if (placement) {
            $(placement).append(error)
          } else {
            error.insertAfter(element);
          }
        }
     });
});

function setLinha(el){
    ln = $(el).attr('linha');
    $('#linha').val(ln);

    for (var i = 0; i < correcoes.length; i++){
        if (correcoes[i].linha == ln){
            $('#texto').val(correcoes[i].texto);
            $('#idcorrecao').val(correcoes[i].idcorrecao);
        }
    }
}

function startCorrectionMode(){
    lineStart = "<span class='line'>";
    lineEnd = "</span>";
    corrTag = "<a class='codeObs modal-trigger' onclick='setLinha(this)' href='#cadObs'></a>";
    //$('#correcao').html($('#correcao').html().replace("\n","q\n"));
    html = $('#correcao').html();
    html = html.replace(new RegExp("\n", 'g'), corrTag+"\n"+lineEnd+lineStart);
    $('#correcao').html(lineStart+html+corrTag+lineEnd);
    
    //Adiciona a propriedade de numero da linha
    $('.codeObs').each(function(i, el){
        $(el).attr('linha',i+1);
    });
}

function salvarCorrecao(){
    if (!$("#formCorrecoes").valid()){
        return;
    }
    $.ajax({
        method: "POST",
        url: base_url + "/Correcoes/salvar/",
        data:$('#formCorrecoes').serialize()
    }).done(function(resp) {
        atualizarCorrecoes();
    });

    $('#cadObs').modal('close');
}

function excluirCorrecao(){
    $.ajax({
        method: "POST",
        url: base_url + "/Correcoes/excluir/"+$('#idcorrecao').val()
    }).done(function(resp) {
        atualizarCorrecoes();
    });

    $('#cadObs').modal('close');
}

function minimizeCorrecao(i){
    $('#corr'+i).hide('fast');
    $('#max'+i).show('fast');
}

function maximizeCorrecao(i){
    $('#corr'+i).show('fast');
    $('#max'+i).hide('fast');
}

function atualizarCorrecoes(){
    $.ajax({
        method: "GET",
        url: base_url + "/Correcoes/listar/"+$('#idarquivo').val(),
        dataType:'json'
    }).done(function(resp) {
        correcoes = resp;
        //limpa todas
        $('.correcaoText, .maximizeCorrecao').remove();
        //atualiza
        for (var i = 0; i < correcoes.length; i++){
            max = $("<img id='max"+i+"' style='display:none;' onclick='maximizeCorrecao("+i+")' class='maximizeCorrecao' src='"+base_url+"assets/imgs/max.png'>");
            $('[linha='+correcoes[i].linha+']').after(max);
            el = $("<span class='correcaoText' id='corr"+i+"' >"+correcoes[i].texto+"<img onclick='minimizeCorrecao("+i+")' class='minimizeCorrecao' src='"+base_url+"assets/imgs/min.png' ></span>");
            $('[linha='+correcoes[i].linha+']').after(el);
        }
    });
}