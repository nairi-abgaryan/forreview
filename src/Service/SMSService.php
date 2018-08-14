<?php

namespace App\Service;

use App\Entity\User;
use Twilio\Rest\Client;

class SMSService
{
    /**
     * @var string
     */
    private $twilioId;

    /**
     * @var string
     */
    private $twilioToken;

    /**
     * @var string
     */
    private $twilioPhone;

    /**
     * @var Client
     */
    private $client;

    public function __construct($twilioId, $twilioToken, $twilioPhone)
    {
        $this->twilioId = $twilioId;
        $this->twilioToken = $twilioToken;
        $this->twilioPhone = $twilioPhone;
        $this->client = new Client($this->twilioId, $this->twilioToken);
    }

    /**
     * @param User $user
     */
    public function sendVerificationCode(User $user)
    {
        $this->client->messages->create('+' . $user->getPhone(), [
            'from' => $this->twilioPhone,
            'body' => 'Your confirmation code is ' . $user->getExpert()->getPlainVerificationCode()
        ]);
    }
}