// ext-icon-map.js

window.FileIconRegistry = (function() {
  const extMap = {
        // Documentos de escritório
    pdf:   { icon: 'bi-file-earmark-pdf-fill',        color: 'text-danger' },
    doc:   { icon: 'bi-file-earmark-word-fill',       color: 'text-primary' },
    docx:  { icon: 'bi-file-earmark-word-fill',       color: 'text-primary' },
    xls:   { icon: 'bi-file-earmark-excel-fill',      color: 'text-success' },
    xlsx:  { icon: 'bi-file-earmark-excel-fill',      color: 'text-success' },
    csv:   { icon: 'bi-file-earmark-spreadsheet-fill',color: 'text-success' },
    ppt:   { icon: 'bi-file-earmark-ppt-fill',        color: 'text-warning' },
    pptx:  { icon: 'bi-file-earmark-ppt-fill',        color: 'text-warning' },
    pptm:  { icon: 'bi-file-earmark-ppt-fill',        color: 'text-warning' },
    txt:   { icon: 'bi-file-earmark-text-fill',       color: 'text-muted' },
    md:    { icon: 'bi-markdown',                     color: 'text-secondary' },

    // Web
    html:  { icon: 'bi-filetype-html',                color: 'text-danger' },
    css:   { icon: 'bi-filetype-css',                 color: 'text-info' },
    js:    { icon: 'bi-filetype-js',                  color: 'text-warning' },
    json:  { icon: 'bi-filetype-json',                color: 'text-warning' },
    xml:   { icon: 'bi-filetype-xml',                 color: 'text-secondary' },

    // Imagens
    png:   { icon: 'bi-file-earmark-image-fill',      color: 'text-info' },
    jpg:   { icon: 'bi-file-earmark-image-fill',      color: 'text-info' },
    jpeg:  { icon: 'bi-file-earmark-image-fill',      color: 'text-info' },
    gif:   { icon: 'bi-file-earmark-image-fill',      color: 'text-info' },
    bmp:   { icon: 'bi-file-earmark-image-fill',      color: 'text-info' },
    svg:   { icon: 'bi-file-earmark-image-fill',      color: 'text-info' },

    // Áudio
    mp3:   { icon: 'bi-file-earmark-music-fill',      color: 'text-primary' },
    wav:   { icon: 'bi-file-earmark-music-fill',      color: 'text-primary' },
    flac:  { icon: 'bi-file-earmark-music-fill',      color: 'text-primary' },

    // Vídeo
    mp4:   { icon: 'bi-file-earmark-play-fill',       color: 'text-info' },
    avi:   { icon: 'bi-file-earmark-play-fill',       color: 'text-info' },
    mov:   { icon: 'bi-file-earmark-play-fill',       color: 'text-info' },
    mkv:   { icon: 'bi-file-earmark-play-fill',       color: 'text-info' },

    // Comprimidos
    zip:   { icon: 'bi-file-earmark-zip-fill',        color: 'text-secondary' },
    rar:   { icon: 'bi-file-earmark-zip-fill',        color: 'text-secondary' },
    '7z':  { icon: 'bi-file-earmark-zip-fill',        color: 'text-secondary' },
    tar:   { icon: 'bi-file-earmark-zip-fill',        color: 'text-secondary' },
    gz:    { icon: 'bi-file-earmark-zip-fill',        color: 'text-secondary' },

    // Executáveis e imagens de disco
    exe:   { icon: 'bi-file-earmark-binary-fill',     color: 'text-dark' },
    dmg:   { icon: 'bi-cpu-fill',                     color: 'text-dark' },
    iso:   { icon: 'bi-hdd-fill',                     color: 'text-secondary' },

    // Design e multimídia
    psd:   { icon: 'bi-file-earmark-image-fill',      color: 'text-primary' },
    ai:    { icon: 'bi-brush-fill',                   color: 'text-warning' },
    eps:   { icon: 'bi-brush-fill',                   color: 'text-warning' },
    indd:  { icon: 'bi-file-earmark-fill',            color: 'text-muted' },

    // ebooks
    epub:  { icon: 'bi-book-fill',                    color: 'text-primary' },
    mobi:  { icon: 'bi-book-fill',                    color: 'text-primary' },

    // Padrão
    default: { icon: 'bi-file-earmark-fill',          color: 'text-muted' }
  };

  return {
    get: function(ext) {
      ext = (ext || '').toLowerCase();
      return extMap[ext] || extMap.default;
    }
  };
})();
