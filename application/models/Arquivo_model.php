<?php

class Arquivo_model extends CI_Model {

        private $table = 'arquivos';
        private $canRead = ['txt','css','html','php','json','js','md','java', 'rst'];

        public function inserir($data){
                //$this->output->enable_profiler(TRUE);

                $data['data_criado'] = Date('Y-m-d H:i:s');
                unset($data['data']);
                unset($data['hora']);
                $this->db->insert($this->table,$data);
                return $this->db->insert_id();

        }

        public function get($idarquivo){
                $data = array('idarquivo'=>$idarquivo);
                if ($_SESSION['user']['is_professor'] == false){
                        $data['idusuario'] = $_SESSION['user']['idusuario'];
                }

                $arr = $this->db->get_where($this->table, $data)->row_array();
                #pega o conteudo do arquivo
                $arr['content'] = file_get_contents($arr['caminho']);
                #pega as demais informacoes do arquivo
                $arr = $this->getFileData($arr, $this->pathHash($arr['idtarefa']));
                return $arr;
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

        public function getFileData($arq,$hash){

                $arq['nome'] = getEnds($arq['caminho'],"/");
                $arq['ext'] = "folder";
                $arq['caminho_exec'] = "/".substr($arq['caminho'], strpos($arq['caminho'],$hash));

                if (is_file($arq['caminho'])){
                        $arq['ext'] = strtolower(getEnds($arq['caminho'],"."));
                        if ($arq['ext'] == strtolower(trim($arq['nome'],"."))){
                                $arq['ext'] = "txt";
                        }
                        $arq['can_read'] = in_array($arq['ext'], $this->canRead);
                }

                return $arq;
        }

        public function getFilesData($idtarefa, $arquivos){
                $hash = $this->pathHash($idtarefa);
                for ($y = 0; $y < sizeof($arquivos); $y++){
                        $arquivos[$y] = $this->getFileData($arquivos[$y],$hash);
                }
                return $arquivos;
        }

        public function excluirByTarefa($idtarefa){
                
		deleteFolder('./uploads/'.$idtarefa);
                return $this->db->delete($this->table, ["idtarefa"=>$idtarefa]);
        }

        public function uploadFiles($idtarefa, $keyFile){
                $path = $this->generateRespostaPath($idtarefa);
		
		$path = saveUploadFile($path, $keyFile);
		
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