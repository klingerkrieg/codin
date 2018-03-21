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
		$data = only($_POST,['idtarefa','titulo','data','hora','texto','idturma']);
		$data['idprofessor'] = $_SESSION['idusuario'];
		$idtarefa = $this->Tarefa_model->salvar($data);
		
		$path = saveUploadFile($idtarefa);
		if ($path != false){
			$this->load->model('Arquivo_model');
			$data = ['idtarefa'=>$idtarefa, 'do_professor'=>true,'caminho'=>$path, 'idprofessor'=>$_SESSION['idusuario']];
			$this->Arquivo_model->inserir($data);
		}

		print $idtarefa;
	}

	public function get($idtarefa){
		$this->load->model('Tarefa_model');
		$tarefa =  $this->Tarefa_model->get($idtarefa);

		$this->load->model('Arquivo_model');
		$tarefa['arquivos'] = $this->getArquivoData($tarefa['idtarefa']);

		print json_encode($tarefa);
	}


	private function getArquivoData($idtarefa){
		$arquivos = $this->Arquivo_model->getArquivosByTarefa($idtarefa);

		for ($y = 0; $y < sizeof($arquivos); $y++){
			$arquivos[$y]['nome'] = getEnds($arquivos[$y]['caminho'],"/");
			$arquivos[$y]['ext'] = getEnds($arquivos[$y]['caminho'],".");
		}
		return $arquivos;
	}

	public function listar($idturma){

		
		$this->load->model('Tarefa_model');
		$tarefas =  $this->Tarefa_model->getTarefasByTurma($idturma);


		$this->load->model('Arquivo_model');
		for($i = 0; $i < sizeof($tarefas); $i++ ){
			$tarefas[$i]['arquivos'] = $this->getArquivoData($tarefas[$i]['idtarefa']);
		}
		
		$this->twig->display('tarefas', ['tarefas'=>$tarefas]);
	}

	public function excluir($idtarefa){

		$this->load->model('Arquivo_model');
		$this->Arquivo_model->excluirByTarefa($idtarefa);

		$this->load->model('Tarefa_model');
		$this->Tarefa_model->excluir($idtarefa);

		deleteFolder('./uploads/'.$idtarefa);
		
	}
}
