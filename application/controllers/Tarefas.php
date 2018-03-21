<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tarefas extends CI_Controller {

	public function __construct(){
		parent::__construct();
		if (_v($_SESSION,'idusuario') == ''){
			Header('Location:login');
		}
	}

	public function salvar() {


		$this->load->model('Tarefa_model');
		$data = only($_POST,['titulo','data','hora','texto','idturma']);
		$data['idprofessor'] = $_SESSION['idusuario'];
		$idTarefa = $this->Tarefa_model->inserir($data);
		
		$path = saveUploadFile($idTarefa);
		if ($path != false){
			$this->load->model('Arquivo_model');
			$data = ['idtarefa'=>$idTarefa, 'do_professor'=>true,'caminho'=>$path, 'idprofessor'=>$_SESSION['idusuario']];
			$this->Arquivo_model->inserir($data);
		}

		print $idTarefa;
	}


	public function listar(){
		
		$this->twig->display('turmas', ['turmas'=>$turmas]);
	}
}
