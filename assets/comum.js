$(function(){
    $("#formPerfil").validate({
        rules: {
            nome: {
                required: true,
                minlength: 5
            },
            email: {
                required: true,
                email:true
            },
            senha: {
				minlength: 6
			},
			resenha: {
				minlength: 6,
				equalTo: "#senha"
			}
        },
        //For custom messages
        messages: {
            nome:{
                required: "Quem é você?",
                minlength: "Isso não é um nome."
            },
            email:{
                required: "Qual seu e-mail?",
                email: "Isso não é um e-mail de verdade."
            },
            senha:{
                required: "Você vai precisar de uma senha.",
                minlength: "Só isso? Digite pelo menos 6 caracteres."
            },
            resenha:{
                required: "Confira a senha que você digitou.",
                minlength: "Ainda não tem os 6 caracteres.",
                equalTo: "As duas senhas têm que ser iguais."
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