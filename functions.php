<?php
// Traitement du formulaire de contact multi-permanences
add_action('admin_post_nopriv_mk_contact_form', 'mk_handle_contact_form');
add_action('admin_post_mk_contact_form', 'mk_handle_contact_form');

function mk_handle_contact_form() {
    // Antispam honeypot
    if (!empty($_POST['website'])) {
        wp_redirect(home_url('/merci/?status=spam'));
        exit;
    }

    // Sécurité & nettoyage
    $name = sanitize_text_field($_POST['name'] ?? '');
    $email = sanitize_email($_POST['email'] ?? '');
    $message = sanitize_textarea_field($_POST['message'] ?? '');
    $permanence = sanitize_text_field($_POST['permanence'] ?? 'autre');

    if (empty($name) || empty($email) || empty($message)) {
        wp_redirect(home_url('/contact/?status=error'));
        exit;
    }

    // Routage selon la permanence choisie
    $routes = [
        'centre' => 'centre@missionlocale.fr',
        'est'    => 'est@missionlocale.fr',
        'autre'  => 'contact@missionlocale.fr'
    ];

    $to = $routes[$permanence] ?? $routes['autre'];

    $subject = 'Nouveau message du formulaire de contact';
    $headers = [
        'Content-Type: text/html; charset=UTF-8',
        'Reply-To: ' . $email
    ];

    $body = "<strong>Nom :</strong> {$name}<br>
             <strong>Email :</strong> {$email}<br>
             <strong>Permanence :</strong> {$permanence}<br><br>
             <strong>Message :</strong><br>" . nl2br($message);

    // Envoi de l'email
    wp_mail($to, $subject, $body, $headers);

    // Redirection avec statut
    wp_redirect(home_url('/merci/?status=success'));
    exit;
}
