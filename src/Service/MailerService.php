<?php

namespace App\Service;

use App\Entity\User;
use GuzzleHttp\Client;
use SparkPost\SparkPost;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;

class MailerService
{
    /**
     * @var string string
     */
    private $apiKey;

    /**
     * MailerService constructor.
     * @param $apiKey
     */
    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * Send contact form
     * @param User $user
     * @param mixed $html
     * @param mixed $subject
     * @throws \Exception
     */
    public function send(User $user, $html, $subject)
    {
        $httpClient = new GuzzleAdapter(new Client());
        $mailer = new SparkPost($httpClient, ['key' => $this->apiKey]);
        $mailer->setOptions(['async' => false]);

        $mailer->transmissions->post([
            'content' => [
                'from' => [
                    'email' => 'noreply@expago.com',
                ],
                'subject' => $subject,
                'html' => $html,
                'text' => '',
            ],
            'recipients' => [
                [
                    'address' => [
                        'email' => $user->getEmail(),
                    ],
                ],
            ],
        ]);
    }
}
