<?php

// Inclure la classe DisclaimerOptions
define('MY_PLUGIN_PATH', plugin_dir_path(__FILE__));
include(MY_PLUGIN_PATH . '../Entity/DisclaimerOptions.php');

// Définir une classe pour gérer les tables de disclaimer
class DisclaimerGestionTable {
    public function creerTable(){
        // instanciation de la classe DisclaimerOptions
        $message = new DisclaimerOptions(1, "", "");

        // on alimente l'objet message avec les valeurs par défaut au setter (mutateur)
        $message->setMessageDisclaimer("Au regard de la loi européenne, vous devez nous confirmer que vous avez plus de 18 ans pour visiter ce site");
        $message->setRedirectionko("https://www.google.com/");
        global $wpdb;
        // création de la table
        $tableDisclaimer = $wpdb->prefix.'disclaimer_options';
        if ($wpdb->get_var("SHOW TABLES LIKE '$tableDisclaimer'") != $tableDisclaimer){
            $sql = "CREATE TABLE $tableDisclaimer (
                id_disclaimer INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                message_disclaimer TEXT NOT NULL,
                redirection_ko TEXT NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
            // Message d'erreur
            if(!$wpdb->query($sql)){
                die("Une erreur est survenue, contactez le développeur du plugin...");
            } 
            // Insertion du message par défaut
            $wpdb->insert(
                $tableDisclaimer,
                array(
                    'message_disclaimer' => $message->getMessageDisclaimer(),
                    'redirection_ko' => $message->getRedirectionko(),
                ),
                array('%s', '%s' )
            );
        }
    }

    public function supprimerTable(){
        // $wpdb sert à récupérer l'objet contenant les informations relatives à la base de données.
        global $wpdb;
        $table_disclaimer = $wpdb->prefix."disclaimer_options";
        $sql = "DROP TABLE $table_disclaimer";
        $wpdb->query($sql);
    }

    
    function insererDansTable($contenu, $url) {
        global $wpdb;
        $table_disclaimer= $wpdb->prefix.'disclaimer_options';
        $sql=$wpdb->prepare(
            "UPDATE $table_disclaimer
            SET message_disclaimer = '%s', redirection_ko = '%s' WHERE
            id_disclaimer = %s",$contenu,$url,1
        );
        $wpdb->query($sql);
    }

    function AfficherDonneModal() {
        global $wpdb;
        $query = "SELECT * FROM wp_disclaimer_options";
        $row = $wpdb->get_row($query);
        $message_disclaimer = $row->message_disclaimer;
        $lien_redirection = $row->redirection_ko;
        return '<div id="monModal" class="modal">
        <p>Le Vapobar, vous souhaite la bienvenue !</p>
        <p>'. $message_disclaimer.'</p>
        <a href="'.$lien_redirection.'" type="button" class="btn-red">Non, j\'ai moins de 18 ans</a>
        <a href="" type="button" rel="modal:close" class="btn-green" id="actionDisclaimer">Oui, j\'ai plus de 18 ans</a>
        </div>';
    }
}

?>
