<?php

class Usuario_model extends CI_Model {

        private $table = 'usuarios';



        public function salvar($data){

                if ($data['senha'] == ""){
                        unset($data['senha']);
                } else {
                        $data['senha'] = sha1($data['senha']);
                }
                

                if (isset($_SESSION['user'])){
                        $this->db->update($this->table,$data,["idusuario"=>$_SESSION['user']['idusuario']]);
                } else {
                        $data['token'] = null;
                        $this->db->update($this->table,$data,["idusuario"=>$_SESSION['novaSenha']['idusuario']]);
                }


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
                return $this->db->get_where($this->table,$data)->row_array();
        }

        public function recuperarByToken($token){
                return $this->db->get_where($this->table,['token'=>$token])->row_array();
        }

        public function recuperar($data){
                global $nomeSistema;

                #this->output->enable_profiler(TRUE);
                $this->db->select('idusuario, nome, email, is_professor, foto');
                $data = $this->db->get_where($this->table,['email'=>$data['email']])->row_array();
                
                $token = sha1(date('dmYhis'));
                $this->db->update($this->table,["token"=>$token],["idusuario"=>$data['idusuario']]);


                $link = base_url("login/token/$token");

                $msg = "Esse e-mail foi enviado pois aparentemente você perdeu a sua senha de acesso ao $nomeSistema."
                        ." Caso você não tenha solicitado a recuperação de senha, ignore este e-mail. "
                        ." Clique no link a seguir para digitar uma nova senha: <a href='$link'>$link</a>. ";

                //mail($data['email'], $nomeSistema." - Recuperação de senha", $msg);

                print $msg;

                return $data;
        }

        public function get($idusuario){
                $this->db->select('idusuario, nome, email, is_professor, foto');
                return $this->db->get_where($this->table,['idusuario'=>$idusuario])->row_array();
        }

}