<?php
require_once 'config.php';
require_once 'classes/Pessoa.php';
require_once 'classes/Estudante.php';
require_once 'classes/Professor.php';
require_once 'classes/Servidor.php';
require_once 'classes/Visitante.php';

// Processar cadastro
if ($_POST && isset($_POST['tipo_pessoa'])) {
    $tipo = $_POST['tipo_pessoa'];
    $nome = $_POST['nome'] ?? '';
    $cpf = $_POST['cpf'] ?? '';
    $email = $_POST['email'] ?? '';
    $telefone = $_POST['telefone'] ?? '';

    // Validar campos básicos
    if (empty($nome) || empty($cpf) || empty($email) || empty($telefone) || empty($tipo)) {
        $_SESSION['erro'] = 'Todos os campos básicos são obrigatórios.';
        header('Location: index.php');
        exit;
    }

    try {
        switch ($tipo) {
            case 'estudante':
                $curso = $_POST['curso'] ?? '';
                $matricula = $_POST['matricula'] ?? '';
                $periodo = $_POST['periodo'] ?? '';
                
                if (empty($curso) || empty($matricula) || empty($periodo)) {
                    throw new Exception('Todos os campos do estudante são obrigatórios.');
                }
                
                $pessoa = new Estudante($nome, $cpf, $email, $telefone, $curso, $matricula, $periodo);
                break;

            case 'professor':
                $disciplina = $_POST['disciplina'] ?? '';
                $formacao = $_POST['formacao'] ?? '';
                $salario = $_POST['salario'] ?? '';
                $cargaHoraria = $_POST['carga_horaria'] ?? '';
                
                if (empty($disciplina) || empty($formacao) || empty($salario) || empty($cargaHoraria)) {
                    throw new Exception('Todos os campos do professor são obrigatórios.');
                }
                
                $pessoa = new Professor($nome, $cpf, $email, $telefone, $disciplina, $formacao, $salario, $cargaHoraria);
                break;

            case 'servidor':
                $funcao = $_POST['funcao'] ?? '';
                $setor = $_POST['setor'] ?? '';
                $salario = $_POST['salario'] ?? '';
                $dataAdmissao = $_POST['data_admissao'] ?? '';
                
                if (empty($funcao) || empty($setor) || empty($salario) || empty($dataAdmissao)) {
                    throw new Exception('Todos os campos do servidor são obrigatórios.');
                }
                
                $pessoa = new Servidor($nome, $cpf, $email, $telefone, $funcao, $setor, $salario, $dataAdmissao);
                break;

            case 'visitante':
                $empresa = $_POST['empresa'] ?? '';
                $motivoVisita = $_POST['motivo_visita'] ?? '';
                $dataVisita = $_POST['data_visita'] ?? '';
                $pessoaContato = $_POST['pessoa_contato'] ?? '';
                
                if (empty($motivoVisita) || empty($dataVisita) || empty($pessoaContato)) {
                    throw new Exception('Motivo da visita, data e pessoa de contato são obrigatórios.');
                }
                
                $pessoa = new Visitante($nome, $cpf, $email, $telefone, $empresa, $motivoVisita, $dataVisita, $pessoaContato);
                break;

            default:
                throw new Exception('Tipo de pessoa inválido.');
        }

        // Salvar no banco de dados
        $pessoa->salvar(); // Salva na tabela pessoas
        $pessoa->salvarInfoEspecifica(); // Salva na tabela específica

        $_SESSION['sucesso'] = ucfirst($tipo) . " '{$nome}' cadastrado(a) com sucesso!";

    } catch (Exception $e) {
        $_SESSION['erro'] = $e->getMessage();
    }

    header('Location: index.php');
    exit;
}

// Se tentou acessar diretamente sem POST, redireciona
header('Location: index.php');
exit;
?>