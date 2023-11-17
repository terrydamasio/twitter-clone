<?php
    namespace App\Models;
    
    use MF\Model\Model;
    use PDO;

    class Usuario extends Model {
        private $id;
        private $nome;
        private $email;
        private $senha;

        public function __get($name)
        {
            return $this->$name;
        }

        public function __set($name, $value)
        {
            $this->$name = $value;
        }

        //salvar cadastro
        public function salvar() {
            $query = "insert into usuarios(nome, email, senha) values(:nome, :email, :senha)";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':nome', $this->__get('nome'));
            $stmt->bindValue(':email', $this->__get('email'));
            $stmt->bindValue(':senha', $this->__get('senha')); //md5() -> hash 32 caracteres
            $stmt->execute();
            
            return $this;
        }

        //validar cadastro
        public function validarCadastro() {
            $valido = true;

            if(strlen($this->__get('nome')) < 3)
                $valido = false;
                
            if(strlen($this->__get('email')) < 3)
                $valido = false;

            if(strlen($this->__get('senha')) < 3)
                $valido = false;

            return $valido;
        }

        //recuperar cadastro por email
        public function getUsuario() {
            $query = "select nome, email from usuarios where email = :email";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':email', $this->__get('email'));
            $stmt->execute();

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }

        //autenticar usuario no login
        public function autenticar() {
            $query = "select id, nome, email from usuarios where email = :email and senha = :senha";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(":email", $this->__get('email'));
            $stmt->bindValue(":senha", $this->__get('senha'));
            $stmt->execute();

            $usuario = $stmt->fetch(\PDO::FETCH_ASSOC);

            if($usuario['id'] != '' && $usuario['nome'] != '') {
                $this->__set('id', $usuario['id']);
                $this->__set('nome', $usuario['nome']);
            }

            return $this;
        }   

        //recuperar todos usuarios de acordo com termo de pesquisa
        public function getAllUsers() {
            $query = "select id, nome, email from usuarios where nome like :nome";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':nome', '%'.$this->__get('nome').'%');
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }