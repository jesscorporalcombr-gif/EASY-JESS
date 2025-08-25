
//ELECT JS BOOTSTRAP

function hexToRgb($hex) {
    $hex = str_replace("#", "", $hex);
    
    if(strlen($hex) == 3) {
      $r = hexdec(substr($hex,0,1).substr($hex,0,1));
      $g = hexdec(substr($hex,1,1).substr($hex,1,1));
      $b = hexdec(substr($hex,2,1).substr($hex,2,1));
    } else {
      $r = hexdec(substr($hex,0,2));
      $g = hexdec(substr($hex,2,2));
      $b = hexdec(substr($hex,4,2));
    }
    
    return "$r, $g, $b"; // Retorna o resultado no formato 'r, g, b'
  }



function calculaHoraFim(horaIni, tempoMin) {
  // 1. Valida o formato de horaIni (hh:mm ou hh:mm:ss)
  const regexHora = /^([01]\d|2[0-3]):([0-5]\d)(:([0-5]\d))?$/;
  if (!regexHora.test(horaIni)) {
    return "Erro: Formato de hora inicial inválido. Use 'hh:mm' ou 'hh:mm:ss'.";
  }

  // 2. Converte tempoMin para um tipo numérico
  const tempoMinNumero = Number(tempoMin);

  // 3. Valida se a conversão de tempoMin resultou em um número válido
  // Se tempoMin for, por exemplo, "abc", Number("abc") resultará em NaN (Not a Number)
  if (isNaN(tempoMinNumero)) {
    return "Erro: O valor fornecido para tempoMin ('" + tempoMin + "') não pôde ser convertido para um número válido.";
  }

  // 4. Extrai horas, minutos e segundos da horaIni
  const partesHora = horaIni.split(':');
  const horas = parseInt(partesHora[0], 10);
  const minutos = parseInt(partesHora[1], 10);
  const segundos = partesHora.length === 3 ? parseInt(partesHora[2], 10) : 0;

  // 5. Cria um objeto Date para facilitar o cálculo
  const data = new Date();
  data.setHours(horas);
  data.setMinutes(minutos);
  data.setSeconds(segundos);
  data.setMilliseconds(0); // Garante que os milissegundos sejam zero

  // 6. Adiciona o tempoMinNumero (convertido para número) à data
  data.setMinutes(data.getMinutes() + tempoMinNumero);

  // 7. Formata a hora final para "hh:mm:ss"
  const horasFinais = String(data.getHours()).padStart(2, '0');
  const minutosFinais = String(data.getMinutes()).padStart(2, '0');
  const segundosFinais = String(data.getSeconds()).padStart(2, '0');

  return `${horasFinais}:${minutosFinais}:${segundosFinais}`;
}



function formatarAniversario(dataInput) {
  const d = new Date(dataInput);
  if (isNaN(d)) return '';
  return new Intl.DateTimeFormat('pt-BR', {
    day:   '2-digit',
    month: 'long',
    timeZone: 'UTC'        // força interpretar como UTC
  }).format(d);
}



function maskHora(input) {
  // 1) Remove tudo que não for dígito e limita a 4 caracteres
  let raw = input.value.replace(/\D/g, '').slice(0, 4);

  // 2) Formata com ':' após os dois primeiros dígitos
  let formatted;
  if (raw.length > 2) {
    formatted = raw.slice(0, 2) + ':' + raw.slice(2);
  } else {
    formatted = raw;
  }

  // 3) Valida se está completo (HH:MM)
  if (formatted.length === 5) {
    let [h, m] = formatted.split(':').map(n => parseInt(n, 10));

    // 4) Limita para hora do dia
    if (isNaN(h) || h < 0) h = 0;
    if (h > 23) h = 23;
    if (isNaN(m) || m < 0) m = 0;
    if (m > 59) m = 59;

    formatted = `${String(h).padStart(2, '0')}:${String(m).padStart(2, '0')}`;
  }

  // 5) Atualiza valor e posiciona cursor no fim
  input.value = formatted;
  const pos = input.value.length;
  input.setSelectionRange(pos, pos);
}





function enviarWhatsapp(numero, mensagem) {
  // Remove tudo que não é número
  let numeroLimpo = numero.replace(/\D/g, '');

  // Se não começa com o DDI, adiciona 55 (Brasil)
  if (!numeroLimpo.startsWith('55')) {
    numeroLimpo = '55' + numeroLimpo;
  }

  // Codifica a mensagem para URL
  const mensagemUrl = encodeURIComponent(mensagem);

  // Monta a URL usando a API oficial do WhatsApp
  const url = `https://api.whatsapp.com/send?phone=${numeroLimpo}&text=${mensagemUrl}`;

  // Abre em nova aba/janela
  window.open(url, '_blank');
}












//Esta função escuta a classe numero-virgula e permite somente números e uma virgula
function formatarDataBr(input) {
  const s = String(input).trim();
  // extrai todos os grupos de dígitos
  const parts = s.match(/\d+/g);
  if (!parts || parts.length < 3) return '';

  let day, month, year;

  // 1) ISO: primeiro grupo com 4 dígitos → ano
  if (parts[0].length === 4) {
    year  = parts[0];
    month = parts[1].padStart(2, '0');
    day   = parts[2].padStart(2, '0');

  // 2) BR: último grupo com 4 dígitos → ano
  } else if (parts[2].length === 4) {
    day   = parts[0].padStart(2, '0');
    month = parts[1].padStart(2, '0');
    year  = parts[2];

  // 3) caso genérico: algum grupo no meio com 4 dígitos?
  } else if (parts[1].length === 4) {
    // ex: '07-2025-05' (não comum, mas cobre casos)
    day   = parts[0].padStart(2, '0');
    year  = parts[1];
    month = parts[2].padStart(2, '0');

  // 4) ano em 2 dígitos ou ambíguo → assumimos partes[2] como ano
  } else {
    day   = parts[0].padStart(2, '0');
    month = parts[1].padStart(2, '0');
    // se tiver só 2 dígitos, prefixa '20'
    year  = parts[2].length === 2
      ? '20' + parts[2].padStart(2, '0')
      : parts[2];
  }

  // validações básicas
  const d = Number(day), m = Number(month), y = Number(year);
  if (d < 1 || d > 31 || m < 1 || m > 12 || isNaN(y)) return '';

  return `${day}/${month}/${year}`;
}

//RETORNA SOMENTE NUMEROS
function so_numero(str) {
    return str.replace(/\D/g, '');
}


function whatsapp(telefone) {
    // Remove tudo que não for número
    const numeroLimpo = telefone.trim().replace(/\D/g, '');

    // Adiciona o +55 na frente
    const numeroComDDI = '+55' + numeroLimpo;

    // Monta a URL do WhatsApp
    const url = `https://wa.me/${numeroComDDI}`;

    return url;
}



function DecimalIngles(valor) {
  // Se vier vazio ou nulo
  if (valor === null || valor === undefined || valor === '') {
    return 0;
  }

  // 1) Normaliza pra string e remove espaços
  const str = String(valor).trim();

  // 2) Remove todos os pontos (milhares)
  const semPontos = str.replace(/\./g, '');

  // 3) Separa por vírgula e monta inteiro + decimal
  const partes = semPontos.split(',');
  let normalized;
  if (partes.length > 1) {
    const decimal = partes.pop();            // pega o último pedaço como centavos
    const inteiro = partes.join('');         // junta o que sobrou como parte inteira
    normalized = `${inteiro}.${decimal}`;    // usa ponto como separador decimal
  } else {
    // não tinha vírgula, trata tudo como inteiro
    normalized = partes[0];
  }

  // 4) Converte para float e valida
  const num = parseFloat(normalized);
  return isNaN(num) ? 0 : num;
}



function DecimalBr(valor) {
  // Converte em número e garante sempre duas casas decimais (padrão float com ponto)
  const numero = Number(valor);
  if (isNaN(numero)) {
    return '0,00';
  }

  // 1) Gera a string com duas casas decimais e substitui o ponto decimal por vírgula
  //    Ex.: "1234.56" → "1234,56"
  const strComVirgula = numero.toFixed(2).replace('.', ',');

  // 2) Separa tudo que vem antes da vírgula (parte inteira) e os centavos
  const [parteInteira, centavos] = strComVirgula.split(',');

  // 3) Na parte inteira, insere um ponto como separador de milhares a cada 3 dígitos
  //    Ex.: "1234" → "1.234"
  const inteiraComMilhares = parteInteira.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

  // 4) Reconstitui o resultado no formato brasileiro
  return `${inteiraComMilhares},${centavos}`;
}


  

  document.addEventListener('input', function (e) {
    const el = e.target;
    if (e.target) {
      if (el.classList.contains('numero-virgula') &&
      el === document.activeElement) {
        validarInput(el);
      }
      if (e.target.classList.contains('porcento')) {
        validarPorcento(e.target);
      }
      if (e.target.classList.contains('numero-uma-virgula')) {
        validarNumeroVirgula(e.target);
      }
    
    }
  });

function setDecimal(valor) {
  console.log('funcao setdecimal', valor);
  if (!valor) return ""; // vazio, null, undefined → ''
  
  // troca vírgula por ponto para o parseFloat
  let num = parseFloat(valor.toString().replace(",", "."));
  
  if (isNaN(num)) return ""; // se não for número → ''
  
  // retorna com 2 casas e vírgula como separador decimal
  return num.toFixed(2).replace(".", ",");
}

  
  document.addEventListener('blur', function (e) {
    const el = e.target;
     if (e.target) {
      if (el.classList.contains('numero-uma-virgula')) {
        let v= setDecimal(el.value);
        el.value = v;
      }
    }
  },true);



  function validarInput(el) {
    // 1) só números


      let nums = el.value.replace(/\D/g, "");
      
      // 2) garante pelo menos 3 dígitos (ex: "1" vira "001")
      nums = nums.padStart(3, "0");
      // 3) separa parte inteira e decimais
      const inteiro = nums.slice(0, -2).replace(/^0+/, "") || "0";
      const decimais = nums.slice(-2);
      // 4) monta o valor final
      el.value = inteiro + "," + decimais;
  }


  function validarNumeroVirgula(el) {
    let v = el.value;
    v = v.replace(/[^0-9,]/g, "");
    console.log('validando virgula', el.value);
    // 2) só permite UMA vírgula (mantém a primeira, remove as outras)
    const primeiraVirgula = v.indexOf(",");
    if (primeiraVirgula !== -1) {
    // corta o texto em duas partes
    const antes = v.slice(0, primeiraVirgula + 1);
    const depois = v.slice(primeiraVirgula + 1).replace(/,/g, "");
    v = antes + depois;
  }

  // 3) atualiza o valor no input
  el.value = v;

  }


  
function validarInteiro(el) {
  const raw = String(el.value || '');
  const nums = raw.replace(/\D+/g, '');   // só números

  if (nums === '') {
    el.value = '';
    return null;                          // nada válido
  }

  const numero = parseInt(nums, 10);      // inteiro
  el.value = String(numero);              // normaliza no input
  return numero;                          // <-- retorna o inteiro
}

  function validarPorcento(elemento) {
    let valor = elemento.value
      .replace(/[^0-9,]/g, '')                
      .replace(/,+/g, ',')                    
      .replace(/^([^,]*,[^,]{0,2}).*$/, '$1');
  
    let numero = parseFloat(valor.replace(',', '.'));
    if (numero > 100) {
      elemento.value = '100,00';
    } else {
      elemento.value = valor;
    }
  }
  
  function completarCasasDecimais(elemento) {
    if (elemento.value === '') return;
    let valor = elemento.value.replace(',', '.');
    let numero = parseFloat(valor);
    if (!isNaN(numero)) {
      elemento.value = DecimalBr(numero);
    }
  }
  

  
  document.addEventListener('blur', function (e) {
    if (e.target && (e.target.classList.contains('numero-virgula') || e.target.classList.contains('porcento'))) {
      completarCasasDecimais(e.target);
    }
  }, true); // importante usar o `true` aqui para escutar o blur na fase de captura
  





//Esta função escuta a classe numVirg2c e transforma para com virgula e duas casas

function formatarNumeroParaVirgula(valor) {
  const numero = parseFloat(valor.toString().replace(',', '.'));
  if (isNaN(numero)) return valor;

  return numero.toLocaleString('pt-BR', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  });
}

function formatarTodosNumVirg2c() {
  document.querySelectorAll('.numVirg2c').forEach(function (el) {
    const textoOriginal = el.textContent.trim();

    if (!isNaN(textoOriginal.replace(',', '.')) && textoOriginal !== '') {
      const valorFormatado = formatarNumeroParaVirgula(textoOriginal);
      el.textContent = valorFormatado;
    }
  });
}







document.addEventListener("DOMContentLoaded", function () {
  function formatNumbers() {
      document.querySelectorAll(".dataTable td.numVirg2C").forEach(function (td) {
          let value = td.textContent.trim();
          if (!isNaN(value) && value !== "") {
              td.textContent = parseFloat(value).toFixed(2).replace(".", ",");
          }
      });
  }

  // Seleciona todas as tabelas com a classe .dataTable e cria um observer para cada uma
  document.querySelectorAll(".dataTable").forEach(function(dataTable) {
      let observer = new MutationObserver(formatNumbers);
      observer.observe(dataTable, { childList: true, subtree: true });
  });

  // Chama a formatação uma vez no carregamento
  formatNumbers();
});

function celularFormatado(input) {
  // 1) Só dígitos
  const raw = input.replace(/\D/g, '');

  // 2) Lista oficial de DDDs brasileiros
  const BRAZIL_DDDS = [
    11,12,13,14,15,16,17,18,19,
    21,22,24,27,28,
    31,32,33,34,35,37,38,
    41,42,43,44,45,46,47,48,49,
    51,53,54,55,
    61,62,63,64,65,66,67,68,69,
    71,73,74,75,77,79,
    81,82,83,84,85,86,87,88,89,
    91,92,93,94,95,96,97,98,99
  ];

  // 3) Função auxiliar para montar o formato "(DD) resto últimos4"
  function montaFormato(ddd, numero) {
    const last4 = numero.slice(-4);
    const rest = numero.slice(0, -4);
    return `(${ddd}) ${rest} ${last4}`;
  }

  // 4) Detecta nacional: começa com "55DD" ou com "DD" direto
  let ddd, numberPart, prefix;
  if (raw.length >= 4 && raw.startsWith('55') && BRAZIL_DDDS.includes(+raw.substr(2,2))) {
    // veio com DDI 55 no início
    prefix     = '+55';
    ddd        = raw.substr(2,2);
    numberPart = raw.substr(4);
  }
  else if (raw.length >= 2 && BRAZIL_DDDS.includes(+raw.substr(0,2))) {
    // código DDD no início, mas sem +55
    prefix     = '+55';
    ddd        = raw.substr(0,2);
    numberPart = raw.substr(2);
  }
  else {
    // 5) Internacional ou ambíguo
    // se tiver 11+ dígitos, considera que os últimos 11 são DDD+num, o resto é DDI
    if (raw.length > 11) {
      const ddiLen    = raw.length - 11;
      prefix          = '+' + raw.substr(0, ddiLen);
      ddd             = raw.substr(ddiLen, 2);
      numberPart      = raw.substr(ddiLen + 2);
    } else {
      // número muito curto/indefinido
      return 'NI-' + raw;
    }
  }

  // 6) Monta e retorna
  return `${prefix} ${montaFormato(ddd, numberPart)}`;
}


function cpfFormatado(str) {
    // Remove tudo que não for dígito numérico
    const numeros = str.replace(/\D/g, '');

    // Limita a 11 caracteres (tamanho padrão do CPF)
    const cpf = numeros.substring(0, 11);

    // Formata CPF com pontos e traço
    if (cpf.length <= 3) return cpf;
    if (cpf.length <= 6) return `${cpf.substring(0,3)}.${cpf.substring(3)}`;
    if (cpf.length <= 9) return `${cpf.substring(0,3)}.${cpf.substring(3,6)}.${cpf.substring(6)}`;
    return `${cpf.substring(0,3)}.${cpf.substring(3,6)}.${cpf.substring(6,9)}-${cpf.substring(9,11)}`;
}



function formatarCPF(event) {

  //exemplo como usar: elemento+escuta+evento->função  document.getElementById('cpf').addEventListener('input', formatarCPF);
    let value = event.target.value.replace(/\D/g, '');
    let formatted = '';
    if (value.length > 0) {
        formatted = value.substring(0, 3);
        if (value.length >= 4) {
            formatted += '.' + value.substring(3, 6);
        }
        if (value.length >= 7) {
            formatted += '.' + value.substring(6, 9);
        }
        if (value.length >= 10) {
            formatted += '-' + value.substring(9, 11);
        }
    }
    event.target.value = formatted;
}


function validarCPF(event) {
    let cpf = event.target.value.replace(/\D/g, '');
    // Se o CPF estiver vazio, simplesmente ignora a validação e remove qualquer estado de erro anterior
    if (cpf.length === 0) {
        document.getElementById('cpfError').style.display = 'none'; // Esconder mensagem de erro
        event.target.classList.remove('is-invalid');
        return; // Sair da função sem fazer mais nada
    }

    if (!checkCPFValidity(cpf)) {
        document.getElementById('cpfError').style.display = 'block'; // Mostrar mensagem de errointei
        event.target.classList.add('is-invalid');
        event.target.focus(); // Opcionalmente mantém o foco no campo
    } else {
        document.getElementById('cpfError').style.display = 'none'; // Esconder mensagem de erro
        event.target.classList.remove('is-invalid');
    }
}


function checkCPFValidity(cpf) {
    if (cpf.length !== 11) {
        return false;
    }

    let sum = 0, remainder;

    for (let i = 1; i <= 9; i++) {
        sum += parseInt(cpf[i - 1]) * (11 - i);
    }
    remainder = (sum * 10) % 11;
    if (remainder === 10 || remainder === 11) remainder = 0;
    if (remainder !== parseInt(cpf[9])) return false;

    sum = 0;
    for (let i = 1; i <= 10; i++) {
        sum += parseInt(cpf[i - 1]) * (12 - i);
    }
    remainder = (sum * 10) % 11;
    if (remainder === 10 || remainder === 11) remainder = 0;
    if (remainder !== parseInt(cpf[10])) return false;

    return true;
}


function formatarCEP(event) {
    let value = this.value.replace(/\D/g, ''); // Assume que "this" é o cepInput
            if (value.length > 5) {
                value = value.slice(0, 5) + '-' + value.slice(5);
            }
            this.value = value; // Atualiza o campo com o valor formatado
}

function consultarCEP(event) {
    const cep = this.value.replace(/\D/g, '');
            if (cep.length === 8) {
                fetch(`https://viacep.com.br/ws/${cep}/json/`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.erro) {
                            alert('CEP não encontrado!');
                            return;
                        }
                        // Preenche os campos de endereço com os dados retornados
                        document.getElementById('rua').value = data.logradouro;
                        document.getElementById('bairro').value = data.bairro;
                        document.getElementById('cidade').value = data.localidade;
                        document.getElementById('estado').value = data.uf;
                    })
                    .catch(error => {
                        console.error('Falha ao buscar o CEP', error);
                    });
            }
}



$(document).on('input', '.num-cpf', formatarCPF);
$(document).on('blur', '.num-cpf', validarCPF);
$(document).on('input', '#cep', formatarCEP);
$(document).on('blur', '#cep', consultarCEP);





function extrairExtensao(arquivo) {
  if (typeof arquivo !== "string") return null;

  // procura o último ponto no nome do arquivo
  const idx = arquivo.lastIndexOf(".");
  if (idx === -1 || idx === arquivo.length - 1) {
    return null; // sem extensão ou ponto no final
  }

  // retorna a parte depois do ponto, em minúsculas
  return arquivo.slice(idx + 1).toLowerCase();
}





/**
 * Converte um valor numérico (ou string) para extenso em Reais (BR).
 * Exemplo:
 *   valorPorExtensoJS("12345,67") 
 *     => "doze mil trezentos e quarenta e cinco reais e sessenta e sete centavos"
 *
 * @param {number|string} valor - Pode ser número ou string (p.ex. "1.234,56").
 * @param {boolean} primeiraMaiuscula - Se true, coloca a primeira letra em maiúsculo.
 * @returns {string} Valor por extenso em português (ex.: "doze reais e cinquenta centavos").
 * 
 * 
 * 
 * 
 * 
 */

function reaisPorExtensoBr(valor, primeiraMaiuscula = false) {
  // 1) Converter string "1.234,56" em número
  let num = 0;

  if (typeof valor === 'string') {
    // Remove pontos de milhar e troca vírgula decimal por ponto
    valor = valor.replace(/\./g, '').replace(',', '.');
    num = parseFloat(valor);
    if (isNaN(num)) num = 0;
  } else if (typeof valor === 'number') {
    num = valor;
  } else {
    num = 0;
  }

  // 2) Verificar se é negativo
  let negativo = false;
  if (num < 0) {
    negativo = true;
    num = Math.abs(num);
  }

  // 3) Separar parte inteira e centavos
  let inteiro = Math.floor(num);
  let centavos = Math.round((num - inteiro) * 100);

  // 4) Quebrar a parte inteira em blocos de 3 dígitos
  //    ex.: 1.234.567 => [1, 234, 567]
  const strInt = inteiro.toString().padStart(15, '0'); // para garantir até trilhões
  let blocos = [
    parseInt(strInt.slice(0, 3), 10),  // trilhões
    parseInt(strInt.slice(3, 6), 10),  // bilhões
    parseInt(strInt.slice(6, 9), 10),  // milhões
    parseInt(strInt.slice(9, 12), 10), // milhares
    parseInt(strInt.slice(12, 15), 10) // unidades
  ];

  // 5) Escalas no singular/plural [trilhão, trilhões, bilhão, bilhões, etc]
  // Cada par (singular, plural) de cada escala
  const escalas = [
    ['trilhão', 'trilhões'],
    ['bilhão',  'bilhões'],
    ['milhão',  'milhões'],
    ['mil',     'mil'],
    ['',        ''] // unidade não tem nome
  ];

  // 6) Montar extenso dos blocos
  let extensoBlocos = [];
  for (let i = 0; i < blocos.length; i++) {
    let n = blocos[i];
    if (n > 0) {
      let escalaSingular = escalas[i][0];
      let escalaPlural   = escalas[i][1];

      let textoBloco = converteAte999(n);

      // singular ou plural?
      if (n === 1) {
        if (escalaSingular) textoBloco += " " + escalaSingular;
      } else {
        if (escalaPlural) textoBloco += " " + escalaPlural;
      }

      extensoBlocos.push(textoBloco);
    }
  }

  // Se não houver nenhum bloco, é zero
  let extensoInteiro = extensoBlocos.length > 0 
                       ? juntaFrasesComE(extensoBlocos) 
                       : 'zero';

  // Ajusta "real" vs "reais"
  extensoInteiro += (inteiro === 1) ? " real" : " reais";

  // 7) Centavos
  let extensoCentavos = "";
  if (centavos > 0) {
    let textoC = converteAte999(centavos);
    textoC = (centavos === 1) 
             ? textoC + " centavo" 
             : textoC + " centavos";

    extensoCentavos = " e " + textoC;
  }

  let resultado = (negativo ? "menos " : "") + extensoInteiro + extensoCentavos;

  // 8) Primeira maiúscula?
  if (primeiraMaiuscula) {
    resultado = resultado.charAt(0).toUpperCase() + resultado.slice(1);
  }

  return resultado;
}








function dataPorExtenso(data, hoje) {
  if (!data) return "";

  // Aceita "yyyy-mm-dd" ou "yyyy/mm/dd"
  let [ano, mes, dia] = data.replace(/\//g, '-').split('-').map(Number);
  // Meses JS são 0-based
  let dataInput = new Date(ano, mes - 1, dia); // <-- Formato local, sem UTC

  // Normaliza para meia-noite local
  dataInput.setHours(0,0,0,0);
  let now = new Date();
  now.setHours(0,0,0,0);

  let diffDias = (dataInput - now) / (1000 * 60 * 60 * 24);

  const meses = [
    'janeiro', 'fevereiro', 'março', 'abril', 'maio', 'junho',
    'julho', 'agosto', 'setembro', 'outubro', 'novembro', 'dezembro'
  ];
  let dataFormatada = `${dia} de ${meses[mes - 1]} de ${ano}`;

  if (hoje) {
    if (diffDias === 0){
      return `*HOJE*, ${dataFormatada}`;
    }else if (diffDias === 1) {
      return `*AMANHÃ*, ${dataFormatada}`;
    }else if (diffDias === -1){
       return `ONTEM, ${dataFormatada}`;
    } else{ 
      return `dia ${dataFormatada}`};
  }

  return dataFormatada;
}












/**
 * Converte números de 0 até 999 (centena, dezena e unidade) para extenso em português.
 * Usado internamente pela função principal.
 */
function converteAte999(n) {
  // Força inteiro e faixa 0..999
  n = Math.floor(n % 1000);

  if (n === 0) return "";

  // Mapeamentos de 0..19
  const unidade = [
    "", "um", "dois", "três", "quatro", "cinco",
    "seis", "sete", "oito", "nove", "dez",
    "onze", "doze", "treze", "catorze", "quinze",
    "dezesseis", "dezessete", "dezoito", "dezenove"
  ];

  // Dezenas [20..90]
  const dezena = [
    "", "", "vinte", "trinta", "quarenta", "cinquenta",
    "sessenta", "setenta", "oitenta", "noventa"
  ];

  // Centenas
  const centena = [
    "", "cem", "duzentos", "trezentos", "quatrocentos",
    "quinhentos", "seiscentos", "setecentos", "oitocentos", "novecentos"
  ];

  let c = Math.floor(n / 100);      // dígito da centena
  let d = Math.floor((n % 100) / 10); // dígito da dezena
  let u = n % 10;                   // dígito da unidade
  let saida = [];

  if (c > 0) {
    if (c === 1 && (d + u) > 0) {
      // caso 100..199, "cem" vs "cento e ..."
      saida.push("cento");
    } else {
      saida.push(centena[c]);
    }
  }

  let resto = d * 10 + u;
  if (resto > 0) {
    if (resto < 20) {
      // 1..19
      saida.push(unidade[resto]);
    } else {
      // 20..99
      saida.push(dezena[d]);
      if (u > 0) {
        saida.push(unidade[u]);
      }
    }
  }

  // Retirar vazios
  saida = saida.filter(Boolean);

  // Montar final com "e" no meio: ex. ["cento", "vinte", "cinco"] => "cento e vinte e cinco"
  return juntaPalavrasComE(saida);
}

/**
 * Recebe um array de palavras e junta com " e " no lugar certo.
 * Exemplo: ["cento", "vinte", "cinco"] => "cento e vinte e cinco"
 */
function juntaPalavrasComE(partes) {
  if (partes.length === 0) return "";
  if (partes.length === 1) return partes[0];

  let frase = partes[0];
  for (let i = 1; i < partes.length; i++) {
    frase += " e " + partes[i];
  }
  return frase;
}

/**
 * Recebe um array de blocos maiores (ex. ["dois milhões", "trezentos mil", "vinte"])
 * e monta com vírgulas e " e " adequados.
 * Ex.: ["um milhão", "dois mil", "trezentos"] => "um milhão, dois mil e trezentos"
 */
function juntaFrasesComE(partes) {
  if (partes.length === 0) return "";
  if (partes.length === 1) return partes[0];

  let resultado = "";
  for (let i = 0; i < partes.length; i++) {
    if (i === 0) {
      resultado = partes[i];
    } else if (i === partes.length - 1) {
      // último
      resultado += " e " + partes[i];
    } else {
      // do meio
      resultado += ", " + partes[i];
    }
  }
  return resultado;
}


/**
 * Recebe um número (ou string) em formato inglês (ex: 1234.56),
 * converte para formato brasileiro (ex: "1.234,56"),
 * e chama a função ReaisPorExtensoBr() para gerar o texto por extenso.
 *
 * @param {number|string} valorIngles - Ex.: 1234.56 ou "1234.56"
 * @param {boolean} primeiraMaiuscula - Se true, começa com letra maiúscula
 * @returns {string} Ex.: "um mil duzentos e trinta e quatro reais e cinquenta e seis centavos"
 */
function reaisPorExtenso(valorIngles, primeiraMaiuscula = false) {
  // 1) Garante que virou float
  const numero = parseFloat(valorIngles);
  if (isNaN(numero)) {
    // Se não for número, podemos retornar vazio ou chamar com zero
    return reaisPorExtensoBr("0,00", primeiraMaiuscula);
  }

  // 2) Se você quer sempre duas casas decimais,
  //    transforma para string "1234.56"
  const strNumero = numero.toFixed(2); // ex: "1234.56"

  // 3) Quebrar em parte inteira e parte decimal
  const [parteInteira, parteDecimal] = strNumero.split('.'); // ["1234", "56"]

  // 4) Formata a parte inteira com pontos (milhares) => "1.234"
  //    usando regex para inserir . a cada grupo de 3 dígitos, da direita para a esquerda
  const parteInteiraBr = parteInteira.replace(/\B(?=(\d{3})+(?!\d))/g, ".");

  // 5) Junta com vírgula e a parte decimal => "1.234,56"
  const numeroFormatoBr = `${parteInteiraBr},${parteDecimal}`;

  // 6) Chama sua função principal
  return reaisPorExtensoBr(numeroFormatoBr, primeiraMaiuscula);
}
