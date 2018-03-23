<?php

class Turma_model extends CI_Model {

        private $table = 'turmas';

        public function salvar($data){
                if ($data['idturma'] != ""){
                        return $this->atualizar($data);
                } else {
                        return $this->inserir($data);
                }
        }

        public function atualizar($data){
                $data['data_atualizado'] = Date('Y-m-d H:i:s');
                $this->db->update($this->table,$data,['idturma'=>$data['idturma']]);
                return $data['idturma'];
        }

        public function get($idturma){
                $this->db->select('idturma, nome, chave');
                $arr = $this->db->get_where($this->table,['idturma'=>$idturma])->row_array();
                return $arr;
        }

        public function getAlunos($idturma){
                $sql = "select usuarios.idusuario, nome, email from usuarios "
                        ." inner join aluno_turma on "
                        ." usuarios.idusuario = aluno_turma.idusuario "
                        ." where aluno_turma.idturma = $idturma";

                return $this->db->query($sql)->result_array();
        }

        public function inserir($data){
                //$this->output->enable_profiler(TRUE);

                $chave = substr(md5(uniqid(mt_rand(), true)) , 0, 8);

                $data['data_criado'] = Date('Y-m-d H:i:s');
                $data['chave'] = $chave;
                $turma = $this->db->insert($this->table,$data);
                $turma_id = $this->db->insert_id();
                

                $data = array();
                $data['idusuario'] = $_SESSION['user']['idusuario'];
                $data['idturma'] = $turma_id;
                $turma = $this->db->insert("professor_turma",$data);
                
                return $chave;

        }

        public function getByChave($chave){
                //$this->output->enable_profiler(TRUE);
                $this->db->select('idturma, nome');
                return $this->db->get_where($this->table,['chave'=>$chave])->row_array();
        }

        public function allFromProfessor(){
                
                $sql = 'select turmas.idturma, turmas.nome, turmas.chave, '
                        ." (select count(*) from aluno_turma where aluno_turma.idturma = turmas.idturma ) as qtd_alunos "
                        ." from turmas inner join "
                        ." professor_turma on "
                        ." professor_turma.idturma = turmas.idturma and "
                        ." professor_turma.idusuario = ". $_SESSION['user']['idusuario'];

                return $this->db->query($sql)->result_array();
        }

        public function allFromAluno(){
                
                $sql = 'select turmas.idturma, turmas.nome, turmas.chave '
                        ." from turmas inner join "
                        ." aluno_turma on "
                        ." aluno_turma.idturma = turmas.idturma and "
                        ." aluno_turma.idusuario = ". $_SESSION['user']['idusuario'];

                return $this->db->query($sql)->result_array();
        }
        
        public function excluir($idturma){
                $this->db->delete("professor_turma", ["idturma"=>$idturma]);
                $this->db->delete("aluno_turma", ["idturma"=>$idturma]);
                return $this->db->delete($this->table, ["idturma"=>$idturma]);
        }

        public function inserirAluno($idaluno, $chave){
                $data = $this->getByChave($chave);

                $sql = "insert into aluno_turma (idusuario, idturma) values ($idaluno, {$data['idturma']} )";
                $this->db->query($sql);
                return $data['idturma'];
        }

        public function removerAluno($idaluno, $idturma){
                $data = $this->getByChave($chave);
                return $this->db->delete("aluno_turma", ["idturma"=>$idturma, "idaluno"=>$idaluno]);
        }
        

        /*
        
        public function get_last_ten_entries() {
                $query = $this->db->get('entries', 10);
                return $query->result();
        }

        public function insert_entry()
        {
                $this->title    = $_POST['title']; // please read the below note
                $this->content  = $_POST['content'];
                $this->date     = time();

                $this->db->insert('entries', $this);
        }

        public function update_entry()
        {
                $this->title    = $_POST['title'];
                $this->content  = $_POST['content'];
                $this->date     = time();

                $this->db->update('entries', $this, array('id' => $_POST['id']));
        }*/

}