<?php
namespace  App\Exception;

use Symfony\Component\Form\FormInterface;

/**
 * Class ValidationException
 * @package App\Exception
 */
class ValidationException extends ApiException
{
    /**
     * @var FormInterface $form
     */
    private $form;

    /**
     * ValidationException constructor.
     * @param FormInterface $form
     */
    public function __construct(FormInterface $form)
    {
        $this->form = $form;
    }

    /**
     * @return array
     */
    public function getErrorDetails()
    {
        return [
            'code' => 400,
            'message' => 'Validation Error',
            'errors' => $this->getFormErrors($this->form),
        ];
    }

    /**
     * @param FormInterface $form
     * @return array
     */
    private function getFormErrors(FormInterface $form)
    {
        $errors = $form->getErrors(true, true);
        $errorArray = [];

        foreach($errors as $e){
            $field = $e->getOrigin();
            $error["field"] = $field->getName();
            $error["message"]= $e->getMessage();
            array_push($errorArray, $error);
        }

        return $errorArray;
    }
}

