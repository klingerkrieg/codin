<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Correcoes extends CI_Controller {

	public function __construct(){
		parent::__construct();
		if (_v($_SESSION,'user') == ''){
			Header('Location:login');
		}

	}

	public function salvar(){
		$this->load->model('Correcoes_model');
		$this->Correcoes_model->salvar($_POST);
		print 'ok';
	}

	public function listar($idarquivo){
		$this->load->model('Correcoes_model');
		$correcoes 	= $this->Correcoes_model->getByArquivo($idarquivo);
		print json_encode($correcoes);
	}

	public function get($idcorrecao){
		$this->load->model('Correcoes_model');
		$arr = $this->Correcoes_model->get($idcorrecao);
		print json_encode($arr);
	}


	public function excluir($idcorrecao){
		$this->load->model('Correcoes_model');
		$this->Correcoes_model->excluir($idcorrecao);
		print 'ok';
	}


}
