<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Turmas extends CI_Controller {

	public function __construct(){
		parent::__construct();
		if (_v($_SESSION,'idusuario') == ''){
			Header('Location:login');
		}
	}

	public function salvar() {

        $this->load->model('Turma_model');
		$this->Turma_model->inserir(only($_POST,['nome']));

		$turmas = $this->queryTurmas();

		$this->twig->display('turmas', ['turmas'=>$turmas]);
	}

	public function queryTurmas(){
		$this->load->model('Turma_model');
		return $this->Turma_model->all();
	}

	public function listar(){
		$turmas = $this->queryTurmas();
		$this->twig->display('turmas', ['turmas'=>$turmas]);
	}
}
