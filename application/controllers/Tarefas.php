<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tarefas extends CI_Controller {

	public function __construct(){
		parent::__construct();
		if (_v($_SESSION,'idusuario') == ''){
			Header('Location:login');
		}
	}

	public function index($idtarefa){
		$this->load->model('Tarefa_model');
		$this->load->model('Turma_model');
		$this->load->model('Arquivo_model');

		$tarefa 	= $this->Tarefa_model->get($idtarefa);
		$turma  	= $this->Turma_model->get($tarefa['idturma']);
		$arquivos  	= $this->Arquivo_model->getByTarefa($tarefa['idtarefa']);
		$alunos 	= $this->Turma_model->getAlunos($tarefa['idturma']);
		$alunos 	= $this->Arquivo_model->getByAlunos($tarefa['idtarefa'],$alunos);


		$this->twig->display('professores/tarefa', ['turma'=>$turma,
													'tarefa'=>$tarefa,
													"alunos"=>$alunos,
													"arquivos"=>$arquivos]);
	}

	public function salvar() {


		$this->load->model('Tarefa_model');
		$data = only($_POST,['idtarefa','titulo','data','hora','texto','idturma']);
		$data['idprofessor'] = $_SESSION['idusuario'];
		$idtarefa = $this->Tarefa_model->salvar($data);
		
		$path = saveUploadFile($idtarefa);
		if ($path != false){
			$this->load->model('Arquivo_model');
			$data = ['idtarefa'=>$idtarefa, 'do_professor'=>true,
					'caminho'=>$path, 'idusuario'=>$_SESSION['idusuario']];
			$this->Arquivo_model->inserir($data);
		}

		print $idtarefa;
	}

	public function get($idtarefa){
		$this->load->model('Tarefa_model');
		$tarefa =  $this->Tarefa_model->get($idtarefa);

		$this->load->model('Arquivo_model');
		$tarefa['arquivos'] = $this->Arquivo_model->getByTarefa($tarefa['idtarefa']);

		print json_encode($tarefa);
	}


	public function listar($idturma){

		$this->load->model('Tarefa_model');
		$tarefas =  $this->Tarefa_model->getByTurmaProfessor($idturma);

		$this->twig->display('professores/lista_tarefas', ['tarefas'=>$tarefas]);
	}

	public function excluir($idtarefa){

		$this->load->model('Tarefa_model');
		$this->Tarefa_model->excluir($idtarefa);

	}
}
