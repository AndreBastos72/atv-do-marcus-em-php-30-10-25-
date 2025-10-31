<?php
session_start();

// Configuração do banco
require_once 'config.php';

// Inicializar tema
if (!isset($_SESSION['theme'])) {
    $_SESSION['theme'] = 'light';
}

// Alternar tema
if (isset($_POST['toggle_theme'])) {
    $_SESSION['theme'] = $_SESSION['theme'] === 'light' ? 'dark' : 'light';
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// Buscar pessoas do banco
function buscarPessoas() {
    $database = new Database();
    $db = $database->getConnection();
    
    $pessoas = [];
    
    try {
        // Buscar pessoas principais
        $query = "SELECT * FROM pessoas ORDER BY data_cadastro DESC";
        $stmt = $db->prepare($query);
        $stmt->execute();
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Buscar informações específicas baseadas no tipo
            switch ($row['tipo']) {
                case 'Estudante':
                    $query_especifica = "SELECT * FROM estudantes WHERE pessoa_id = :pessoa_id";
                    $stmt_esp = $db->prepare($query_especifica);
                    $stmt_esp->bindParam(":pessoa_id", $row['id']);
                    $stmt_esp->execute();
                    $estudante = $stmt_esp->fetch(PDO::FETCH_ASSOC);
                    
                    if ($estudante) {
                        $pessoas[] = [
                            'dados' => $row,
                            'especifico' => $estudante,
                            'tipo' => 'estudante'
                        ];
                    }
                    break;
                    
                case 'Professor':
                    $query_especifica = "SELECT * FROM professores WHERE pessoa_id = :pessoa_id";
                    $stmt_esp = $db->prepare($query_especifica);
                    $stmt_esp->bindParam(":pessoa_id", $row['id']);
                    $stmt_esp->execute();
                    $professor = $stmt_esp->fetch(PDO::FETCH_ASSOC);
                    
                    if ($professor) {
                        $pessoas[] = [
                            'dados' => $row,
                            'especifico' => $professor,
                            'tipo' => 'professor'
                        ];
                    }
                    break;
                    
                case 'Servidor':
                    $query_especifica = "SELECT * FROM servidores WHERE pessoa_id = :pessoa_id";
                    $stmt_esp = $db->prepare($query_especifica);
                    $stmt_esp->bindParam(":pessoa_id", $row['id']);
                    $stmt_esp->execute();
                    $servidor = $stmt_esp->fetch(PDO::FETCH_ASSOC);
                    
                    if ($servidor) {
                        $pessoas[] = [
                            'dados' => $row,
                            'especifico' => $servidor,
                            'tipo' => 'servidor'
                        ];
                    }
                    break;
                    
                case 'Visitante':
                    $query_especifica = "SELECT * FROM visitantes WHERE pessoa_id = :pessoa_id";
                    $stmt_esp = $db->prepare($query_especifica);
                    $stmt_esp->bindParam(":pessoa_id", $row['id']);
                    $stmt_esp->execute();
                    $visitante = $stmt_esp->fetch(PDO::FETCH_ASSOC);
                    
                    if ($visitante) {
                        $pessoas[] = [
                            'dados' => $row,
                            'especifico' => $visitante,
                            'tipo' => 'visitante'
                        ];
                    }
                    break;
            }
        }
        
    } catch(PDOException $exception) {
        // Não mostrar erro para não quebrar o layout
    }
    
    return $pessoas;
}

$pessoas = buscarPessoas();

// Estatísticas
$estatisticas = [
    'total' => count($pessoas),
    'estudantes' => count(array_filter($pessoas, function($p) { return $p['tipo'] === 'estudante'; })),
    'professores' => count(array_filter($pessoas, function($p) { return $p['tipo'] === 'professor'; })),
    'servidores' => count(array_filter($pessoas, function($p) { return $p['tipo'] === 'servidor'; })),
    'visitantes' => count(array_filter($pessoas, function($p) { return $p['tipo'] === 'visitante'; }))
];
?>
<!DOCTYPE html>
<html lang="pt-BR" data-theme="<?php echo $_SESSION['theme']; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🎓 Sistema de Cadastro - IFTO</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="header-content">
                <h1><i class="fas fa-university"></i> Sistema de Cadastro - IFTO</h1>
                <form method="POST" class="theme-form">
                    <button type="submit" name="toggle_theme" class="theme-toggle">
                        <?php if ($_SESSION['theme'] === 'light'): ?>
                            <i class="fas fa-moon"></i> Modo Escuro
                        <?php else: ?>
                            <i class="fas fa-sun"></i> Modo Claro
                        <?php endif; ?>
                    </button>
                </form>
            </div>
        </div>

        <!-- Estatísticas -->
        <div class="stats-container">
            <div class="stat-card total">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-number"><?php echo $estatisticas['total']; ?></div>
                    <div class="stat-label">Total Cadastrado</div>
                </div>
            </div>
            <div class="stat-card estudante">
                <div class="stat-icon">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-number"><?php echo $estatisticas['estudantes']; ?></div>
                    <div class="stat-label">Estudantes</div>
                </div>
            </div>
            <div class="stat-card professor">
                <div class="stat-icon">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-number"><?php echo $estatisticas['professores']; ?></div>
                    <div class="stat-label">Professores</div>
                </div>
            </div>
            <div class="stat-card servidor">
                <div class="stat-icon">
                    <i class="fas fa-briefcase"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-number"><?php echo $estatisticas['servidores']; ?></div>
                    <div class="stat-label">Servidores</div>
                </div>
            </div>
            <div class="stat-card visitante">
                <div class="stat-icon">
                    <i class="fas fa-user-clock"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-number"><?php echo $estatisticas['visitantes']; ?></div>
                    <div class="stat-label">Visitantes</div>
                </div>
            </div>
        </div>

        <!-- Formulário de Cadastro -->
        <div class="card cadastro-card">
            <div class="card-header">
                <h2><i class="fas fa-user-plus"></i> Cadastrar Nova Pessoa</h2>
                <div class="card-actions">
                    <button class="btn btn-outline" onclick="limparFormulario()">
                        <i class="fas fa-broom"></i> Limpar
                    </button>
                </div>
            </div>

            <form action="cadastro.php" method="POST" id="form-cadastro">
                <div class="tipo-pessoa">
                    <div class="tipo-option" data-tipo="estudante" onclick="selecionarTipo('estudante')">
                        <div class="tipo-icon">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <div class="tipo-text">
                            <div class="tipo-title">Estudante</div>
                            <div class="tipo-desc">Vinculado a curso</div>
                        </div>
                    </div>
                    <div class="tipo-option" data-tipo="professor" onclick="selecionarTipo('professor')">
                        <div class="tipo-icon">
                            <i class="fas fa-chalkboard-teacher"></i>
                        </div>
                        <div class="tipo-text">
                            <div class="tipo-title">Professor</div>
                            <div class="tipo-desc">Com disciplina e salário</div>
                        </div>
                    </div>
                    <div class="tipo-option" data-tipo="servidor" onclick="selecionarTipo('servidor')">
                        <div class="tipo-icon">
                            <i class="fas fa-briefcase"></i>
                        </div>
                        <div class="tipo-text">
                            <div class="tipo-title">Servidor</div>
                            <div class="tipo-desc">Função específica</div>
                        </div>
                    </div>
                    <div class="tipo-option" data-tipo="visitante" onclick="selecionarTipo('visitante')">
                        <div class="tipo-icon">
                            <i class="fas fa-user-clock"></i>
                        </div>
                        <div class="tipo-text">
                            <div class="tipo-title">Visitante</div>
                            <div class="tipo-desc">Controle de visitas</div>
                        </div>
                    </div>
                </div>

                <input type="hidden" name="tipo_pessoa" id="tipo_pessoa" value="">

                <div class="form-section">
                    <h3><i class="fas fa-id-card"></i> Informações Básicas</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="nome"><i class="fas fa-user"></i> Nome Completo *</label>
                            <input type="text" id="nome" name="nome" class="form-control" placeholder="Digite o nome completo" required>
                        </div>
                        <div class="form-group">
                            <label for="cpf"><i class="fas fa-id-card"></i> CPF *</label>
                            <input type="text" id="cpf" name="cpf" class="form-control" placeholder="000.000.000-00" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="email"><i class="fas fa-envelope"></i> E-mail *</label>
                            <input type="email" id="email" name="email" class="form-control" placeholder="exemplo@email.com" required>
                        </div>
                        <div class="form-group">
                            <label for="telefone"><i class="fas fa-phone"></i> Telefone *</label>
                            <input type="text" id="telefone" name="telefone" class="form-control" placeholder="(00) 00000-0000" required>
                        </div>
                    </div>
                </div>

                <!-- Campos específicos para Estudante -->
                <div id="campos-estudante" class="campos-dinamicos">
                    <div class="form-section">
                        <h3><i class="fas fa-graduation-cap"></i> Informações do Estudante</h3>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="curso"><i class="fas fa-book"></i> Curso *</label>
                                <input type="text" id="curso" name="curso" class="form-control" placeholder="Ex: Informática, Administração">
                            </div>
                            <div class="form-group">
                                <label for="matricula"><i class="fas fa-id-badge"></i> Matrícula *</label>
                                <input type="text" id="matricula" name="matricula" class="form-control" placeholder="Número da matrícula">
                            </div>
                            <div class="form-group">
                                <label for="periodo"><i class="fas fa-calendar-alt"></i> Período *</label>
                                <input type="number" id="periodo" name="periodo" class="form-control" min="1" max="10" placeholder="Ex: 3">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Campos específicos para Professor -->
                <div id="campos-professor" class="campos-dinamicos">
                    <div class="form-section">
                        <h3><i class="fas fa-chalkboard-teacher"></i> Informações do Professor</h3>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="disciplina"><i class="fas fa-book-open"></i> Disciplina *</label>
                                <input type="text" id="disciplina" name="disciplina" class="form-control" placeholder="Ex: Matemática, Programação">
                            </div>
                            <div class="form-group">
                                <label for="formacao"><i class="fas fa-user-graduate"></i> Formação *</label>
                                <input type="text" id="formacao" name="formacao" class="form-control" placeholder="Ex: Graduação, Mestrado">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="salario_professor"><i class="fas fa-money-bill-wave"></i> Salário *</label>
                                <input type="number" id="salario_professor" name="salario" class="form-control" step="0.01" min="0" placeholder="0,00">
                            </div>
                            <div class="form-group">
                                <label for="carga_horaria"><i class="fas fa-clock"></i> Carga Horária (horas) *</label>
                                <input type="number" id="carga_horaria" name="carga_horaria" class="form-control" min="1" placeholder="Ex: 40">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Campos específicos para Servidor -->
                <div id="campos-servidor" class="campos-dinamicos">
                    <div class="form-section">
                        <h3><i class="fas fa-briefcase"></i> Informações do Servidor</h3>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="funcao"><i class="fas fa-tasks"></i> Função *</label>
                                <input type="text" id="funcao" name="funcao" class="form-control" placeholder="Ex: Coordenador, Secretário">
                            </div>
                            <div class="form-group">
                                <label for="setor"><i class="fas fa-building"></i> Setor *</label>
                                <input type="text" id="setor" name="setor" class="form-control" placeholder="Ex: Administração, TI">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="salario_servidor"><i class="fas fa-money-bill-wave"></i> Salário *</label>
                                <input type="number" id="salario_servidor" name="salario" class="form-control" step="0.01" min="0" placeholder="0,00">
                            </div>
                            <div class="form-group">
                                <label for="data_admissao"><i class="fas fa-calendar-plus"></i> Data de Admissão *</label>
                                <input type="date" id="data_admissao" name="data_admissao" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Campos específicos para Visitante -->
                <div id="campos-visitante" class="campos-dinamicos">
                    <div class="form-section">
                        <h3><i class="fas fa-user-clock"></i> Informações do Visitante</h3>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="empresa"><i class="fas fa-building"></i> Empresa/Instituição</label>
                                <input type="text" id="empresa" name="empresa" class="form-control" placeholder="Nome da empresa">
                            </div>
                            <div class="form-group">
                                <label for="motivo_visita"><i class="fas fa-comment-alt"></i> Motivo da Visita *</label>
                                <input type="text" id="motivo_visita" name="motivo_visita" class="form-control" placeholder="Ex: Reunião, Visita técnica">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="data_visita"><i class="fas fa-calendar-day"></i> Data da Visita *</label>
                                <input type="date" id="data_visita" name="data_visita" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="pessoa_contato"><i class="fas fa-user-tie"></i> Pessoa de Contato *</label>
                                <input type="text" id="pessoa_contato" name="pessoa_contato" class="form-control" placeholder="Nome do contato no IFTO">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save"></i> Cadastrar Pessoa
                    </button>
                </div>
            </form>
        </div>

        <!-- Lista de Pessoas Cadastradas -->
        <div class="card">
            <div class="card-header">
                <h2><i class="fas fa-users"></i> Pessoas Cadastradas (<?php echo $estatisticas['total']; ?>)</h2>
                <div class="card-actions">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" id="search-input" placeholder="Buscar pessoas..." onkeyup="filtrarPessoas()">
                    </div>
                </div>
            </div>
            
            <?php if (empty($pessoas)): ?>
                <div class="empty-state">
                    <i class="fas fa-users-slash"></i>
                    <h3>Nenhuma pessoa cadastrada ainda</h3>
                    <p>Comece cadastrando a primeira pessoa usando o formulário acima.</p>
                </div>
            <?php else: ?>
                <div class="filtros">
                    <button class="filtro-btn active" onclick="filtrarPorTipo('todos')">Todos</button>
                    <button class="filtro-btn" onclick="filtrarPorTipo('estudante')">
                        <i class="fas fa-graduation-cap"></i> Estudantes
                    </button>
                    <button class="filtro-btn" onclick="filtrarPorTipo('professor')">
                        <i class="fas fa-chalkboard-teacher"></i> Professores
                    </button>
                    <button class="filtro-btn" onclick="filtrarPorTipo('servidor')">
                        <i class="fas fa-briefcase"></i> Servidores
                    </button>
                    <button class="filtro-btn" onclick="filtrarPorTipo('visitante')">
                        <i class="fas fa-user-clock"></i> Visitantes
                    </button>
                </div>

                <div class="grid" id="pessoas-grid">
                    <?php foreach ($pessoas as $pessoa): ?>
                        <div class="pessoa-card <?php echo $pessoa['tipo']; ?>" data-tipo="<?php echo $pessoa['tipo']; ?>">
                            <div class="pessoa-header">
                                <div class="pessoa-avatar">
                                    <?php if ($pessoa['tipo'] === 'estudante'): ?>
                                        <i class="fas fa-graduation-cap"></i>
                                    <?php elseif ($pessoa['tipo'] === 'professor'): ?>
                                        <i class="fas fa-chalkboard-teacher"></i>
                                    <?php elseif ($pessoa['tipo'] === 'servidor'): ?>
                                        <i class="fas fa-briefcase"></i>
                                    <?php else: ?>
                                        <i class="fas fa-user-clock"></i>
                                    <?php endif; ?>
                                </div>
                                <div class="pessoa-tipo">
                                    <span class="badge badge-<?php echo $pessoa['tipo']; ?>">
                                        <?php echo $pessoa['dados']['tipo']; ?>
                                    </span>
                                </div>
                            </div>
                            <div class="pessoa-info">
                                <h4><?php echo htmlspecialchars($pessoa['dados']['nome']); ?></h4>
                                <p class="pessoa-cpf"><?php echo htmlspecialchars($pessoa['dados']['cpf']); ?></p>
                                <p class="pessoa-email">
                                    <i class="fas fa-envelope"></i>
                                    <?php echo htmlspecialchars($pessoa['dados']['email']); ?>
                                </p>
                                <p class="pessoa-telefone">
                                    <i class="fas fa-phone"></i>
                                    <?php echo htmlspecialchars($pessoa['dados']['telefone']); ?>
                                </p>
                                
                                <div class="pessoa-especifica">
                                    <?php if ($pessoa['tipo'] === 'estudante'): ?>
                                        <p><i class="fas fa-book"></i> <strong>Curso:</strong> <?php echo htmlspecialchars($pessoa['especifico']['curso']); ?></p>
                                        <p><i class="fas fa-id-badge"></i> <strong>Matrícula:</strong> <?php echo htmlspecialchars($pessoa['especifico']['matricula']); ?></p>
                                        <p><i class="fas fa-calendar-alt"></i> <strong>Período:</strong> <?php echo $pessoa['especifico']['periodo']; ?>º</p>
                                    <?php elseif ($pessoa['tipo'] === 'professor'): ?>
                                        <p><i class="fas fa-book-open"></i> <strong>Disciplina:</strong> <?php echo htmlspecialchars($pessoa['especifico']['disciplina']); ?></p>
                                        <p><i class="fas fa-user-graduate"></i> <strong>Formação:</strong> <?php echo htmlspecialchars($pessoa['especifico']['formacao']); ?></p>
                                        <p><i class="fas fa-money-bill-wave"></i> <strong>Salário:</strong> R$ <?php echo number_format($pessoa['especifico']['salario'], 2, ',', '.'); ?></p>
                                        <p><i class="fas fa-clock"></i> <strong>Carga Horária:</strong> <?php echo $pessoa['especifico']['carga_horaria']; ?>h</p>
                                    <?php elseif ($pessoa['tipo'] === 'servidor'): ?>
                                        <p><i class="fas fa-tasks"></i> <strong>Função:</strong> <?php echo htmlspecialchars($pessoa['especifico']['funcao']); ?></p>
                                        <p><i class="fas fa-building"></i> <strong>Setor:</strong> <?php echo htmlspecialchars($pessoa['especifico']['setor']); ?></p>
                                        <p><i class="fas fa-money-bill-wave"></i> <strong>Salário:</strong> R$ <?php echo number_format($pessoa['especifico']['salario'], 2, ',', '.'); ?></p>
                                        <p><i class="fas fa-calendar-plus"></i> <strong>Admissão:</strong> <?php echo date('d/m/Y', strtotime($pessoa['especifico']['data_admissao'])); ?></p>
                                    <?php else: ?>
                                        <p><i class="fas fa-building"></i> <strong>Empresa:</strong> <?php echo htmlspecialchars($pessoa['especifico']['empresa'] ?: 'Não informado'); ?></p>
                                        <p><i class="fas fa-comment-alt"></i> <strong>Motivo:</strong> <?php echo htmlspecialchars($pessoa['especifico']['motivo_visita']); ?></p>
                                        <p><i class="fas fa-calendar-day"></i> <strong>Data Visita:</strong> <?php echo date('d/m/Y', strtotime($pessoa['especifico']['data_visita'])); ?></p>
                                        <p><i class="fas fa-user-tie"></i> <strong>Contato:</strong> <?php echo htmlspecialchars($pessoa['especifico']['pessoa_contato']); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="pessoa-footer">
                                <span class="pessoa-data">
                                    <i class="fas fa-calendar"></i>
                                    Cadastrado em: <?php echo date('d/m/Y H:i', strtotime($pessoa['dados']['data_cadastro'])); ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Selecionar tipo de pessoa
        function selecionarTipo(tipo) {
            // Remover seleção de todos os tipos
            document.querySelectorAll('.tipo-option').forEach(opt => {
                opt.classList.remove('selected');
            });
            
            // Adicionar seleção ao tipo clicado
            document.querySelector(`[data-tipo="${tipo}"]`).classList.add('selected');
            
            // Atualizar campo hidden
            document.getElementById('tipo_pessoa').value = tipo;
            
            // Ocultar todos os campos dinâmicos
            document.querySelectorAll('.campos-dinamicos').forEach(campo => {
                campo.classList.remove('active');
            });
            
            // Mostrar campos do tipo selecionado
            document.getElementById(`campos-${tipo}`).classList.add('active');
        }

        // Máscaras para os campos
        document.getElementById('cpf').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length <= 11) {
                value = value.replace(/(\d{3})(\d)/, '$1.$2')
                           .replace(/(\d{3})(\d)/, '$1.$2')
                           .replace(/(\d{3})(\d{1,2})$/, '$1-$2');
            }
            e.target.value = value;
        });

        document.getElementById('telefone').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length === 11) {
                value = value.replace(/(\d{2})(\d)/, '($1) $2')
                           .replace(/(\d{5})(\d)/, '$1-$2');
            } else if (value.length === 10) {
                value = value.replace(/(\d{2})(\d)/, '($1) $2')
                           .replace(/(\d{4})(\d)/, '$1-$2');
            }
            e.target.value = value;
        });

        // Limpar formulário
        function limparFormulario() {
            document.getElementById('form-cadastro').reset();
            document.querySelectorAll('.tipo-option').forEach(opt => {
                opt.classList.remove('selected');
            });
            document.querySelectorAll('.campos-dinamicos').forEach(campo => {
                campo.classList.remove('active');
            });
            document.getElementById('tipo_pessoa').value = '';
        }

        // Filtrar pessoas
        function filtrarPessoas() {
            const searchTerm = document.getElementById('search-input').value.toLowerCase();
            const pessoas = document.querySelectorAll('.pessoa-card');
            
            pessoas.forEach(pessoa => {
                const texto = pessoa.textContent.toLowerCase();
                if (texto.includes(searchTerm)) {
                    pessoa.style.display = 'block';
                } else {
                    pessoa.style.display = 'none';
                }
            });
        }

        // Filtrar por tipo
        function filtrarPorTipo(tipo) {
            // Atualizar botões ativos
            document.querySelectorAll('.filtro-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.classList.add('active');
            
            const pessoas = document.querySelectorAll('.pessoa-card');
            
            pessoas.forEach(pessoa => {
                if (tipo === 'todos' || pessoa.dataset.tipo === tipo) {
                    pessoa.style.display = 'block';
                } else {
                    pessoa.style.display = 'none';
                }
            });
        }

        // Validar formulário
        document.getElementById('form-cadastro').addEventListener('submit', function(e) {
            const tipoSelecionado = document.getElementById('tipo_pessoa').value;
            if (!tipoSelecionado) {
                e.preventDefault();
                alert('Por favor, selecione um tipo de pessoa.');
                return false;
            }
        });
    </script>
</body>
</html>