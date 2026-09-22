<?php
// ============================================
// la partie pour se  DÉCONNECTER
// ============================================
session_start();
session_destroy();
header('Location: index.php');
exit;
?>
