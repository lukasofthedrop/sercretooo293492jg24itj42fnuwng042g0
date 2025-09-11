<!DOCTYPE html>
<html>
<head>
    <title>Verificação 2FA</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f5f5f5; }
        .container { max-width: 400px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h1 { color: #333; text-align: center; margin-bottom: 30px; }
        input[type="text"] { width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 4px; margin: 10px 0; text-align: center; font-size: 24px; }
        button { background: #007cba; color: white; padding: 12px 24px; border: none; border-radius: 4px; cursor: pointer; width: 100%; margin: 10px 0; }
        button:hover { background: #005a87; }
        .error { color: red; margin: 5px 0; text-align: center; }
        .info { color: #666; text-align: center; margin: 10px 0; font-size: 14px; }
        .logout-link { text-align: center; margin-top: 20px; }
        .logout-link a { color: #666; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Verificação 2FA</h1>
        <p class="info">Digite o código de 6 dígitos do seu aplicativo autenticador</p>
        
        <form method="POST" action="{{ route('2fa.verify.submit') }}">
            @csrf
            <input type="text" name="code" placeholder="000000" maxlength="6" required autofocus>
            @error('code')
                <div class="error">{{ $message }}</div>
            @enderror
            <button type="submit">Verificar</button>
        </form>
        
        <p class="info">Não consegue acessar seu aplicativo?<br>Use um código de recuperação no campo acima</p>
        
        <div class="logout-link">
            <a href="{{ route('logout.completo') }}">Fazer logout</a>
        </div>
    </div>

    <script>
    document.querySelector('input[name="code"]').addEventListener('input', function(e) {
        e.target.value = e.target.value.replace(/[^0-9]/g, '');
        if (e.target.value.length === 6) {
            e.target.form.submit();
        }
    });
    </script>
</body>
</html>