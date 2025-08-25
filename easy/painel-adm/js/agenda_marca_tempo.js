
(function(){
  let timerId = null;
  let slotsData = [];

  // Mapeia inicialmente minutes e offsetTop (centro do cell)
  function mapSlots() {
    const container = document.getElementById('agenda-container');
    const tds = container.querySelectorAll('table td:nth-child(1)'); // ajuste índice
    return Array.from(tds).map(td => {
      const [h, m] = td.textContent.trim().split(':').map(Number);
      return {
        minutes: h*60 + m,
        offsetCenter: td.offsetTop + td.clientHeight/2
      };
    });
  }

  function calcPosition(nowMin) {
    if (!slotsData.length) return 0;
    const first = slotsData[0];
    const last  = slotsData[slotsData.length - 1];
    if (nowMin <= first.minutes) return first.offsetCenter;
    if (nowMin >= last.minutes)  return last.offsetCenter;
    for (let i = 0; i < slotsData.length - 1; i++) {
      const a = slotsData[i], b = slotsData[i+1];
      if (nowMin >= a.minutes && nowMin <= b.minutes) {
        const ratio = (nowMin - a.minutes) / (b.minutes - a.minutes);
        return a.offsetCenter + ratio * (b.offsetCenter - a.offsetCenter);
      }
    }
    return 0;
  }

  function updateMarker() {
    const container = document.getElementById('agenda-container');
    if (!container) return;

    const now = new Date();
    const nowMin = now.getHours()*60 + now.getMinutes() + now.getSeconds()/60;

    // posição no conteúdo (antes do scroll)
    const contentY = calcPosition(nowMin);
    // compensa scroll do container
    const visibleY = contentY - container.scrollTop;

    const ind  = document.getElementById('time-indicator');
    const line = document.getElementById('time-line');
    if (!ind || !line) return;

    // recuo vertical do balão (seta no meio)
    const halfH = ind.offsetHeight/2;
    ind.style.top  = `${visibleY}px`;
    line.style.top = `${visibleY}px`;

    ind.querySelector('.time-label').textContent = now.toLocaleTimeString();
  }

  window.initTimeIndicator = function() {
    const container = document.getElementById('agenda-container');
    if (!container) return console.warn('Container não encontrado');
    container.style.position = 'relative';

    clearInterval(timerId);
    ['time-indicator','time-line'].forEach(id => {
      const el = document.getElementById(id);
      if (el) el.remove();
    });

    // injeta elementos
    const ind  = document.createElement('div');
    ind.id     = 'time-indicator';
    ind.innerHTML = '<span class="time-label"></span>';
    const line = document.createElement('div');
    line.id    = 'time-line';
    container.append(ind, line);

    // mapeia slots UMA ÚNICA vez
    slotsData = mapSlots();

    // posicione de imediato e inicie loop
    requestAnimationFrame(updateMarker);
    timerId = setInterval(updateMarker, 1000);
  };
})();
