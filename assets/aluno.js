var turmaSelected;

$(function(){
    if ($('#turmas').length > 0)
        listarTurmas();

    if ($('#tarefas').length > 0)
        listarTarefas();

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

function deletarArquivo(idtarefa,path){
    $.ajax({
        method: "POST",
        url: base_url + "/AlunoTurma/deletarArquivo/" + idtarefa,
        data: {"path":path}
    }).done(function(resp) {
        window.location.reload();
    });
}