
let linhasAgendamentos = [];


function updateProfissionais() {
  const headers = document.querySelectorAll('#easy-table th[data-id-profissional]');
  const profissionais = Array.from(headers).map(th => ({
    id:   th.dataset.idProfissional,
    nome: th.dataset.nomeAgenda
  }));
  const ids = profissionais.map(p => p.id).join(',');

  fetch(`endPoints/profissionais_servicos_dia.php?ids=${ids}`)
    .then(res => res.json())
    .then(data => {
      profissionais.forEach(p => {
        profServData[p.id] = {
          nome:     p.nome,
          servicos: data.profissionais[p.id] || []
        };
      });
      defaultServ = data.defaults || {};
      
      document.querySelectorAll('#tabela-agendamentos-janela tr').forEach(tr => {
        populaLinha(tr);
        preenchePrecoTempo(tr);
      });
    })
    .catch(console.error);
}

