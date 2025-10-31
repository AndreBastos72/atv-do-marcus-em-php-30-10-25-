<?php
require_once 'Pessoa.php';

class Visitante extends Pessoa {
    private $empresa;
    private $motivoVisita;
    private $dataVisita;
    private $pessoaContato;

    public function __construct($nome, $cpf, $email, $telefone, $empresa, $motivoVisita, $dataVisita, $pessoaContato) {
        parent::__construct($nome, $cpf, $email, $telefone, 'Visitante');
        $this->empresa = $empresa;
        $this->motivoVisita = $motivoVisita;
        $this->dataVisita = $dataVisita;
        $this->pessoaContato = $pessoaContato;
    }

    public function getInfoEspecifica() {
        return "Empresa: {$this->empresa} | Motivo: {$this->motivoVisita} | Data da Visita: " . date('d/m/Y', strtotime($this->dataVisita)) . " | Contato: {$this->pessoaContato}";
    }

    public function salvarInfoEspecifica() {
        $database = new Database();
        $db = $database->getConnection();

        try {
            $query = "INSERT INTO visitantes (id, pessoa_id, empresa, motivo_visita, data_visita, pessoa_contato) 
                      VALUES (:id, :pessoa_id, :empresa, :motivo_visita, :data_visita, :pessoa_contato)";
            
            $stmt = $db->prepare($query);
            $id_especifico = uniqid();
            $stmt->bindParam(":id", $id_especifico);
            $stmt->bindParam(":pessoa_id", $this->id);
            $stmt->bindParam(":empresa", $this->empresa);
            $stmt->bindParam(":motivo_visita", $this->motivoVisita);
            $stmt->bindParam(":data_visita", $this->dataVisita);
            $stmt->bindParam(":pessoa_contato", $this->pessoaContato);
            
            return $stmt->execute();
            
        } catch(PDOException $exception) {
            throw new Exception("Erro ao salvar visitante: " . $exception->getMessage());
        }
    }

    // Getters específicos
    public function getEmpresa() { return $this->empresa; }
    public function getMotivoVisita() { return $this->motivoVisita; }
    public function getDataVisita() { return $this->dataVisita; }
    public function getPessoaContato() { return $this->pessoaContato; }
}
?>