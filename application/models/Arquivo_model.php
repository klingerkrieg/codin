<?php

class Arquivo_model extends CI_Model {

        private $table = 'arquivos';
        private $canRead = ['txt','css','html', 'xhtml','php','json','js','md','java', 'rst'];
        private $canAccept = ['txt','css','html', 'xhtml','php','json','js','md','java', 'rst',
                                'png', 'jpg', 'gif', 'jpeg', 'zip'];




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


                $this->db->select("idarquivo, idtarefa, caminho, idusuario, DATE_FORMAT(ifnull(data_atualizado, data_criado), '%d/%m/%Y %H:%i:%s') as data");

                $arr = $this->db->get_where($this->table, $data)->row_array();
                #pega o conteudo do arquivo
                $arr['content'] = file_get_contents($arr['caminho']);
                #se estiver latin1 transforma para utf8
                $encode = mb_detect_encoding($arr['content'],"UTF-8,ISO-8859-1");
                if ($encode == "ISO-8859-1"){
                        $arr['content'] = utf8_encode($arr['content']);
                }
                
                #pega as demais informacoes do arquivo
                $arr = $this->getFileData($arr, $this->pathHash($arr['idtarefa']));
                return $arr;
        }

        public function getByAlunos($idtarefa, $alunos, $idaluno, $nivel, $path){
                $this->load->model('Arquivo_model');
                for($i = 0; $i < sizeof($alunos); $i++ ){
                        if ($idaluno != $alunos[$i]['idusuario']) {
                                $nivel = 0;
                                $path = null;
                        }
                        $alunos[$i]['respostas'] = $this->getByTarefa($idtarefa, $alunos[$i]['idusuario'], $nivel, $path);
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

        public function pathHash($idtarefa, $idusuario = null){
                if ($idusuario == null){
                        return substr(sha1($idtarefa.$_SESSION['user']['idusuario']),0,10);
                } else {
                        return substr(sha1($idtarefa.$idusuario),0,10);
                }
        }

        public function generateRespostaPath($idtarefa){
                return "./uploads/".$idtarefa."/". $this->pathHash($idtarefa);
        }

        public function getByTarefa($idtarefa, $idaluno = null, $nivel = null, $path = null){

                $sql = "select arquivos.idarquivo, caminho, arquivos.idusuario, "
                        ." (select count(correcoes.idarquivo) from correcoes where correcoes.idarquivo = arquivos.idarquivo)  as qtd_correcoes,  "
                        ." DATE_FORMAT(ifnull(arquivos.data_atualizado, arquivos.data_criado), '%d/%m/%Y %H:%i:%s') as data "
                        ." from arquivos "
                        ." where idtarefa = $idtarefa";
                
                if ($idaluno != null){
                        
                        $sql .= " and nivel = $nivel + (SELECT min(nivel) FROM saladeaula.arquivos where idusuario = $idaluno and idtarefa = $idtarefa) ";
                        $sql .= " and arquivos.idusuario = $idaluno ";

                        if ($path != null){
                                $path = trim($path, "/");
                                $path .= "/";
                                $sql .= " and caminho like '%".urldecode($path)."%' ";
                        }
                        
                } else {
                        $sql .= " and do_professor = 1 ";
                }

                $sql .= " order by is_folder desc ";



                $arquivos = $this->db->query($sql)->result_array();
                $arquivos = $this->getFilesData($idtarefa, $idaluno, $arquivos);
                

                return $arquivos;

        }

        public function getFilesData($idtarefa, $idusuario, $arquivos){
                $hash = $this->pathHash($idtarefa, $idusuario);
                for ($y = 0; $y < sizeof($arquivos); $y++){
                        $arquivos[$y] = $this->getFileData($arquivos[$y],$hash);
                }
                return $arquivos;
        }

        public function getFileData($arq,$hash){

                $arq['nome'] = getEnds($arq['caminho'],"/");
                $arq['ext'] = "folder";
                $arq['caminho_exec'] = "/".substr($arq['caminho'], strpos($arq['caminho'],$hash));
                $arq['exists'] = true;
                if (is_file($arq['caminho'])){
                        $arq['ext'] = strtolower(getEnds($arq['caminho'],"."));
                        if ($arq['ext'] == strtolower(trim($arq['nome'],"."))){
                                $arq['ext'] = "txt";
                        }
                        $arq['can_read'] = in_array($arq['ext'], $this->canRead);
                }
                if (!file_exists($arq['caminho'])){
                        $arq['ext'] = "forbbiden";
                        $arq['exists'] = false;
                }

                return $arq;
        }

        

        public function excluirByTarefa($idtarefa){
                
		deleteFolder('./uploads/'.$idtarefa);
                return $this->db->delete($this->table, ["idtarefa"=>$idtarefa]);
        }

        public function checkSecurity($file){
                if (!is_file($file)){
                        //Se for diretorio pode
                        return true;
                }

                $ext = strtolower(getEnds($file,"."));
                
                if (in_array($ext, $this->canAccept)){
                        $content = str_replace(" ","",file_get_contents($file));

                        global $badCode;
                        foreach($badCode as $func){
                                if (strpos($content,$func) !== false){
                                        unlink($file);
                                        $texto = "Esse código parece muito perigoso. Você não pode enviar arquivos que contenham \"$func\".";
                                        $this->db->insert("alertas",['idusuario'=>$_SESSION['user']['idusuario'],
                                                                        'texto'=>$texto]);
                                        $_SESSION['user']['errors'] = $texto;
                                        return false;
                                }
                        }

                } else {
                        unlink($file);
                        $texto = "Você não pode enviar arquivos com extensão '$ext'.";
                        $this->db->insert("alertas",['idusuario'=>$_SESSION['user']['idusuario'],
                                                        'texto'=>$texto]);
                        $_SESSION['user']['errors'] = $texto;
                        return false;
                }

                return true;
        }

        public function uploadFiles($idtarefa, $keyFile, $override=false){
                $path = $this->generateRespostaPath($idtarefa);
		
		$path = saveUploadFile($path, $keyFile, $override);
                
		$files = unzip($path);
		if (sizeof($files) > 0){
                        
                        foreach($files as $file) {

                                if ($this->checkSecurity($file)){
                                        $file = str_replace("//","/",str_replace("\\","/",trim($file,"/")));

                                        $rw = $this->db->get_where($this->table,['caminho'=>$file])->row_array();

                                        #só reinsere no banco caso não exista
                                        //if (sizeof($rw) == 0){
                                        if ( $rw == false ){
                                                $data = ['idtarefa'=>$idtarefa, 'do_professor'=>false,
                                                        'caminho'=>$file, 'idusuario'=>$_SESSION['user']['idusuario'],
                                                        'nivel'=>substr_count($file,"/"),
                                                        'is_folder'=>!is_file($file)];
                                                $this->inserir($data);
                                        } else {
                                                #se ja existir apenas atualiza a hora
                                                $this->db->update($this->table,
                                                                ['data_atualizado'=> Date('Y-m-d H:i:s')],
                                                                ["idarquivo"=>$rw['idarquivo']] );
                                        }
                                }
                                
                        }
		}
        }


        public function excluir($idtarefa,$idarquivo){

                $this->load->model('Correcoes_model');
                $this->Correcoes_model->excluirByArquivo($idarquivo);

                $this->db->select('caminho');
                $rw = $this->db->get_where($this->table,['idarquivo'=>$idarquivo,
                                                   'idtarefa'=>$idtarefa,
                                                   'idusuario'=>$_SESSION['user']['idusuario'],
                                                ])->row_array();
                
                $sql = "delete from arquivos where "
                        ." caminho = '{$rw['caminho']}'";

                $this->db->query($sql);

                $sql = "delete from arquivos where "
                        ." caminho like '{$rw['caminho']}/%'";

                $this->db->query($sql);
        }


}