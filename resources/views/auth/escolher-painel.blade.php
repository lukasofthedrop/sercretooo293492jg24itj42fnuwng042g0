<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Escolher Painel - LucrativaBet</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Roboto Condensed', sans-serif;
            background: linear-gradient(135deg, #0f0f0f 0%, #1a1a1a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .container {
            text-align: center;
            padding: 40px;
            background: rgba(0, 0, 0, 0.8);
            border-radius: 20px;
            border: 2px solid #00ff00;
            box-shadow: 0 0 30px rgba(0, 255, 0, 0.3);
            max-width: 500px;
            width: 90%;
        }
        
        h1 {
            color: #00ff00;
            font-size: 2.5em;
            margin-bottom: 10px;
            text-shadow: 0 0 10px rgba(0, 255, 0, 0.5);
        }
        
        .subtitle {
            color: #888;
            margin-bottom: 40px;
            font-size: 1.1em;
        }
        
        .buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 30px;
            background: rgba(0, 0, 0, 0.9);
            border: 2px solid #333;
            border-radius: 15px;
            text-decoration: none;
            transition: all 0.3s;
            min-width: 180px;
        }
        
        .btn:hover {
            border-color: #00ff00;
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 255, 0, 0.3);
        }
        
        .btn-admin {
            border-color: #ff4444;
        }
        
        .btn-admin:hover {
            border-color: #ff6666;
            box-shadow: 0 10px 25px rgba(255, 68, 68, 0.3);
        }
        
        .btn-affiliate {
            border-color: #00ff00;
        }
        
        .icon {
            font-size: 3em;
            margin-bottom: 15px;
        }
        
        .btn-admin .icon {
            color: #ff4444;
        }
        
        .btn-affiliate .icon {
            color: #00ff00;
        }
        
        .btn-title {
            color: white;
            font-size: 1.3em;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .btn-desc {
            color: #888;
            font-size: 0.9em;
        }
        
        .logo {
            width: 150px;
            height: auto;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>LucrativaBet</h1>
        <p class="subtitle">Escolha o painel que deseja acessar</p>
        
        <div class="buttons">
            <a href="/admin/login" class="btn btn-admin">
                <div class="icon">👔</div>
                <div class="btn-title">Administrador</div>
                <div class="btn-desc">Painel de gestão completa</div>
            </a>
            
            <a href="/afiliado/login" class="btn btn-affiliate">
                <div class="icon">🤝</div>
                <div class="btn-title">Afiliado</div>
                <div class="btn-desc">Dashboard de afiliados</div>
            </a>
        </div>
    </div>
</body>
</html>