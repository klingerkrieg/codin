<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AlunoTurma extends CI_Controller {

	public function __construct(){
		parent::__construct();
		if (_v($_SESSION,'idusuario') == ''){
			Header('Location:login');
		}
	}

	public function index($chave){
		$this->load->model('Turma_model');
		$turma = $this->Turma_model->getByChave($chave);

		$this->twig->display('alunos/turma', ['turma'=>$turma]);
	}

	public function entrar($chave){
		$this->load->model('Turma_model');
		print $this->Turma_model->inserirAluno($_SESSION['idusuario'],$chave);
	}

	public function listar(){
		$this->load->model('Turma_model');
		$turmas = $this->Turma_model->allFromAluno();
		$this->twig->display('alunos/lista_turmas', ['turmas'=>$turmas]);
	}

	public function listarTarefas($idturma){
		$this->load->model('Tarefa_model');
		$tarefas = $this->Tarefa_model->getByTurmaAluno($idturma);
		$this->twig->display('alunos/lista_tarefas', ['tarefas'=>$tarefas]);
	}

}
