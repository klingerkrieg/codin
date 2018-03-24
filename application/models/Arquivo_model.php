<?php

class Arquivo_model extends CI_Model {

        private $table = 'arquivos';

        public function inserir($data){
                //$this->output->enable_profiler(TRUE);

                $data['data_criado'] = Date('Y-m-d H:i:s');
                unset($data['data']);
                unset($data['hora']);
                $this->db->insert($this->table,$data);
                return $this->db->insert_id();

        }

        public function getByAlunos($idtarefa, $alunos){
                $this->load->model('Arquivo_model');
                for($i = 0; $i < sizeof($alunos); $i++ ){
                        $alunos[$i]['respostas'] = $this->getByTarefa($idtarefa, false, $alunos[$i]['idusuario']);
                }
                return $alunos;
        }

        public function getByTarefas($tarefas){
                $this->load->model('Arquivo_model');
                for($i = 0; $i < sizeof($tarefas); $i++ ){
                        $tarefas[$i]['arquivos'] = $this->getByTarefa($tarefas[$i]['idtarefa']);
                }
                return $tarefas;
        }

        public function generateRespostaPath($idtarefa){
                return "./uploads/".$idtarefa."/". substr(sha1($idtarefa.$_SESSION['user']['idusuario']),0,10);
        }

        public function getByTarefa($idtarefa, $doProfessor = true, $idaluno = null){

                if ($idaluno != null){
                        //Caso seja de aluno le todo o diretorio do aluno
                        $path = $this->generateRespostaPath($idtarefa);
                        $arquivos = $this->readFiles($path, $idaluno);
                } else {
                        //trazer tambem se o aluno já entregou essa tarefa
                        $this->db->select('caminho, idusuario ');
                        $where = ['idtarefa'=>$idtarefa, 'do_professor'=>$doProfessor];
                        #Caso tenha um aluno como filtro
                        if ($idaluno != null){
                                $where['idusuario'] = $idaluno;
                        }
                        $arquivos = $this->db->get_where($this->table,$where)->result_array();
                        $arquivos = $this->getFilesData($arquivos);
                }
                return $arquivos;
        }

        function readFiles($path, $idusuario){
                $arquivos = array();
                if ($handle = opendir($path)) {
                        while (false !== ($file = readdir($handle))) {
                                if ($file != "." && $file != "..")
                                        array_push($arquivos, ["idusuario"=>$idusuario, "caminho"=>$path."/".$file]);
                        }
                }
                return $this->getFilesData($arquivos);
        }

        public function getFilesData($arquivos){
                for ($y = 0; $y < sizeof($arquivos); $y++){
                        if (is_file($arquivos[$y]['caminho'])){
                                $arquivos[$y]['nome'] = getEnds($arquivos[$y]['caminho'],"/");
                                $arquivos[$y]['ext'] = strtolower(getEnds($arquivos[$y]['caminho'],"."));

                                if (strtolower($arquivos[$y]['ext']) == strtolower(trim($arquivos[$y]['nome'],"."))){
                                        $arquivos[$y]['ext'] = "txt";
                                }
                        } else {
                                $arquivos[$y]['nome'] = getEnds($arquivos[$y]['caminho'],"/");
                                $arquivos[$y]['ext'] = "folder";
                                $arquivos[$y]['caminho_pasta'] = $arquivos[$y]['caminho'];
                                $arquivos[$y]['caminho_pasta'] = substr($arquivos[$y]['caminho_pasta'],strpos($arquivos[$y]['caminho_pasta'],"/")+1);
                                $arquivos[$y]['caminho_pasta'] = substr($arquivos[$y]['caminho_pasta'],strpos($arquivos[$y]['caminho_pasta'],"/")+1);
                                $arquivos[$y]['caminho_pasta'] = "/".substr($arquivos[$y]['caminho_pasta'],strpos($arquivos[$y]['caminho_pasta'],"/")+1);
                        }
                }
                return $arquivos;
        }

        public function excluirByTarefa($idtarefa){
                
		deleteFolder('./uploads/'.$idtarefa);
                return $this->db->delete($this->table, ["idtarefa"=>$idtarefa]);
        }


}