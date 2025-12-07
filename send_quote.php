<?php
// send_quote.php

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo 'Method Not Allowed';
    exit;
}

// Проста функція захисту від інʼєкцій у заголовки
function clean_field($value) {
    $value = $value ?? '';
    $value = trim($value);
    // Забираємо переводи рядків, щоб не підкидати додаткові заголовки
    $value = str_replace(["\r", "\n"], ' ', $value);
    return $value;
}

$name    = clean_field($_POST['name'] ?? '');
$phone   = clean_field($_POST['phone'] ?? '');
$service = clean_field($_POST['service'] ?? '');
$message = trim($_POST['message'] ?? '');

if ($name === '' || $phone === '' || $service === '') {
    http_response_code(400);
    echo 'Будь ласка, заповніть обовʼязкові поля (імʼя, телефон, тип запиту).';
    exit;
}

$to      = 'stepan2000kniaz@gmail.com';
$subject = 'Нова заявка з сайту EcoMove Trans';

$serviceTitles = [
    'waste'  => 'Вивіз будівельного сміття',
    'move'   => 'Переїзд / перевезення речей',
    'cargo'  => 'Вантажне перевезення',
    'other'  => 'Інше'
];

$serviceReadable = $serviceTitles[$service] ?? $service;

$body = "Імʼя: {$name}\n"
      . "Телефон / WhatsApp: {$phone}\n"
      . "Тип запиту: {$serviceReadable}\n\n"
      . "Повідомлення:\n{$message}\n";

$headers  = "From: EcoMove Trans <no-reply@ecomove-trans.fr>\r\n";
$headers .= "Reply-To: no-reply@ecomove-trans.fr\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

if (mail($to, $subject, $body, $headers)) {
    // Можеш зробити окрему сторінку подяки, наприклад thank-you.html
    header('Location: thank-you.html');
    exit;
} else {
    http_response_code(500);
    echo 'Сталася помилка при надсиланні заявки. Спробуйте, будь ласка, пізніше.';
    exit;
}