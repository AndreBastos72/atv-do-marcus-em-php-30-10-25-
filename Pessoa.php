<?php
require_once 'config.php';

abstract class Pessoa {
    protected $id;
    protected $nome;
    protected $cpf;
    protected $email;
    protected $telefone;
    protected $dataCadastro;
    protected $tipo;

    public function __construct($nome, $cpf, $email, $telefone, $tipo) {
        $this->id = uniqid();
        $this->nome = $nome;
        $this->cpf = $cpf;
        $this->email = $email;
        $this->telefone = $telefone;
        $this->dataCadastro = date('Y-m-d H:i:s');
        $this->tipo = $tipo;
    }

    // Getters
    public function getId() { return $this->id; }
    public function getNome() { return $this->nome; }
    public function getCpf() { return $this->cpf; }
    public function getEmail() { return $this->email; }
    public function getTelefone() { return $this->telefone; }
    public function getDataCadastro() { return $this->dataCadastro; }
    public function getTipo() { return $this->tipo; }

    // Método para salvar no banco
    public function salvar() {
        $database = new Database();
        $db = $database->getConnection();

        try {
            // Inserir na tabela pessoas
            $query = "INSERT INTO pessoas (id, nome, cpf, email, telefone, tipo, data_cadastro) 
                      VALUES (:id, :nome, :cpf, :email, :telefone, :tipo, :data_cadastro)";
            
            $stmt = $db->prepare($query);
            $stmt->bindParam(":id", $this->id);
            $stmt->bindParam(":nome", $this->nome);
            $stmt->bindParam(":cpf", $this->cpf);
            $stmt->bindParam(":email", $this->email);
            $stmt->bindParam(":telefone", $this->telefone);
            $stmt->bindParam(":tipo", $this->tipo);
            $stmt->bindParam(":data_cadastro", $this->dataCadastro);
            
            return $stmt->execute();
            
        } catch(PDOException $exception) {
            throw new Exception("Erro ao salvar pessoa: " . $exception->getMessage());
        }
    }

    abstract public function getInfoEspecifica();
    abstract public function salvarInfoEspecifica();
}
?>