

$(function(){
    if ($('#turmas').length > 0)
        listarTurmas();

    if ($('#tarefas').length > 0)
        listarTarefas();
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