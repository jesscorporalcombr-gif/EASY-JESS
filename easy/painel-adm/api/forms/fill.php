<?php
$form_id     = isset($_GET['form_id']) ? (int)$_GET['form_id'] : 0;
$token       = isset($_GET['token']) ? $_GET['token'] : '';
$response_id = isset($_GET['response_id']) ? (int)$_GET['response_id'] : 0;
$patient_id  = isset($_GET['patient_id']) ? $_GET['patient_id'] : '';
$q = [];
if ($form_id)     $q[] = "form_id=".$form_id;
if ($token)       $q[] = "token=".urlencode($token);
if ($response_id) $q[] = "response_id=".$response_id;
if ($patient_id)  $q[] = "patient_id=".urlencode($patient_id);
$src = "forms/fill_full.php" . ($q ? ("?".implode("&",$q)) : "");
?>
<div style="height: calc(100vh - 180px);">
  <iframe
    src="<?= htmlspecialchars($src) ?>"
    style="width:100%;height:100%;border:0;"
    loading="eager"
    referrerpolicy="no-referrer-when-downgrade"
  ></iframe>
</div>
