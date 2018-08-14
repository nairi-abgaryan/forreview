<?php
namespace App\Controller;

use App\Exception\ApiException;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

/**
 * Class ExceptionController
 * @package App\Controller
 */
class ExceptionController extends FOSRestController
{
    /**
     * @param $exception
     * @return \FOS\RestBundle\View\View
     */
    public function showAction($exception)
    {
        $originException = $exception;

        if (!$exception instanceof ApiException && !$exception instanceof HttpException) {
            $exception = new HttpException($this->getStatusCode($exception), $this->getStatusText($exception));
        }

        if ($exception instanceof HttpException) {
            $exception = new ApiException($this->getStatusText($exception), $this->getStatusCode($exception));
        }

        $error = $exception->getErrorDetails();

        $code = $this->getStatusCode($originException);
        return $this->view($error, $code);
    }

    /**
     * @param \Exception $exception
     * @return int
     */
    protected function getStatusCode(\Exception $exception)
    {
        if ($exception instanceof HttpExceptionInterface) {
            return $exception->getStatusCode();
        }

        return 400;
    }

    /**
     * @param \Exception $exception
     * @param string $default
     * @return string
     */
    protected function getStatusText(\Exception $exception, $default = 'Internal Server Error')
    {
        $code = $this->getStatusCode($exception);

        return array_key_exists($code, Response::$statusTexts) ? Response::$statusTexts[$code] : $default;
    }
}
