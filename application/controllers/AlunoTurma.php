<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AlunoTurma extends CI_Controller {

	public function __construct(){
		parent::__construct();
		if (_v($_SESSION,'user') == ''){
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
		print $this->Turma_model->inserirAluno($_SESSION['user']['idusuario'],$chave);
	}

	public function sair($idturma){
		$this->load->model('Turma_model');
		print $this->Turma_model->removerAluno($_SESSION['user']['idusuario'],$idturma);
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

	public function tarefa($idtarefa){

		$i = 4;
		$path = "";
		$back = "";
		while ($this->uri->segment($i) != false){
			$back = $path;
			$path .= "/".$this->uri->segment($i);
			$i++;
		}
		
		$this->load->model('Tarefa_model');
		$this->load->model('Turma_model');
		$this->load->model('Arquivo_model');

		$tarefa 	= $this->Tarefa_model->get($idtarefa);
		$turma  	= $this->Turma_model->get($tarefa['idturma']);

		#procura os arquivos do aluno
		$nivel = max([substr_count($path,"/") - 1, 0]);
		$respostas  = $this->Arquivo_model->getByTarefa($tarefa['idtarefa'],
														$_SESSION['user']['idusuario'],
														$nivel, $path);
		
		$arquivos  	= $this->Arquivo_model->getByTarefa($tarefa['idtarefa']);

		$this->twig->display('alunos/tarefa', ['turma'=>$turma,
												'tarefa'=>$tarefa,
												"respostas"=>$respostas,
												"arquivos"=>$arquivos,
												"voltar"=>$back]);

		$_SESSION['user']['errors'] = "";
	}

	public function responderTarefa($idtarefa){
		$this->load->model('Arquivo_model');

		$this->Arquivo_model->uploadFiles($idtarefa, "arquivo", true);
		die();
		Header('Location:' . base_url("alunoturma/tarefa/$idtarefa"));
	}

	function deletarArquivo($idtarefa,$idarquivo){

		$this->load->model('Arquivo_model');
		$path = $this->Arquivo_model->excluir($idtarefa,$idarquivo);
		
	}

	function verarquivo($idtarefa, $idarquivo){
		$this->load->model('Arquivo_model');
		$this->load->model('Tarefa_model');
		$this->load->model('Turma_model');

		$arq 		= $this->Arquivo_model->get($idarquivo);
		$tarefa 	= $this->Tarefa_model->get($idtarefa);
		$turma 		= $this->Turma_model->get($tarefa['idturma']);

		if ($_SESSION['user']['is_professor'] == true){
			$this->twig->display('professores/arquivo', ['arquivo'=>$arq,
														'tarefa'=>$tarefa,
														'turma'=>$turma]);
		} else {
			$this->twig->display('alunos/arquivo', ['arquivo'=>$arq,
												'tarefa'=>$tarefa,
												'turma'=>$turma]);
		}
	}

}
