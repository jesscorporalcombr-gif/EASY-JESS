
const modalsEstado = {};

function abrirModal(modal, id, tipo) {
  if (modalsEstado[modal]) return;
  modalsEstado[modal] = true;

  $.ajax({
    url: `Modals/${modal}.php`,
    type: "POST",
    data: { id, tipo },
    success(response) {
      const $modal = $(response).appendTo('body');
      new bootstrap.Modal($modal[0]).show();
      aplicarEventosModal($modal);

      $modal.on('hidden.bs.modal', () => {
        modalsEstado[modal] = false;
        $modal.remove();
      });
    },
    error(err) {
      console.error(err);
      modalsEstado[modal] = false;
    }
  });
}


function aplicarEventosModal(modalElement) {
    // Assume-se que você tem funções `configurarCPF` e `configurarCEP` já definidas
    var cpfInput = modalElement.find('.num-cpf');
    var cepInput = modalElement.find('#cep');

    if (cpfInput.length) {
        cpfInput.on('input', formatarCPF);
        cpfInput.on('blur', validarCPF);
    }

    if (cepInput.length) {
        cepInput.on('input', formatarCEP);
        cepInput.on('blur', consultarCEP);
    }
}




