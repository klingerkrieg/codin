<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Turmas extends CI_Controller {

	public function __construct(){
		parent::__construct();
		if (_v($_SESSION,'user') == ''){
			Header('Location:login');
		}
	}

	public function index($chave){
		$this->load->model('Turma_model');
		$turma = $this->Turma_model->getByChave($chave);

		#$this->load->model('Tarefa_model');
		#$tarefas =  $this->Tarefa_model->getByTurma($turma['idturma']);
		
		$this->twig->display('professores/turma', ['turma'=>$turma]);

	}

	public function salvar() {

        $this->load->model('Turma_model');
		$chave = $this->Turma_model->salvar(only($_POST,['idturma','nome']));
		print $chave;
	}


	public function listar(){
		$this->load->model('Turma_model');
		$turmas = $this->Turma_model->allFromProfessor();
		$this->twig->display('professores/lista_turmas', ['turmas'=>$turmas]);
	}

	public function get($idturma){
		$this->load->model('Turma_model');
		$turma =  $this->Turma_model->get($idturma);

		print json_encode($turma);
	}

	public function excluir($idturma){

		$this->load->model('Tarefa_model');
		$this->load->model('Turma_model');
		
		$tarefas = $this->Tarefa_model->getByTurma($idturma);
		
		//exclui todas as tarefas
		foreach($tarefas as $tarefa){
			$this->Tarefa_model->excluir($tarefa['idtarefa']);
		}
		
		$this->Turma_model->excluir($idturma);
		
		
	}
}
