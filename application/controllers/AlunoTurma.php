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

		if ($path != ""){
			$respostas  = $this->Arquivo_model->readFiles("./uploads/$idtarefa/". $path, $_SESSION['user']['idusuario']);
			$voltar = $back;
		} else {
			$respostas  = $this->Arquivo_model->getByTarefa($tarefa['idtarefa'], false, $_SESSION['user']['idusuario']);
			$voltar = $back;
		}
		$arquivos  	= $this->Arquivo_model->getByTarefa($tarefa['idtarefa']);

		$this->twig->display('alunos/tarefa', ['turma'=>$turma,
												'tarefa'=>$tarefa,
												"respostas"=>$respostas,
												"arquivos"=>$arquivos,
												"voltar"=>$back]);
	}

	public function responderTarefa($idtarefa){
		$this->load->model('Arquivo_model');
		$path = $this->Arquivo_model->generateRespostaPath($idtarefa);
		
		$path = saveUploadFile($path);
		
		unzip($path);
		if ($path != false){
			
			$data = ['idtarefa'=>$idtarefa, 'do_professor'=>false,
					'caminho'=>$path, 'idusuario'=>$_SESSION['user']['idusuario']];
			$this->Arquivo_model->inserir($data);
		}
		
		Header('Location:' . base_url("alunoturma/tarefa/$idtarefa"));
	}

	function deletarArquivo($idtarefa){

		$this->load->model('Arquivo_model');
		$path = $this->Arquivo_model->generateRespostaPath($idtarefa);

		#remove as possiveis barras duplas
		$_POST['path'] = str_replace("//","/", $_POST['path']);
		
		#o endereco de exclusao tem que bater com o esperado
		if (strpos($_POST['path'] , $path ) !== false){
			if (is_file($_POST['path'])){
				unlink($_POST['path']);
			} else {
				deleteFolder($_POST['path']);
			}
			print 'excluido';
		} else {
			print "falha ao excluir";
		}
		
	}

}
