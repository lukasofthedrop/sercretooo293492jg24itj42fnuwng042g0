import './bootstrap.js';

// Importa o CSS
import '../css/app.css';

// Importa Chart.js e disponibiliza globalmente
import {
    Chart,
    CategoryScale,
    LinearScale,
    BarElement,
    Title,
    Tooltip,
    Legend,
    ArcElement,
    DoughnutController,
    BarController
} from 'chart.js';

Chart.register(
    CategoryScale,
    LinearScale,
    BarElement,
    Title,
    Tooltip,
    Legend,
    ArcElement,
    DoughnutController,
    BarController
);

// Disponibilizar Chart.js globalmente
window.Chart = Chart;

console.log('==== INICIANDO SISTEMA ORIGINAL SEM VUE ====');

// Sistema original sem Vue.js - carrega jogos via API
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM carregado, iniciando carregamento de jogos...');
    loadCasinoGames();
});

async function loadCasinoGames() {
    const container = document.getElementById('ondagames_oficial');

    if (!container) {
        console.error('Container #ondagames_oficial não encontrado!');
        return;
    }

    container.innerHTML = `
        <div style="padding: 40px; text-align: center; color: white;">
            <div style="font-size: 18px; margin-bottom: 10px;">Carregando Cassino...</div>
            <div style="font-size: 14px; opacity: 0.7;">Buscando jogos disponíveis...</div>
        </div>
    `;

    try {
        const games = await fetchAllGames();
        renderGames(games, container);
    } catch (error) {
        console.error('Erro ao carregar jogos:', error);
        container.innerHTML = `
            <div style="padding: 40px; text-align: center; color: white;">
                <div style="font-size: 18px; margin-bottom: 10px; color: #ef4444;">Erro ao carregar cassino</div>
                <div style="font-size: 14px; opacity: 0.7;">Erro: ${error.message}</div>
                <button onclick="loadCasinoGames()" style="margin-top: 20px; padding: 10px 20px; background: #3b82f6; color: white; border: none; border-radius: 5px; cursor: pointer;">Tentar novamente</button>
            </div>
        `;
    }
}

async function fetchAllGames() {
    const allGames = [];
    let page = 1;
    let hasNext = true;

    while (hasNext) {
        const response = await fetch(`/api/casinos/games?per_page=120&page=${page}`);

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const payload = await response.json();
        const pageGames = extractGames(payload);
        allGames.push(...pageGames);

        const nextPageUrl = payload?.games?.next_page_url ?? null;
        hasNext = Boolean(nextPageUrl);
        page += 1;
    }

    return allGames;
}

function extractGames(payload) {
    if (Array.isArray(payload)) {
        return payload;
    }

    if (Array.isArray(payload?.data)) {
        return payload.data;
    }

    if (Array.isArray(payload?.games)) {
        return payload.games;
    }

    if (Array.isArray(payload?.games?.data)) {
        return payload.games.data;
    }

    return [];
}

function resolveImageSource(game) {
    const candidate = game.banner || game.cover;

    if (!candidate) {
        return null;
    }

    if (/^https?:/i.test(candidate)) {
        return candidate;
    }

    if (candidate.startsWith('/')) {
        return candidate;
    }

    return `/storage/${candidate.replace(/^\/*/, '')}`;
}

function renderGames(data, container) {
    const games = Array.isArray(data) ? data : [];

    let html = `
        <div style="min-height: 100vh; background: var(--background_geral, #0f172a); color: var(--background_geral_text_color, #f8fafc); padding: 20px;">
            <div style="max-width: 1280px; margin: 0 auto;">
                <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px; margin-bottom: 32px;">
                    <div>
                        <h1 style="font-size: 32px; margin: 8px 0;">🎰 Cassino Online</h1>
                        <p style="font-size: 14px; opacity: 0.8;">${games.length} jogos disponíveis nas categorias mais jogadas do mercado.</p>
                    </div>
                    <button onclick="loadCasinoGames()" style="padding: 10px 20px; border-radius: 999px; border: 1px solid rgba(255,255,255,0.2); background: rgba(255,255,255,0.08); color: #fff; cursor: pointer;">
                        Atualizar lista
                    </button>
                </div>
    `;

    if (games.length) {
        html += `
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px;">
        `;

        games.forEach((game) => {
            const imageSrc = resolveImageSource(game);
            const provider = game.provider?.name || game.provider_name || 'Provedor';
            const gameName = game.game_name || game.name || 'Jogo';
            const actionUrl = `/game/${encodeURIComponent(game.game_code || game.id || '')}`;

            html += `
                <div style="background: rgba(15, 23, 42, 0.6); border-radius: 16px; padding: 16px; text-align: center; border: 1px solid rgba(148, 163, 184, 0.25); box-shadow: 0 8px 20px rgba(15, 23, 42, 0.45);">
                    <div style="width: 100%; aspect-ratio: 16/10; background: rgba(148,163,184,0.08); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 12px; overflow: hidden;">
                        ${imageSrc ? `<img src="${imageSrc}" alt="${gameName}" style="width: 100%; height: 100%; object-fit: cover;">` : `<span style="font-size: 42px;">🎮</span>`}
                    </div>
                    <div style="font-size: 15px; font-weight: 600; margin-bottom: 4px; color: #e2e8f0;">${gameName}</div>
                    <div style="font-size: 12px; opacity: 0.7; margin-bottom: 12px;">${provider}</div>
                    <button onclick="window.open('${actionUrl}', '_blank')" style="width: 100%; padding: 10px; background: linear-gradient(135deg, #22c55e, #15803d); color: white; border: none; border-radius: 999px; cursor: pointer; font-size: 13px; font-weight: 600;">
                        ▶️ Jogar agora
                    </button>
                </div>
            `;
        });

        html += '</div>';
    } else {
        html += `
            <div style="text-align: center; padding: 60px 20px;">
                <div style="font-size: 64px; margin-bottom: 20px;">🎰</div>
                <div style="font-size: 24px; margin-bottom: 10px;">Nenhum jogo encontrado</div>
                <div style="font-size: 16px; opacity: 0.7;">Os jogos do cassino serão carregados em breve.</div>
            </div>
        `;
    }

    html += `
            </div>
        </div>
    `;

    container.innerHTML = html;
}

// Disponibiliza função globalmente para botão de retry
window.loadCasinoGames = loadCasinoGames;

console.log('==== SISTEMA ORIGINAL INICIALIZADO ====');
