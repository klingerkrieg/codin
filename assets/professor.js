var tarefaSelected;
var turmaSelected;

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
    $('.modal').modal({
        ready:function(){
            $('#titulo').focus();
            $('#nome').focus();
        },
    });

    if ($('#turmas').length > 0)
        listarTurmas();
    if ($('#tarefas').length > 0)
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



    $("#cadTarefaForm").validate({
        rules: {
            titulo: {
				required: true,
                minlength: 5
            },
            data: {
                minlength: 10
            },
            hora: {
                minlength: 5
            }
        },
        //For custom messages
        messages: {
            titulo: {
				required: "Digite pelo menos o título da tarefa.",
                minlength: "Isso lá é título."
            },
            data: {
                minlength: "Digite no formato dd/mm/yyyy."
            },
            hora: {
                minlength: "Digite no formato hh:mm."
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

  
function listarTurmas(){
    $.ajax({
        method: "GET",
        url: base_url + "/turmas/listar"
    }).done(function(resp) {
        $('#turmas').html(resp);
    });
}

function salvarTurma(){
    if ($('#cadTurmaForm').valid() == false){
        return;
    }

    $.ajax({
        method: "POST",
        url: base_url + "/turmas/salvar",
        data: $('#cadTurmaForm').serialize()
    }).done(function(resp) {
        listarTurmas();
    });

    
    $('#cadTurma').modal('close');

  
}


function editarTurma(id){
    $.ajax({
        method: "GET",
        url: base_url + "/turmas/get/"+id,
        dataType: 'json'
    }).done(function(resp) {
        $('#cadTurma input').each(function(k,el){
            $(el).val(resp[el.id]);
        });
        Materialize.updateTextFields();
    });
}


function selectTurma(id){
    turmaSelected = id;
}

function excluirTurma(){
    $.ajax({
        method: "POST",
        url: base_url + "/turmas/excluir/"+turmaSelected
    }).done(function(resp) {
        listarTurmas();
        $('#confirmDeleteTurma').modal('close');
    });
}

function salvarTarefa(){
    if ($('#cadTarefaForm').valid() == false){
        return;
    }

    $.ajax({
        method: "POST",
        url: base_url + "/tarefas/salvar",
        data: new FormData($('#cadTarefaForm')[0]),
        contentType: false,
        processData: false
    }).done(function(resp) {
        if ($('#tarefas').length > 0){
            listarTarefas();
        } else {
            window.location.reload();
        }
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
        if ($('#tarefas').length > 0){
            listarTarefas();
        } else {
            window.location = base_url + "/turmas/index/" + resp;
        }
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

function salvarNota(idtarefa, idaluno, el){
    el = $(el);
    nota = el.val().replace(",",".");
    el.val(nota);
    if (nota < 0 || nota > 100 || isNaN(nota)){
        el.val("");
        return;
    }
    
    el.addClass('orange');
    el.removeClass('light-green');
    
    $.ajax({
        method: "POST",
        url: base_url + "/tarefas/salvarNota/"+idtarefa+"/"+idaluno,
        data: {'nota':el.val()}
    }).done(function(resp) {
        if (resp == 'ok'){        
            el.addClass('light-green');
            el.removeClass('orange');
        }
    });
}