<?php 
$pag = 'agenda';
@session_start();
require_once('../../conexao.php');
require_once('../verificar-permissao.php');
require_once ('../personalizacoes/personalizacao_agenda.php');


$dataHoje = new DateTime();

$mostrarCancelados = $_GET['mostrarCancelados'] === 'true' ? true : false;
$dataAgenda = $_GET['data'] ?? '';
$pasta = $_SESSION['x_url'] ?? '';



function horarioAbertura($pdo, string $dataAgenda): ?array
{
    // 1. Mapeia o dia da semana para o nome da coluna
    $diaSemanaMap = [
        0 => 'domingo',  // (date('w') em PHP: 0 = domingo)
        1 => 'segunda',
        2 => 'terca',
        3 => 'quarta',
        4 => 'quinta',
        5 => 'sexta',
        6 => 'sabado'
    ];
    $weekdayCol = $diaSemanaMap[(int)date('w', strtotime($dataAgenda))];

    /** ----------------------------------------------------------------
     * 2. Primeiro procura horários ESPECIAIS (padrao = 0 ou NULL)
     * ----------------------------------------------------------------*/
    $sqlEspecial = "
        SELECT inicio, fim
          FROM horario_agenda_estabelecimento
         WHERE (padrao = 0 OR padrao IS NULL)
           AND :data BETWEEN data_inicio AND data_fim
           AND $weekdayCol = 1
         LIMIT 1";
    $stmt = $pdo->prepare($sqlEspecial);
    $stmt->execute([':data' => $dataAgenda]);
    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        return ['inicio' => $row['inicio'], 'fim' => $row['fim']];
    }

    /** ---------------------------------------------------------------
     * 3. Se não achar, cai no HORÁRIO‑PADRÃO (padrao = 1)
     * ---------------------------------------------------------------*/
    $sqlPadrao = "
        SELECT inicio, fim
          FROM horario_agenda_estabelecimento
         WHERE padrao = 1
           AND $weekdayCol = 1
         LIMIT 1";
    $stmt = $pdo->query($sqlPadrao);
    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        return ['inicio' => $row['inicio'], 'fim' => $row['fim']];
    }

    /** ---------------------------------------------------------------
     * 4. Se ainda não achar, verifica se é FERIADO e data já passou
     * ---------------------------------------------------------------*/
    $sqlFeriado = "SELECT 1 FROM feriados WHERE dia = :data LIMIT 1";
    $stmt = $pdo->prepare($sqlFeriado);
    $stmt->execute([':data' => $dataAgenda]);
    $feriado = $stmt->fetchColumn();
    $hoje    = date('Y-m-d');

    if ($feriado && $dataAgenda < $hoje) {
        // fechado por feriado passado
        return null;
    }

    /** ---------------------------------------------------------------
     * 5. Último fallback: se houver agendamentos nesse dia,
     *    usa a menor hora como início e a maior como fim
     * ---------------------------------------------------------------*/
    $sqlAg = "
        SELECT MIN(hora)  AS hIni,
               MAX(ADDTIME(hora, SEC_TO_TIME(tempo_min*60))) AS hFim
          FROM agendamentos
         WHERE data = :data";
    $stmt = $pdo->prepare($sqlAg);
    $stmt->execute([':data' => $dataAgenda]);
    $ag = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($ag && $ag['hIni']) {
        return ['inicio' => substr($ag['hIni'], 0, 5),
                'fim'    => substr($ag['hFim'], 0, 5)];
    }

    /** ---------------------------------------------------------------
     * 6. Nada encontrado: considera fechado
     * ---------------------------------------------------------------*/
    return null;
}





$horario = horarioAbertura($pdo, $dataAgenda);

if ($horario) {
    $abertura_agenda   = $horario['inicio'];   // ex. 08:00
    $fechamento_agenda = $horario['fim'];      // ex. 21:00
     $agendaFechada     = false;
} else {
    // clínica fechada e sem agendamentos → decide o que fazer
    // 1) pode exibir uma mensagem “Clínica fechada” e não mostrar grade,
//  2) ou mostrar grade vazia 08–18 só para visualização
   $agendaFechada = true;
    $MensagemAgenda = 'Sem Agenda Neste Dia';
}




function menorEntrada(array $row) {
    return array_reduce(['e1','e2','e3'], function($carry,$col) use($row){
        $val = $row[$col] ?? '00:00:00';
        if ($val && $val !== '00:00:00' && ($carry===null || $val < $carry)) {
            return $val;
        }
        return $carry;
    }, null);
}
function maiorSaida(array $row) {
    return array_reduce(['s1','s2','s3'], function($carry,$col) use($row){
        $val = $row[$col] ?? '00:00:00';
        if ($val && $val !== '00:00:00' && ($carry===null || $val > $carry)) {
            return $val;
        }
        return $carry;
    }, null);
}





if (!$dataAgenda){
    $dataAgenda = $dataHoje;
}


$agendamentos = []; // Para armazenar todos os agendamentos

$profissionaisDoDia = []; // Para armazenar detalhes dos profissionais agendados
$cor_padrao_fundo_profissional = $cor_fundo_profissional;
$cor_padrao_fonte_profissional = $cor_fonte_profissional;

if (!empty($dataAgenda)) {
    // Busca todos os agendamentos para a data especificada
 

$sql = "
    SELECT 
        a.*, 
        c.foto     AS foto_cliente, 
        c.aniversario,
        c.sexo
    FROM agendamentos AS a
    LEFT JOIN clientes AS c
        ON a.id_cliente = c.id
    WHERE a.data = :dataAgenda
    ORDER BY a.id_profissional_1
";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':dataAgenda', $dataAgenda);
$stmt->execute();
$agendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);





if ($horario && count($agendamentos) > 0) {
    // Menor hora de início dos agendamentos
    $horas = array_column($agendamentos, 'hora');
    $menorHora = min($horas);
    if ($menorHora < $abertura_agenda) {
        $abertura_agenda = substr($menorHora, 0, 5);
    }

    // Maior hora de término dos agendamentos
    $maiorFim = $fechamento_agenda;
    foreach ($agendamentos as $ag) {
        $horaFim = date('H:i', strtotime("{$ag['hora']} +{$ag['tempo_min']} minutes"));
        if ($horaFim > $maiorFim) {
            $maiorFim = $horaFim;
        }
    }
    if ($maiorFim > $fechamento_agenda) {
        $fechamento_agenda = $maiorFim;
    }
}










// Preparação: coletar IDs únicos cliente-serviço
$ids_clientes_servicos = [];
foreach ($agendamentos as $ag) {
    $key = "{$ag['id_cliente']}-{$ag['id_servico']}";
    $ids_clientes_servicos[$key] = [
        'id_cliente' => $ag['id_cliente'],
        'id_servico' => $ag['id_servico']
    ];
}



// ===== PRIMEIRA CONSULTA: venda_itens =====
if (!empty($ids_clientes_servicos)) {

  

  $placeholders_cliente_servico = [];
    $params_cliente_servico = [];

    foreach ($ids_clientes_servicos as $comb) {
        $placeholders_cliente_servico[] = '(id_cliente = ? AND id_item = ?)';
        $params_cliente_servico[] = $comb['id_cliente'];
        $params_cliente_servico[] = $comb['id_servico'];
    }




    $sql_vendas = "
        SELECT id_cliente, id_item, 
            SUM(quantidade - realizados - transferidos - convertidos - descontados) AS saldo
        FROM venda_itens
        WHERE venda = 1
        AND tipo_item = 'servico'
        AND (quantidade - realizados - transferidos - convertidos - descontados) > 0
        AND (data_validade IS NULL OR data_validade = '' OR data_validade >= ?)
        AND (" . implode(' OR ', $placeholders_cliente_servico) . ")
        GROUP BY id_cliente, id_item
    ";

    $stmt_vendas = $pdo->prepare($sql_vendas);
    $stmt_vendas->execute(array_merge([$dataAgenda], $params_cliente_servico));



    $saldo_vendas_map = [];
    while ($row = $stmt_vendas->fetch(PDO::FETCH_ASSOC)) {
        $key = "{$row['id_cliente']}-{$row['id_item']}";
        $saldo_vendas_map[$key] = (int)$row['saldo'];
    }


} else {
    $saldo_vendas_map = [];
}

// ===== SEGUNDA CONSULTA: agendamentos anteriores =====
$sql_agendamentos_anteriores = "
    SELECT id_cliente, id_servico, data, id
    FROM agendamentos
    WHERE status IN ('Agendado', 'Confirmado', 'Em Atendimento', 'Atendimento Concluido', 'Aguardando')
";

$stmt_agenda_ant = $pdo->prepare($sql_agendamentos_anteriores);
$stmt_agenda_ant->execute();

$agendamentos_anteriores = $stmt_agenda_ant->fetchAll(PDO::FETCH_ASSOC);

$agendamentos_map = [];
foreach ($agendamentos_anteriores as $ant) {
    $key = "{$ant['id_cliente']}-{$ant['id_servico']}";
    if (!isset($agendamentos_map[$key])) {
        $agendamentos_map[$key] = [];
    }
    $agendamentos_map[$key][] = ['data' => $ant['data'], 'id' => $ant['id']];
}




// ===== ATUALIZAÇÃO FINAL do array de agendamentos =====
foreach ($agendamentos as &$agendamento) {
    $key = "{$agendamento['id_cliente']}-{$agendamento['id_servico']}";

    $contagem = 0;
    if (!empty($agendamentos_map[$key])) {
        foreach ($agendamentos_map[$key] as $ant) {
            if ($ant['data'] < $agendamento['data'] || 
                ($ant['data'] == $agendamento['data'] && $ant['id'] < $agendamento['id'])) {
                $contagem++;
            }
        }
    }



    $saldo_venda = $saldo_vendas_map[$key] ?? 0;
    $quantidade_disponivel = $saldo_venda - $contagem;

    $agendamento['quantidade'] = $quantidade_disponivel;
}



unset($agendamento);



}




/* ===========================================================
   COMPLEMENTA $profissionaisDoDia  c/ hora_inicio / hora_fim
   =========================================================== */





/* -----------------------------------------------------------------
   1.  Profissionais que já temos (via agendamentos)
------------------------------------------------------------------*/



$idsProfsNaAgenda = array_column($agendamentos, 'id_profissional_1'); ///importante
$idsJaNoArray = array_column($profissionaisDoDia, 'id_profissional');



$idsAgenda = $idsProfsNaAgenda; 
/* -----------------------------------------------------------------
   2.  Busca em contratos_colaboradores quem ainda falta
------------------------------------------------------------------*/
// 1) prepare placeholders para o IN
// 1) placeholders para o IN
// 1) placeholders para o IN (ou 'NULL' se vazio)
// seu array de IDs da agenda$ids = $idsProfsNaAgenda; // ou $idsJaNoArray


$ids = $idsProfsNaAgenda;              // IDs vindos dos agendamentos
$ph  = $ids ? implode(',', array_fill(0, count($ids), '?')) : 'NULL';

/* --------------  SQL  --------------------- */
$sql = "
/* === BLOCO A – contrato vigente (mais recente) ============================ */
SELECT
  c.id, 
  c.nome, 
  c.nome_agenda,
  c.ativo_agenda,
  c.cor_fundo_agenda, 
  c.cor_fonte_agenda, 
  c.foto_agenda, 
  c.id_colaborador, 
  c.ordem_agenda,
  NULL    AS telefone,
  c.especialidade_agenda,
  c.descricao_agenda, 
  c.id_quadro_horario
FROM colaboradores_contratos  AS c
JOIN (
    SELECT id_colaborador, MAX(data_inicio) AS max_inicio
    FROM colaboradores_contratos
    WHERE ativo_agenda = 1
      AND data_inicio <= ?
      AND (
          data_fim IS NULL
       OR data_fim >= ?
       OR data_fim <  data_inicio
      )
    GROUP BY id_colaborador
) subA
  ON subA.id_colaborador = c.id_colaborador
 AND subA.max_inicio    = c.data_inicio

UNION

/* === BLOCO B – na agenda, SEM contrato vigente, último histórico ========== */
SELECT
  c2.id, 
  c2.nome, 
  c2.nome_agenda, 
  c2.ativo_agenda,
  c2.cor_fundo_agenda, 
  c2.cor_fonte_agenda, 
  c2.foto_agenda,
  c2.id_colaborador, 
  c2.ordem_agenda,
  NULL as telefone,
  c2.especialidade_agenda,
  c2.descricao_agenda, 
  c2.id_quadro_horario
FROM colaboradores_contratos AS c2
JOIN (
    SELECT id_colaborador, MAX(data_inicio) AS max_inicio
    FROM colaboradores_contratos
    WHERE id_colaborador IN ($ph)
    GROUP BY id_colaborador
) subB
  ON subB.id_colaborador = c2.id_colaborador
 AND subB.max_inicio    = c2.data_inicio
WHERE NOT EXISTS (                               /* garante que NÃO há vigente */
    SELECT 1
    FROM colaboradores_contratos x
    WHERE x.id_colaborador = c2.id_colaborador
      AND x.ativo_agenda   = 1
      AND x.data_inicio   <= ?
      AND (
          x.data_fim IS NULL
       OR x.data_fim >=  ?
       OR x.data_fim <  x.data_inicio
      )
)";



  

/* ---------- parâmetros: data, data | ids agenda | data, data -------------- */
$params = array_merge(
    [$dataAgenda, $dataAgenda],   // bloco A
    $ids ?: [],                   // ids p/  IN(...)
    [$dataAgenda, $dataAgenda]    // NOT EXISTS do bloco B
);

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$contratos = $stmt->fetchAll(PDO::FETCH_ASSOC);




/* ------------------------------------------------------------------
 * $contratos  → resultado que já contém 1 linha por colaborador
 * $idsProfsNaAgenda → todos os id_profissional_1 da agenda
 * ------------------------------------------------------------------ */

/* 1.  Quais IDs da agenda ainda não retornaram? */
$idsComContrato = array_column($contratos, 'id_colaborador');


$idsSemContrato = array_diff($idsProfsNaAgenda, $idsComContrato);



if ($idsSemContrato) {

    /* 2. Placeholders para o IN (…) */
    $ph = implode(',', array_fill(0, count($idsSemContrato), '?'));

    /* 3. Consulta em colaboradores_cadastros (nomes conferidos) */
    $sqlSemContrato = "
        SELECT
            NULL                      AS id,                 -- não há id do contrato
            c.nome,
            c.nome_agenda,
            c.ativo_agenda,                                   -- já existe na tabela
            c.cor_fundo_agenda,
            c.cor_fonte_agenda,
            c.foto_agenda,
            c.id                       AS id_colaborador,     -- aqui id = colaborador
            c.ordem_agenda,
            c.telefone,
            c.especialidade_agenda,
            c.descricao_agenda,
            NULL                      AS id_quadro_horario    -- não existe na tabela
        FROM colaboradores_cadastros AS c
        WHERE c.id IN ($ph)
    ";

    $stmt = $pdo->prepare($sqlSemContrato);

   
   $stmt->execute(array_values($idsSemContrato));


   /* 4. Junta aos contratos já obtidos */
    $contratos = array_merge($contratos, $stmt->fetchAll(PDO::FETCH_ASSOC));
}

/* -----------------------------------------------------------------
 * 1. Coletar todos os id_colaborador que apareceram em $contratos
 * -----------------------------------------------------------------*/
$idsTodos = array_unique(array_column($contratos, 'id_colaborador'));

if ($idsTodos) {

    /* 2. Buscar telefone desses IDs na tabela colaboradores_cadastros */
    $ph       = implode(',', array_fill(0, count($idsTodos), '?'));
    $sqlTel   = "
        SELECT id      AS id_colaborador,
               telefone
        FROM   colaboradores_cadastros
        WHERE  id IN ($ph)";
    $stmtTel  = $pdo->prepare($sqlTel);
    $stmtTel->execute($idsTodos);

    /* 3. Cria um mapa [ id_colaborador => telefone ]  */
    $mapaTel = $stmtTel->fetchAll(PDO::FETCH_KEY_PAIR);

    /* 4. Percorre $contratos acrescentando telefone quando existir   */
    foreach ($contratos as &$c) {
        if (empty($c['telefone']) && isset($mapaTel[$c['id_colaborador']])) {
            $c['telefone'] = $mapaTel[$c['id_colaborador']];
        }
    }
    unset($c);           // quebra a referência
}




$totalContratos = count($contratos);










function fotoCadastro(PDO $pdo, int $idColaborador): ?string
{
    /*  cache simples em memória ─ evita N consultas iguais  */
    static $cache = [];

    if (array_key_exists($idColaborador, $cache)) {
        return $cache[$idColaborador];      // já temos
    }

    $stmt = $pdo->prepare(
        "SELECT foto_cadastro
         FROM   colaboradores_cadastros
         WHERE  id = ?
         LIMIT  1"
    );
    $stmt->execute([$idColaborador]);

    /*  pode vir string vazia ou NULL ─ normalizamos para NULL  */
    $foto = $stmt->fetchColumn();
    $foto = $foto !== false && $foto !== '' ? $foto : null;

    return $cache[$idColaborador] = $foto;
}





$dow     = (int)date('w', strtotime($dataAgenda));     // 0‑6
$dowCols = ['domingo','segunda','terca','quarta','quinta','sexta','sabado'];


foreach ($contratos as $contr) {

    $idColab = (int)$contr['id_colaborador'];

    /* ——— pula se já incluído ——— */
    if (in_array($idColab, $idsJaNoArray, true)) {
        continue;
    }

 
    
    
    /* -----------------------------------------------------------------
       2a.  tenta horario_agenda_profissionais (ESPECIAL depois PADRÃO)
    ------------------------------------------------------------------*/
    $horaIni = $horaFim = null;

    $paramsHap = [
        ':id_prof' => $idColab,
        ':id_contrato' => $contr['id'],
        ':data' => $dataAgenda
    ];
    $whereBase = "
        id_profissional = :id_prof
        AND id_contrato  = :id_contrato
        AND :data BETWEEN data_inicio AND COALESCE(data_fim, :data)
        AND {$dowCols[$dow]} = 1
    ";

    // (a) especiais
    $sqlEsp = "SELECT * FROM horario_agenda_profissionais
               WHERE ($whereBase) AND (padrao = 0 OR padrao IS NULL)";
    $hapRows = $pdo->prepare($sqlEsp);
    $hapRows->execute($paramsHap);
    $rows = $hapRows->fetchAll(PDO::FETCH_ASSOC);

    // (b) se não achou, padrão
    if (!$rows) {
        $sqlPad = "SELECT * FROM horario_agenda_profissionais
                   WHERE ($whereBase) AND padrao = 1";
        $hapRows = $pdo->prepare($sqlPad);
        $hapRows->execute($paramsHap);
        $rows = $hapRows->fetchAll(PDO::FETCH_ASSOC);
    }

    if ($rows) {
        foreach ($rows as $r) {
            $e = menorEntrada($r);
            $s = maiorSaida($r);
            if ($e && ($horaIni === null || $e < $horaIni)) $horaIni = $e;
            if ($s && ($horaFim === null || $s > $horaFim)) $horaFim = $s;
        }
    }

    /* -----------------------------------------------------------------
       2b.  se ainda sem horário -> busca grade do quadro
    ------------------------------------------------------------------*/
    if (!$horaIni || !$horaFim) {
      
        $idQuadro = (int)$contr['id_quadro_horario'];
        if ($idQuadro) {
            $sqlQuadro = "SELECT * FROM horarios_tabela
                          WHERE id_horario = :idq AND id_dia_semana = :dow";
            $stmQuadro = $pdo->prepare($sqlQuadro);
            $stmQuadro->execute([':idq'=>$idQuadro, ':dow'=>$dow]);
            if ($qt = $stmQuadro->fetch(PDO::FETCH_ASSOC)) {
                $horaIni = menorEntrada($qt);
                $horaFim = maiorSaida($qt);
                
            }
            if(!$horaIni){
                $horaIni = '00:00';
                $horaFim = '00:00';

            }
        }
    }

    /* -----------------------------------------------------------------
       2c.  fallback final → abertura / fechamento do dia
    ------------------------------------------------------------------*/
    if (!$horaIni || !$horaFim) {
      $horaIni = $abertura_agenda;
      $horaFim = $fechamento_agenda;
    }

    /* -----------------------------------------------------------------
       2d.  busca dados de exibição em colaboradores_cadastros
    ------------------------------------------------------------------*/
    //$col = $pdo->prepare("SELECT * FROM colaboradores_contratos WHERE id_colaborador = :id");
    //$col->execute([':id'=>$idColab]);
    //$rowCol = $col->fetch(PDO::FETCH_ASSOC) ?: [];
    //$rowCol=$contratos;
    // nome_agenda — se não houver, usa primeiro nome do campo 'nome'
    
    
    $nomeAgenda = $contr['nome_agenda']
        ?: (isset($contr['nome']) ? strtok($contr['nome'],' ') : '');

    // se ainda vazio, último fallback = “Profissional <id>”
    if (!$nomeAgenda) $nomeAgenda = "Profissional $idColab";

    
$profissionaisDoDia[] = [
        'id_profissional'    => $idColab,
        'id_contrato' => $contr['id'],
        'profissional'       => $contr['nome']          ?? '',
        'profissional_ag'    => $nomeAgenda, //.'  entrada: '. substr($horaIni,0,5) . '  SAida: '. substr($horaFim,0,5),
        'cor_fundo_agenda'   => $contr['cor_fundo_agenda']  ?? $cor_padrao_fundo_profissional,
        'cor_fonte_agenda'   => $contr['cor_fonte_agenda']  ?? $cor_padrao_fonte_profissional,
        'foto_agenda' => !empty($contr['foto_agenda'])
                        ? '/easy/'.$pasta.'/img/cadastro_colaboradores/' . $contr['foto_agenda']
                        : (($fc = fotoCadastro($pdo, $idColab))
                            ? '/easy/'.$pasta.'/img/cadastro_colaboradores/' . $fc
                            : '/easy/img/sem-foto.svg'),
                            
        'descricao_agenda' => $contr['descricao_agenda'],
        'especialidade_agenda' => $contr['especialidade_agenda'],

        'ativo_agenda'       => 1,
        'ordem_agenda'       => $contr['ordem_agenda'],
        'telefone' =>$contr['telefone'],


        'hora_inicio'        => substr($horaIni,0,5),   // HH:MM
        'hora_fim'           => substr($horaFim,0,5)

                
    ];

    $idsJaNoArray[] = $idColab;  // evita duplicar se houver outro contrato
}
























// (1) Monta o array $iAgen com horaIni/horaFim.
$iAgen = [];
foreach ($agendamentos as $iAgendamento) {
    if ($mostrarCancelados || $iAgendamento['status'] != "Cancelado") {
        $horaInicio = DateTime::createFromFormat('H:i:s', $iAgendamento['hora']) 
                      ?: DateTime::createFromFormat('H:i', $iAgendamento['hora']);
        // soma tempo_min
        $horaFimObj = clone $horaInicio;
        $horaFimObj->add(new DateInterval("PT{$iAgendamento['tempo_min']}M"));

        $iAgen[] = [
            'id'       => $iAgendamento['id'],
            'idProf'   => $iAgendamento['id_profissional_1'],
            'horaIni'  => $horaInicio->format('H:i'),
            'horaFim'  => $horaFimObj->format('H:i'),
            'colIndex' => null,
            'left'     => '',
            'width'    => ''
        ];
    }
}



$iAgen = $iAgen ?? [];







// (2) Agrupar $iAgen por profissional
$agByProf = [];
foreach ($iAgen as &$ag) {
    $agByProf[$ag['idProf']][] = &$ag;
}

unset($ag);





foreach ($profissionaisDoDia as &$prof) {
    if (!isset($prof['hora_inicio'])) {            // ainda sem horário
        // calcula pelo $iAgen  (já contém left/width)
        $meus = array_filter($iAgen, fn($ag)=>$ag['idProf']==$prof['id_profissional']);
        if ($meus) {
            $ini = min(array_column($meus,'horaIni'));
            $fim = max(array_column($meus,'horaFim'));
            $prof['hora_inicio'] = $ini;
            $prof['hora_fim']    = $fim;
        } else {
            // fallback clínica
            $prof['hora_inicio'] = $abertura_agenda;
            $prof['hora_fim']    = $fechamento_agenda;
        }
    }
}
unset($prof);


usort($profissionaisDoDia, function($a,$b){
    $ordA = $a['ordem_agenda'] ?? null;
    $ordB = $b['ordem_agenda'] ?? null;
    if ($ordA === $ordB) {
        return strnatcmp($a['profissional_ag'],$b['profissional_ag']);
    }
    if ($ordA === null) return 1;
    if ($ordB === null) return -1;
    return $ordA <=> $ordB;
});
































// Função auxiliar para converter HH:MM => minutos
function timeToMinutes($horaStr) {
    list($h, $m) = explode(':', $horaStr);
    return $h * 60 + $m;
}
// (3) Para cada profissional, vamos:
//     - ordenar todos os agendamentos
//     - criar "clusters" e fazer column assignment por cluster
foreach ($agByProf as &$listaAg) {
    // Ordena por horaIni
    usort($listaAg, function($a, $b){
        return timeToMinutes($a['horaIni']) <=> timeToMinutes($b['horaIni']);
    });

    // Aqui vamos armazenar "clusters" = array de arrays
    $clusters = [];
    $currentCluster = [];
    $currentMaxEnd = -1; // em minutos

    // (3.1) Identificar clusters
    foreach ($listaAg as &$ag) {
        $start = timeToMinutes($ag['horaIni']);
        $end   = timeToMinutes($ag['horaFim']);

        if (empty($currentCluster)) {
            // se cluster está vazio, inicia
            $currentCluster[] = &$ag;
            $currentMaxEnd = $end;
        } else {
            // se este evento começa depois que o cluster terminou
            // (ou seja, start >= currentMaxEnd)
            // -> fecha o cluster anterior e inicia um novo
            if ($start >= $currentMaxEnd) {
                $clusters[] = $currentCluster;
                // inicia um novo cluster
                $currentCluster = [];
                $currentCluster[] = &$ag;
                $currentMaxEnd = $end;
            } else {
                // faz parte do mesmo cluster
                $currentCluster[] = &$ag;
                // se esse evento termina mais tarde, atualiza o currentMaxEnd
                if ($end > $currentMaxEnd) {
                    $currentMaxEnd = $end;
                }
            }
        }
    }
    // se sobrou algo no currentCluster
    if (!empty($currentCluster)) {
        $clusters[] = $currentCluster;
    }

    // (3.2) Agora para cada cluster, rodamos "column assignment"
    //       e definimos left/width
    foreach ($clusters as $cluster) {
        // Monta array para "colunas" => cada item é "fimOcupado"
        $colunas = [];

        // Percorre cada agendamento do cluster
        foreach ($cluster as &$ag) {
            $ini = timeToMinutes($ag['horaIni']);
            $fim = timeToMinutes($ag['horaFim']);

            $colIndex = null;
            foreach ($colunas as $idx => $fimOcupado) {
                if ($ini >= $fimOcupado) {
                    $colIndex = $idx;
                    $colunas[$idx] = $fim; 
                    break;
                }
            }
            if ($colIndex === null) {
                $colIndex = count($colunas);
                $colunas[] = $fim;
            }
            $ag['colIndex'] = $colIndex;
        }
        unset($ag);

        // quantas colunas este cluster precisa?
        $colCount = count($colunas);

        // define left e width p/ cada item do cluster
        foreach ($cluster as &$ag) {
            $colIndex = $ag['colIndex'];
            // ex: 90% para blocos
            $widthPct = 90 / $colCount;
            $leftPct  = $colIndex * $widthPct;
            $ag['width'] = $widthPct; 
            $ag['left']  = $leftPct; 
        }
        unset($ag);
    }
}
unset($listaAg);

// Pronto, agora cada evento que não se sobrepõe com outro (em outro cluster)
// terá colCount=1 => width=90%
// e, se 3 se sobrepõem, dentro daquele cluster => width=30%, etc.




$alturaLinha = $altura_linha_agenda;
$intervaloMin = $intervalo_tempo_agenda;
$intervalo = "+" . $intervaloMin . " minutes";
$intervaloTabela = "PT". $intervaloMin. "M";

 // Defina como true se quiser mostrar os agendamentos cancelados também





/* -----------------------------------------------------------------
   3.  Acrescenta hora_inicio/fim aos que já existiam via agendamentos
------------------------------------------------------------------*/

/* -----------------------------------------------------------------
   4.  Ordenação final: ordem_agenda, depois nome_agenda
------------------------------------------------------------------*/












$tHead='';


//echo '<pre>';
//print_r($profissionaisDoDia);
//echo '</pre>';


$tt_contratos = count($idsJaNoArray);
$tt_prof = $tt_contratos;


$fontSize= min(120/ $tt_prof, 14);


foreach ($profissionaisDoDia as $profissionalAgenda) {

                            $profissional_ag = $profissionalAgenda['profissional_ag']; // NOME NA AGENDA
                            $id_profissional = $profissionalAgenda['id_profissional']; // ID DO PROFISSIONAL
                           
                            $corProfissional = $profissionalAgenda['cor_fundo_agenda'];
                            $corProfissional = !empty($corProfissional) ? $corProfissional : $cor_padrao_fundo_profissional;
                            
                            
                            
                            $corFonteProfissional = $profissionalAgenda['cor_fonte_agenda'];
                            $corFonteProfissional =!empty($corFonteProfissional)? $corFonteProfissional: $cor_padrao_fonte_profissional;
                        
                            $imagem = $profissionalAgenda['foto_agenda'];
                            $caminhoImagem = $imagem;

                            
                            $tHead .=
                            '<th  class="agenda-easy-th" style="background-color:' . $corProfissional . ';"
                                data-nome="'.$profissionalAgenda['profissional'].'" 
                                data-id-profissional="'.$profissionalAgenda['id_profissional'].'" 
                                data-nome-agenda="'.$profissionalAgenda['profissional_ag'].'"
                                data-contrato="'.$profissionalAgenda['id_contrato'].'" 
                                data-descricao="'.$profissionalAgenda['descricao_agenda'].'" 
                                data-entrada="'.substr($profissionalAgenda['hora_inicio'], 0, 5).'" 
                                data-saida="'.substr($profissionalAgenda['hora_fim'], 0, 5).'" 
                                data-especialidade="'.$profissionalAgenda['especialidade_agenda'].'"
                                data-telefone = "'.$profissionalAgenda['telefone'].'">
                                
                                <div class="col-profissional"  style="text-align: center; padding-top: 10px;">
                                    <img src="' . $caminhoImagem . '" alt="Imagem de ' . $profissional_ag . '" style="width:40px; height:40px; display: block; margin: 0 auto; border-radius: 50%;">
                                    <p class="font-head-prof" style="color:' .$corFonteProfissional . '; font-size: ' . $fontSize . 'px;"> ' . $profissional_ag . '</p>
                                </div>
                            </th>'; 
                           
                            };









                            




if ($agendaFechada){
 echo ($MensagemAgenda)?$MensagemAgenda:'';
  echo "<input type='hidden' id='usuarios-data' value=''>";     
}else{
echo '<section class="scrollableTableContainer">';
 
    echo '<table id="easy-table" class="agenda-easy">';
        echo '<thead class="agenda-easy-thead">';                       
                echo '<tr class="agenda-easy-tr agenda-easy-tr-profissionais" style="z-index:5000;">';

                //===================CABEÇALHO===============
                        echo '<th style="background-color:transparent; width:' . $alturaLinha * 2.5 .'px;" class="agenda-easy-th agenda-easy-th-horario"></th>'; //primeira coluna do cabeçalho vazia
                  
                    echo $tHead;
                        

        echo '</thead>';           
                    //===================== FECHA CABEÇALHO ===================//


                    //strtotime("08:00")strtotime("21:00");
                    $startTime = strtotime($abertura_agenda);
                    $endTime = strtotime($fechamento_agenda);
                    $ind = 0;
                    $tdind = 0;
                    for ($i = $startTime; $i <= $endTime; $i = strtotime($intervalo, $i)) {
                        
                        $hora = date("H", $i);
                        $minuto = date("i", $i);
                        $horaMinuto = $hora . ":" . $minuto;
                        $horaMinutoDateTime = DateTime::createFromFormat('H:i', $horaMinuto);
                        $fimIntervalo = clone $horaMinutoDateTime; // Clonar para não alterar o original
                        $fimIntervalo->add(new DateInterval($intervaloTabela)); 


                        echo '<tr class="agenda-easy-tr" style="height:' . $alturaLinha . 'px;">';
                        echo '<td class="agenda-easy-td-horario" data-hora_agenda="'. $horaMinuto .'"  style = "font-size: ' . $alturaLinha * 50 / 100 . 'px; width: 200px;">' . $horaMinuto. '</td>';
 
                                foreach ($profissionaisDoDia as $prof) {
                                 
                                    $ztdInd= 3500-$tdInd;
                                    $tdInd = $tdInd +1;
                                    $horaMinutoTd = $hora . ':' . $minuto;                   // ex. 14:30
                                    $horaIniProf  = substr($prof['hora_inicio'], 0, 5);      // garante HH:MM
                                    $horaFimProf  = substr($prof['hora_fim'],   0, 5);

                                    $outAgenda = ($horaMinutoTd < $horaIniProf || $horaMinutoTd >= $horaFimProf)
                                                ? ' out-agenda' 
                                                : ' in-agenda';

                                    echo '<td class="celula' . $outAgenda . '"                              
                                            data-id_profissional="' . $prof['id_profissional'] . '"
                                            data-data_agenda="' . $dataAgenda . '"
                                            data-profissional="' . $prof['profissional'] . '"
                                            data-hora_agenda="' . $horaMinutoTd . '" 
                                            style="z-index:' . $ztdInd . '; font-size:7px; position:relative; 
                                                    vertical-align:bottom; text-align:right;">';

                                        echo '<p class="font-td-tab">'. $horaMinutoTd .'</p>';

                                         
                                 $ttcol = 0;
                                                  // somente para contagem 
                                       
                                                   $loop = 0;
                                                   $meuLeft = 0;
                                                   $tempoServico = 0;
                                          foreach ($agendamentos as $agendamento) {
                                                    $horaServico = substr($agendamento['hora'], 0, 2);
                                                    $minutoServico = substr($agendamento['hora'], 3, 2);
                                                    $horaMinutoServico = $horaServico . ":" . $minutoServico;
                                                    $horaMinutoServicoDateTime = DateTime::createFromFormat('H:i', $horaMinutoServico);
                                                    $tempoServico = $agendamento['tempo_min'];
                                                    
                                                    //$oAgen=[];
                                                    foreach ($iAgen as $agenItem) {
                                                        if ($agenItem['id']===$agendamento['id']) {
                                                            $meuLeft = $agenItem['left'];
                                                            $meuWidth = $agenItem['width'];
                                                            //echo'<div> Deu certo </div>';

                                                        }

                                                    }



                                                
                                                if($agendamento['status'] == "Nao Realizado") { $corStatus = $corSNaoRealizado; }
                                                if($agendamento['status'] == "Cancelado") { $corStatus = $corSCancelado; }
                                                if($agendamento['status'] == "Faltou") { $corStatus = $corSFaltou; }
                                                if($agendamento['status'] == "Agendado") { $corStatus = $corSAgendado; }
                                                if($agendamento['status'] == "Confirmado") { $corStatus = $corSConfirmado; }
                                                if($agendamento['status'] == "Aguardando") { $corStatus = $corSAguardando; }
                                                if($agendamento['status'] == "Em Atendimento") { $corStatus = $corSAtendimento; }
                                                if($agendamento['status'] == "Atendimento Concluido") { $corStatus = $corSConcluido; }
                                                if($agendamento['status'] == "Finalizado") { $corStatus = $corSFinalizado; }

                                                if ($prof['id_profissional'] == $agendamento['id_profissional_1'] && $agendamento['data'] == $dataAgenda && ($mostrarCancelados == true || ($mostrarCancelados == false && $agendamento['status'] != "Cancelado"))){

                                                
                                                
                                                }
                                                
                                                if ($prof['id_profissional'] == $agendamento['id_profissional_1'] && $horaMinutoServicoDateTime >= $horaMinutoDateTime && $horaMinutoServicoDateTime < $fimIntervalo && $agendamento['data'] == $dataAgenda && ($mostrarCancelados == true || ($mostrarCancelados == false && $agendamento['status'] != "Cancelado"))){
                                                   
                                                    
                                                   $zInd = 4000+$ind;
                                                   $ind = $ind+1;
                                                   $horaFim = clone $horaMinutoServicoDateTime;
                                                    // Adicionamos o intervalo em minutos
                                                   $horaFim->add(new DateInterval("PT{$tempoServico}M"));
                                                  
                                                   // agora vai carregar as informações para cada agendamento, essas informações ficaraõ no balão de informações de quando passa o mouse
                                                   $alturaElemento = (($agendamento['tempo_min']/ $intervaloMin) * $alturaLinha) - (3); //Calcula a altura da linha
                                                  // aqui calcula o top do agendamento com base nos intervalos da agenda, da hora e do tempo de agendamento
                                                   $minutosDesdeInicioTabela = $hora * 60 + $minuto;
                                                   $minutosDesdeInicioServico = $horaServico * 60 + $minutoServico;
                                                   $diferencaMinutos = $minutosDesdeInicioServico - $minutosDesdeInicioTabela;
                                                   $pixelsPorMinuto = $alturaLinha / $intervaloMin;
                                                   // resultado do TOP
                                                   $top = $diferencaMinutos * $pixelsPorMinuto;
                                                   
                                                   //Dados do agendamento
                                                        //classe "agendamento"
                                                        //arrastavel
                                                        //coluna
                                                        //linha
                                                        //id agendamento
                                                        //hora da agenda
                                                // a partir daqui, colocar o if para bloqueio ou agendamento.
                                                $bloqueio = $agendamento['bloqueio'];
                                                $dataObs = htmlspecialchars($agendamento['observacoes'], ENT_QUOTES, 'UTF-8');
                                                
                                                $quantidade = $agendamento['quantidade'];
                                                
                                                if($quantidade and $quantidade>0){
                                                    $statusEtiqueta = 'Pago';
                                                }
                                                if($quantidade and $quantidade<1){
                                                    $statusEtiqueta = 'Pendente';
                                                }

                                                if(!$quantidade){
                                                    $statusEtiqueta='Pendente';

                                                }
                                                if($agendamento['status']=='Finalizado'){
                                                    $statusEtiqueta='Finalizado';
                                                }
                                                if($agendamento['status']=='Faltou'){
                                                    $statusEtiqueta='Faltou';
                                                } 
                                                if($agendamento['status']=='NRealizado'){
                                                    $statusEtiqueta='NRealizado';
                                                }                                               
                                                if($agendamento['status']=='Cancelado'){
                                                    $statusEtiqueta='Cancelado';
                                                }



                                                if ($bloqueio){

                                                        echo '
                                                        
                                                        <div class="bloqueio"
                                                            draggable="true" 
                                                            
                                                            data-id_bloqueio = "' .  $agendamento['id'] . '" 
                                                            data-id-lote-bloqueio = "' .$agendamento['id_bloqueio_lote'] . '" 
                                                            data-serv-hora="' .  $agendamento['hora'] . '" 
                                                            data-serv-id_profissional="' . $agendamento['id_profissional_1'] .'" 
                                                            data-serv-nome_profissional="' . $agendamento['profissional_1'] .'" 
                                                            data-serv-observacoes= "' . $dataObs. '"
                                                            data-titulo-bloqueio = "' .$agendamento['titulo_bloqueio'].'"
                                                            data-tempo-bloqueio = "' .$agendamento['tempo_min'].'"
                                                            style=" 
                                                                height:' . $alturaElemento . 'px;    
                                                                left:' . $meuLeft . '%; 
                                                                width:' . $meuWidth . '%; 
                                                                top: ' .$top . 'px; 
                                                                position: absolute; 
                                                                z-index: ' . $zInd . ' ;">
                                                                <div class="etiqueta-bloqueio">Bloqueio</div>
                                                                <div class="bloqueio-titulo">' .$agendamento['titulo_bloqueio']. '</div>

                                                                        <div class="row">
                                                                                <p class="bloqueio-p">' . $horaMinutoServico. ' </p>
                                                                        </div>

                                                        </div>';


                                                }else{

                                                
                                                        
                                                        if ($agendamento['status'] =='Em Atendimento'){
                                                            
                                                            $StatusClass='Atendimento';} 
                                                        elseif ($agendamento['status'] =='Nao Realizado'){
                                                            $StatusClass='NRealizado';} 
                                                        elseif($agendamento['status']=='Atendimento Concluido'){
                                                            $StatusClass='Concluido';
                                                            }
                                                        else{
                                                               $StatusClass=$agendamento['status'];
                                                            }



                                                        echo '
                                                        
                                                        <div class="agendamento status' . $StatusClass .'"  
                                                            draggable="true" 
                                                            
                                                            data-id_agendamento = "' .  $agendamento['id'] . '" 
                                                            data-serv-dataAgenda="' . $dataAgenda . '" 
                                                            data-serv-hora="' .  $agendamento['hora'] . '" 
                                                            data-serv-cliente= "' . $agendamento['nome_cliente'] . '" 
                                                            data-sexo-cliente = "'.$agendamento['sexo'].'"
                                                            data-id-cliente="' . $agendamento['id_cliente'] . '"
                                                            data-serv-telefone= "' . $agendamento['telefone_cliente'] . '" 
                                                            data-serv-servico="' . $agendamento['servico'] . '"  
                                                            data-serv-id_servico="' . $agendamento['id_servico'] .'"
                                                            data-tempo-min = "'. $agendamento['tempo_min'].'"
                                                                                                            
                                                            data-serv-status="' . $agendamento['status'] .'" 
                                                            data-serv-id_profissional="' . $agendamento['id_profissional_1'] .'" 
                                                            data-serv-nome_profissional="' . $agendamento['profissional_1'] .'" 
                                                            data-serv-preco="' . $agendamento['preco'] .'"
                                                            data-foto_cliente="' . $agendamento['foto_cliente'] . '" 
                                                            data-aniversario="' . $agendamento['aniversario'] . '" 
                                                            data-serv-observacoes= "' . $dataObs. '"
                                                            data-serv-origem="' . $agendamento['origem'] .'"
                                                            data-sala="'.$agendamento['sala'] .'"
                                                            data-equipamento="'.$agendamento['equipamento'] .'"
                                                            data-etiqueta = "'. $statusEtiqueta . '"

                                                            style=" 
                                                                '//background-color:' . $corStatus . '; 
                                                                . '
                                                                height:' . $alturaElemento . 'px;    
                                                                left:' . $meuLeft . '%; 
                                                                width:' . $meuWidth . '%; 
                                                                top: ' .$top . 'px; 
                                                                position: absolute; z-index: ' . $zInd . ' ;">

                                                          <div class="etiqueta-agenda-pagamentos etiquetaStatus'.$statusEtiqueta. ' " data-id_agendamento_et = "' .  $agendamento['id'] . '" >'. $statusEtiqueta. ' </div> 
                                                            
                                                                        <div class="row" >
                                                                        
                                                                            <p class = "agendamento-p" style="max-height:20px; margin-right:35px;" >' . $agendamento['nome_cliente'] . ' </p>
                                                                            <p class = "agendamento-p txt-servico" style="max-height:'.$alturaElemento -24 .'px;" ><b>' . $horaMinutoServico.':</b> ' . $agendamento['servico'] . ' </p>
                                                                        </div>';
                                                                        
                                                                        
                                                                        if($agendamento['sala']){
                                                                            echo '<span class="etiqueta-agenda-sala">
                                                                                        '.$agendamento['sala'].'
                                                                            </span>';
                                                                        }
                                                                                                                                        
                                                                        

                                                        echo '
                                                        </div>';
                                                } 
                                                       
                                                  
                                                  $loop = $loop + 1;
                                              }
                                            }
                                            //
                                            
                                            
                                            
                                   echo '</td>';
                                }
                        echo '</tr>';
                    }
                    
                    echo '</table>';
                    echo '<p><br>
                    <br></p>

                    ';
                    
                    

                    echo '</section>';     
                }

    
    ?>
    <script>
            agendamentos = <?php echo json_encode($agendamentos, JSON_UNESCAPED_UNICODE); ?>;
            // Pode testar:
            console.log(agendamentos);
    </script>
<?php


?>