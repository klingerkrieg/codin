<?php

class Turma_model extends CI_Model {

        private $table = 'turmas';

        public function inserir($data){
                //$this->output->enable_profiler(TRUE);
                $data['data_criado'] = Date('Y/m/d H:i:s');
                $data['chave'] = substr(md5(uniqid(mt_rand(), true)) , 0, 8);
                $turma = $this->db->insert($this->table,$data);
                $turma_id = $this->db->insert_id();
                

                $data = array();
                $data['idusuario'] = $_SESSION['idusuario'];
                $data['idturma'] = $turma_id;
                $turma = $this->db->insert("professor_turma",$data);
                
                return true;

        }

        public function get($data){
                $this->output->enable_profiler(TRUE);
                $this->db->select('idturma, nome');
                return $this->db->get_where($this->table,$data);
        }

        public function all(){
                
                $sql = 'select turmas.idturma, turmas.nome,'
                        ." (select count(*) from aluno_turma where aluno_turma.idturma = turmas.idturma ) as qtd_alunos "
                        ." from turmas inner join "
                        ." professor_turma on "
                        ." professor_turma.idturma = turmas.idturma and "
                        ." professor_turma.idusuario = ". $_SESSION['idusuario'];

                return $this->db->query($sql)->result_array();;
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