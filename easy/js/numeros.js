//by Tiago

document.addEventListener('DOMContentLoaded', function() {
    document.body.addEventListener('input', function(e) {
        
        if (e.target && (e.target.matches('.numVirg') || e.target.matches('.numVirg-2c') || e.target.matches('.numVirgPerc'))) {
            
            let valorAtual = e.target.value.replace(/[^0-9,]/g, '');
            let partes = valorAtual.split(',');

            if (e.target.classList.contains('numVirg-2c') && partes.length > 1) {
                
                partes[1] = partes[1].substring(0, 2);
            }

            if (e.target.classList.contains('numVirgPerc')) {
                
                let valorNumerico = parseFloat(partes.join('.').replace(/,/g, '.'));
                if (valorNumerico > 100) {
                    e.target.value = '100';
                } else {
                    
                    e.target.value = partes.length > 2 ? partes[0] + ',' + partes.slice(1).join('') : partes.join(',');
                }
            } else {
                
                e.target.value = partes.length > 2 ? partes[0] + ',' + partes.slice(1).join('') : partes.join(',');
            }
        }

    });


    

});




        function formatData(data) {
            if (!data) return '';

            // Verifica se a data já está no formato ISO (YYYY-MM-DD)
            if (/^\d{4}-\d{2}-\d{2}$/.test(data)) {
                // Se estiver, apenas reordena para DD/MM/YYYY
                var partes = data.split('-');
                return partes[2] + '/' + partes[1] + '/' + partes[0];
            }

            // Verifica se a data está em algum dos formatos conhecidos
            var regex = /(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{2,4})/;
            var match = data.match(regex);
            if (match) {
                var dia = match[1];
                var mes = match[2];
                var ano = match[3];
                // Corrige o ano se estiver no formato de dois dígitos
                if (ano.length === 2) {
                    ano = parseInt(ano, 10) > 50 ? '19' + ano : '20' + ano;
                }
                // Adiciona zero à esquerda se necessário para dia e mês
                dia = dia.padStart(2, '0');
                mes = mes.padStart(2, '0');
                return `${dia}/${mes}/${ano}`;
            }

            // Se a data não corresponder a nenhum dos formatos conhecidos, retorna vazio
            return '';
        }