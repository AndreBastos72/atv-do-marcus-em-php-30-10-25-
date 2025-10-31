<?php
require_once 'Pessoa.php';

class Estudante extends Pessoa {
    private $curso;
    private $matricula;
    private $periodo;

    public function __construct($nome, $cpf, $email, $telefone, $curso, $matricula, $periodo) {
        parent::__construct($nome, $cpf, $email, $telefone, 'Estudante');
        $this->curso = $curso;
        $this->matricula = $matricula;
        $this->periodo = $periodo;
    }

    public function getInfoEspecifica() {
        return "Curso: {$this->curso} | Matrícula: {$this->matricula} | Período: {$this->periodo}";
    }

    public function salvarInfoEspecifica() {
        $database = new Database();
        $db = $database->getConnection();

        try {
            $query = "INSERT INTO estudantes (id, pessoa_id, curso, matricula, periodo) 
                      VALUES (:id, :pessoa_id, :curso, :matricula, :periodo)";
            
            $stmt = $db->prepare($query);
            $id_especifico = uniqid();
            $stmt->bindParam(":id", $id_especifico);
            $stmt->bindParam(":pessoa_id", $this->id);
            $stmt->bindParam(":curso", $this->curso);
            $stmt->bindParam(":matricula", $this->matricula);
            $stmt->bindParam(":periodo", $this->periodo);
            
            return $stmt->execute();
            
        } catch(PDOException $exception) {
            throw new Exception("Erro ao salvar estudante: " . $exception->getMessage());
        }
    }

    // Getters específicos
    public function getCurso() { return $this->curso; }
    public function getMatricula() { return $this->matricula; }
    public function getPeriodo() { return $this->periodo; }
}
?>