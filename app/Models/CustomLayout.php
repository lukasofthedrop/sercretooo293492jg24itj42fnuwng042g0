<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class CustomLayout extends Model
{
    use HasFactory;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'custom_layouts';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [



        'link_app',
        'link_telegram',
        'link_facebook',
        'link_whatsapp',
        'link_instagram',
        'link_siderbar_1',
        'link_siderbar_2',
        'link_siderbar_3',
        'link_lincenca',

        'navbar_imglogin',
        'menucell_inicio',
        'menucell_carteira',
        'menucell_suporte',
        'menucell_afiliado',
        'menucell_saque',
        'menucell_sair',
        'footer_imagen1',
        'footer_imagen2',
        'footer_imagen3',
        'footer_telegram',
        'footer_facebook',
        'footer_whatsapp',
        'footer_instagram',
        'footer_mais18',
        'navebar_app',
  
        
        
        'font_family_default',
        'primary_color',
        'primary_opacity_color',
        'secundary_color',
        'gray_dark_color',
        'gray_light_color',
        'gray_medium_color',
        'gray_over_color',
        'title_color',
        'text_color',
        'sub_text_color',
        'placeholder_color',
        'background_color',

        'background_geral',
        'background_geral_dark',

        'input_primary',
        'input_primary_dark',

        'custom_modal_pop',
        'carousel_banners_dark',

        'sidebar_color',
        'sidebar_color_dark',

        'navtop_color',
        'navtop_color_dark',

        'side_menu',
        'side_menu_dark',

        'footer_color',
        'footer_color_dark',

        'card_color',
        'card_color_dark',

        'border_radius',
        'custom_css',
        'custom_js',
        'custom_modal_pop',

        'image_Jackpot',
        'image_navbar',
        'image_hot1',
        'image_hot2',
        'image_hot3',
        'image_hot4',
        'image_hot5',
        'banner_deposito1',
        'banner_deposito2',
        'banner_registro',
        'banner_login',
        'popup_fluatuante',
        'link_fluatuante',
        'link_suporte',
        'popup2_fluatuante',
        'link_fluatuante2',
        'idPixelFC',
        'idPixelGoogle',
        'token_jivochat',





    /////////////////////////////////////////////////////////////////////
    ///////////////////////// CENTRAL DE DISIGN /////////////////////////
    /////////////////////////////////////////////////////////////////////

    /////////////////////////////////////////////////////////////////////
    // PAGINA FOOTER   | FICA EM BAIXO DO SITE
    'footer_background',
    'footer_text_color',
    'footer_links',
    'footer_button_background',
    'footer_button_text',
    'footer_button_border',
    'footer_icons',
    'footer_titulos',
    'footer_button_hover_language',
    'footer_button_color_language',
    'footer_button_background_language',
    'footer_button_border_language',
    'footer_background_language',





    /////////////////////////////////////////////////////////////////////

    // PAGINA SIDEBAR   | FICA NA LATERAL DO SITE
    'sidebar_background',
    'sidebar_button_missoes_background',
    'sidebar_button_vip_background',
    'sidebar_button_ganhe_background',
    'sidebar_button_bonus_background',
    'sidebar_button_missoes_text',
    'sidebar_button_ganhe_text',
    'sidebar_button_vip_text',
    'sidebar_button_hover',
    'sidebar_text_hover',
    'sidebar_text_color',
    'sidebar_border',
    'sidebar_icons',
    'sidebar_icons_background',


    /////////////////////////////////////////////////////////////////////

    // PAGINA NAVBAR  | FICA NA LATERAL DO SITE


    'navbar_background',
    'navbar_text',
    'navbar_icon_menu',
    'navbar_icon_promocoes',
    'navbar_icon_casino',
    'navbar_icon_sport',
    'navbar_button_text_login',
    'navbar_button_text_registro',
    'navbar_button_background_login',
    'navbar_button_background_registro',
    'navbar_button_border_color',
    'navbar_button_text_superior',
    'navbar_button_background_superior',
    'navbar_text_superior',
    'navbar_perfil_icon_color_border',
    'navbar_perfil_modal_icon_color',
    'navbar_perfil_modal_text_modal',
    'navbar_perfil_modal_background_modal',

    'navbar_button_deposito_background',
    'navbar_button_deposito_text_color',
    'navbar_button_deposito_border_color',
    'navbar_button_deposito_píx_color_text',
    'navbar_button_deposito_píx_background',
    'navbar_button_deposito_píx_icon',
    'navbar_button_carteira_background',
    'navbar_button_carteira_text_color',
    'navbar_button_carteira_border_color',
    'navbar_perfil_text_color',
    'navbar_perfil_background',
    'navbar_perfil_icon_color',
    'navbar_icon_promocoes_segunda_cor',
    'navbar_perfil_modal_hover_modal',
     
    /////////////////////////////////////////////////////////////////////
    // PAGINA HOME  | PAGINA PRINCIPAL DO SITE

    'home_background_button_banner',
    'home_icon_color_button_banner',

    'home_background_categorias',
    'home_text_color_categorias',

    'home_background_pesquisa',
    'home_text_color_pesquisa',

    'home_background_input_pesquisa',
    'home_icon_color_input_pesquisa',
    'home_border_color_input_pesquisa',
    
    'home_background_pesquisa_aviso',
    'home_text_color_pesquisa_aviso',
    'home_background_button_pesquisa',
    'home_icon_color_button_pesquisa',

    'home_background_button_vertodos',
    'home_text_color_button_vertodos',

    'home_background_button_jogar',
    'home_text_color_button_jogar',
    'home_icon_color_button_jogar',
    'home_hover_jogar',
    'home_background',

    'home_text_color',
    'topo_icon_color',
    'topo_button_text_color',
    'topo_button_background',
    'topo_button_border_color',


    /////////////////////////////////////////////////////////////////////
    // PAGINA listgames  | PAGINA DE JOGOS


    'gamelist_background',
    'gamelist_input_background',
    'gamelist_input_text_color',
    'gamelist_input_border_color',
    'gamelist_text_color',
    'gamelist_button_background',
    'gamelist_button_text_color',
    'gamelist_button_border_color',




    /////////////////////////////////////////////////////////////////////
    // PAGINA LOGIN - RESGISTRO E ESQUECI A SENHA 

    'register_background',
    'register_text_color',
    'register_links_color',
    'register_input_background',
    'register_input_text_color',
    'register_input_border_color',
    'register_button_text_color',
    'register_button_background',
    'register_button_border_color',
    'geral_icons_color',
    'register_security_color',
    'register_security_background',
    'register_security_border_color',

    'login_background',
    'login_text_color',
    'login_links_color',
    'login_input_background',
    'login_input_text_color',
    'login_input_border_color',
    'login_button_text_color',
    'login_button_background',
    'login_button_border_color',

    'esqueci_background',
    'esqueci_text_color',
    'esqueci_input_background',
    'esqueci_input_text_color',
    'esqueci_input_border_color',
    'esqueci_button_text_color',
    'esqueci_button_background',
    'esqueci_button_border_color',

    /////////////////////////////////////////////////////////////////////
    // PAGINA AFILIADOS
    'affiliates_background',
    'affiliates_sub_background',
    'affiliates_text_color',
    'affiliates_numero_color',
    'affiliates_button_saque_background',
    'affiliates_button_saque_text',
    'affiliates_button_copie_background',
    'affiliates_button_copie_text',
    'affiliates_icom_color',


    /////////////////////////////////////////////////////////////////////
    // PAGINA CARTEIRA

    'carteira_button_background',
    'carteira_button_text_color',
    'carteira_button_border_color',
    'carteira_icon_color',
    'carteira_text_color',

    'carteira_saldo_background',
    'carteira_saldo_text_color',
    'carteira_saldo_border_color',
    'carteira_saldo_title_color',
    'carteira_saldo_icon_color',
    'carteira_saldo_number_color',
    'carteira_saldo_button_deposito_background',
    'carteira_saldo_button_saque_background',
    'carteira_saldo_button_deposito_text_color',
    'carteira_saldo_button_saque_text_color',

    'carteira_deposito_background',
    'carteira_deposito_contagem_background',
    'carteira_deposito_contagem_text_color',
    'carteira_deposito_contagem_number_color',
    'carteira_deposito_contagem_quadrado_background',
    'carteira_deposito_input_background',
    'carteira_deposito_input_text_color',
    'carteira_deposito_input_number_color',
    'carteira_deposito_input_border_color',
    'carteira_deposito_title_color',
    'carteira_deposito_number_color',
    'carteira_deposito_number_background',
    'carteira_deposito_button_background',
    'carteira_deposito_button_text_color',

    'carteira_saque_background',
    'carteira_saque_text_color',
    'carteira_saque_number_color',
    'carteira_saque_input_background',
    'carteira_saque_input_text_color',
    'carteira_saque_input_border_color',
    'carteira_saque_button_text_color',
    'carteira_saque_button_background',
    'carteira_saque_icon_color',

    'carteira_history_background',
    'carteira_history_title_color',
    'carteira_history_text_color',
    'carteira_history_number_color',
    'carteira_history_status_finalizado_color',
    'carteira_history_status_finalizado_background',
    'carteira_history_status_pedente_color',
    'carteira_history_status_pedente_background',
    'carteira_history_barra_color',
    'carteira_history_barra_text_color',
    'carteira_history_barra_background',
    'carteira_history_icon_color',
    'carteira_background',



    'carteira_saldo_pix_Texot_color',
    'carteira_saldo_pix_sub_text_color',
    'carteira_saldo_pix_button_background',
    'carteira_saldo_pix_button_text_color',
    'carteira_saldo_pix_input_background',
    'carteira_saldo_pix_input_text_color',
    'carteira_saldo_pix_border_color',
    'carteira_saldo_pix_icon_color',
    'carteira_saldo_pix_background',



    // PAGINA PROMOCOES
    'promocoes_background',
    'promocoes_title_color',
    'promocoes_text_color',
    'promocoes_sub_background',
    'promocoes_button_background',
    'promocoes_button_text_color',
    'promocoes_pupup_background',
    'promocoes_pupup_text_color',
    'promocoes_icon_color',

    // PAGINA VIPS
    'vips_background', 
    'vips_title_color',
    'vips_text_color',
    'vips_description_color',
    'vips_button_background',
    'vips_button_text_color',
    'vips_progress_background',
    'vips_progress_text_color',
    'vips_progress_prenchimento_background',
    'vips_icons_text_color',
    'vips_icons_background',
    'vips_icons_sub_text_color',
    'vips_background_profile_vip',
    'vips_icon_mover_color',
    'vip_atual_background',
    'vip_atual_text_color',
    'vip_proximo_background',
    'vip_proximo_text_color',

    // PAGINA MISSOES
    'missoes_background',
    'missoes_sub_background',
    'missoes_text_color',
    'missoes_title_color',
    'missoes_bonus_background',
    'missoes_bonus_text_color',
    'missoes_button_background',
    'missoes_button_text_color',
    'missoes_barraprogresso_background',
    'missoes_barraprogresso_prenchimento_background',
    'missoes_barraprogresso_text_color',



    'menu_cell_background',
    'menu_cell_text_color',


    // PAGINA DE CARREGAMENTO
    'background_geral',
    'background_geral_text_color',
    'carregando_background',
    'carregando_text_color',

   //POPUP COKIES
    'popup_cookies_background',
    'popup_cookies_text_color',
    'popup_cookies_button_background',
    'popup_cookies_button_text_color',
    'popup_cookies_button_border_color',


    /////////////////////////////////////////////////////////////////////
    // TEXTOS DA PLATFORMA
    'homepage_jogos_em_destaque',

    'vip_titulo',
    'vip_descriçao',
    'vip_sub_texto',
    'vip_sub_titulo',

    /////////////////////////////////////////////////////////////////////
    // SPORTE E TERMOS DE USO
    'aviso_sport_background',
    'aviso_sport_text_color',
    'titulo_principal_termos',
    'titulo_segundario_termos',
    'descriçao_segundario_termos',




    ////////////////////////////////////////////////////////////////////
    'central_suporte_background',
    'central_suporte_sub_background',
    'central_suporte_button_background_ao_vivo',
    'central_suporte_button_texto_ao_vivo',
    'central_suporte_button_icon_ao_vivo',
    'central_suporte_button_background_denuncia',
    'central_suporte_button_texto_denuncia',
    'central_suporte_button_icon_denuncia',
    'central_suporte_title_text_color',
    'central_suporte_icon_title_text_color',
    'central_suporte_info_text_color',
    'central_suporte_info_icon_color',
    'central_suporte_aviso_title_color',
    'central_suporte_aviso_text_color',
    'central_suporte_aviso_text_negrito_color',
    'central_suporte_aviso_icon_color',



    /////////////////////////////////////////////////////////////////////
    // MODAL MYCONTA
    'myconta_background',
    'myconta_text_color',
    'myconta_button_background',
    'myconta_button_icon_color',
    'myconta_sub_background',

    /////////////////////////////////////////////////////////////////////
    // BONUS DIARIO
    'bonus_diario_texto',
    'bonus_diario_texto_icon',
    'bonus_diario_texto_valor_bonus',
    'bonus_diario_aviso',
    'bonus_diario_botao_backgroud',
    'bonus_diario_botao_texto_color',
    'bonus_diario_regras_title',
    'bonus_diario_regras_titulo',
    'bonus_diario_bola_interna',
    'bonus_diario_bola_fora_',
    'bonus_diario_bola_carregamento',
    'bonus_diario_texto_bola',
    'bonus_diario_background',
    'bonus_diario_sub_background',

    ];

      /**
     * Obter os dados do CustomLayout com cache.
     *
     * @return mixed
     */
    public static function getCachedCustomLayout()
    {
        // Defina o tempo de expiração do cache (30 minutos)
        $cacheTime = 30 * 60; // 30 minutos em segundos

        return Cache::remember('custom_layout', $cacheTime, function () {
            // Busca todos os dados da tabela custom_layouts
            return self::first();
        });
    }

}
