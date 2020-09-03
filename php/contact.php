<?php
header('Content-type: application/json');

$error=[];
//trim ścina spacje
$mail=trim($_POST['mail']);
$subject=trim($_POST['subject']);
$message=trim($_POST['message']);

$subject=filter_var($subject, FILTER_SANITIZE_STRING);
$message=filter_var($message, FILTER_SANITIZE_STRING);

if(!filter_var($mail, FILTER_VALIDATE_EMAIL)){
    $error['mail'] = 'Adres e-mail jest nieprawidłowy.';
}

if(strlen($subject)<3){
    $error['subject'] = 'Temat wiadomości jest za krótki (min 3 znaki).';
}

if(strlen($message)<3){
    $error['content'] = 'Treść wiadomości jest za krótka (min 3 znaki).';
}

if(!$_POST['captchaG']){
    $error['captcha'] = 'Zaznacz Nie jestem robotem.';    
}

//to jest weryfikacja captcha - z jej dokumentacji
if(count($error) === 0){
    $captcha = $_POST['captchaG'];
    $secret = '6LfuM-cUAAAAAJNrU6on_8Dd8ApVidixDfm3oFIj';
    $response = json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$secret."&response=".$captcha."&remoteip=".$_SERVER['REMOTE_ADDR']), true);
    if($response['success'] != false){
        //$capt = 'OK';
    } else {
        $error['captcha'] = 'Błąd captcha. Nie można użyć ponownie tej samej weryfikacji.<br><a href="http://localhost">Kliknij tutaj</a> aby odświeżyć stronę i zredagować wiadomość ponownie.';   
    }
        //var_dump($response);
}

if(count($error) === 0){
    
    //do kilku mejli $to = 'piotr@trener-it.pl;kony100@interia.pl';
    $to = 'psychoterapia.ahadula@gmail.com';
    
    $htmlCodeStart='
            <!DOCTYPE html>
            <html lang="pl">
            <head>
            <meta charset="UTF-8">
            </head>
            <body>
            ';

    $htmlCodeEnd='</body></html>';
    
    //rozwiazanie problemu z nieodpowiednim kodowaniem znakow PL w temacie maila - bez tego beda sie pojawiac krzaki
    $subject = "=?UTF-8?B?".base64_encode($subject)."?=";
    
    $mailMessage = $htmlCodeStart.$message.$htmlCodeEnd;
    
    //hedery opcjonalne - można wyrzucić?
    $headers = "MIME-Version: 1.0\n";
    $headers .= "Content-type: text/html; charset=UTF-8\n";
    $headers .= "From: $mail\n";
    
    $s_mail=mail($to, $subject, $mailMessage, $headers);
    
    if($s_mail){
        echo json_encode(['success'=>'Dziękujemy, Twoja wiadomość została wysłana.']);
    }

}else{
    echo json_encode($error);
}