<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">

        <!-- Meta Tags Essenciais -->
        <title>Cassino e Apostas Online com Bônus Exclusivos</title>
        <meta name="description" content="Jogue e descubra o melhor cassino online ao vivo! Aposte em esportes e cassino com bônus exclusivos. Depósitos rápidos via Pix.">

        <!-- Open Graph / Facebook -->
        <meta property="og:type" content="website">
        <meta property="og:title" content="O Melhor Cassino Online e Apostas Esportivas">
        <meta property="og:description" content="Aposte e ganhe! Cassino ao vivo, jogos de roleta, blackjack, slots e apostas esportivas. Depósitos via Pix e saques instantâneos.">
        <meta property="og:image" content="/storage/icon/icon-padrao.webp">
        <meta property="og:url" content="/">
        <meta property="og:site_name" content="Casino">

        <!-- Twitter -->
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="Cassino Online e Apostas com Pix">
        <meta name="twitter:description" content="Entre no mundo das apostas! Cassino ao vivo, jogos exclusivos e bônus especiais para novos jogadores.">
        <meta name="twitter:image" content="/storage/icon/icon-padrao.webp">

        <!-- SEO Keywords -->
        <meta name="keywords" content="Cassino online, apostas esportivas, jogos de azar, slots, roleta, blackjack, poker, cassino ao vivo, bônus cassino, apostas com Pix">

        <!-- Robots (Indexação) -->
        <meta name="robots" content="noindex, nofollow">

        <!-- Canonical -->
        <link rel="canonical" href="/">

        @php
            $setting = \Helper::getSetting();
            $favicon = !empty($setting['software_favicon'])
                ? asset('/storage/' . $setting['software_favicon'])
                : asset('/storage/icon/icon-padrao.webp'); // Caminho do favicon padrão
        @endphp

        <link rel="icon" type="image/png" sizes="32x32" href="{{ $favicon }}">




        <link rel="stylesheet" href="{{ asset('assets/css/fontawesome.min.css') }}">
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700&family=Roboto+Condensed:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;1,100&display=swap" rel="stylesheet">        <title>{{ env('APP_NAME') }}</title>
            
        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        @php $custom = \Helper::getCustom() @endphp
        <!-- Meta Pixel Code -->
        <noscript><img height="1" width="1" style="display:none"
            src="https://www.facebook.com/tr?id={{ $custom->idPixelFC }}&ev=PageView&noscript=1"
        /></noscript>

        <!-- End Meta Pixel Code -->

        <!-- Google tag (gtag.js) -->
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ $custom->idPixelGoogle }}"></script>
        <style>
            body{
                font-family: {{ $custom['font_family_default'] ?? "'Roboto Condensed', sans-serif" }};
            }
            :root {
             
            /*/////////////////////////////////////////////////////////////////
            ///////////////////////// CENTRAL DE DISIGN /////////////////////////
            /////////////////////////////////////////////////////////////////////

            //////////////////////////////////////////////////////////////// 
            ///////////// PAGINA FOOTER   | FICA EM BAIXO DO SITE ///// */


            --footer-background: {{ $custom['footer_background'] }};
            --footer-text-color: {{ $custom['footer_text_color'] }};
            --footer-links: {{ $custom['footer_links'] }};
            --footer-button-background: {{ $custom['footer_button_background'] }};
            --footer-button-text: {{ $custom['footer_button_text'] }};
            --footer-button-border: {{ $custom['footer_button_border'] }};
            --footer-icons: {{ $custom['footer_icons'] }};
            --footer-titulos: {{ $custom['footer_titulos'] }};
            --footer-button-hover-language: {{ $custom['footer_button_hover_language'] }};
            --footer-button-color-language: {{ $custom['footer_button_color_language'] }};
            --footer-button-background-language: {{ $custom['footer_button_background_language'] }};
            --footer-button-border-language: {{ $custom['footer_button_border_language'] }};
            --footer-background-language: {{ $custom['footer_background_language'] }};

            /* //////////////////////////////////////////////////////////////// */
            /* /////////// PAGINA SIDEBAR   | FICA NA LATERAL DO SITE////////// */
            /* //////////////////////////////////////////////////////////////// */
            
            --sidebar-background: {{ $custom['sidebar_background'] }};
            --sidebar-button-missoes-background: {{ $custom['sidebar_button_missoes_background'] }};
            --sidebar-button-vip-background: {{ $custom['sidebar_button_vip_background'] }};
            --sidebar-button-ganhe-background: {{ $custom['sidebar_button_ganhe_background'] }};
            --sidebar-button-bonus-background: {{ $custom['sidebar_button_bonus_background'] }};
            --sidebar-button-missoes-text: {{ $custom['sidebar_button_missoes_text'] }};
            --sidebar-button-ganhe-text: {{ $custom['sidebar_button_ganhe_text'] }};
            --sidebar-button-vip-text: {{ $custom['sidebar_button_vip_text'] }};
            --sidebar-button-hover: {{ $custom['sidebar_button_hover'] }};
            --sidebar-text-hover: {{ $custom['sidebar_text_hover'] }};
            --sidebar-text-color: {{ $custom['sidebar_text_color'] }};
            --sidebar-border: {{ $custom['sidebar_border'] }};
            --sidebar-icons: {{ $custom['sidebar_icons'] }};
            --sidebar_icons_background: {{ $custom['sidebar_icons_background'] }};
            




            /*///////////////////////////////////////////////////////////////// */ 



            /* //////////////////////////////////////////////////////////////// */
            /* /////////// PAGINA NAVHAR   | FICA EM CIMA DO SITE////////// */
            /* //////////////////////////////////////////////////////////////// */


            --navbar-background: {{ $custom['navbar_background'] }};
            --navbar-text: {{ $custom['navbar_text'] }};
            --navbar-icon-menu: {{ $custom['navbar_icon_menu'] }};
            --navbar-icon-promocoes: {{ $custom['navbar_icon_promocoes'] }};
            --navbar-icon-casino: {{ $custom['navbar_icon_casino'] }};
            --navbar-icon-sport: {{ $custom['navbar_icon_sport'] }};
            --navbar-button-text-login: {{ $custom['navbar_button_text_login'] }};
            --navbar-button-text-registro: {{ $custom['navbar_button_text_registro'] }};
            --navbar-button-background-login: {{ $custom['navbar_button_background_login'] }};
            --navbar-button-background-registro: {{ $custom['navbar_button_background_registro'] }};
            --navbar-button-border-color: {{ $custom['navbar_button_border_color'] }};
            --navbar-button-text-superior: {{ $custom['navbar_button_text_superior'] }};
            --navbar-button-background-superior: {{ $custom['navbar_button_background_superior'] }};
            --navbar-text-superior: {{ $custom['navbar_text_superior'] }};
            --navbar_button_deposito_background: {{ $custom['navbar_button_deposito_background'] }};
            --navbar_button_deposito_text_color: {{ $custom['navbar_button_deposito_text_color'] }};
            --navbar_button_deposito_border_color: {{ $custom['navbar_button_deposito_border_color'] }};
            
            --navbar_button_deposito_píx_color_text: {{ $custom['navbar_button_deposito_píx_color_text'] }};
            --navbar_button_deposito_píx_background: {{ $custom['navbar_button_deposito_píx_background'] }};
            --navbar_button_deposito_píx_icon: {{ $custom['navbar_button_deposito_píx_icon'] }};
            
            --navbar_button_carteira_background: {{ $custom['navbar_button_carteira_background'] }};
            --navbar_button_carteira_text_color: {{ $custom['navbar_button_carteira_text_color'] }};
            --navbar_button_carteira_border_color: {{ $custom['navbar_button_carteira_border_color'] }};

            --navbar_perfil_text_color: {{ $custom['navbar_perfil_text_color'] }};
            --navbar_perfil_background: {{ $custom['navbar_perfil_background'] }};
            --navbar_perfil_icon_color: {{ $custom['navbar_perfil_icon_color'] }};

            --navbar_perfil_icon_color_border: {{ $custom['navbar_perfil_icon_color_border'] }};
            --navbar_perfil_modal_icon_color: {{ $custom['navbar_perfil_modal_icon_color'] }};
            --navbar_perfil_modal_text_modal: {{ $custom['navbar_perfil_modal_text_modal'] }};
            --navbar_perfil_modal_background_modal: {{ $custom['navbar_perfil_modal_background_modal'] }};
            --navbar_perfil_modal_hover_modal: {{ $custom['navbar_perfil_modal_hover_modal'] }};
            --navbar_icon_promocoes_segunda_cor: {{ $custom['navbar_icon_promocoes_segunda_cor'] }};


 

            /*///////////////////////////////////////////////////////////////// */ 

            

            /* //////////////////////////////////////////////////////////////// */
            /* /////////// PAGINA HOME   | PAGINA PRINCIPAL DO SITE////////// */
            /* //////////////////////////////////////////////////////////////// */


            
            --home_background_button_banner: {{ $custom['home_background_button_banner'] }};
            --home_icon_color_button_banner: {{ $custom['home_icon_color_button_banner'] }};
            --home_background_categorias: {{ $custom['home_background_categorias'] }};
            --home_text_color_categorias: {{ $custom['home_text_color_categorias'] }};

 
            --topo_icon_color: {{ $custom['topo_icon_color'] }};
            --topo_button_text_color: {{ $custom['topo_button_text_color'] }};
            --topo_button_background: {{ $custom['topo_button_background'] }};
            --topo_button_border_color: {{ $custom['topo_button_border_color'] }};

            --home_background_pesquisa: {{ $custom['home_background_pesquisa'] }};
            --home_text_color_pesquisa: {{ $custom['home_text_color_pesquisa'] }};
            --home_background_pesquisa_aviso: {{ $custom['home_background_pesquisa_aviso'] }};
            --home_text_color_pesquisa_aviso: {{ $custom['home_text_color_pesquisa_aviso'] }};
            --home_background_button_pesquisa: {{ $custom['home_background_button_pesquisa'] }};
            --home_icon_color_button_pesquisa: {{ $custom['home_icon_color_button_pesquisa'] }};


            --home_background_button_vertodos: {{ $custom['home_background_button_vertodos'] }};
            --home_text_color_button_vertodos: {{ $custom['home_text_color_button_vertodos'] }};
            --home_background_button_jogar: {{ $custom['home_background_button_jogar'] }};
            --home_text_color_button_jogar: {{ $custom['home_text_color_button_jogar'] }};
            --home_icon_color_button_jogar: {{ $custom['home_icon_color_button_jogar'] }};
            --home_hover_jogar: {{ $custom['home_hover_jogar'] }};
            --home_text_color: {{ $custom['home_text_color'] }};
            --home_background: {{ $custom['home_background'] }};

            --home_background_input_pesquisa: {{ $custom['home_background_input_pesquisa'] }};
            --home_icon_color_input_pesquisa: {{ $custom['home_icon_color_input_pesquisa'] }};
            --home_border_color_input_pesquisa: {{ $custom['home_border_color_input_pesquisa'] }};



            /* //////////////////////////////////////////////////////////////// */
            /* /////////// PAGINA LISTGAME   | PAGINA DE LISTAGEM DE JOGOS////////// */
            /* //////////////////////////////////////////////////////////////// */

            --gamelist_background: {{ $custom['gamelist_background'] }};
            --gamelist_input_background: {{ $custom['gamelist_input_background'] }};
            --gamelist_input_text_color: {{ $custom['gamelist_input_text_color'] }};
            --gamelist_input_border_color: {{ $custom['gamelist_input_border_color'] }};
            --gamelist_text_color: {{ $custom['gamelist_text_color'] }};
            --gamelist_button_background: {{ $custom['gamelist_button_background'] }};
            --gamelist_button_text_color: {{ $custom['gamelist_button_text_color'] }};
            --gamelist_button_border_color: {{ $custom['gamelist_button_border_color'] }};


            /* //////////////////////////////////////////////////////////////// */
            /* /////////// PAGINA LOGIN - RESGISTRO E ESQUECI A SENHA   /////// */
            /* //////////////////////////////////////////////////////////////// */
            --register_background: {{ $custom['register_background'] }};
            --register_text_color: {{ $custom['register_text_color'] }};
            --register_links_color: {{ $custom['register_links_color'] }};
            --register_input_background: {{ $custom['register_input_background'] }};
            --register_input_text_color: {{ $custom['register_input_text_color'] }};
            --register_input_border_color: {{ $custom['register_input_border_color'] }};
            --register_button_text_color: {{ $custom['register_button_text_color'] }};
            --register_button_background: {{ $custom['register_button_background'] }};
            --register_button_border_color: {{ $custom['register_button_border_color'] }};
            --geral_icons_color: {{ $custom['geral_icons_color'] }};
            --register_security_color: {{ $custom['register_security_color'] }};
            --register_security_background: {{ $custom['register_security_background'] }};
            --register_security_border-color: {{ $custom['register_security_border_color'] }};


            --login_background: {{ $custom['login_background'] }};
            --login_text_color: {{ $custom['login_text_color'] }};
            --login_links_color: {{ $custom['login_links_color'] }};
            --login_input_background: {{ $custom['login_input_background'] }};
            --login_input_text_color: {{ $custom['login_input_text_color'] }};
            --login_input_border_color: {{ $custom['login_input_border_color'] }};
            --login_button_text_color: {{ $custom['login_button_text_color'] }};
            --login_button_background: {{ $custom['login_button_background'] }};
            --login_button_border_color: {{ $custom['login_button_border_color'] }};

            --esqueci_background: {{ $custom['esqueci_background'] }};
            --esqueci_text_color: {{ $custom['esqueci_text_color'] }};
            --esqueci_input_background: {{ $custom['esqueci_input_background'] }};
            --esqueci_input_text_color: {{ $custom['esqueci_input_text_color'] }};
            --esqueci_input_border_color: {{ $custom['esqueci_input_border_color'] }};
            --esqueci_button_text_color: {{ $custom['esqueci_button_text_color'] }};
            --esqueci_button_background: {{ $custom['esqueci_button_background'] }};
            --esqueci_button_border_color: {{ $custom['esqueci_button_border_color'] }};


            /* //////////////////////////////////////////////////////////////// */
            /* /////////// PAGINA AFFILIATE   | PAGINA DE AFILIADOS////////// */
            /* //////////////////////////////////////////////////////////////// */


            --affiliates_background: {{ $custom['affiliates_background'] }};
            --affiliates_sub_background: {{ $custom['affiliates_sub_background'] }};
            --affiliates_text_color: {{ $custom['affiliates_text_color'] }};
            --affiliates_numero_color: {{ $custom['affiliates_numero_color'] }};
            --affiliates_button_saque_background: {{ $custom['affiliates_button_saque_background'] }};
            --affiliates_button_saque_text: {{ $custom['affiliates_button_saque_text'] }};
            --affiliates_button_copie_background: {{ $custom['affiliates_button_copie_background'] }};
            --affiliates_button_copie_text: {{ $custom['affiliates_button_copie_text'] }};
            --affiliates_icom_color: {{ $custom['affiliates_icom_color'] }};


            /* //////////////////////////////////////////////////////////////// */
            /* /////////// PAGINA CART   | PAGINA DE  DE COMPRAS     ////////// */
            /* //////////////////////////////////////////////////////////////// */

            --carteira_button_background: {{ $custom['carteira_button_background'] }};
            --carteira_button_text_color: {{ $custom['carteira_button_text_color'] }};
            --carteira_button_border_color: {{ $custom['carteira_button_border_color'] }};
            --carteira_icon_color: {{ $custom['carteira_icon_color'] }};
            --carteira_text_color: {{ $custom['carteira_text_color'] }};
            --carteira_background: {{ $custom['carteira_background'] }};

            --carteira_saldo_background: {{ $custom['carteira_saldo_background'] }};
            --carteira_saldo_text_color: {{ $custom['carteira_saldo_text_color'] }};
            --carteira_saldo_border_color: {{ $custom['carteira_saldo_border_color'] }};
            --carteira_saldo_title_color: {{ $custom['carteira_saldo_title_color'] }};
            --carteira_saldo_icon_color: {{ $custom['carteira_saldo_icon_color'] }};
            --carteira_saldo_number_color: {{ $custom['carteira_saldo_number_color'] }};
            --carteira_saldo_button_deposito_background: {{ $custom['carteira_saldo_button_deposito_background'] }};
            --carteira_saldo_button_saque_background: {{ $custom['carteira_saldo_button_saque_background'] }};
            --carteira_saldo_button_deposito_text_color: {{ $custom['carteira_saldo_button_deposito_text_color'] }};
            --carteira_saldo_button_saque_text_color: {{ $custom['carteira_saldo_button_saque_text_color'] }};

            --carteira_deposito_background: {{ $custom['carteira_deposito_background'] }};
            --carteira_deposito_contagem_background: {{ $custom['carteira_deposito_contagem_background'] }};
            --carteira_deposito_contagem_text_color: {{ $custom['carteira_deposito_contagem_text_color'] }};
            --carteira_deposito_contagem_number_color: {{ $custom['carteira_deposito_contagem_number_color'] }};
            --carteira_deposito_contagem_quadrado_background: {{ $custom['carteira_deposito_contagem_quadrado_background'] }};
            --carteira_deposito_input_background: {{ $custom['carteira_deposito_input_background'] }};
            --carteira_deposito_input_text_color: {{ $custom['carteira_deposito_input_text_color'] }};
            --carteira_deposito_input_number_color: {{ $custom['carteira_deposito_input_number_color'] }};
            --carteira_deposito_input_border_color: {{ $custom['carteira_deposito_input_border_color'] }};
            --carteira_deposito_title_color: {{ $custom['carteira_deposito_title_color'] }};
            --carteira_deposito_number_color: {{ $custom['carteira_deposito_number_color'] }};
            --carteira_deposito_number_background: {{ $custom['carteira_deposito_number_background'] }};
            --carteira_deposito_button_background: {{ $custom['carteira_deposito_button_background'] }};
            --carteira_deposito_button_text_color: {{ $custom['carteira_deposito_button_text_color'] }};

            --carteira_saldo_pix_text_color: {{ $custom['carteira_saldo_pix_text_color'] }};
            --carteira_saldo_pix_sub_text_color: {{ $custom['carteira_saldo_pix_sub_text_color'] }};
            --carteira_saldo_pix_button_background: {{ $custom['carteira_saldo_pix_button_background'] }};
            --carteira_saldo_pix_button_text_color: {{ $custom['carteira_saldo_pix_button_text_color'] }};
            --carteira_saldo_pix_input_background: {{ $custom['carteira_saldo_pix_input_background'] }};
            --carteira_saldo_pix_input_text_color: {{ $custom['carteira_saldo_pix_input_text_color'] }};
            --carteira_saldo_pix_border_color: {{ $custom['carteira_saldo_pix_border_color'] }};
            --carteira_saldo_pix_icon_color: {{ $custom['carteira_saldo_pix_icon_color'] }};
            --carteira_saldo_pix_background: {{ $custom['carteira_saldo_pix_background'] }};




            --carteira_saque_background: {{ $custom['carteira_saque_background'] }};
            --carteira_saque_text_color: {{ $custom['carteira_saque_text_color'] }};
            --carteira_saque_number_color: {{ $custom['carteira_saque_number_color'] }};
            --carteira_saque_input_background: {{ $custom['carteira_saque_input_background'] }};
            --carteira_saque_input_text_color: {{ $custom['carteira_saque_input_text_color'] }};
            --carteira_saque_input_border_color: {{ $custom['carteira_saque_input_border_color'] }};
            --carteira_saque_button_text_color: {{ $custom['carteira_saque_button_text_color'] }};
            --carteira_saque_button_background: {{ $custom['carteira_saque_button_background'] }};
            --carteira_saque_icon_color: {{ $custom['carteira_saque_icon_color'] }};

            --carteira_history_background: {{ $custom['carteira_history_background'] }};
            --carteira_history_title_color: {{ $custom['carteira_history_title_color'] }};
            --carteira_history_text_color: {{ $custom['carteira_history_text_color'] }};
            --carteira_history_number_color: {{ $custom['carteira_history_number_color'] }};
            --carteira_history_status_finalizado_color: {{ $custom['carteira_history_status_finalizado_color'] }};
            --carteira_history_status_finalizado_background: {{ $custom['carteira_history_status_finalizado_background'] }};
            --carteira_history_status_pedente_color: {{ $custom['carteira_history_status_pedente_color'] }};
            --carteira_history_status_pedente_background: {{ $custom['carteira_history_status_pedente_background'] }};
            --carteira_history_barra_color: {{ $custom['carteira_history_barra_color'] }};
            --carteira_history_barra_text_color: {{ $custom['carteira_history_barra_text_color'] }};
            --carteira_history_icon_color: {{ $custom['carteira_history_icon_color'] }};
            --carteira_history_barra_background: {{ $custom['carteira_history_barra_background'] }};





            /* //////////////////////////////////////////////////////////////// */
            /* /////////// PAGINA VIP   | PAGINA DE VIP     ////////// */
            /* //////////////////////////////////////////////////////////////// */

            --vips_background: {{ $custom['vips_background'] }};
            --vips_title_color: {{ $custom['vips_title_color'] }};
            --vips_text_color: {{ $custom['vips_text_color'] }};
            --vips_description_color: {{ $custom['vips_description_color'] }};
            --vips_button_background: {{ $custom['vips_button_background'] }};
            --vips_button_text_color: {{ $custom['vips_button_text_color'] }};
            --vips_progress_background: {{ $custom['vips_progress_background'] }};
            --vips_progress_text_color: {{ $custom['vips_progress_text_color'] }};
            --vips_progress_prenchimento_background: {{ $custom['vips_progress_prenchimento_background'] }};
            --vips_icons_text_color: {{ $custom['vips_icons_text_color'] }};
            --vips_icons_background: {{ $custom['vips_icons_background'] }};
            --vips_icons_sub_text_color: {{ $custom['vips_icons_sub_text_color'] }};
            --vips_background_profile_vip: {{ $custom['vips_background_profile_vip'] }};
            --vips_icon_mover_color: {{ $custom['vips_icon_mover_color'] }};
            --vip_atual_background: {{ $custom['vip_atual_background'] }};
            --vip_atual_text_color: {{ $custom['vip_atual_text_color'] }};
            --vip_proximo_background: {{ $custom['vip_proximo_background'] }};
            --vip_proximo_text_color: {{ $custom['vip_proximo_text_color'] }};



            /* //////////////////////////////////////////////////////////////// */
            /* /////////// PAGINA PROMOCOES   | PAGINA DE PROMOCOES     ////////// */
            /* //////////////////////////////////////////////////////////////// */

            --promocoes_background: {{ $custom['promocoes_background'] }};
            --promocoes_title_color: {{ $custom['promocoes_title_color'] }};
            --promocoes_text_color: {{ $custom['promocoes_text_color'] }};
            --promocoes_sub_background: {{ $custom['promocoes_sub_background'] }};
            --promocoes_button_background: {{ $custom['promocoes_button_background'] }};
            --promocoes_button_text_color: {{ $custom['promocoes_button_text_color'] }};
            --promocoes_pupup_background: {{ $custom['promocoes_pupup_background'] }};
            --promocoes_pupup_text_color: {{ $custom['promocoes_pupup_text_color'] }};
            --promocoes_icon_color: {{ $custom['promocoes_icon_color'] }};
 


            /* //////////////////////////////////////////////////////////////// */
            /* /////////// PAGINA MISSOES   | PAGINA DE MISSOES     ////////// */
            /* //////////////////////////////////////////////////////////////// */



            --missoes_background: {{ $custom['missoes_background'] }};
            --missoes_sub_background: {{ $custom['missoes_sub_background'] }};
            --missoes_text_color: {{ $custom['missoes_text_color'] }};
            --missoes_title_color: {{ $custom['missoes_title_color'] }};
            --missoes_bonus_background: {{ $custom['missoes_bonus_background'] }};
            --missoes_bonus_text_color: {{ $custom['missoes_bonus_text_color'] }};
            --missoes_button_background: {{ $custom['missoes_button_background'] }};
            --missoes_button_text_color: {{ $custom['missoes_button_text_color'] }};
            --missoes_barraprogresso_background: {{ $custom['missoes_barraprogresso_background'] }};
            --missoes_barraprogresso_prenchimento_background: {{ $custom['missoes_barraprogresso_prenchimento_background'] }};
            --missoes_barraprogresso_text_color: {{ $custom['missoes_barraprogresso_text_color'] }};



            --background_geral: {{ $custom['background_geral'] }};
            --background_geral_text_color: {{ $custom['background_geral_text_color'] }};
            --carregando_background: {{ $custom['carregando_background'] }};
            --carregando_text_color: {{ $custom['carregando_text_color'] }};


            /* //////////////////////////////////////////////////////////////// */
            /* /////////// PAGINA COOKIES   | PAGINA DE COOKIES     ////////// */
            /* //////////////////////////////////////////////////////////////// */

            --popup_cookies_background: {{ $custom['popup_cookies_background'] }};
            --popup_cookies_text_color: {{ $custom['popup_cookies_text_color'] }};
            --popup_cookies_button_background: {{ $custom['popup_cookies_button_background'] }};
            --popup_cookies_button_text_color: {{ $custom['popup_cookies_button_text_color'] }};
            --popup_cookies_button_border_color: {{ $custom['popup_cookies_button_border_color'] }};


            /* //////////////////////////////////////////////////////////////// */
            /* /////////// PAGINA MENU CELULAR   | MENU CELULAR     ////////// */
            /* //////////////////////////////////////////////////////////////// */

            --menu_cell_background: {{ $custom['menu_cell_background'] }};
            --menu_cell_text_color: {{ $custom['menu_cell_text_color'] }};

            /* //////////////////////////////////////////////////////////////// */
            /* /////////// PAGINA SPORTE E TERMOS | TERMOS E SPORT  ////////// */
            /* //////////////////////////////////////////////////////////////// */

            --aviso_sport_background: {{ $custom['aviso_sport_background'] }};
            --aviso_sport_text_color: {{ $custom['aviso_sport_text_color'] }};
            --titulo_principal_termos: {{ $custom['titulo_principal_termos'] }};
            --titulo_segundario_termos: {{ $custom['titulo_segundario_termos'] }};
            --descriçao_segundario_termos: {{ $custom['descriçao_segundario_termos'] }};

            /*///////////////////////////////////////////////////////////////// */ 


            
            /* //////////////////////////////////////////////////////////////// */
            /* /////////// Modal  MY ACCOUNT | MINHA CONTA  ////////// */
            /* //////////////////////////////////////////////////////////////// */

            --myconta_background: {{ $custom['myconta_background'] }};
            --myconta_text_color: {{ $custom['myconta_text_color'] }};
            --myconta_button_background: {{ $custom['myconta_button_background'] }};
            --myconta_button_icon_color: {{ $custom['myconta_button_icon_color'] }};
            --myconta_sub_background: {{ $custom['myconta_sub_background'] }};


            /*///////////////////////////////////////////////////////////////// */ 


            /* //////////////////////////////////////////////////////////////// */
            /* /////////// Modal  MY ACCOUNT | MINHA CONTA  ////////// */
            /* //////////////////////////////////////////////////////////////// */

            --central_suporte_background: {{ $custom['central_suporte_background'] }};
            --central_suporte_sub_background: {{ $custom['central_suporte_sub_background'] }};
            --central_suporte_button_background_ao_vivo: {{ $custom['central_suporte_button_background_ao_vivo'] }};
            --central_suporte_button_texto_ao_vivo: {{ $custom['central_suporte_button_texto_ao_vivo'] }};
            --central_suporte_button_icon_ao_vivo: {{ $custom['central_suporte_button_icon_ao_vivo'] }};
            --central_suporte_button_background_denuncia: {{ $custom['central_suporte_button_background_denuncia'] }};
            --central_suporte_button_texto_denuncia: {{ $custom['central_suporte_button_texto_denuncia'] }};
            --central_suporte_button_icon_denuncia: {{ $custom['central_suporte_button_icon_denuncia'] }};
            --central_suporte_title_text_color: {{ $custom['central_suporte_title_text_color'] }};
            --central_suporte_icon_title_text_color: {{ $custom['central_suporte_icon_title_text_color'] }};
            --central_suporte_info_text_color: {{ $custom['central_suporte_info_text_color'] }};
            --central_suporte_info_icon_color: {{ $custom['central_suporte_info_icon_color'] }};
            --central_suporte_aviso_title_color: {{ $custom['central_suporte_aviso_title_color'] }};
            --central_suporte_aviso_text_color: {{ $custom['central_suporte_aviso_text_color'] }};
            --central_suporte_aviso_text_negrito_color: {{ $custom['central_suporte_aviso_text_negrito_color'] }};
            --central_suporte_aviso_icon_color: {{ $custom['central_suporte_aviso_icon_color'] }};
            
            /*///////////////////////////////////////////////////////////////// */ 
            --bonus_diario_texto: {{ $custom['bonus_diario_texto'] }};
            --bonus_diario_texto_icon: {{ $custom['bonus_diario_texto_icon'] }};
            --bonus_diario_texto_valor_bonus: {{ $custom['bonus_diario_texto_valor_bonus'] }};
            --bonus_diario_aviso: {{ $custom['bonus_diario_aviso'] }};
            --bonus_diario_botao_backgroud: {{ $custom['bonus_diario_botao_backgroud'] }};
            --bonus_diario_botao_texto_color: {{ $custom['bonus_diario_botao_texto_color'] }};
            --bonus_diario_regras_title: {{ $custom['bonus_diario_regras_title'] }};
            --bonus_diario_regras_titulo: {{ $custom['bonus_diario_regras_titulo'] }};
            --bonus_diario_bola_interna: {{ $custom['bonus_diario_bola_interna'] }};
            --bonus_diario_bola_fora_: {{ $custom['bonus_diario_bola_fora_'] }};
            --bonus_diario_bola_carregamento: {{ $custom['bonus_diario_bola_carregamento'] }};
            --bonus_diario_texto_bola: {{ $custom['bonus_diario_texto_bola'] }};
            --bonus_diario_background: {{ $custom['bonus_diario_background'] }};
            --bonus_diario_sub_background: {{ $custom['bonus_diario_sub_background'] }};
           
            /*///////////////////////////////////////////////////////////////// */ 


            --ci-primary-color: {{ $custom['primary_color'] }};
            --ci-primary-opacity-color: {{ $custom['primary_opacity_color'] }};
            --ci-secundary-color: {{ $custom['secundary_color'] }};
            --ci-gray-dark: {{ $custom['gray_dark_color'] }};
            --ci-gray-light: {{ $custom['gray_light_color'] }};
            --ci-gray-medium: {{ $custom['gray_medium_color'] }};
            --ci-gray-over: {{ $custom['gray_over_color'] }};
            --title-color: {{ $custom['title_color'] }};
            --text-color: {{ $custom['text_color'] }};
            --sub-text-color: {{ $custom['sub_text_color'] }};
            --placeholder-color: {{ $custom['placeholder_color'] }};
            --background-color: {{ $custom['background_color'] }};
            --standard-color: #1C1E22;
            --shadow-color: #111415;
            --page-shadow: linear-gradient(to right, #111415, rgba(17, 20, 21, 0));
            --autofill-color: #f5f6f7;
            --yellow-color: #FFBF39;
            --yellow-dark-color: #d7a026;
            --border-radius: {{ $custom['border_radius'] }};
            --tw-border-spacing-x: 0;
            --tw-border-spacing-y: 0;
            --tw-translate-x: 0;
            --tw-translate-y: 0;
            --tw-rotate: 0;
            --tw-skew-x: 0;
            --tw-skew-y: 0;
            --tw-scale-x: 1;
            --tw-scale-y: 1;
            --tw-scroll-snap-strictness: proximity;
            --tw-ring-offset-width: 0px;
            --tw-ring-offset-color: #fff;
            --tw-ring-color: rgba(59,130,246,.5);
            --tw-ring-offset-shadow: 0 0 #0000;
            --tw-ring-shadow: 0 0 #0000;
            --tw-shadow: 0 0 #0000;
            --tw-shadow-colored: 0 0 #0000;
            --input-primary: {{ $custom['input_primary'] }};
            --input-primary-dark: {{ $custom['input_primary_dark'] }};
            --carousel-banners: {{ $custom['background_geral'] }};
            --carousel-banners-dark: {{ $custom['background_geral'] }};
            --sidebar-color: {{ $custom['background_geral'] }};
            --sidebar-color-dark: {{ $custom['background_geral'] }};
            --navtop-color {{ $custom['navtop_color'] }};
            --navtop-color-dark: {{ $custom['navtop_color_dark'] }};
            --side-menu {{ $custom['side_menu'] }};
            --side-menu-dark: {{ $custom['side_menu_dark'] }};
            --footer-color {{ $custom['footer_color'] }};
            --footer-color-dark: {{ $custom['footer_color_dark'] }};
            --card-color {{ $custom['card_color'] }};
            --card-color-dark: {{ $custom['card_color_dark'] }};
            --card-color: {{ $custom['card_color'] }};
            --footer-color: {{ $custom['footer_color'] }};
            --side-menu-color: {{ $custom['side_menu'] }};
            }
            .navtop-color {
                background-color: {{ $custom['background_geral'] }};
            }
            :is(.dark .navtop-color) {
                background-color: {{ $custom['background_geral'] }};
            }

            .bg-base {
                background-color: {{ $custom['background_geral'] }};
            }
            :is(.dark .bg-base) {
                background-color: {{ $custom['background_geral'] }};
            }
        </style>

        @if(!empty($custom['custom_css']))
            <style>

                {!! $custom['custom_css'] !!}
            </style>
        @endif

        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="base-url" content="{{ config('app.url') }}">
        <link rel="stylesheet" href="{{ asset('build/assets/app-BiLvXd5_.css') }}">
        <script type="module" src="{{ asset('build/assets/app-CRDk2_8R.js') }}"></script>
    </head>
    <body color-theme="dark" class="text-gray-800 bg-base dark:text-gray-300 ">
        <div id="ondagames_oficial"></div>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.0.0/datepicker.min.js"></script>
        <script>
            window.Livewire?.on('copiado', (texto) => {
                navigator.clipboard.writeText(texto).then(() => {
                    Livewire.emit('copiado');
                });
            });

            window._token = '{{ csrf_token() }}';
            //if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            if (localStorage.getItem('color-theme') === 'light') {
                document.documentElement.classList.remove('dark')
                document.documentElement.classList.add('light');
            } else {
                document.documentElement.classList.remove('light')
                document.documentElement.classList.add('dark')
            }
        </script>

        @if(!empty($custom['custom_js']))
            <script>
                {!! $custom['custom_js'] !!}
            </script>
        @endif

    </body>
</html>
