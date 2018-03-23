<?php

class Usuario_model extends CI_Model {

        private $table = 'usuarios';


        public function salvar($data){

                if ($data['senha'] == ""){
                        unset($data['senha']);
                }
                if ($data['email'] == ""){
                        unset($data['senha']);
                } else {
                        $data['senha'] = sha1($data['senha']);
                }
                
                
                $this->db->update($this->table,$data,["idusuario"=>$_SESSION['user']['idusuario']]);

                $_SESSION['user'] = $this->get($_SESSION['user']['idusuario']);
        }

        public function inserir($data){
                //$this->output->enable_profiler(TRUE);
                $data['data_criado'] = Date('Y/m/d H:i:s');
                $data['senha'] = sha1($data['senha']);
                return $this->db->insert($this->table,$data);
        }

        public function login($data){
                #this->output->enable_profiler(TRUE);
                $data['senha'] = sha1($data['senha']);
                $this->db->select('idusuario, nome, email, is_professor, foto');
                return $this->db->get_where($this->table,$data);
        }

        public function get($idusuario){
                #this->output->enable_profiler(TRUE);
                $data['senha'] = sha1($data['senha']);
                $this->db->select('idusuario, nome, email, is_professor, foto');
                return $this->db->get_where($this->table,['idusuario'=>$idusuario])->row_array();
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