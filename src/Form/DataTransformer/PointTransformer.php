<?php

namespace App\Form\DataTransformer;

use App\ORM\ValueObject\Point;
use Symfony\Component\Form\DataTransformerInterface;

class PointTransformer implements DataTransformerInterface
{
    /**
     * Transforms a Point to a string "lat lng".
     *
     * @param Point|null $point
     *
     * @return string
     */
    public function transform($point)
    {
        if (null === $point) {
            return '';
        }

        return implode(' ', [$point->getLongitude(), $point->getLatitude()]);
    }

    /**
     * Transforms a string "lng lat" to a Point.
     *
     * @param mixed $string
     *
     * @return Point|null
     *
     * @internal param string $number
     */
    public function reverseTransform($string)
    {
        if (!$string) {
            return null;
        }

        list($longitude, $latitude) = explode(' ', $string, 2);

        return new Point($longitude, $latitude);
    }
}
