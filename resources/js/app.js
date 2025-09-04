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
    
    console.log('Container encontrado, carregando jogos...');
    
    // Exibe loading
    container.innerHTML = `
        <div style="padding: 40px; text-align: center; color: white;">
            <div style="font-size: 18px; margin-bottom: 10px;">Carregando Cassino...</div>
            <div style="font-size: 14px; opacity: 0.7;">Buscando jogos disponíveis...</div>
        </div>
    `;
    
    try {
        // Busca jogos via API
        console.log('Fazendo requisição para /api/casinos/games...');
        const response = await fetch('/api/casinos/games');
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        console.log('Resposta da API recebida:', data);
        
        // Renderiza jogos
        renderGames(data, container);
        
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

function renderGames(data, container) {
    console.log('Renderizando jogos no container...');
    
    // Estrutura básica do cassino
    let html = `
        <div style="min-height: 100vh; background: var(--background_geral, #1a1a1a); color: var(--background_geral_text_color, white); padding: 20px;">
            <div style="max-width: 1200px; margin: 0 auto;">
                <h1 style="font-size: 32px; margin-bottom: 30px; text-align: center;">🎰 Cassino Online</h1>
    `;
    
    if (data && Array.isArray(data) && data.length > 0) {
        html += `
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px;">
        `;
        
        data.forEach((game, index) => {
            if (index < 50) { // Limita para mostrar apenas 50 jogos
                html += `
                    <div style="background: rgba(255,255,255,0.1); border-radius: 10px; padding: 15px; text-align: center; border: 1px solid rgba(255,255,255,0.2);">
                        <div style="width: 100%; height: 120px; background: rgba(255,255,255,0.05); border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-bottom: 10px;">
                            ${game.banner ? 
                                `<img src="${game.banner}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 8px;" alt="${game.game_name || 'Jogo'}" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                 <div style="display: none; font-size: 48px;">🎮</div>` :
                                `<div style="font-size: 48px;">🎮</div>`
                            }
                        </div>
                        <div style="font-size: 14px; font-weight: bold; margin-bottom: 5px;">${game.game_name || 'Jogo sem nome'}</div>
                        <div style="font-size: 12px; opacity: 0.7; margin-bottom: 10px;">${game.provider?.name || 'Provedor'}</div>
                        <button onclick="window.open('/game/${game.game_code || game.id}', '_blank')" style="width: 100%; padding: 8px; background: #22c55e; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 12px;">
                            ▶️ Jogar
                        </button>
                    </div>
                `;
            }
        });
        
        html += `</div>`;
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
    console.log('Jogos renderizados com sucesso!');
}

// Disponibiliza função globalmente para botão de retry
window.loadCasinoGames = loadCasinoGames;

console.log('==== SISTEMA ORIGINAL INICIALIZADO ====');