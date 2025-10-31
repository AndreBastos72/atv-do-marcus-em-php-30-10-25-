<?php
require_once 'Pessoa.php';

class Professor extends Pessoa {
    private $disciplina;
    private $formacao;
    private $salario;
    private $cargaHoraria;

    public function __construct($nome, $cpf, $email, $telefone, $disciplina, $formacao, $salario, $cargaHoraria) {
        parent::__construct($nome, $cpf, $email, $telefone, 'Professor');
        $this->disciplina = $disciplina;
        $this->formacao = $formacao;
        $this->salario = $salario;
        $this->cargaHoraria = $cargaHoraria;
    }

    public function getInfoEspecifica() {
        return "Disciplina: {$this->disciplina} | Formação: {$this->formacao} | Salário: R$ " . number_format($this->salario, 2, ',', '.') . " | Carga Horária: {$this->cargaHoraria}h";
    }

    public function salvarInfoEspecifica() {
        $database = new Database();
        $db = $database->getConnection();

        try {
            $query = "INSERT INTO professores (id, pessoa_id, disciplina, formacao, salario, carga_horaria) 
                      VALUES (:id, :pessoa_id, :disciplina, :formacao, :salario, :carga_horaria)";
            
            $stmt = $db->prepare($query);
            $id_especifico = uniqid();
            $stmt->bindParam(":id", $id_especifico);
            $stmt->bindParam(":pessoa_id", $this->id);
            $stmt->bindParam(":disciplina", $this->disciplina);
            $stmt->bindParam(":formacao", $this->formacao);
            $stmt->bindParam(":salario", $this->salario);
            $stmt->bindParam(":carga_horaria", $this->cargaHoraria);
            
            return $stmt->execute();
            
        } catch(PDOException $exception) {
            throw new Exception("Erro ao salvar professor: " . $exception->getMessage());
        }
    }

    // Getters específicos
    public function getDisciplina() { return $this->disciplina; }
    public function getFormacao() { return $this->formacao; }
    public function getSalario() { return $this->salario; }
    public function getCargaHoraria() { return $this->cargaHoraria; }
}
?>