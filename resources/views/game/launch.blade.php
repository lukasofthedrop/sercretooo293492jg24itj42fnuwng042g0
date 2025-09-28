<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jogar | {{ $gameName }}</title>
    <style>
        body { background: #0b1220; color: #e5e7eb; font-family: system-ui, -apple-system, Segoe UI, Roboto, Ubuntu, Cantarell, 'Helvetica Neue', Arial; }
        .wrap { max-width: 760px; margin: 10vh auto; padding: 24px; background: #0f172a; border: 1px solid #1f2937; border-radius: 12px; }
        .btn { display:inline-block; padding: 10px 16px; border-radius: 8px; text-decoration:none; }
        .btn-primary { background:#22c55e; color:#0b1220; }
        .btn-alt { background:#111827; color:#e5e7eb; border:1px solid #374151; }
        .muted { color:#9ca3af; }
        .mt { margin-top: 16px; }
        .err { color:#ef4444; }
    </style>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
<div class="wrap">
    <h1 style="margin:0 0 8px 0; font-size: 22px;">Jogar: {{ $gameName }}</h1>
    <p class="muted">Estamos preparando seu jogo…</p>

    <div id="status" class="mt" style="white-space: pre-wrap"></div>
    <div id="actions" class="mt"></div>
</div>

<script>
const gameId = {{ (int) $gameId }};
const statusEl = document.getElementById('status');
const actionsEl = document.getElementById('actions');

async function tryLaunch() {
  statusEl.textContent = 'Consultando servidor do jogo…';
  try {
    const res = await fetch(`/api/games/single/${gameId}`);
    const txt = await res.text();
    let data;
    try { data = JSON.parse(txt); } catch (_) { data = {}; }

    if (res.ok && data && data.gameUrl) {
      statusEl.textContent = 'Abrindo o jogo…';
      window.open(data.gameUrl, '_blank');
      actionsEl.innerHTML = `<div class="mt"><a class="btn btn-alt" href="/">Voltar</a></div>`;
      return;
    }

    if (data && data.error) {
      statusEl.innerHTML = `<span class="err">${data.error}</span>`;
      if (data.action === 'deposit') {
        actionsEl.innerHTML = `
          <div class="mt">
            <a class="btn btn-primary" href="/admin">Fazer Depósito</a>
            <a class="btn btn-alt" href="/">Voltar</a>
          </div>
        `;
      } else {
        actionsEl.innerHTML = `
          <div class="mt">
            <a class="btn btn-primary" href="/admin">Entrar / Registrar</a>
            <a class="btn btn-alt" href="/">Voltar</a>
          </div>
        `;
      }
      return;
    }

    statusEl.innerHTML = '<span class="err">Não foi possível iniciar o jogo agora.</span>';
    actionsEl.innerHTML = `<div class="mt"><a class="btn btn-alt" href="/">Voltar</a></div>`;
  } catch (e) {
    statusEl.innerHTML = '<span class="err">Falha de conexão com a API.</span>';
    actionsEl.innerHTML = `<div class="mt"><a class="btn btn-alt" href="/">Voltar</a></div>`;
  }
}

tryLaunch();
</script>
</body>
</html>
