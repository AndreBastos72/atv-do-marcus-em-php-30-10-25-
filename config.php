<?php
class Database {
    private $host = 'localhost';
    private $db_name = 'Cadastrodealunosatv25';
    private $username = 'root'; // Altere conforme seu ambiente
    private $password = ''; // Altere conforme seu ambiente
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8",
                $this->username, 
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            echo "Erro de conexão: " . $exception->getMessage();
        }
        return $this->conn;
    }
}

// Iniciar sessão
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Inicializar tema
if (!isset($_SESSION['theme'])) {
    $_SESSION['theme'] = 'light';
}
?>