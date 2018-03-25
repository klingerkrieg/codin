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

        public function pathHash($idtarefa){
                return substr(sha1($idtarefa.$_SESSION['user']['idusuario']),0,10);
        }

        public function generateRespostaPath($idtarefa){
                return "./uploads/".$idtarefa."/". $this->pathHash($idtarefa);
        }

        public function getByTarefa($idtarefa, $idaluno = null, $nivel = 0){

                //trazer tambem se o aluno já entregou essa tarefa
                /*$this->db->select('idarquivo, caminho, idusuario ');
                $where = ['idtarefa'=>$idtarefa, 'do_professor'=>$doProfessor];
                #Caso tenha um aluno como filtro
                if ($idaluno != null){
                        $where['idusuario'] = $idaluno;
                }

                if ($fromPath != ""){
                        $this->db->like(['caminho'=>$fromPath]);
                }

                $arquivos = $this->db->get_where($this->table,$where)->result_array();
                $arquivos = $this->getFilesData($arquivos);
                
                return $arquivos;*/
                $sql = "select idarquivo, caminho, idusuario "
                        ." from arquivos where idtarefa = $idtarefa";
                

                if ($idaluno != null){
                        $sql .= " and nivel = $nivel + (SELECT min(nivel) FROM saladeaula.arquivos where idusuario = $idaluno and idtarefa = $idtarefa) ";
                        $sql .= " and idusuario = $idaluno ";
                } else {
                        $sql .= " and do_professor = 1 ";
                }

                $sql .= " order by is_folder desc ";

                $arquivos = $this->db->query($sql)->result_array();
                $arquivos = $this->getFilesData($idtarefa,$arquivos);
                
                return $arquivos;

        }

        /*function readFiles($path, $idusuario){
                $arquivos = array();
                if ($handle = opendir($path)) {
                        while (false !== ($file = readdir($handle))) {
                                if ($file != "." && $file != "..")
                                        array_push($arquivos, ["idusuario"=>$idusuario, "caminho"=>$path."/".$file]);
                        }
                }
                return $this->getFilesData($arquivos);
        }*/

        public function getFilesData($idtarefa, $arquivos){
                $hash = $this->pathHash($idtarefa);
                for ($y = 0; $y < sizeof($arquivos); $y++){
                        if (is_file($arquivos[$y]['caminho'])){
                                $arquivos[$y]['nome'] = getEnds($arquivos[$y]['caminho'],"/");
                                $arquivos[$y]['ext'] = strtolower(getEnds($arquivos[$y]['caminho'],"."));

                                $arquivos[$y]['caminho_arq'] = "/".substr($arquivos[$y]['caminho'], strpos($arquivos[$y]['caminho'],$hash));

                                if (strtolower($arquivos[$y]['ext']) == strtolower(trim($arquivos[$y]['nome'],"."))){
                                        $arquivos[$y]['ext'] = "txt";
                                }
                        } else {
                                $arquivos[$y]['nome'] = getEnds($arquivos[$y]['caminho'],"/");
                                $arquivos[$y]['ext'] = "folder";
                                $arquivos[$y]['caminho_pasta'] = "/".substr($arquivos[$y]['caminho'], strpos($arquivos[$y]['caminho'],$hash));
                        }
                }
                return $arquivos;
        }

        public function excluirByTarefa($idtarefa){
                
		deleteFolder('./uploads/'.$idtarefa);
                return $this->db->delete($this->table, ["idtarefa"=>$idtarefa]);
        }

        public function uploadFiles($idtarefa,$files){
                $path = $this->generateRespostaPath($idtarefa);
		
		$path = saveUploadFile($path);
		
		$files = unzip($path);
		if (sizeof($files) > 0){
                        
                        foreach($files as $file) {
                                $file = str_replace("//","/",str_replace("\\","/",trim($file,"/")));
                                $data = ['idtarefa'=>$idtarefa, 'do_professor'=>false,
                                        'caminho'=>$file, 'idusuario'=>$_SESSION['user']['idusuario'],
                                        'nivel'=>substr_count($file,"/"),
                                        'is_folder'=>!is_file($file)];
                                $this->inserir($data);
                        }
		}
        }


        public function excluir($idtarefa,$idarquivo){

                $this->db->select('caminho');
                $rw = $this->db->get_where($this->table,['idarquivo'=>$idarquivo,
                                                   'idtarefa'=>$idtarefa,
                                                   'idusuario'=>$_SESSION['user']['idusuario'],
                                                ])->row_array();
                
                $sql = "delete from arquivos where "
                        ." caminho like '{$rw['caminho']}%'";

                $this->db->query($sql);
        }


}