var tarefaSelected;

$.extend($.fn.pickadate.defaults, {
    monthsFull: [ 'Janeiro', 'Fevereiro', 'Marco', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro' , 'Dezembro' ],
    monthsShort: [ 'Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez' ],
    weekdaysShort: [ 'Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sab' ],
    weekdaysLetter: [ 'D', 'S', 'T', 'Q', 'Q', 'S', 'S' ],
    today: 'Hoje',
    clear: 'Limpar',
    formatSubmit: 'yyyy/mm/dd',
    format : 'dd/mm/yyyy'
  })


  $(document).ready(function(){
    $('.modal').modal();

    listarTurmas();
    listarTarefas();

    $('.datepicker').pickadate({
        selectMonths: true, // Creates a dropdown to control month
        selectYears: 2, // Creates a dropdown of 15 years to control year,
        today: 'Hoje',
        clear: 'Limpar',
        close: 'Ok',
        closeOnSelect: true // Close upon selecting a date,
    });

    
    $('.timepicker').pickatime({
        default: 'now', // Set default time: 'now', '1:30AM', '16:30'
        fromnow: 0,       // set default time to * milliseconds from now (using with default = 'now')
        twelvehour: false, // Use AM/PM or 24-hour format
        donetext: 'Ok', // text for done-button
        cleartext: 'Limpar', // text for clear-button
        canceltext: 'Cancelar', // Text for cancel-button
        autoclose: true, // automatic close timepicker
        ampmclickable: false, // make AM PM clickable
        format: 'dd/mm/yy',
        formatSubmit: "yyyy-mm-dd",
        min: new Date(new Date().getFullYear() - 1, 0, 1),
        aftershow: function(){} //Function for after opening timepicker
    });

    $('textarea').characterCounter();
        
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

function salvarTarefa(){
    $.ajax({
        method: "POST",
        url: base_url + "/tarefas/salvar",
        data: new FormData($('#cadTarefaForm')[0]),
        contentType: false,
        processData: false
    }).done(function(resp) {
        listarTarefas();
    });

    
  $('#cadTarefa').modal('close');
}


function selectTarefa(id){
    tarefaSelected = id;
}

function excluirTarefa(){
    $.ajax({
        method: "POST",
        url: base_url + "/tarefas/excluir/"+tarefaSelected
    }).done(function(resp) {
        listarTarefas();
    });
}

function listarTarefas(){
    $.ajax({
        method: "GET",
        url: base_url + "/tarefas/listar/"+$('#idturma').val()
    }).done(function(resp) {
        $('#tarefas').html(resp);
        $('#confirmDeleteTarefa').modal('close');
    });
}

function editarTarefa(id){
    $.ajax({
        method: "GET",
        url: base_url + "/tarefas/get/"+id,
        dataType: 'json'
    }).done(function(resp) {
        $('#cadTarefa input,#cadTarefa textarea').each(function(k,el){
            $(el).val(resp[el.id]);
        });
        Materialize.updateTextFields();
    });
}