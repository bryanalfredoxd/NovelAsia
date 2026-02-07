<?php
// Esto ejecutará el comando git y mostrará el resultado en el navegador
echo "<h2>Estado de la conexión Git:</h2>";
echo "<pre>";
// Ejecutamos git remote -v
$output = shell_exec('git remote -v 2>&1');
echo "Remotos actuales:\n" . $output;

echo "\n\nIntentando verificar conexión SSH con GitHub:\n";
// Esto verifica si la llave SSH funciona
$ssh_test = shell_exec('ssh -T git@github.com 2>&1');
echo $ssh_test;
echo "</pre>";