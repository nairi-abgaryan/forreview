<?php

namespace App\Service;

use App\Entity\User;

class StatusService
{
    CONST NOT_APPROVED = 3;
    CONST PENDING = 2;
    CONST ACTIVE = 1;
    CONST REJECTED = 0;
    CONST PUBLISHED = 1;
    CONST UNPUBLISHED = 0;
    CONST IS_DELETED = 2;

    /**
     * @param mixed $entity
     * @param $status
     * @return mixed
     */
    public function changeAppStatus($entity, $status)
    {
        switch ($status) {
            case 0:
                $entity->setAppStatus($this::UNPUBLISHED);
                break;
            case 1:
                $entity->setAppStatus($this::PUBLISHED);
                break;
            case 2:
                $entity->setAppStatus($this::IS_DELETED);
                break;
        }

        return $entity;
    }
    /**
     * @param mixed $entity
     * @param $status
     * @return mixed
     */
    public function changeStatus($entity, $status)
    {
        switch ($status) {
            case 0:
                $entity->setStatus($this::REJECTED);
                $entity->setAppStatus($this::UNPUBLISHED);
                break;
            case 1:
                $entity->setStatus($this::ACTIVE);
                break;
            case 2:
                $entity->setStatus($this::PENDING);
                $entity->setAppStatus($this::UNPUBLISHED);
                break;
            default:
                $entity->setStatus($this::NOT_APPROVED);
                $entity->setAppStatus($this::UNPUBLISHED);
        }

        return $entity;
    }

    /**
     * @param User $user
     * @param $status
     * @param $inAppStatus
     * @return bool|null
     */
    public function validate(User $user, $status, $inAppStatus)
    {
        if (($status !== StatusService::ACTIVE || $inAppStatus !== StatusService::PUBLISHED) && !in_array("ROLE_ADMIN",$user->getRoles()))
        {
            return false;
        }
        return true;
    }

    /**
     * @param User $user
     * @param $inAppStatus
     * @return bool|null
     */
    public function validateAppStatus(User $user, $inAppStatus)
    {
        if ($inAppStatus !== StatusService::PUBLISHED && !in_array("ROLE_ADMIN",$user->getRoles()))
        {
            return false;
        }
        return true;
    }
}

