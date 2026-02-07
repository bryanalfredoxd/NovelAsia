<?php
// 1. Seguridad: Solo tú y GitHub pueden activar esto
$token_seguridad = "Bryan_Moreno_2026_Project"; // Cambia esto por algo único
if ($_GET['token'] !== $token_seguridad) {
    header('HTTP/1.0 403 Forbidden');
    die('Acceso denegado');
}

echo "<h2>Iniciando Despliegue...</h2>";

// 2. Ejecutar comandos de Git y Cpanel
// Intentamos actualizar el repositorio
$output_pull = shell_exec("git pull origin main 2>&1");
echo "<b>Resultado Pull:</b> <pre>$output_pull</pre>";

// 3. Forzar el despliegue manual si existe el .cpanel.yml
// Algunos servidores permiten llamar al binario de cpanel directamente
$output_deploy = shell_exec("/usr/local/cpanel/bin/git-deploy 2>&1");
echo "<b>Resultado Deploy:</b> <pre>$output_deploy</pre>";

echo "<h2>Despliegue finalizado.</h2>";