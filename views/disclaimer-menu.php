<?php
// Vérifie si le formulaire est soumis avec des valeurs non vides
if (!empty($_POST['message_disclaimer']) && !empty($_POST['redirection_ko'])) {
    // Crée une instance de DisclaimerOptions et définit les valeurs du formulaire
    $text = new DisclaimerOptions();
    $text->setMessageDisclaimer(htmlspecialchars($_POST['message_disclaimer'])); // Utilisation de htmlspecialchars() pour éviter les attaques XSS
    $text->setRedirectionKo(htmlspecialchars($_POST['redirection_ko']));
    // Insère les données dans la table de gestion des disclaimers
    $message = DisclaimerGestionTable::insererDansTable($text);
}

// Fonction pour récupérer les valeurs des placeholders depuis la base de données
function getPlaceholderValues() {
    global $wpdb;
    $table_disclaimer = $wpdb->prefix . 'disclaimer_options';
    $sql = "SELECT * FROM $table_disclaimer WHERE id_disclaimer = 1";
    $disclaimer_data = $wpdb->get_row($sql);
    return $disclaimer_data;
}

// Récupère les valeurs des placeholders
$placeholders = getPlaceholderValues();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EU-DISCLAIMER</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<div class="container"><br>
    <h2><strong>EU-DISCLAIMER</strong></h2><br>

    <h3>Configuration</h3><br>

    <div id="messageContainer"></div>
    
    <!-- Formulaire de configuration -->
    <form method="post" action="" novalidate="novalidate" id="disclaimerForm">
        <div class="form-group row">
            <label for="message_disclaimer" class="col-sm-3 col-form-label">Message du disclaimer :</label>
            <div class="col-sm-10">
                <input name="message_disclaimer" type="text" id="message_disclaimer"
                       value="<?php echo $placeholders->message_disclaimer; ?>" class="form-control" required />
            </div>
        </div>
        <div class="form-group row">
            <label for="url_redirection" class="col-sm-3 col-form-label">URL de redirection :</label>
            <div class="col-sm-10">
                <input name="url_redirection" type="text" id="url_redirection"
                       value="<?php echo $placeholders->redirection_ko; ?>" class="form-control" required />
            </div>
        </div><br>
        <div class="form-group row">
            <div class="col-sm-12">
                <button type="submit" name="submit" id="submit" class="btn btn-primary" disabled>Enregistrer les modifications</button>
            </div>
        </div>
    </form>

    <!-- Exemple de disclaimer -->
    <p>Exemple: La législation nous impose de vous informer sur la nocivité des produits
        à base de nicotine, vous devez avoir plus de 18 ans pour consulter ce site !</p><br>

    <div>
        <h5>Centre AFPAR / session DWWM</h5>
        <img src="<?php echo plugin_dir_url(dirname(__FILE__)) . 'assets/img/layout_set_logo.png'; ?>" width="15%">
    </div>
</div>

<!-- JavaScript -->
<script>
    // Variables contenant les valeurs initiales des placeholders
    var originalMessage = "<?php echo $placeholders->message_disclaimer; ?>";
    var originalUrl = "<?php echo $placeholders->redirection_ko; ?>";

    // Fonction pour vérifier les changements dans le formulaire et activer/désactiver le bouton de soumission en conséquence
    function checkChanges() {
        var messageInput = document.getElementById("message_disclaimer").value.trim();
        var urlInput = document.getElementById("url_redirection").value.trim();
        var submitButton = document.getElementById("submit");

        // Désactive le bouton si aucun changement n'a été apporté ou si les champs sont vides
        if ((messageInput === originalMessage && urlInput === originalUrl) || messageInput === '' || urlInput === '') {
            submitButton.disabled = true;
        } else {
            submitButton.disabled = false;
        }
    }

    // Écouteurs d'événements pour les champs de saisie du formulaire
    document.getElementById("message_disclaimer").addEventListener("input", function() {
        checkChanges();
    });
    document.getElementById("url_redirection").addEventListener("input", function() {
        checkChanges();
    });

    // Vérifie les changements lors du chargement de la page
    checkChanges();

    // Soumission du formulaire en utilisant AJAX
    document.getElementById("disclaimerForm").addEventListener("submit", function(event) {
        event.preventDefault(); // Empêche l'envoi du formulaire par défaut

        // Envoie les données du formulaire en utilisant AJAX
        var formData = new FormData(this);
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "", true);
        xhr.onload = function() {
            if (xhr.status === 200) {
                // Affiche un message de succès et recharge la page pour mettre à jour les valeurs
                showMessage("success", "Les modifications ont été enregistrées avec succès !");
                location.reload();
            } else {
                // Affiche un message d'erreur
                showMessage("error", "Une erreur est survenue. Veuillez réessayer.");
            }
        };
        xhr.send(formData);
    });

    // Fonction pour afficher un message dans la div messageContainer
    function showMessage(type, message) {
        var messageContainer = document.getElementById("messageContainer");
        messageContainer.innerHTML = '<div class="alert alert-' + type + '" role="alert">' + message + '</div>';
    }
</script>

<!-- Bootstrap JS -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>

<?php
// Vérification si le formulaire a été soumis
if(!empty($_POST['message_disclaimer']) && !empty($_POST['url_redirection'])) {
    // Met à jour les valeurs dans la base de données
    global $wpdb;
    $table_disclaimer = $wpdb->prefix . 'disclaimer_options';
    $wpdb->update(
        $table_disclaimer,
        array(
            'message_disclaimer' => $_POST['message_disclaimer'],
            'redirection_ko' => $_POST['url_redirection']
        ),
        array('id_disclaimer' => 1)
    );

    // Retourne les nouvelles valeurs au format JSON
    $new_values = array(
        'message_disclaimer' => $_POST['message_disclaimer'],
        'redirection_ko' => $_POST['url_redirection']
    );
    echo json_encode($new_values);
    exit; // Arrête l'exécution du script après avoir envoyé les nouvelles valeurs
}
?>
