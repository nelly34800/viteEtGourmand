<?php

namespace App\Controller;

use App\Services\MailService;
use App\Helper\RequestHelper;
use App\Helper\ResponseHelper;

class ContactController
{
    public function send(): void
    {
        $data = RequestHelper::getJson();
        if (empty($data['email']) ||empty($data['message'])) {
            ResponseHelper::json(['error' => 'Missing fields'], 400);
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            ResponseHelper::json(['error' => 'Invalid email'], 400);
        }

        $mailService = new MailService();

        $success = $mailService->sendContactMail(
            $data['email'],
            $data['message']
        );

        if (!$success) {
            ResponseHelper::json(['error' => 'Mail send failed'], 500);
        }

        ResponseHelper::json(['message' => 'Mail sent successfully'], 200);
    }
}