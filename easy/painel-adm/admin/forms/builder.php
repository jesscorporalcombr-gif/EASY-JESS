<?php
// admin/forms/builder.php
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$src = "builder_full.php" . ($id ? ("?id=".$id) : "");
?>
<div style="height: calc(100vh - 180px); /* ajuste a altura se seu topo/menus forem maiores */">
  <iframe
    src="<?= htmlspecialchars($src) ?>"
    style="width:100%;height:100%;border:0;"
    loading="eager"
    referrerpolicy="no-referrer-when-downgrade"
  ></iframe>
</div>