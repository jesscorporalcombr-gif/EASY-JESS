<?php

session_start();
require_once('../../conexao.php');
//require_once('../../verificar-permissao.php');
$id_usuario    = $_SESSION['id_usuario'];    // user_criacao ou user_alteracao
$nome_usuario  = $_SESSION['nome_usuario'];  // id_user_criacao ou id_user_alteracao

require_once __DIR__ . '/../../dompdf/vendor/autoload.php'; // Caminho do autoload do Composer


use Dompdf\Dompdf;
use Dompdf\Options;

// Configuração domPDF
$options = new Options();



$options->set('isRemoteEnabled', true); // Ativa imagens externas, se tiver no contrato

$dompdf = new Dompdf($options);

// Conexão com banco (certifique-se que tua conexão $pdo está ativa aqui)

// Recebe os dados do AJAX
$idCliente = $_POST['id_cliente'] ?? '';
$id_venda = $_POST['id_venda'] ?? '';
        //valor_original: DecimalBr($('#valor-original').val()),
        //valor_desconto: DecimalBr($('#valor-desconto').val()),
        //valor_final: DecimalBr($('#valor-final').val()),
        
        //texto_valor_original: $('#txt-valor-original').val(),
        //texto_valor_desconto: $('txt-valor-desconto').val(),
        //texto_valor_final: $('#txt-valor-final').val(),

$valorOriginal = $_POST['valor_original'] ?? '';
$valorDesconto = $_POST['valor_desconto'] ?? '';
$valorFinal = $_POST['valor_final'] ?? '';

$textoValorOriginal = $_POST['texto_valor_original'] ?? '';
$textoValorDesconto = $_POST['texto_valor_desconto'] ?? '';
$textoValorFinal = $_POST['texto_valor_final'] ?? '';

$dataContrato = $_POST['data_contrato'] ?? '';
$diasValidade = $_POST['dias_validade'] ?? '';
echo 'dataContrato';

$textoItensTabela = $_POST['texto_itens_tabela'] ?? '';
$textoItensCobrado = $_POST['texto_itens_cobrado'] ?? '';
// Consulta ao cliente
$query_clientes = $pdo->prepare("SELECT nome, cpf, rg, passaport, outro_documento, aniversario, celular, sexo, endereco, numero, estado, cidade, complemento, bairro FROM clientes WHERE id = :id");
$query_clientes->execute([':id' => $idCliente]);

$cliente = $query_clientes->fetch(PDO::FETCH_ASSOC);

$cpf = $cliente['cpf'];
$celular = formatarCelular($cliente['celular']);
$sexoCliente = strtoupper(substr($cliente['sexo'], 0, 1));
$nome = $cliente['nome'];
$aniversario = formatarData($cliente['aniversario']);
// Pega a data e hora atual
if ($valorDesconto>0){
    $texto_desconto = 'recebe um desconto total de R$ ' .number_format($valorDesconto, 2, ',', '.'). ' e ';
} else{
    $texto_desconto = '';
}
//recebe um desconto total de #VALORDESCONTO (#VALOREXTDESCONTO) e 



$dataHoraAtual = date('YmdHis'); // Ano, mês, dia, hora, minuto, segundo

// Preenche ID da venda com zeros à esquerda (6 dígitos)
$idVendaFormatado = str_pad($id_venda, 6, '0', STR_PAD_LEFT);

// Extrai apenas números do CPF
$cpfNumeros = preg_replace('/\D/', '', $cpf);

// Pega os 5 primeiros números do CPF
$cpfCincoPrimeiros = substr($cpfNumeros, 0, 5);

// Monta o ID do contrato
$id_contrato = 'JC' . $dataHoraAtual . $idVendaFormatado . $cpfCincoPrimeiros;


function dataPorExtenso($dataBR) {
    // Converte de dd/mm/yyyy para yyyy-mm-dd
    $partes = explode('/', $dataBR);
    
    if (count($partes) !== 3) {
        return '';
    }

    list($dia, $mes, $ano) = $partes;
    $dataFormatada = "$ano-$mes-$dia";

    // Converte para timestamp
    $timestamp = strtotime($dataFormatada);

    if (!$timestamp) {
        return '';
    }

    // Meses por extenso
    $meses = [
        1 => 'janeiro',
        2 => 'fevereiro',
        3 => 'março',
        4 => 'abril',
        5 => 'maio',
        6 => 'junho',
        7 => 'julho',
        8 => 'agosto',
        9 => 'setembro',
        10 => 'outubro',
        11 => 'novembro',
        12 => 'dezembro'
    ];

    $dia = date('j', $timestamp);
    $mes = $meses[date('n', $timestamp)];
    $ano = date('Y', $timestamp);

    return "$dia de $mes de $ano";
}

// Usa tua variável
$dataContExtenso = dataPorExtenso($dataContrato);



// Verifica se cliente foi encontrado
if (!$cliente) {
    die('Cliente não encontrado.');
}

// Funções extras para formatar dados
function formatarCPF($cpf) {
    return preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", "$1.$2.$3-$4", preg_replace('/\D/', '', $cpf));
}

function formatarCelular($celular) {
    return preg_replace("/(\d{2})(\d{5})(\d{4})/", "($1) $2-$3", preg_replace('/\D/', '', $celular));
}

function formatarData($data) {
    return date('d/m/Y', strtotime($data));
}


// Variáveis cliente formatadas


// Monta documentos do cliente
$documentos = [];

// Verifica cada campo e adiciona ao array se existir valor
if (!empty($cliente['cpf'])) {
    $documentos[] = 'CPF: ' . formatarCPF($cliente['cpf']);
}

if (!empty($cliente['rg'])) {
    $documentos[] = 'RG: ' . $cliente['rg'];
}

if (empty($documentos) && !empty($cliente['passaport'])) {
    $documentos[] = 'Passaporte: ' . $cliente['passaport'];
}

if (empty($documentos) && !empty($cliente['outro_documento'])) {
    $documentos[] = ' ' . $cliente['outro_documento'];
}

// Junta tudo em uma string final
$documentosTexto = implode(' | ', $documentos);




$endereco = $cliente['endereco'] . ' nº ' . $cliente['numero'] . ', ';
$endereco .= $cliente['complemento'] . ', bairro ' . $cliente['bairro'] . ', ';
$endereco .= 'cidade: ' . $cliente['cidade'] . '-' . $cliente['estado'];


// Gênero para o contrato
if ($sexoCliente === 'F') {
    $sex = 'a';
    $aoSex = 'à';
    $uSex = 'A';
} else {
    $sex = 'o';
    $aoSex = 'ao';
    $uSex = 'O';
}

// Dados financeiros recebidos


// Carrega o template do contrato

if (!file_exists('contrato_servicos.html')) {
    die('Arquivo de template não encontrado.');
}

//contrato_servicos.htm

$valorOriginal = number_format($valorOriginal, 2, ',', '.');
$valorDesconto = number_format($valorDesconto, 2, ',', '.');
$valorFinal = number_format($valorFinal, 2, ',', '.');






$template = file_get_contents('contrato_servicos.html');




// Substitui as tags coringa pelo conteúdo recebido e formatado
$placeholders = [
    '#IDCONTRATO' => $id_contrato,
    '#NOMECLIENTE' => $nome,
    '#DOCUMENTOS#' => $documentosTexto,
    '#NASC' => $aniversario,
    '#ENDERECO' => $endereco,
    '#FONE' => $celular,
    '#DATACONTRATO' => $dataContrato,

    '#DATACONTEXTENSO' => $dataContExtenso,
    '#TEXTOSERVORIGINAL' => $textoItensTabela,
    '#VALORORIGINAL' => 'R$ ' . $valorOriginal,
    '#VALOREXTENSOORIGINAL' => $textoValorOriginal,
    '#SEX' => $sex,
    '#AOSEX' => $aoSex,
    '#USEX' => $uSex,
    '#TEXTODESCONTO' => $texto_desconto,
    '#VALORPROP' => 'R$ ' . $valorFinal,
    '#VALOREXTENSOPROP' => $textoValorFinal,
    '#TEXTOSERVPROP' => $textoItensCobrado,
    '#VALIDADE' => $diasValidade
];

$htmlContrato = str_replace(array_keys($placeholders), array_values($placeholders), $template);

// Gera o PDF
$dompdf->loadHtml($htmlContrato);

$dompdf->setPaper('A4', 'portrait');

$dompdf->render();

// Retorna o PDF para download
header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="contrato.pdf"');
echo $dompdf->output();
exit;
