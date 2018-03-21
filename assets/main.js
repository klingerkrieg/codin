

  $(document).ready(function(){
    $('.modal').modal();

    listarTurmas();
  });

  
function listarTurmas(){
    $.ajax({
        method: "GET",
        url: base_url + "/turmas/listar"
    }).done(function(resp) {
        $('#turmas').html(resp);
    });
}

function salvarTurma(){
    
    $.ajax({
        method: "POST",
        url: base_url + "/turmas/salvar",
        data: $('#carTurmaForm').serialize()
    }).done(function(resp) {
        $('#turmas').html(resp);
    });

    
  $('#cadTurma').modal('close');
  
}
