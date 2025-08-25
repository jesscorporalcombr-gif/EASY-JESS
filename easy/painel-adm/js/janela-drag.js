// janela-drag.js
function initDrag(janelaId, headerId, minimizarId, fecharId) {
  const janela  = document.getElementById(janelaId);
  const header  = document.getElementById(headerId);
  const minBtn  = document.getElementById(minimizarId);
  const closeBtn= document.getElementById(fecharId);
  const cancelarBtn = document.getElementById('btn-cancelar-janela-ag');

  if (!janela || !header) return;

  let drag = false, offX, offY;

  header.addEventListener('mousedown', e => {
    drag = true;
    offX = e.clientX - janela.offsetLeft;
    offY = e.clientY - janela.offsetTop;
    document.addEventListener('mousemove', move);
    document.addEventListener('mouseup',   up);
  });

  function move(e){
    if (!drag) return;
    janela.style.left = e.clientX - offX + 'px';
    janela.style.top  = e.clientY - offY + 'px';
  }
  function up(){
    drag = false;
    document.removeEventListener('mousemove', move);
    document.removeEventListener('mouseup',   up);
  }

  minBtn?.addEventListener('click', () => {
    const body = janela.querySelector('.janela-body');
    const hide = body.style.display === 'none';
    body.style.display = hide ? 'block' : 'none';
    minBtn.textContent = hide ? '_' : '⬜';
  });



   // 🔴 FECHAR  = remover do DOM
  closeBtn.addEventListener('click', () => removerJanela(janela));

  // 🔴 CANCELAR = idem (se preferir só esconder, troque por hideJanela)
  cancelarBtn?.addEventListener('click', () => removerJanela(janela));

  
}
function removerJanela(el) {
  // animação opcional
  el.style.transition = 'opacity .25s';
  el.style.opacity = 0;

  // aguarda fim do fade antes de remover
  setTimeout(() => {
    el.remove();               // navegs. modernos
    // el.parentNode?.removeChild(el); // fallback IE11
  }, 250);
}

/* Caso queira apenas esconder */
function hideJanela(el) {
  el.style.display = 'none';
}
