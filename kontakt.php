<?php
// Kontaktformular-Versand über den eigenen easyname-Webspace (keine Drittanbieter)
const EMPFAENGER = 'nachhilfe@sebastian-pillinger.at';
const ABSENDER   = 'nachhilfe@sebastian-pillinger.at'; // muss eine Adresse deiner Domain sein

function zurueck(string $param): void {
    header('Location: index.html?' . $param . '=1#kontaktformular');
    exit;
}
function einzeilig(string $v, int $max): string {
    $v = preg_replace('/[\r\n\t]+/', ' ', trim($v));
    return mb_substr($v, 0, $max);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') zurueck('fehler');

// Spam-Falle: Bots füllen das versteckte Feld aus
if (!empty($_POST['website'])) zurueck('gesendet');

$name      = einzeilig($_POST['name'] ?? '', 100);
$email     = einzeilig($_POST['email'] ?? '', 150);
$fach      = einzeilig($_POST['fach'] ?? '', 150);
$nachricht = mb_substr(trim((string)($_POST['nachricht'] ?? '')), 0, 3000);

if ($name === '' || $nachricht === '' || empty($_POST['datenschutz'])
    || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    zurueck('fehler');
}

$betreff = mb_encode_mimeheader('Nachhilfe-Anfrage von ' . $name, 'UTF-8');
$text = "Name: $name\nE-Mail: $email\nKlasse/Fach: $fach\n\nNachricht:\n$nachricht\n";
$header = [
    'From'         => ABSENDER,
    'Reply-To'     => $email,
    'MIME-Version' => '1.0',
    'Content-Type' => 'text/plain; charset=UTF-8',
];

zurueck(mail(EMPFAENGER, $betreff, $text, $header) ? 'gesendet' : 'fehler');
