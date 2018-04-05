var turmaSelected;

$(function(){
    if ($('#turmas').length > 0)
        listarTurmas();

    if ($('#tarefas').length > 0)
        listarTarefas();

    if ($('#correcao').length > 0){
        startCorrectionMode();
        atualizarCorrecoes();
    }

        

    $('.modal').modal();
});

function entrarTurma(){
    $.ajax({
        method: "GET",
        url: base_url + "/AlunoTurma/entrar/" + $('#chave').val()
    }).done(function(resp) {
        window.location = base_url + 'AlunoTurma/' +  resp;
    });
}

  
function listarTurmas(){
    $.ajax({
        method: "GET",
        url: base_url + "/AlunoTurma/listar"
    }).done(function(resp) {
        $('#turmas').html(resp);
    });
}

function listarTarefas(){
    $.ajax({
        method: "GET",
        url: base_url + "/AlunoTurma/listarTarefas/" + $('#idturma').val()
    }).done(function(resp) {
        $('#tarefas').html(resp);
    });
}

function selectTurma(id){
    turmaSelected = id;
}

function sairTurma(){
    $.ajax({
        method: "POST",
        url: base_url + "/AlunoTurma/sair/" + $('#chave').val()
    }).done(function(resp) {
        listarTurmas();
    });
}

function deletarArquivo(idtarefa,idarquivo){
    $.ajax({
        method: "POST",
        url: base_url + "/AlunoTurma/deletarArquivo/"+idtarefa+"/"+idarquivo
    }).done(function(resp) {
        window.location.reload();
    });
}



function minimizeCorrecao(i){
    $('#corr'+i).hide('fast');
    $('#max'+i).show('fast');
}

function maximizeCorrecao(i){
    $('#corr'+i).show('fast');
    $('#max'+i).hide('fast');
}

function startCorrectionMode(){
    lineStart = "<span class='line'>";
    lineEnd = "</span>";
    corrTag = "<a class='codeObsAluno'></a>";
    //$('#correcao').html($('#correcao').html().replace("\n","q\n"));
    html = $('#correcao').html();
    html = html.replace(new RegExp("\n", 'g'), corrTag+"\n"+lineEnd+lineStart);
    $('#correcao').html(lineStart+html+corrTag+lineEnd);
    
    //Adiciona a propriedade de numero da linha
    $('.codeObsAluno').each(function(i, el){
        $(el).attr('linha',i+1);
    });
}

function atualizarCorrecoes(){
    $.ajax({
        method: "GET",
        url: base_url + "/Correcoes/listar/"+$('#idarquivo').val(),
        dataType:'json'
    }).done(function(resp) {
        correcoes = resp;
        //limpa todas
        $('.correcaoText, maximizeCorrecao').remove();
        //atualiza
        for (var i = 0; i < correcoes.length; i++){
            max = $("<img id='max"+i+"' style='display:none;' onclick='maximizeCorrecao("+i+")' class='maximizeCorrecao' src='"+base_url+"assets/imgs/max.png'>");
            $('[linha='+correcoes[i].linha+']').after(max);
            el = $("<span class='correcaoText' id='corr"+i+"' >"+correcoes[i].texto+"<img onclick='minimizeCorrecao("+i+")' class='minimizeCorrecao' src='"+base_url+"assets/imgs/min.png' ></span>");
            $('[linha='+correcoes[i].linha+']').after(el);
        }
    });
}