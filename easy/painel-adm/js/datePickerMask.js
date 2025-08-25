function initDatepickers(context = document) {
    console.log('iniciando datepicker');
    const instances = flatpickr(
        document.querySelectorAll("input.datepicker"),
        {
          dateFormat: "Y-m-d",
          altInput: true,
          altInputClass: "form-control datepicker", // ⬅️ transfere .datepicker para o altInput
          altFormat: "d/m/Y",
          locale: "pt",
          allowInput: true
        }
      );
    
      // 2) Para cada instância, anexa o mask ao altInput (visível)
      instances.forEach(fp => {
        const el = fp.altInput;           // o campo que o usuário enxerga
        el.addEventListener("input", () => {
          let v = el.value.replace(/\D/g, "").slice(0, 8);
          v = v
             .replace(/^(\d{2})(\d)/, "$1/$2")
             .replace(/^(\d{2}\/\d{2})(\d)/, "$1/$2");
          el.value = v;
    
          // Se estiver completo DD/MM/YYYY, sincroniza o valor interno
          if (/^\d{2}\/\d{2}\/\d{4}$/.test(v)) {
            fp.setDate(v, false, "d/m/Y");
          }
        });
      });
  }