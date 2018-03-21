<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

	public function __construct(){
		parent::__construct();
		if (_v($_SESSION,'idusuario') == ''){
			Header('Location:login');
		}

		
	}

	public function index() {


		if ($_SESSION['is_professor'] == true){
			$dados = array(
				'texto' => 'Esse é um Template Twig'
			);
			$this->twig->display('home_professor', $dados);
		} else {

		}
		
	}
}
