<x-filament-panels::page>
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    <style>
        .tools-container {
            display: grid;
            gap: 2rem;
        }
        
        .tool-section {
            background: linear-gradient(135deg, rgba(30, 41, 59, 0.98) 0%, rgba(15, 23, 42, 0.98) 100%);
            border: 1px solid rgba(34, 197, 94, 0.25);
            border-radius: 12px;
            padding: 1.5rem;
        }
        
        .section-title {
            color: #f1f5f9;
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .section-title i {
            color: #22c55e;
        }
        
        .link-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
        }
        
        .link-card {
            background: rgba(15, 23, 42, 0.5);
            border: 1px solid rgba(241, 245, 249, 0.1);
            border-radius: 8px;
            padding: 1rem;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .link-card:hover {
            background: rgba(34, 197, 94, 0.1);
            border-color: rgba(34, 197, 94, 0.3);
            transform: translateY(-2px);
        }
        
        .link-card-header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 0.75rem;
        }
        
        .link-card-icon {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            font-size: 1.25rem;
        }
        
        .whatsapp-icon {
            background: #25D366;
            color: white;
        }
        
        .telegram-icon {
            background: #0088cc;
            color: white;
        }
        
        .twitter-icon {
            background: #1DA1F2;
            color: white;
        }
        
        .facebook-icon {
            background: #1877F2;
            color: white;
        }
        
        .link-card-title {
            color: #f1f5f9;
            font-weight: 600;
        }
        
        .link-card-description {
            color: rgba(241, 245, 249, 0.6);
            font-size: 0.875rem;
            margin-bottom: 0.75rem;
        }
        
        .share-button {
            width: 100%;
            background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .share-button:hover {
            transform: scale(1.02);
            box-shadow: 0 4px 12px rgba(34, 197, 94, 0.3);
        }
        
        .copy-text-item {
            background: rgba(15, 23, 42, 0.5);
            border: 1px solid rgba(241, 245, 249, 0.1);
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
        }
        
        .copy-text-title {
            color: #22c55e;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .copy-text-content {
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(241, 245, 249, 0.1);
            border-radius: 6px;
            padding: 0.75rem;
            color: rgba(241, 245, 249, 0.8);
            font-size: 0.875rem;
            line-height: 1.6;
            margin-bottom: 0.75rem;
            white-space: pre-wrap;
        }
        
        .copy-button {
            background: rgba(34, 197, 94, 0.2);
            border: 1px solid rgba(34, 197, 94, 0.3);
            color: #22c55e;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-size: 0.875rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .copy-button:hover {
            background: rgba(34, 197, 94, 0.3);
            border-color: rgba(34, 197, 94, 0.5);
        }
        
        .link-display {
            background: linear-gradient(135deg, rgba(34, 197, 94, 0.1) 0%, rgba(34, 197, 94, 0.05) 100%);
            border: 1px solid rgba(34, 197, 94, 0.3);
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .link-display-label {
            color: rgba(241, 245, 249, 0.6);
            font-size: 0.875rem;
            margin-bottom: 0.5rem;
        }
        
        .link-display-content {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .link-input {
            flex: 1;
            background: rgba(15, 23, 42, 0.8);
            border: 1px solid rgba(34, 197, 94, 0.2);
            color: #22c55e;
            padding: 0.75rem;
            border-radius: 6px;
            font-family: monospace;
            font-size: 0.875rem;
        }
        
        .qr-code-section {
            text-align: center;
            padding: 1.5rem;
            background: white;
            border-radius: 8px;
            margin-top: 1rem;
        }
        
        .qr-code-section img {
            width: 200px;
            height: 200px;
            margin: 0 auto;
        }
        
        .stats-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid rgba(241, 245, 249, 0.1);
        }
        
        .stat-item {
            text-align: center;
        }
        
        .stat-value {
            color: #22c55e;
            font-size: 1.5rem;
            font-weight: 700;
        }
        
        .stat-label {
            color: rgba(241, 245, 249, 0.6);
            font-size: 0.75rem;
            margin-top: 0.25rem;
        }
    </style>
    
    <div class="tools-container">
        <!-- Seção do Link Principal -->
        <div class="tool-section">
            <h2 class="section-title">
                <i class="fas fa-link"></i> Seu Link de Afiliado
            </h2>
            
            <div class="link-display">
                <div class="link-display-label">Link completo para compartilhar:</div>
                <div class="link-display-content">
                    <input type="text" class="link-input" value="{{ $inviteLink }}" readonly id="mainLink">
                    <button class="share-button" style="width: auto;" onclick="copyLink('mainLink')">
                        <i class="fas fa-copy"></i> Copiar
                    </button>
                </div>
            </div>
            
            <div class="stats-row">
                <div class="stat-item">
                    <div class="stat-value">{{ $affiliateCode }}</div>
                    <div class="stat-label">Seu Código</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">40%</div>
                    <div class="stat-label">Comissão RevShare</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">∞</div>
                    <div class="stat-label">Validade Vitalícia</div>
                </div>
            </div>
        </div>
        
        <!-- Links para Redes Sociais -->
        <div class="tool-section">
            <h2 class="section-title">
                <i class="fas fa-share-alt"></i> Compartilhar em Redes Sociais
            </h2>
            
            <div class="link-grid">
                <div class="link-card" onclick="shareLink('{{ $linkWhatsapp }}')">
                    <div class="link-card-header">
                        <div class="link-card-icon whatsapp-icon">
                            <i class="fab fa-whatsapp"></i>
                        </div>
                        <div class="link-card-title">WhatsApp</div>
                    </div>
                    <div class="link-card-description">
                        Compartilhe diretamente no WhatsApp com mensagem pronta
                    </div>
                    <button class="share-button">
                        <i class="fas fa-share"></i> Compartilhar
                    </button>
                </div>
                
                <div class="link-card" onclick="shareLink('{{ $linkTelegram }}')">
                    <div class="link-card-header">
                        <div class="link-card-icon telegram-icon">
                            <i class="fab fa-telegram-plane"></i>
                        </div>
                        <div class="link-card-title">Telegram</div>
                    </div>
                    <div class="link-card-description">
                        Envie para grupos e contatos do Telegram
                    </div>
                    <button class="share-button">
                        <i class="fas fa-share"></i> Compartilhar
                    </button>
                </div>
                
                <div class="link-card" onclick="shareLink('{{ $linkTwitter }}')">
                    <div class="link-card-header">
                        <div class="link-card-icon twitter-icon">
                            <i class="fab fa-twitter"></i>
                        </div>
                        <div class="link-card-title">Twitter/X</div>
                    </div>
                    <div class="link-card-description">
                        Poste um tweet com seu link de afiliado
                    </div>
                    <button class="share-button">
                        <i class="fas fa-share"></i> Compartilhar
                    </button>
                </div>
                
                <div class="link-card" onclick="shareLink('{{ $linkFacebook }}')">
                    <div class="link-card-header">
                        <div class="link-card-icon facebook-icon">
                            <i class="fab fa-facebook-f"></i>
                        </div>
                        <div class="link-card-title">Facebook</div>
                    </div>
                    <div class="link-card-description">
                        Compartilhe no seu feed do Facebook
                    </div>
                    <button class="share-button">
                        <i class="fas fa-share"></i> Compartilhar
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Textos Prontos para Copy -->
        <div class="tool-section">
            <h2 class="section-title">
                <i class="fas fa-file-alt"></i> Textos Prontos para Divulgação
            </h2>
            
            @foreach($copyTexts as $index => $text)
            <div class="copy-text-item">
                <div class="copy-text-title">
                    <i class="fas fa-tag"></i> {{ $text['title'] }}
                </div>
                <div class="copy-text-content" id="copyText{{ $index }}">{{ $text['text'] }}</div>
                <button class="copy-button" onclick="copyText('copyText{{ $index }}')">
                    <i class="fas fa-copy"></i> Copiar Texto
                </button>
            </div>
            @endforeach
        </div>
        
        <!-- QR Code -->
        <div class="tool-section">
            <h2 class="section-title">
                <i class="fas fa-qrcode"></i> QR Code para Divulgação
            </h2>
            
            <div class="qr-code-section">
                <div id="qrcode"></div>
                <p style="color: #333; margin-top: 1rem; font-size: 0.875rem;">
                    Escaneie ou baixe o QR Code para compartilhar
                </p>
                <button class="share-button" style="margin-top: 1rem;" onclick="downloadQR()">
                    <i class="fas fa-download"></i> Baixar QR Code
                </button>
            </div>
        </div>
        
        <!-- Dicas de Marketing -->
        <div class="tool-section">
            <h2 class="section-title">
                <i class="fas fa-bullhorn"></i> Dicas de Marketing
            </h2>
            
            <div style="color: rgba(241, 245, 249, 0.8); line-height: 1.8;">
                <div style="margin-bottom: 1rem;">
                    <strong style="color: #22c55e;">📱 Redes Sociais:</strong><br>
                    Poste regularmente sobre suas vitórias e experiências positivas. Use hashtags relevantes como #cassino #jogos #apostas.
                </div>
                
                <div style="margin-bottom: 1rem;">
                    <strong style="color: #22c55e;">👥 Grupos e Comunidades:</strong><br>
                    Participe de grupos relacionados a jogos e entretenimento. Compartilhe seu link de forma natural e não invasiva.
                </div>
                
                <div style="margin-bottom: 1rem;">
                    <strong style="color: #22c55e;">📧 Email Marketing:</strong><br>
                    Se você tem uma lista de contatos, envie newsletters com dicas e seu link de afiliado.
                </div>
                
                <div style="margin-bottom: 1rem;">
                    <strong style="color: #22c55e;">🎥 Conteúdo em Vídeo:</strong><br>
                    Crie vídeos mostrando a plataforma, jogos disponíveis e processos de saque. Inclua seu link na descrição.
                </div>
                
                <div>
                    <strong style="color: #22c55e;">💡 Seja Autêntico:</strong><br>
                    Compartilhe experiências reais e seja transparente. A autenticidade gera mais conversões que propaganda agressiva.
                </div>
            </div>
        </div>
    </div>
    
    <!-- QR Code Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    
    <script>
        // Gera QR Code
        document.addEventListener('DOMContentLoaded', function() {
            new QRCode(document.getElementById("qrcode"), {
                text: "{{ $inviteLink }}",
                width: 200,
                height: 200,
                colorDark: "#22c55e",
                colorLight: "#ffffff",
                correctLevel: QRCode.CorrectLevel.H
            });
        });
        
        function copyLink(elementId) {
            const element = document.getElementById(elementId);
            element.select();
            document.execCommand('copy');
            
            // Feedback visual
            const button = event.target.closest('button');
            const originalText = button.innerHTML;
            button.innerHTML = '<i class="fas fa-check"></i> Copiado!';
            button.style.background = 'linear-gradient(135deg, #16a34a 0%, #15803d 100%)';
            
            setTimeout(() => {
                button.innerHTML = originalText;
                button.style.background = '';
            }, 2000);
        }
        
        function copyText(elementId) {
            const element = document.getElementById(elementId);
            const text = element.textContent;
            
            // Cria elemento temporário
            const textarea = document.createElement('textarea');
            textarea.value = text;
            document.body.appendChild(textarea);
            textarea.select();
            document.execCommand('copy');
            document.body.removeChild(textarea);
            
            // Feedback visual
            const button = event.target.closest('button');
            const originalText = button.innerHTML;
            button.innerHTML = '<i class="fas fa-check"></i> Copiado!';
            
            setTimeout(() => {
                button.innerHTML = originalText;
            }, 2000);
        }
        
        function shareLink(url) {
            window.open(url, '_blank', 'width=600,height=400');
        }
        
        function downloadQR() {
            const canvas = document.querySelector('#qrcode canvas');
            const url = canvas.toDataURL('image/png');
            const link = document.createElement('a');
            link.download = 'qrcode-lucrativabet.png';
            link.href = url;
            link.click();
        }
    </script>
</x-filament-panels::page>