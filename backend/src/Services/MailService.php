<?php

namespace App\Services;

use Mail;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use App\Helper\ResponseHelper;

class MailService
{
    private array $config;

    public function __construct()
    {
        $this->config = require __DIR__ . '/../../config/mail.php';
    }
    // récupére mail de contact 
    public function sendContactMail(string $email, string $title, string $description): bool
    {
        // création objet mail
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = $this->config['host'];
            $mail->SMTPAuth = true;
            $mail->Username = $this->config['username'];
            $mail->Password = $this->config['password'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = (int) $this->config['port'];

            if (empty($this->config['from_email'])) {
                \App\Helper\ResponseHelper::json([
                    'error' => 'MAIL_FROM est vide',
                    'config' => $this->config
                ], 500);
            }

            $mail->setFrom(
                $this->config['from_email'],
                $this->config['from_name']
            );

            $mail->addAddress($this->config['from_email']);
            $mail->addReplyTo($email);

            $safeTitle = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
            $safeDescription = nl2br(htmlspecialchars($description, ENT_QUOTES, 'UTF-8'));
            $safeEmail = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');

            $mail->isHTML(true);
            $mail->Subject = 'Nouveau message de contact : ' . $safeTitle;

            $mail->Body = "
                <p><strong>Email :</strong> {$safeEmail}</p>
                <p><strong>Titre :</strong> {$safeTitle}</p>
                <p><strong>Description :</strong></p>
                <p>{$safeDescription}</p>
            ";
            $mail->AltBody = "Email : {$email}\nTitre : {$title}\nDescription : {$description}";
            return $mail->send();

        } catch (Exception $e) {
          ResponseHelper::json([
              'error' => 'Mail send failed',
              'debug' => $mail->ErrorInfo
          ], 500);

          return false;
        }
    }
    // envoie mail
    private function sendMail(string $to, string $subject, string $htmlBody, string $textBody = ''): bool
    {
        // création objet mail
        $mail = new PHPMailer(true);
        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'base64';

        try {
            $mail->isSMTP();
            $mail->Host = $this->config['host'];
            $mail->SMTPAuth = true;
            $mail->Username = $this->config['username'];
            $mail->Password = $this->config['password'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = (int) $this->config['port'];

            $mail->setFrom($this->config['from_email'], $this->config['from_name']);
            $mail->addAddress($to);

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $htmlBody;
            $mail->AltBody = $textBody ?: strip_tags($htmlBody);

            return $mail->send();
        } catch (Exception $e) {
            error_log('Mail error: ' . $mail->ErrorInfo);
            return false;
        }
    }
    // texte en fonction des mails
    // mail création de compte client
    public function sendWelcomeCustomerMail(string $email, string $firstname): bool
    {
        return $this->sendMail(
            $email,
            'Bienvenue sur Vite et Gourmand',
            "<p>Bonjour {$firstname},</p>
            <p>Votre compte client a bien été créé.</p>
            <p>Nous vous remercions de votre confiance et restons à votre disposition pour toute question.</p>
            <p>Cordialement,</p>
            <p>L'équipe Vite et Gourmand</p>"
        );
    }
    // mail création de compte employé
    public function sendWelcomeEmployeeMail(string $email, string $firstname): bool
    {
        return $this->sendMail(
            $email,
            'Bienvenue chez Vite et Gourmand',
            "<p>Bonjour {$firstname},</p>
            <p>Votre compte employé a bien été créé.</p>
            <p>Merci de vous rapprocher de votre administrateur afin de récupérer vos identifiants de connexion.</p>
            <p>Cordialement,</p>
            <p>L'équipe Vite et Gourmand</p>"
        );
    }
    // mail création de commande
    public function sendOrderCreatedMail(string $email, string $orderNumber): bool
    {
        return $this->sendMail(
            $email,
            'Votre commande a bien été enregistrée',
            "<p>Bonjour,</p>
            <p>Votre commande n° {$orderNumber} a bien été prise en compte.</p>
            <p>Elle sera traitée dans les meilleurs délais par notre équipe.</p>
            <p>Vous pouvez suivre son évolution depuis votre espace « Mon compte ».</p>
            <p>Cordialement,</p>
            <p>L'équipe Vite et Gourmand</p>"
        );
    }
    // mail retour de matériel
    public function sendReturnMaterialMail(string $email, string $orderNumber): bool
    {
        return $this->sendMail(
            $email,
            'Retour du matériel loué',
            "<p>Bonjour,</p>
            <p>Votre commande n° {$orderNumber} a bien été livrée.</p>
            <p>Le matériel loué pour la prestation doit être restitué propre et en bon état dans un délai maximum de 10 jours après la prestation.</p>
            <p>Rappel : en cas de non-restitution dans ce délai, une facturation forfaitaire de <strong>600 €</strong> pourra être appliquée.</p>
            <p>Cordialement,</p>
            <p>L'équipe Vite et Gourmand</p>"
        );
    }
    // mail commande terminée + avis
    public function sendOrderCompletedMail(string $email, string $orderNumber): bool
    {
        return $this->sendMail(
            $email,
            'Votre commande est terminée',
            "<p>Bonjour,</p>
            <p>Votre commande n° {$orderNumber} est désormais terminée.</p>
            <p>Nous vous invitons à vous connecter à votre espace « Mon compte » afin de déposer un avis sur votre expérience.</p>
            <p>Cordialement,</p>
            <p>L'équipe Vite et Gourmand</p>"
        );
    }
    // mail réinitialisation mot de passe
    public function sendResetPasswordMail(string $email, string $firstname, string $token): bool
    {
        $resetLink = $_ENV['FRONT_URL'] . "/editPassword?token=" . urlencode($token);

        return $this->sendMail(
            $email,
            'Réinitialisation de votre mot de passe',
            "<p>Bonjour {$firstname},</p>
            <p>Une demande de réinitialisation de mot de passe a été effectuée pour votre compte.</p>
            <p>Pour définir un nouveau mot de passe, cliquez sur le lien ci-dessous :</p>
            <p>
                <a href='{$resetLink}'>
                    Réinitialiser mon mot de passe
                </a>
            </p>
            <p>Si vous n'êtes pas à l'origine de cette demande, vous pouvez ignorer cet email.</p>
            <p>Cordialement,</p>
            <p>L'équipe Vite et Gourmand</p>"
        );
    }
}