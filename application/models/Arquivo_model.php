<?php

class Arquivo_model extends CI_Model {

        private $table = 'arquivos';
        private $canRead = ['txt','css','html', 'xhtml','php','json','js','md','java', 'rst'];
        private $canAccept = ['txt','css','html', 'xhtml','php','json','js','md','java', 'rst',
                                'png', 'jpg', 'gif', 'jpeg', 'zip'];

        private $pathPartSaveds;



        public function inserirIfNotExists($data){
                $rw = $this->db->get_where($this->table,['caminho'=>$data['caminho'],
                                                        'idtarefa'=>$data['idtarefa'],
                                                        'idusuario'=>$data['idusuario'],
                                                        'idpasta'=>$data['idpasta']])->row_array();


                if ($rw == false){
                        return $this->inserir($data);
                } else {
                        $this->db->update($this->table,
                                        ['data_atualizado'=> Date('Y-m-d H:i:s')],
                                        ["idarquivo"=>$rw['idarquivo']] );
                        return $rw['idarquivo'];
                }
        }


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


                $this->db->select("idarquivo, idpasta, idtarefa, is_folder, caminho, idusuario, DATE_FORMAT(ifnull(data_atualizado, data_criado), '%d/%m/%Y %H:%i:%s') as data");

                $arr = $this->db->get_where($this->table, $data)->row_array();
                
                #pega as demais informacoes do arquivo
                $arr = $this->getFileData($arr, $this->generateRespostaPath($arr['idtarefa'], $arr['idusuario']));
                
                if (is_file($arr['caminho'])){
                        #pega o conteudo do arquivo
                        $arr['content'] = file_get_contents($arr['caminho']);
                        #se estiver latin1 transforma para utf8
                        $encode = mb_detect_encoding($arr['content'],"UTF-8,ISO-8859-1");
                        if ($encode == "ISO-8859-1"){
                                $arr['content'] = utf8_encode($arr['content']);
                        }
                }
                
                return $arr;
        }

        public function getByAlunos($idtarefa, $alunos, $idaluno, $idpasta){
                $this->load->model('Arquivo_model');
                for($i = 0; $i < sizeof($alunos); $i++ ){
                        /*if ($idaluno != $alunos[$i]['idusuario']) {
                                $nivel = 0;
                                $path = null;
                        }*/
                        $alunos[$i]['respostas'] = $this->getByTarefa($idtarefa, $alunos[$i]['idusuario'], $idpasta);
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

        public function generateRespostaPath($idtarefa, $idusuario = null){
                return "./uploads/".$idtarefa."/". $this->pathHash($idtarefa, $idusuario);
        }

        public function getByTarefa($idtarefa, $idaluno = null, $idpasta = null){

                $sql = "select arquivos.idarquivo, idpasta, caminho, arquivos.idusuario, "
                        ." (select count(correcoes.idarquivo) from correcoes where correcoes.idarquivo = arquivos.idarquivo)  as qtd_correcoes,  "
                        ." DATE_FORMAT(ifnull(arquivos.data_atualizado, arquivos.data_criado), '%d/%m/%Y %H:%i:%s') as data "
                        ." from arquivos "
                        ." where idtarefa = $idtarefa";
                
                if ($idaluno != null){
                        $sql .= " and arquivos.idusuario = $idaluno ";

                        if ($idpasta == null){
                                $sql .= " and arquivos.idpasta is null ";
                        } else {
                                $sql .= " and arquivos.idpasta = $idpasta ";
                        }
                        
                } else {
                        $sql .= " and do_professor = 1 ";
                }

                $sql .= " order by is_folder desc ";



                $arquivos = $this->db->query($sql)->result_array();
                $arquivos = $this->getFilesData($idtarefa,$idaluno, $arquivos);
                

                return $arquivos;

        }

        public function getFilesData($idtarefa, $idusuario, $arquivos){
                
                if ($idusuario == null){
                        $pathHash = "uploads/$idtarefa";
                } else {
                        $pathHash = $this->generateRespostaPath($idtarefa, $idusuario);
                }

                for ($y = 0; $y < sizeof($arquivos); $y++){
                        $arquivos[$y] = $this->getFileData($arquivos[$y],$pathHash);
                }
                return $arquivos;
        }

        public function getFileData($arq,$pathHash){

                $rw['idpasta'] = $arq['idpasta'];
                $arq['back'] = -1;
                $rlPath = "";
                //constroi o caminho completo até o arquivo
                while ($rw['idpasta'] != null){
                        $this->db->select("caminho, idpasta");
                        $rw = $this->db->get_where($this->table,['idarquivo'=>$rw['idpasta']])->row_array();
                        $rlPath = "/". $rw['caminho'] . $rlPath;
                        if ($arq['back'] == -1){
                                $arq['back'] = $rw['idpasta'];
                        }
                }

                $arq['nome'] = getEnds($arq['caminho'],"/");
                $arq['ext'] = "folder";
                $arq['caminho_exec'] = "/".$arq['caminho'];
                $arq['caminho'] = $pathHash . $rlPath . $arq['caminho_exec'];
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

        public function createAlunoInfo($idtarefa){
                $pathHash = $this->generateRespostaPath($idtarefa, $_SESSION['user']['idusuario']);
                $f = fopen($pathHash."/aluno-info.txt",'w');
                fwrite($f,"id:\t".$_SESSION['user']['idusuario']."\r\n");
                fwrite($f,"nome:\t".$_SESSION['user']['nome']);
                fclose($f);
        }

        public function uploadFiles($idtarefa, $keyFile, $override=false, $idpasta){

                

                if ($_SESSION['user']['is_professor']){
                        $pathHash = "uploads/".$idtarefa;
                } else {
                        $pathHash = $this->generateRespostaPath($idtarefa, $_SESSION['user']['idusuario']);
                }

                #se tiver sido feito o upload para uma pasta específica
                $insideFolder = "";
                if ($idpasta != null){
                        $pasta = $this->get($idpasta);
                        if ($pasta['is_folder'] && $pasta['idusuario'] == $_SESSION['user']['idusuario']){
                                $insideFolder = str_replace($pathHash,"",$pasta['caminho'] . "/");
                        }
                }
                
                #salva o arquivo em disco
                $path = saveUploadFile($pathHash . $insideFolder, $keyFile, $override);
                #caso esteja zipado extrai e retorna uma lista com os arquivos
                $files = unzip($path);

		if (sizeof($files) > 0){
                        
                        foreach($files as $file) {

                                if ($_SESSION['user']['is_professor'] == true || $this->checkSecurity($file)){
                                        $file = str_replace("//","/",str_replace("\\","/",trim($file,"/")));
                                        
                                        //remover uploads/idtarefa/
                                        //remover uploads/idtarefa/hash
                                        $file = trim(str_replace(trim($pathHash,"./"),"",$file),"/");
                                        

                                        $entirePath = ".";
                                        $prevId = null;
                                        foreach( explode("/", $file) as $pathPart ){
                                                
                                                $entirePath .= "/". $pathPart;
                                                $isFolder = !is_file($pathHash.$entirePath);
                                                $data = ['idtarefa'     =>$idtarefa,
                                                        'do_professor'  =>$_SESSION['user']['is_professor'],
                                                        'caminho'       =>$pathPart,
                                                        'idusuario'     =>$_SESSION['user']['idusuario'],
                                                        'idpasta'       =>$prevId,
                                                        'is_folder'     =>$isFolder];
                                                
                                                $prevId = $this->inserirIfNotExists($data);
                                                
                                        }
                                        
                                }
                                
                        }
                        
		}
        }


        public function excluir($idtarefa,$idarquivo){

                #deleta todas as correções do arquivo em questão
                $this->load->model('Correcoes_model');
                $this->Correcoes_model->excluirByArquivo($idarquivo);

                $this->db->select('idarquivo');
                $rw = $this->db->get_where($this->table,['idpasta'=>$idarquivo,
                                                   'idtarefa'=>$idtarefa,
                                                   'idusuario'=>$_SESSION['user']['idusuario'],
                                                ])->result_array();
                
                #deleta recursivamente todos os arquivos que estão dentro desta pasta(se for uma pasta)
                foreach($rw as $arq){
                        $this->excluir($idtarefa,$arq['idarquivo']);
                }

                #deleta o arquivo
                $this->db->delete($this->table,['idarquivo'=>$idarquivo]);
        }


        public function makeDownload($idtarefa,$idaluno){
                if ($idaluno == null){
                        $path = "./uploads/$idtarefa";
                        $destination = str_replace("\\","/",sys_get_temp_dir())."/tarefa-$idtarefa.zip";
                        #retorna o endereco do zip
                        return zip($path, $destination);
                } else {
                        $pathHash = $this->generateRespostaPath($idtarefa,$idaluno);
                        $this->db->select("nome");
                        #cria com o nome do aluno
                        $rw = $this->db->get_where("usuarios",["idusuario"=>$idaluno])->row_array();
                
                        $destination = str_replace("\\","/",sys_get_temp_dir())."/{$rw['nome']}.zip";
                        #retorna o endereco do zip
                        return zip($pathHash, $destination);
                }
        }


}