# 🔒 Configuração HTTPS/SSL - CRÍTICO PARA PRODUÇÃO

## ⚠️ AVISO IMPORTANTE
**NÃO COLOQUE EM PRODUÇÃO SEM HTTPS!** Senhas e dados sensíveis serão transmitidos em texto puro!

## 📋 Passo a Passo para Configurar HTTPS

### 1. Obter Certificado SSL

#### Opção A: Let's Encrypt (Gratuito)
```bash
# Instalar Certbot
sudo apt-get update
sudo apt-get install certbot python3-certbot-nginx

# Gerar certificado
sudo certbot certonly --standalone -d seudominio.com -d www.seudominio.com

# Renovação automática
sudo certbot renew --dry-run
```

#### Opção B: Cloudflare (Recomendado)
1. Criar conta em cloudflare.com
2. Adicionar seu domínio
3. Ativar SSL/TLS → Full (strict)
4. Ativar Always Use HTTPS
5. Configurar Page Rules para forçar HTTPS

### 2. Configurar Nginx

Criar arquivo `/etc/nginx/sites-available/lucrativabet`:

```nginx
server {
    listen 80;
    server_name seudominio.com www.seudominio.com;
    
    # Redirecionar todo tráfego HTTP para HTTPS
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name seudominio.com www.seudominio.com;
    
    # Certificados SSL
    ssl_certificate /etc/letsencrypt/live/seudominio.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/seudominio.com/privkey.pem;
    
    # Configurações SSL seguras
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;
    ssl_prefer_server_ciphers on;
    ssl_session_cache shared:SSL:10m;
    ssl_session_timeout 10m;
    ssl_stapling on;
    ssl_stapling_verify on;
    
    # Headers de segurança
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains; preload" always;
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    
    # Root do projeto
    root /var/www/lucrativabet/public;
    index index.php index.html;
    
    # Logs
    access_log /var/log/nginx/lucrativabet-access.log;
    error_log /var/log/nginx/lucrativabet-error.log;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
    
    location ~ /\.(?!well-known).* {
        deny all;
    }
    
    # Limitar tamanho de upload
    client_max_body_size 5M;
    
    # Timeout de segurança
    client_body_timeout 10;
    client_header_timeout 10;
    keepalive_timeout 5 5;
    send_timeout 10;
}
```

### 3. Configurar Laravel

#### Atualizar .env:
```env
APP_URL=https://seudominio.com
ASSET_URL=https://seudominio.com
SESSION_SECURE_COOKIE=true
SESSION_ENCRYPT=true
FORCE_HTTPS=true
```

#### Atualizar app/Providers/AppServiceProvider.php:
```php
public function boot()
{
    if (config('app.env') === 'production') {
        \URL::forceScheme('https');
    }
}
```

### 4. Configurar Firewall

```bash
# UFW (Ubuntu)
sudo ufw allow 22/tcp    # SSH
sudo ufw allow 80/tcp    # HTTP
sudo ufw allow 443/tcp   # HTTPS
sudo ufw enable
```

### 5. Testar Configuração

#### Verificar SSL:
```bash
# Testar certificado
openssl s_client -connect seudominio.com:443

# Testar headers
curl -I https://seudominio.com
```

#### Sites para validar:
- https://www.ssllabs.com/ssltest/
- https://securityheaders.com/
- https://observatory.mozilla.org/

## 🚨 Checklist de Segurança HTTPS

- [ ] Certificado SSL válido instalado
- [ ] Redirecionamento HTTP → HTTPS configurado
- [ ] HSTS header configurado
- [ ] Cookies marcados como Secure
- [ ] Mixed content resolvido (sem recursos HTTP)
- [ ] TLS 1.2 ou 1.3 apenas
- [ ] Ciphers fracos desabilitados
- [ ] SSL Stapling ativado
- [ ] Certificado com pelo menos 2048 bits
- [ ] Renovação automática configurada

## 🔧 Troubleshooting

### Erro: Mixed Content
```javascript
// Forçar HTTPS em assets
<script src="{{ secure_asset('js/app.js') }}"></script>
<link href="{{ secure_asset('css/app.css') }}" rel="stylesheet">
```

### Erro: Redirect Loop
```php
// Adicionar em TrustProxies middleware
protected $proxies = '*';
protected $headers = Request::HEADER_X_FORWARDED_ALL;
```

### Erro: Certificado inválido
```bash
# Verificar data de expiração
openssl x509 -in /path/to/cert.pem -noout -dates

# Renovar certificado
sudo certbot renew
```

## 📱 App Mobile

Se tiver app mobile, adicionar certificate pinning:

```swift
// iOS
let publicKeyHash = "AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA="
```

```kotlin
// Android
<network-security-config>
    <domain-config>
        <domain includeSubdomains="true">seudominio.com</domain>
        <pin-set expiration="2025-01-01">
            <pin digest="SHA-256">AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA=</pin>
        </pin-set>
    </domain-config>
</network-security-config>
```

## 🎯 Resultado Esperado

Após configuração correta:
- ✅ Site acessível apenas via HTTPS
- ✅ Nota A+ no SSL Labs
- ✅ Nota A no Security Headers
- ✅ Dados criptografados em trânsito
- ✅ Proteção contra man-in-the-middle
- ✅ Conformidade com LGPD/GDPR

---

**⚠️ LEMBRE-SE: Sem HTTPS, seu sistema NÃO está seguro para produção!**