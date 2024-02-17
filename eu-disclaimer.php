<?php
/**
 * Plugin Name: eu-disclaimer
 * Plugin URI: http://URL_de_l_extension
 * Description: Plugin sur la législation des produits à base de nicotine.
 * Version: 1.5
 * Author: Elodie BOYER
 * Author URI: http://www.afpa.fr
 * License: (Lien de la licence)
 */

// Inclusion de la classe pour la gestion des tables
require_once('Model/Repository/DisclaimerGestionTable.php');

// Vérification de l'existence de la classe avant d'exécuter les hooks
if (class_exists("DisclaimerGestionTable")) {
    function activate_eu_disclaimer() {
        $gerer_table = new DisclaimerGestionTable();
        $gerer_table->creerTable();
    }
    
    function deactivate_eu_disclaimer() {
        $gerer_table = new DisclaimerGestionTable();
        $gerer_table->supprimerTable();
    }

    // Hooks d'activation et de désactivation
    register_activation_hook(__FILE__, 'activate_eu_disclaimer');
    register_deactivation_hook(__FILE__, 'deactivate_eu_disclaimer');
}

// Fonction pour ajouter un élément au menu d'administration
function ajouterAuMenu() {
    $page = 'eu-disclaimer';
    $menu = 'eu-disclaimer';
    $capability = 'edit_pages';
    $slug = 'eu-disclaimer';
    $function = 'disclaimerFonction';
    $icon = '';
    $position = 80;
    if (is_admin()) {
        add_menu_page($page, $menu, $capability, $slug, $function, $icon, $position);
    }
}

//Afficher la page de configuration du plugin eu-disclaimer
add_action("admin_menu", "ajouterAuMenu");

function disclaimerFonction() {
    require_once('views/disclaimer-menu.php');
}

//Ajout du js à l'activation du plugin
add_action('init', 'inserer_js_dans_footer');

function inserer_js_dans_footer() {
    if(!is_admin()):
        wp_register_script( 'jQuery_modal',
        'https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.2/jquery.modal.min.js',
        array('jquery'), null, true);
        wp_enqueue_script('jQuery_modal');
        wp_register_script ('jQuery_eu', plugins_url ('assets/js/eu-disclaimer.js', __FILE__), array ('jquery'), '1.1', true);
        wp_enqueue_script('jQuery_eu');
    endif;
}

//Ajout du css à l'activation du plugin
add_action('wp_head', 'ajouter_css', 1);

function ajouter_css() {
    if(!is_admin()):
        wp_register_style('eu-disclaimer-css', plugins_url ('assets/css/eu-disclaimer-css.css', __FILE__), null, null, false);
        wp_enqueue_style('eu-disclaimer-css');
        wp_register_style( 'modal',
        'https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.2/jquery.modal.min.css',
        null, null, false );
        wp_enqueue_style( 'modal' );
    endif;
}


/**
 * Utilisation : add_action('nom du hook', 'nom de la fonction');
 * @author Elodie Boyer
 */
add_action('wp_footer', 'afficherModalDansFooter');

function afficherModalDansFooter() {
    $disclaimerTable = new DisclaimerGestionTable();
    echo $disclaimerTable->AfficherDonneModal();
}
