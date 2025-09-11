<?php
/**
 * AUDITORIA DE SEGURANÇA - LUCRATIVABET
 * Desenvolvido por: CIRURGIÃO DEV
 * Data: 10/09/2025
 */

echo "========================================\n";
echo "   AUDITORIA DE SEGURANÇA LUCRATIVABET  \n";
echo "========================================\n\n";

$baseDir = __DIR__;
$issues = [];
$warnings = [];

// 1. Verificar funções perigosas
echo "[1] Verificando funções perigosas no código...\n";
$dangerousFunctions = [
    'eval' => 'CRÍTICO: Execução de código arbitrário',
    'exec' => 'ALTO: Execução de comandos shell',
    'system' => 'ALTO: Execução de comandos sistema',
    'shell_exec' => 'ALTO: Execução de comandos shell',
    'passthru' => 'ALTO: Execução de comandos com output',
    'proc_open' => 'MÉDIO: Abertura de processos',
    'file_get_contents' => 'BAIXO: Leitura de arquivos (verificar URLs)',
    'file_put_contents' => 'BAIXO: Escrita de arquivos',
    'include' => 'MÉDIO: Inclusão dinâmica de arquivos',
    'require' => 'MÉDIO: Requisição dinâmica de arquivos'
];

$phpFiles = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($baseDir . '/app'),
    RecursiveIteratorIterator::SELF_FIRST
);

foreach ($phpFiles as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $content = file_get_contents($file->getPathname());
        foreach ($dangerousFunctions as $func => $risk) {
            if (preg_match('/\b' . preg_quote($func) . '\s*\(/i', $content, $matches, PREG_OFFSET_CAPTURE)) {
                $line = substr_count(substr($content, 0, $matches[0][1]), "\n") + 1;
                $relativePath = str_replace($baseDir . '/', '', $file->getPathname());
                
                if (strpos($risk, 'CRÍTICO') !== false || strpos($risk, 'ALTO') !== false) {
                    $issues[] = "$risk - $func() em $relativePath:$line";
                } else {
                    $warnings[] = "$risk - $func() em $relativePath:$line";
                }
            }
        }
    }
}

// 2. Verificar permissões de arquivos sensíveis
echo "[2] Verificando permissões de arquivos sensíveis...\n";
$sensitiveFiles = [
    '.env',
    'storage/app',
    'storage/logs',
    'database'
];

foreach ($sensitiveFiles as $file) {
    $path = $baseDir . '/' . $file;
    if (file_exists($path)) {
        $perms = substr(sprintf('%o', fileperms($path)), -4);
        if ($perms > '0755' && is_file($path)) {
            $issues[] = "PERMISSÃO: $file tem permissões muito abertas ($perms)";
        }
    }
}

// 3. Verificar configurações de segurança no .env
echo "[3] Verificando configurações de segurança...\n";
if (file_exists($baseDir . '/.env')) {
    $envContent = file_get_contents($baseDir . '/.env');
    
    // Verificar APP_DEBUG
    if (preg_match('/APP_DEBUG\s*=\s*true/i', $envContent)) {
        $warnings[] = "CONFIG: APP_DEBUG está TRUE (desativar em produção)";
    }
    
    // Verificar TOKEN_2FA fraco
    if (preg_match('/TOKEN_DE_2FA\s*=\s*([a-fA-F0-9]+)/', $envContent, $matches)) {
        if (strlen($matches[1]) < 64) {
            $issues[] = "SEGURANÇA: TOKEN_2FA muito fraco (menos de 64 caracteres)";
        } elseif (preg_match('/^(0+|1234|abcd|1111|0000)/', $matches[1])) {
            $issues[] = "SEGURANÇA: TOKEN_2FA com padrão previsível";
        }
    }
    
    // Verificar APP_URL
    if (preg_match('/APP_URL\s*=\s*http:\/\/127\.0\.0\.1/i', $envContent)) {
        $warnings[] = "CONFIG: APP_URL ainda como localhost";
    }
    
    // Verificar senhas vazias
    if (preg_match('/PASSWORD\s*=\s*$/m', $envContent)) {
        $issues[] = "SEGURANÇA: Senha vazia encontrada no .env";
    }
}

// 4. Verificar Headers de Segurança
echo "[4] Verificando headers de segurança...\n";
$securityHeadersFile = $baseDir . '/app/Http/Middleware/SecurityHeaders.php';
if (file_exists($securityHeadersFile)) {
    $content = file_get_contents($securityHeadersFile);
    if (strpos($content, 'unsafe-eval') !== false) {
        $issues[] = "CSP: 'unsafe-eval' permitido (vulnerável a XSS)";
    }
    if (strpos($content, 'unsafe-inline') !== false) {
        $warnings[] = "CSP: 'unsafe-inline' permitido (considerar remover)";
    }
}

// 5. Verificar SQL Injection vulnerabilities
echo "[5] Verificando possíveis SQL Injections...\n";
$sqlPatterns = [
    '/DB::raw\s*\([^)]*\$_(?:GET|POST|REQUEST)/',
    '/DB::select\s*\([^)]*\$_(?:GET|POST|REQUEST)/',
    '/whereRaw\s*\([^)]*\$_(?:GET|POST|REQUEST)/'
];

foreach ($phpFiles as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $content = file_get_contents($file->getPathname());
        foreach ($sqlPatterns as $pattern) {
            if (preg_match($pattern, $content)) {
                $relativePath = str_replace($baseDir . '/', '', $file->getPathname());
                $issues[] = "SQL: Possível SQL Injection em $relativePath";
            }
        }
    }
}

// RELATÓRIO FINAL
echo "\n========================================\n";
echo "         RESULTADO DA AUDITORIA         \n";
echo "========================================\n\n";

if (count($issues) > 0) {
    echo "❌ PROBLEMAS CRÍTICOS ENCONTRADOS: " . count($issues) . "\n";
    echo "----------------------------------------\n";
    foreach ($issues as $issue) {
        echo "• $issue\n";
    }
    echo "\n";
}

if (count($warnings) > 0) {
    echo "⚠️  AVISOS DE SEGURANÇA: " . count($warnings) . "\n";
    echo "----------------------------------------\n";
    foreach ($warnings as $warning) {
        echo "• $warning\n";
    }
    echo "\n";
}

if (count($issues) === 0 && count($warnings) === 0) {
    echo "✅ Nenhum problema crítico de segurança encontrado!\n";
}

// RECOMENDAÇÕES
echo "========================================\n";
echo "          RECOMENDAÇÕES                \n";
echo "========================================\n\n";
echo "1. URGENTE: Remover eval() de CustomPermissionResource.php:140\n";
echo "2. CRÍTICO: Alterar TOKEN_DE_2FA para valor seguro\n";
echo "3. IMPORTANTE: Desativar 'unsafe-eval' no CSP\n";
echo "4. RECOMENDADO: Implementar rate limiting\n";
echo "5. RECOMENDADO: Adicionar HTTPS/SSL\n";
echo "6. RECOMENDADO: Configurar firewall\n";
echo "\n";

// Salvar relatório
$report = [
    'date' => date('Y-m-d H:i:s'),
    'issues' => $issues,
    'warnings' => $warnings,
    'total_files_scanned' => iterator_count($phpFiles)
];

file_put_contents($baseDir . '/security-audit-report.json', json_encode($report, JSON_PRETTY_PRINT));
echo "📄 Relatório salvo em: security-audit-report.json\n\n";