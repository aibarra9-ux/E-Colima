<?php
echo "<h1>Diagnóstico de conexión a Google</h1>";

// 1. Verificar extensiones
echo "<h2>Extensiones PHP:</h2>";
echo "cURL: " . (extension_loaded('curl') ? "✅ Activado" : "❌ No disponible") . "<br>";
echo "OpenSSL: " . (extension_loaded('openssl') ? "✅ Activado" : "❌ No disponible") . "<br>";
echo "JSON: " . (extension_loaded('json') ? "✅ Activado" : "❌ No disponible") . "<br>";
echo "allow_url_fopen: " . (ini_get('allow_url_fopen') ? "✅ Activado" : "❌ Desactivado") . "<br>";

// 2. Probar cURL
echo "<h2>Prueba de cURL a Google:</h2>";
$ch = curl_init("https://www.google.com");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
$response = curl_exec($ch);
$error = curl_error($ch);
$info = curl_getinfo($ch);
curl_close($ch);

if ($response !== false) {
    echo "✅ cURL funciona correctamente<br>";
    echo "HTTP Code: " . $info['http_code'] . "<br>";
} else {
    echo "❌ Error en cURL: " . $error . "<br>";
}

// 3. Probar conexión SSL
echo "<h2>Prueba de SSL:</h2>";
$context = stream_context_create(["ssl" => ["verify_peer" => true, "verify_peer_name" => true]]);
$test_ssl = @file_get_contents("https://www.google.com", false, $context);
echo $test_ssl ? "✅ SSL funciona correctamente" : "❌ Problemas con SSL";

// 4. Información del servidor
echo "<h2>Información del servidor:</h2>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Servidor: " . $_SERVER['SERVER_SOFTWARE'] . "<br>";
echo "IP del servidor: " . $_SERVER['SERVER_ADDR'] . "<br>";
?>