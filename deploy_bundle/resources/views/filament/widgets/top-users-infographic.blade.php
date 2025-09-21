<x-filament-widgets::widget class="fi-wi-chart">
    <x-filament::section 
        :heading="$this->getHeading()"
        :description="$this->getDescription()"
    >
        {{-- Professional Infographic Header --}}
        <div class="top-users-header" style="
            background: linear-gradient(135deg, #2d1b69 0%, #000000 100%);
            border: 2px solid #ffd700;
            border-radius: 12px 12px 0 0;
            padding: 20px;
            margin: -16px -16px 25px -16px;
            text-align: center;
            position: relative;
            overflow: hidden;
        ">
            <div style="
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                height: 4px;
                background: linear-gradient(90deg, #ffd700, #00ff00, #00ff7f, #00bfff, #ffd700);
                animation: ranking-pulse 3s linear infinite;
            "></div>
            
            <div style="
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 15px;
            ">
                <div style="
                    background: rgba(255, 215, 0, 0.2);
                    border: 2px solid #ffd700;
                    border-radius: 50%;
                    width: 60px;
                    height: 60px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 24px;
                    animation: crown-glow 2s ease-in-out infinite alternate;
                ">
                    👑
                </div>
                
                <div>
                    <h3 style="
                        color: #ffd700;
                        font-weight: 900;
                        font-size: 18px;
                        margin: 0;
                        text-shadow: 0 0 15px rgba(255, 215, 0, 0.6);
                        letter-spacing: 2px;
                    ">
                        RANKING ELITE DE PERFORMANCE
                    </h3>
                    <p style="
                        color: #ffffff;
                        font-size: 13px;
                        margin: 5px 0 0 0;
                        opacity: 0.9;
                        letter-spacing: 1px;
                    ">
                        Análise Comparativa Profissional | Top Usuários VIP
                    </p>
                </div>
            </div>
            
            {{-- Performance Indicators --}}
            <div style="
                display: flex;
                justify-content: space-around;
                margin-top: 15px;
                gap: 10px;
            ">
                <div style="
                    background: rgba(0, 255, 0, 0.1);
                    border: 1px solid rgba(0, 255, 0, 0.3);
                    border-radius: 8px;
                    padding: 8px;
                    text-align: center;
                    min-width: 80px;
                ">
                    <div style="color: #00ff00; font-size: 12px; font-weight: bold;">DEPÓSITOS</div>
                    <div style="color: #ffffff; font-size: 10px; opacity: 0.8;">Volume</div>
                </div>
                
                <div style="
                    background: rgba(0, 255, 127, 0.1);
                    border: 1px solid rgba(0, 255, 127, 0.3);
                    border-radius: 8px;
                    padding: 8px;
                    text-align: center;
                    min-width: 80px;
                ">
                    <div style="color: #00ff7f; font-size: 12px; font-weight: bold;">APOSTAS</div>
                    <div style="color: #ffffff; font-size: 10px; opacity: 0.8;">Atividade</div>
                </div>
                
                <div style="
                    background: rgba(255, 215, 0, 0.1);
                    border: 1px solid rgba(255, 215, 0, 0.3);
                    border-radius: 8px;
                    padding: 8px;
                    text-align: center;
                    min-width: 80px;
                ">
                    <div style="color: #ffd700; font-size: 12px; font-weight: bold;">AFILIADOS</div>
                    <div style="color: #ffffff; font-size: 10px; opacity: 0.8;">Comissões</div>
                </div>
            </div>
        </div>

        {{-- Professional Chart Container --}}
        <div class="ranking-infographic-container" style="
            background: radial-gradient(ellipse at center, #1e1e1e 0%, #0a0a0a 100%);
            border: 2px solid rgba(255, 215, 0, 0.3);
            border-radius: 12px;
            padding: 25px;
            position: relative;
            min-height: 450px;
            box-shadow: 
                0 6px 20px rgba(255, 215, 0, 0.2),
                inset 0 0 40px rgba(255, 215, 0, 0.1);
        ">
            {{-- Professional Chart Canvas --}}
            <div style="position: relative; height: 380px;">
                <canvas
                    x-data="{
                        chart: null,
                        init() {
                            $nextTick(() => {
                                this.chart = new Chart($el, {
                                    type: @js($this->getType()),
                                    data: @js($this->getData()),
                                    options: {
                                        ...@js($this->getOptions()),
                                        // Enhanced professional interactions
                                        interaction: {
                                            intersect: false,
                                            mode: 'y',
                                        },
                                        onHover: (event, elements) => {
                                            event.native.target.style.cursor = elements.length > 0 ? 'pointer' : 'default';
                                            
                                            // Highlight effect
                                            if (elements.length > 0) {
                                                const element = elements[0];
                                                const datasetIndex = element.datasetIndex;
                                                
                                                // Add glow effect to hovered bar
                                                this.chart.data.datasets.forEach((dataset, index) => {
                                                    if (index === datasetIndex) {
                                                        dataset.backgroundColor = dataset.backgroundColor.map(color => 
                                                            color.replace('0.8', '1.0')
                                                        );
                                                    }
                                                });
                                                this.chart.update('none');
                                            }
                                        },
                                        onClick: (event, elements) => {
                                            if (elements.length > 0) {
                                                const element = elements[0];
                                                const dataIndex = element.index;
                                                const userName = this.chart.data.labels[dataIndex];
                                                
                                                // Professional click feedback
                                                console.log('Usuário selecionado:', userName);
                                            }
                                        }
                                    }
                                });
                            });
                        }
                    }"
                    x-on:filament-widget-chart-update.window="chart.data = $event.detail.data; chart.update()"
                ></canvas>
            </div>
            
            {{-- Professional Performance Metrics Footer --}}
            <div style="
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                gap: 15px;
                margin-top: 25px;
                padding-top: 20px;
                border-top: 2px solid rgba(255, 215, 0, 0.2);
            ">
                <div style="
                    text-align: center;
                    background: rgba(0, 255, 0, 0.1);
                    padding: 12px;
                    border-radius: 8px;
                    border: 1px solid rgba(0, 255, 0, 0.2);
                ">
                    <div style="
                        color: #00ff00; 
                        font-weight: 900; 
                        font-size: 16px;
                        text-shadow: 0 0 5px rgba(0, 255, 0, 0.5);
                    ">ELITE</div>
                    <div style="color: #ffffff; font-size: 11px; opacity: 0.9; margin-top: 2px;">Top Tier</div>
                </div>
                
                <div style="
                    text-align: center;
                    background: rgba(255, 215, 0, 0.1);
                    padding: 12px;
                    border-radius: 8px;
                    border: 1px solid rgba(255, 215, 0, 0.2);
                ">
                    <div style="
                        color: #ffd700; 
                        font-weight: 900; 
                        font-size: 16px;
                        text-shadow: 0 0 5px rgba(255, 215, 0, 0.5);
                    ">VIP</div>
                    <div style="color: #ffffff; font-size: 11px; opacity: 0.9; margin-top: 2px;">Premium</div>
                </div>
                
                <div style="
                    text-align: center;
                    background: rgba(0, 255, 127, 0.1);
                    padding: 12px;
                    border-radius: 8px;
                    border: 1px solid rgba(0, 255, 127, 0.2);
                ">
                    <div style="
                        color: #00ff7f; 
                        font-weight: 900; 
                        font-size: 16px;
                        text-shadow: 0 0 5px rgba(0, 255, 127, 0.5);
                    ">ATIVO</div>
                    <div style="color: #ffffff; font-size: 11px; opacity: 0.9; margin-top: 2px;">High Vol</div>
                </div>
                
                <div style="
                    text-align: center;
                    background: rgba(0, 191, 255, 0.1);
                    padding: 12px;
                    border-radius: 8px;
                    border: 1px solid rgba(0, 191, 255, 0.2);
                ">
                    <div style="
                        color: #00bfff; 
                        font-weight: 900; 
                        font-size: 16px;
                        text-shadow: 0 0 5px rgba(0, 191, 255, 0.5);
                    ">PRO</div>
                    <div style="color: #ffffff; font-size: 11px; opacity: 0.9; margin-top: 2px;">Expert</div>
                </div>
            </div>
        </div>
    </x-filament::section>

    {{-- Professional CSS Animations and Effects --}}
    <style>
        @keyframes ranking-pulse {
            0%, 100% { opacity: 0.6; }
            50% { opacity: 1; }
        }
        
        @keyframes crown-glow {
            0% { 
                box-shadow: 0 0 10px rgba(255, 215, 0, 0.4);
                transform: scale(1);
            }
            100% { 
                box-shadow: 0 0 25px rgba(255, 215, 0, 0.8), 0 0 40px rgba(255, 215, 0, 0.4);
                transform: scale(1.05);
            }
        }
        
        .top-users-header::before {
            content: "";
            position: absolute;
            top: -3px;
            left: -3px;
            right: -3px;
            bottom: -3px;
            background: linear-gradient(45deg, #ffd700, transparent, #ffd700, transparent, #ffd700);
            z-index: -1;
            border-radius: 15px;
            animation: header-glow 4s ease-in-out infinite alternate;
        }
        
        @keyframes header-glow {
            0% { 
                opacity: 0.4;
                filter: blur(5px);
            }
            100% { 
                opacity: 0.8;
                filter: blur(2px);
            }
        }
        
        .ranking-infographic-container:hover {
            transform: translateY(-3px);
            transition: all 0.4s ease;
            box-shadow: 
                0 12px 35px rgba(255, 215, 0, 0.3),
                inset 0 0 50px rgba(255, 215, 0, 0.15);
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .top-users-header > div:first-child {
                flex-direction: column;
                gap: 10px;
            }
            
            .ranking-infographic-container div[style*="grid-template-columns"] {
                grid-template-columns: repeat(2, 1fr) !important;
            }
        }
    </style>
</x-filament-widgets::widget>