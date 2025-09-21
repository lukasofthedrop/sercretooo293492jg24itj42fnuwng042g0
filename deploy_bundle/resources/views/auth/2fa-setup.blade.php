<!DOCTYPE html>
<html>
<head>
    <title>Configurar 2FA</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f5f5f5; }
        .container { max-width: 500px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h1 { color: #333; text-align: center; margin-bottom: 30px; }
        .qr-section { text-align: center; margin: 20px 0; }
        .secret-key { background: #f0f0f0; padding: 10px; word-break: break-all; font-family: monospace; margin: 10px 0; }
        input[type="text"] { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; margin: 10px 0; }
        button { background: #007cba; color: white; padding: 12px 24px; border: none; border-radius: 4px; cursor: pointer; width: 100%; }
        button:hover { background: #005a87; }
        .error { color: red; margin: 5px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Configurar 2FA</h1>
        
        <div class="qr-section">
            <h3>1. Escaneie o QR Code</h3>
            {!! $qrCode !!}
            <p>Use Google Authenticator ou Authy</p>
        </div>
        
        <div>
            <h3>2. Chave manual (backup)</h3>
            <div class="secret-key">{{ $secret }}</div>
        </div>
        
        <form method="POST" action="{{ route('2fa.enable') }}">
            @csrf
            <h3>3. Digite o código de 6 dígitos</h3>
            <input type="text" name="code" placeholder="000000" maxlength="6" required>
            @error('code')
                <div class="error">{{ $message }}</div>
            @enderror
            <button type="submit">Ativar 2FA</button>
        </form>
    </div>
</body>
</html>