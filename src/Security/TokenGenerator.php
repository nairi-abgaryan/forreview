<?php

namespace App\Security;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;

class TokenGenerator
{
    /**
     * @var JWTEncoderInterface
     */
    private $encoder;

    /**
     * TokenService constructor.
     *
     * @param JWTEncoderInterface $encoder
     */
    public function __construct(JWTEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    /**
     * @param User $user
     * @return string
     * @throws \Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTEncodeFailureException
     */
    public function getTokenForUser(User $user)
    {
        return $this->encoder->encode(['id' => $user->getId(), "role" => $user->getRole()->getName()]);
    }

    /**
     * @param User $user
     * @param mixed $expireTime
     * @return string
     * @throws \Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTEncodeFailureException
     */
    public function getTokenForActivation(User $user, $expireTime = 1)
    {
        $expireTime = strtotime("+$expireTime hours");
        return $this->encoder->encode(['id' => $user->getId(), 'role' => $user->getRoles(), 'activate' => true, 'exp' => $expireTime]);
    }

    /**
     * @param string $token
     * @return array
     * @throws \Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException
     */
    public function decode(string $token)
    {
        return $this->encoder->decode($token);
    }
}
