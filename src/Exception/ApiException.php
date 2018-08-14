<?php
namespace  App\Exception;

/**
 * Class ApiException
 * @package App\Exception
 */
class ApiException extends \Exception
{
    /**
     * @return array
     */
    public function getErrorDetails()
    {
        return [
            'code' => $this->getCode() ?: 400,
            'message' => $this->getMessage()?:'API Exception',
        ];
    }
}