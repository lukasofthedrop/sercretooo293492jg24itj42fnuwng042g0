<x-filament-widgets::widget class="fi-wi-chart">
    <x-filament::section 
        :heading="$this->getHeading()"
        :description="$this->getDescription()"
    >
        {{-- Professional Infographic Header --}}
        <div class="top5-games-header" style="
            background: linear-gradient(135deg, #1a1a1a 0%, #000000 100%);
            border: 2px solid #00ff00;
            border-radius: 12px 12px 0 0;
            padding: 15px;
            margin: -16px -16px 20px -16px;
            text-align: center;
            position: relative;
            overflow: hidden;
        ">
            <div style="
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                height: 3px;
                background: linear-gradient(90deg, #00ff00, #00ff7f, #ffd700, #ffa500, #00bfff);
                animation: pulse-bar 2s ease-in-out infinite alternate;
            "></div>
            
            <h3 style="
                color: #00ff00;
                font-weight: 900;
                font-size: 16px;
                margin: 0;
                text-shadow: 0 0 10px rgba(0, 255, 0, 0.5);
                letter-spacing: 1px;
            ">
                🎮 ANÁLISE PROFISSIONAL - TOP 5 JOGOS
            </h3>
            <p style="
                color: #90EE90;
                font-size: 12px;
                margin: 5px 0 0 0;
                opacity: 0.9;
            ">
                Infográfico de Performance | Apostas por Popularidade
            </p>
        </div>

        {{-- Professional Chart Container --}}
        <div class="chart-infographic-container" style="
            background: radial-gradient(ellipse at center, #2a2a2a 0%, #1a1a1a 100%);
            border: 1px solid #00ff00;
            border-radius: 12px;
            padding: 20px;
            position: relative;
            min-height: 400px;
            box-shadow: 
                0 4px 15px rgba(0, 255, 0, 0.2),
                inset 0 0 30px rgba(0, 255, 0, 0.1);
        ">
            {{-- Chart Canvas --}}
            <div style="position: relative; height: 350px;">
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
                                        // Enhanced professional options
                                        interaction: {
                                            intersect: false,
                                            mode: 'index',
                                        },
                                        onHover: (event, elements) => {
                                            event.native.target.style.cursor = elements.length > 0 ? 'pointer' : 'default';
                                        },
                                        plugins: {
                                            ...@js($this->getOptions())['plugins'],
                                            legend: {
                                                ...@js($this->getOptions())['plugins']['legend'],
                                                onHover: (event, legendItem, legend) => {
                                                    legend.chart.getDatasetMeta(0).data.forEach((element, index) => {
                                                        element.options.backgroundColor = index === legendItem.index 
                                                            ? element.options.backgroundColor 
                                                            : element.options.backgroundColor.replace('0.8', '0.3');
                                                    });
                                                    legend.chart.update('none');
                                                },
                                                onLeave: (event, legendItem, legend) => {
                                                    legend.chart.getDatasetMeta(0).data.forEach((element, index) => {
                                                        element.options.backgroundColor = element.options.backgroundColor.replace('0.3', '0.8');
                                                    });
                                                    legend.chart.update('none');
                                                }
                                            }
                                        }
                                    }
                                });
                            });
                        }
                    }"
                    x-on:filament-widget-chart-update.window="chart.data = $event.detail.data; chart.update()"
                ></canvas>
                
                {{-- Center Statistics --}}
                <div style="
                    position: absolute;
                    top: 50%;
                    left: 35%;
                    transform: translate(-50%, -50%);
                    text-align: center;
                    pointer-events: none;
                ">
                    <div style="
                        color: #00ff00;
                        font-size: 24px;
                        font-weight: 900;
                        text-shadow: 0 0 10px rgba(0, 255, 0, 0.8);
                        line-height: 1;
                    ">
                        TOP 5
                    </div>
                    <div style="
                        color: #ffffff;
                        font-size: 12px;
                        opacity: 0.8;
                        margin-top: 5px;
                    ">
                        GAMES
                    </div>
                </div>
            </div>
            
            {{-- Professional Stats Footer --}}
            <div style="
                display: flex;
                justify-content: space-around;
                margin-top: 20px;
                padding-top: 15px;
                border-top: 1px solid rgba(0, 255, 0, 0.3);
            ">
                <div style="text-align: center;">
                    <div style="color: #00ff00; font-weight: bold; font-size: 14px;">LIDERANÇA</div>
                    <div style="color: #ffffff; font-size: 12px; opacity: 0.8;">Jogo #1</div>
                </div>
                <div style="text-align: center;">
                    <div style="color: #ffd700; font-weight: bold; font-size: 14px;">DIVERSIDADE</div>
                    <div style="color: #ffffff; font-size: 12px; opacity: 0.8;">5 Tipos</div>
                </div>
                <div style="text-align: center;">
                    <div style="color: #00bfff; font-weight: bold; font-size: 14px;">ENGAJAMENTO</div>
                    <div style="color: #ffffff; font-size: 12px; opacity: 0.8;">Alto</div>
                </div>
            </div>
        </div>
    </x-filament::section>

    {{-- Professional CSS Animations --}}
    <style>
        @keyframes pulse-bar {
            0% { opacity: 0.6; transform: scaleX(0.8); }
            100% { opacity: 1; transform: scaleX(1); }
        }
        
        .top5-games-header::before {
            content: "";
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            background: linear-gradient(45deg, #00ff00, transparent, #00ff00);
            z-index: -1;
            border-radius: 12px;
            animation: border-glow 3s ease-in-out infinite alternate;
        }
        
        @keyframes border-glow {
            0% { box-shadow: 0 0 10px rgba(0, 255, 0, 0.3); }
            100% { box-shadow: 0 0 20px rgba(0, 255, 0, 0.7), 0 0 30px rgba(0, 255, 0, 0.4); }
        }
        
        .chart-infographic-container:hover {
            transform: translateY(-2px);
            transition: all 0.3s ease;
            box-shadow: 
                0 8px 25px rgba(0, 255, 0, 0.4),
                inset 0 0 40px rgba(0, 255, 0, 0.15);
        }
    </style>
</x-filament-widgets::widget>