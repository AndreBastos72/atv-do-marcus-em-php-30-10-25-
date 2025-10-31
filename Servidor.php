<?php
require_once 'Pessoa.php';

class Servidor extends Pessoa {
    private $funcao;
    private $setor;
    private $salario;
    private $dataAdmissao;

    public function __construct($nome, $cpf, $email, $telefone, $funcao, $setor, $salario, $dataAdmissao) {
        parent::__construct($nome, $cpf, $email, $telefone, 'Servidor');
        $this->funcao = $funcao;
        $this->setor = $setor;
        $this->salario = $salario;
        $this->dataAdmissao = $dataAdmissao;
    }

    public function getInfoEspecifica() {
        return "Função: {$this->funcao} | Setor: {$this->setor} | Salário: R$ " . number_format($this->salario, 2, ',', '.') . " | Admissão: " . date('d/m/Y', strtotime($this->dataAdmissao));
    }

    public function salvarInfoEspecifica() {
        $database = new Database();
        $db = $database->getConnection();

        try {
            $query = "INSERT INTO servidores (id, pessoa_id, funcao, setor, salario, data_admissao) 
                      VALUES (:id, :pessoa_id, :funcao, :setor, :salario, :data_admissao)";
            
            $stmt = $db->prepare($query);
            $id_especifico = uniqid();
            $stmt->bindParam(":id", $id_especifico);
            $stmt->bindParam(":pessoa_id", $this->id);
            $stmt->bindParam(":funcao", $this->funcao);
            $stmt->bindParam(":setor", $this->setor);
            $stmt->bindParam(":salario", $this->salario);
            $stmt->bindParam(":data_admissao", $this->dataAdmissao);
            
            return $stmt->execute();
            
        } catch(PDOException $exception) {
            throw new Exception("Erro ao salvar servidor: " . $exception->getMessage());
        }
    }

    // Getters específicos
    public function getFuncao() { return $this->funcao; }
    public function getSetor() { return $this->setor; }
    public function getSalario() { return $this->salario; }
    public function getDataAdmissao() { return $this->dataAdmissao; }
}
?>