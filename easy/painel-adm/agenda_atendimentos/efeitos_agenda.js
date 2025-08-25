const efeito1 = ($c, htmlAgenda) => {
    $c.css({opacity: 0}).html(htmlAgenda).animate({opacity: 1}, 400);
};


const efeito2 = ($c, htmlAgenda) => {
    $c.css({transform: 'scale(0.9)', opacity: 0})
  .html(htmlAgenda)
  .animate({opacity: 1}, {
      duration: 400,
      step(now) {
        $c.css('transform', `scale(${0.9 + 0.1 * now})`);
      }
  });
};
  
  
 const efeito3 = ($c, htmlAgenda) => { 
    $c.css({filter: 'blur(10px)', opacity: 0})
  .html(htmlAgenda)
  .animate({opacity: 1}, {
    duration: 500,
    step(now) {
      const blur = 10 - (now * 10);
      $c.css('filter', `blur(${blur}px)`);
    },
    complete() { $c.css('filter', ''); }
  });
 };


 const efeito4 = ($c, htmlAgenda) => { $c.css({filter: 'brightness(2)', opacity: 0})
  .html(htmlAgenda)
  .animate({opacity: 1}, {
    duration: 500,
    step(now) {
      const bright = 2 - now;
      $c.css('filter', `brightness(${bright})`);
    },
    complete() { $c.css('filter', ''); }
  });

   };



 const efeito5 = ($c, htmlAgenda) => { $c.css({filter: 'grayscale(100%)', opacity: 0})
  .html(htmlAgenda)
  .animate({opacity: 1}, {
    duration: 500,
    step(now) {
      const gs = 100 - now * 100;
      $c.css('filter', `grayscale(${gs}%)`);
    },
    complete() { $c.css('filter', ''); }
  });

   };

 const efeito6 = ($c, htmlAgenda) => { $c.css({transform: 'scale(1.1)', opacity: 0})
  .html(htmlAgenda)
  .animate({opacity: 1}, {
    duration: 400,
    step(now) {
      $c.css('transform', `scale(${1.1 - 0.1 * now})`);
    }
  });

   };

 const efeito7 = ($c, htmlAgenda) => { $c.css({filter: 'sepia(100%)', opacity: 0})
  .html(htmlAgenda)
  .animate({opacity: 1}, {
    duration: 500,
    step(now) {
      const sepia = 100 - now * 100;
      $c.css('filter', `sepia(${sepia}%)`);
    },
    complete() { $c.css('filter', ''); }
  });

   };

  const efeito8 = ($c, htmlAgenda) => { $c.css({filter: 'saturate(0%)', opacity: 0})
  .html(htmlAgenda)
  .animate({opacity: 1}, {
    duration: 500,
    step(now) {
      const sat = now * 100;
      $c.css('filter', `saturate(${sat}%)`);
    },
    complete() { $c.css('filter', ''); }
  });

   };

 const efeito9 = ($c, htmlAgenda) => { $c.css({filter: 'invert(100%)', opacity: 0})
  .html(htmlAgenda)
  .animate({opacity: 1}, {
    duration: 100,
    step(now) {
      const inv = 100 - now * 100;
      $c.css('filter', `invert(${inv}%)`);
    },
    complete() { $c.css('filter', ''); }
  });

   };

 const efeito10 = ($c, htmlAgenda) => { $c.css({filter: 'contrast(200%)', opacity: 0})
  .html(htmlAgenda)
  .animate({opacity: 1}, {
    duration: 500,
    step(now) {
      const contrast = 200 - now * 100;
      $c.css('filter', `contrast(${contrast}%)`);
    },
    complete() { $c.css('filter', ''); }
  });

   };