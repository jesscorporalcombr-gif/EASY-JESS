


<?php 
$pag = 'agenda';
@session_start();
require_once('../../conexao.php');
require_once('../verificar-permissao.php');
require_once ('../personalizacoes/personalizacao_agenda.php');


$mostrarCancelados = $_GET['mostrarCancelados'] === 'true' ? true : false;

$dataAgenda = $_GET['data'] ?? '';
$agendamentos = []; // Para armazenar todos os agendamentos
$profissionaisAgendados = []; // Para armazenar profissionais agendados sem repetir
$usuarios = []; // Para armazenar detalhes dos profissionais agendados
//$cor_padrao = "#683769";

if (!empty($dataAgenda)) {
    // Busca todos os agendamentos para a data especificada
    $queryAgendamentos = $pdo->prepare("SELECT * FROM agendamentos WHERE data = :dataAgenda ORDER BY id_profissional_1");
    $queryAgendamentos->bindParam(':dataAgenda', $dataAgenda);
    $queryAgendamentos->execute();
    $agendamentos = $queryAgendamentos->fetchAll(PDO::FETCH_ASSOC);

    // Processar resultados para construir as duas listas necessárias
    $idsProfissionaisUnicos = [];
    foreach ($agendamentos as $agendamento) {
        // Adiciona a lista de agendamentos completa
        // (Pode optar por incluir uma lógica condicional aqui, se necessário, para tratar campos específicos)

        // Filtra para obter uma lista única de IDs de profissionais
        if (!in_array($agendamento['id_profissional_1'], $idsProfissionaisUnicos)) {
            $idsProfissionaisUnicos[] = $agendamento['id_profissional_1'];
            
            // Supõe que cada agendamento contém 'id_profissional_1' e 'profissional_1'
            $profissionaisAgendados[] = [
                'id_profissional' => $agendamento['id_profissional_1'],
                'profissional' => $agendamento['profissional_1'],
                
                // Outros campos necessários podem ser adicionados aqui
            ];
        }
    }



    $dataHoje = new DateTime();
    $DataAgComp = new DateTime($dataAgenda);
    $dataHoje->setTime(0, 0, 0);
    $DataAgComp->setTime(0, 0, 0);


    $condicoesWhere = [];
    
    // Se dataAgenda >= hoje, adiciona condição para ativo_agenda
    if ($DataAgComp >= $dataHoje) {
        $condicoesWhere[] = "ativo_agenda = true";
       // echo '<input type="text" value="chegou">';
    }
    
    // Sempre adiciona condição para incluir profissionais agendados
    $idsProfissionais = implode(',', array_map(function($profissional) { return $profissional['id_profissional']; }, $profissionaisAgendados));
    if (!empty($idsProfissionais)) { // Garante que a lista não esteja vazia
        $condicoesWhere[] = "id IN ($idsProfissionais)";
        
    }
    
    // Combina as condições com OR (ajuste conforme necessário)
    $queryWhere = implode(' OR ', $condicoesWhere);
    
    $query = "SELECT * FROM colaboradores_cadastros WHERE $queryWhere";

    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $profissionais = $stmt->fetchAll(PDO::FETCH_ASSOC);


    foreach ($profissionais as $profissional) {// Utiliza os detalhes previamente carregados
    
        $usuarios[] = [
            'id_profissional' => $profissional['id'],
            'profissional' => $profissional['nome'],
            'profissional_ag' => $profissional['nome_agenda'],
            'cor_agenda' => $profissional ? $profissional['cor_agenda'] : '#683769', // Exemplo de cor padrão
            'foto_agenda' => $profissional ? $profissional['foto_agenda'] : 'caminho/padrão/para/imagem.jpg',
            'ativo_agenda' => $profissional['ativo_agenda'] ,
            'situacao' => $profissional['cor_agenda']
        ];
    }

  
    }

$tt_prof = @count($usuarios);



// (1) Monta o array $iAgen com horaIni/horaFim. Você já faz isso:
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

// Função auxiliar para converter HH:MM => minutos
function timeToMinutes($horaStr) {
    list($h, $m) = explode(':', $horaStr);
    return $h * 60 + $m;
}

// (2) Agrupar $iAgen por profissional
$agByProf = [];
foreach ($iAgen as &$ag) {
    $agByProf[$ag['idProf']][] = &$ag;
}
unset($ag);

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




 
echo '<section class="scrollableTableContainer" style="padding-left: 25px;">';
        echo '<table id="easy-table" class="agenda-easy">';
        echo '<thead class="agenda-easy-thead">';                       
                echo '<tr class="agenda-easy-tr agenda-easy-tr-profissionais" style="z-index:5000;">';

                //===================CABEÇALHO===============
                        echo '<th style="background-color:transparent; width:' . $alturaLinha * 2.5 .'px;" class="agenda-easy-th agenda-easy-th-horario"></th>'; //primeira coluna do cabeçalho vazia
                  
                    
                        foreach ($usuarios as $usuario) {
                            $profissional_ag = $usuario['profissional_ag'];
                            $id_profissional = $usuario['id_profissional'];
                            $corProfissional = $usuario['cor_agenda'];
                            $corProfissional = !empty($corProfissional) ? $corProfissional : '#683769';
                            $imagem = $usuario['foto_agenda'];
                            $caminhoImagem = !empty($imagem) ? "/easy/img/cadastro_colaboradores/{$imagem}" : '/easy/img/sem-foto.svg';
                            
                            echo '<th  class="agenda-easy-th" style="background-color:' . $corProfissional . ';">';
                            echo '<div class="col-profissional" style="text-align: center; padding-top: 10px;"><img src="' . $caminhoImagem . '" alt="Imagem de ' . $profissional_ag . '" style="width:40px; height:40px; display: block; margin: 0 auto; border-radius: 50%;"><p class="font-head-prof" style="font-size: ' . min(100 / $tt_prof, 25) . 'px;"> ' . $profissional_ag . '</p></div>';
                            echo '</th>';
                            };

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
                        echo '<td class="agenda-easy-td-horario" style = "font-size: ' . $alturaLinha * 50 / 100 . 'px; width: 200px;">' . $horaMinuto. '</td>';
 
                                foreach ($usuarios as $prof) {
                                 
                                    $ztdInd= 3500-$tdInd;
                                    $tdInd = $tdInd +1;
                               
                                    
                           echo '<td class ="celula" data-id_profissional="' . $prof['id_profissional'] . '" data-data_agenda="' . $dataAgenda . '" data-profissional="' . $prof['profissional'] . '" data-hora_agenda="' . $hora . ':' . $minuto . '"  style="z-index: ' . $ztdInd . '; font-size: 7px; position: relative; vertical-align: bottom; text-align: right; ">';
                                    echo '<p class = "font-td-tab" >'. $hora . ':' . $minuto . '</p>';

                                         
                                 $ttcol = 0;
                                                  // somente para contagem 
                                        foreach ($agendamentos as $agendamento) {
                                                        
                                                        $horaServico =substr($agendamento['hora'], 0, 2); // hora está formadado como hh:mm:ss do banco
                                                        $minutoServico = substr($agendamento['hora'], 3, 2); //substr($agendamento['hora'], 3, 5); 
                                                       
                                                        $horaMinutoServico = $horaServico . ":" . $minutoServico;
                                                        $horaMinutoServicoDateTime = DateTime::createFromFormat('H:i', $horaMinutoServico);
  
                                                        if ($prof['id_profissional'] == $agendamento['id_profissional_1'] && $horaMinutoServicoDateTime >= $horaMinutoDateTime && $horaMinutoServicoDateTime < $fimIntervalo && $agendamento['data'] == $dataAgenda && ($mostrarCancelados == true || ($mostrarCancelados == false && $agendamento['status'] != "Cancelado"))) {
                                                            $ttcol = $ttcol +1 ;
                                                                
                                                            foreach ($agendamentos as $key => $agendamento) {
                                                                // Obtendo o id_cliente do agendamento atual
                                                                $id_cliente = $agendamento['id_cliente'];
                                                            
                                                                // Query para buscar o cliente correspondente no banco de dados
                                                                $sql = "SELECT foto, aniversario FROM clientes WHERE id = :id_cliente";
                                                                $stmt = $pdo->prepare($sql);
                                                                $stmt->bindParam(':id_cliente', $id_cliente);
                                                                $stmt->execute();
                                                            
                                                                // Verificando se o cliente foi encontrado
                                                                if ($cliente = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                                                    // Adicionando os campos foto_cliente e aniversario ao agendamento atual
                                                                    $agendamentos[$key]['foto_cliente'] = $cliente['foto'];
                                                                    $agendamentos[$key]['aniversario'] = $cliente['aniversario'];
                                                                }
                                                            }
                                                                
                                                            }
                                        }     
                                                  
                                                     
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



                                                if($agendamento['status'] == "Pago") { $corStatus = $corSPago; }
                                                if($agendamento['status'] == "Cancelado") { $corStatus = $corSCancelado; }
                                                if($agendamento['status'] == "Faltou") { $corStatus = $corSFaltou; }
                                                if($agendamento['status'] == "Agendado") { $corStatus = $corSAgendado; }
                                                if($agendamento['status'] == "Confirmado") { $corStatus = $corSConfirmado; }
                                                if($agendamento['status'] == "Aguardando") { $corStatus = $corSAguardando; }
                                                if($agendamento['status'] == "Em Atendimento") { $corStatus = $corSAtendimento; }
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
                                                
                                                $dataObs = htmlspecialchars($agendamento['observacoes'], ENT_QUOTES, 'UTF-8');

                                                echo '
                                                
                                                <div class="agendamento"  
                                                    draggable="true" 
                                                    data-id_agendamento = "' .  $agendamento['id'] . '" 
                                                    data-serv-dataAgenda="' . $dataAgenda . '" 
                                                    data-serv-hora="' .  $agendamento['hora'] . '" 
                                                    data-serv-observacoes= "' . $dataObs. '" 
                                                    data-serv-cliente= "' . $agendamento['nome_cliente'] . '" 
                                                    data-serv-servico="' . $agendamento['servico'] . '"  
                                                    data-serv-telefone= "' . $agendamento['telefone_cliente'] . '" 
                                                    data-serv-status="' . $agendamento['status'] .'" 
                                                    data-serv-id_profissional="' . $agendamento['id_profissional_1'] .'" 
                                                    data-foto_cliente="' . $agendamento['foto_cliente'] . '" 
                                                    data-aniversario="' . $agendamento['aniversario'] . '" 
                                                    data-serv-id_servico="' . $agendamento['id_servico'] .'" 
                                                    style=" 
                                                        background-color:' . $corStatus . '; 
                                                        height:' . $alturaElemento . 'px;    
                                                        left:' . $meuLeft . '%; 
                                                        width:' . $meuWidth . '%; 
                                                        top: ' .$top . 'px; 
                                                        position: absolute; z-index: ' . $zInd . ' ;">
                                                    
                                                                <div class="row" >';
                                                                if ($agendamento['foto_cliente'] && $agendamento['foto_cliente'] != "sem-foto.svg"){ echo ' 
                                                                    
                                                                    <div class="col-md-1" style="margin-left: 5px; margin-top:5px;">
                                                                        <img src="../img/clientes/'. $agendamento['foto_cliente'] . '" style="width:20px; border-radius:20%;">
                                                                    </div>
                                                                    <div class="col-md-10">
                                                                            <p class = "agendamento-p">' . $agendamento['nome_cliente'] . '</p>
                                                                    </div>
                                                                </div>

                                                                    <div class="row">
                                                                        <p class="agendamento-p">' . $horaMinutoServico.'- ' . $agendamento['servico'] . ' </p>
                                                                    </div>';

                                                                } else { echo '
                                                                    <p class = "agendamento-p">' . $agendamento['nome_cliente'] . ' <br>
                                                                        ' . $horaMinutoServico.' - ' . $agendamento['servico'] . ' </p>
                                                                </div>';
                                                                }

                                                echo '
                                                </div>';
                                                       
                                                       
                                                  
                                                  $loop = $loop + 1;
                                              }
                                            }
                                            //
                                            
                                            
                                            
                                   echo '</td>';
                                }
                        echo '</tr>';
                    }
                    
                    echo '</table>';
                    echo '<p>Total de Registros: $total_usuarios</p>';
          $usuariosJson = htmlspecialchars(json_encode($usuarios), ENT_QUOTES, 'UTF-8');

// Inclua o JSON em um input hidden no HTML
        echo "<input type='hidden' id='usuarios-data' value='" . $usuariosJson . "'>";       
                    

        echo '</section>';     
            
        
   
    
    ?>