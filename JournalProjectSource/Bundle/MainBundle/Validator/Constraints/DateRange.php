<?php  
namespace Tribuca\Bundle\MainBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;  
use Symfony\Component\Validator\Exception\MissingOptionsException;

/**
 * @Annotation
 */
class DateRange extends Constraint  
{
    public $minMessage = 'La date de publication doit être supérieure à {{ limit }}.';
    public $maxMessage = 'La date de publication doit être inférieure à {{ limit }}.';
    public $invalidMessage = "La date n'est pas valide.";
    public $min;
    public $max;

    public function __construct($options = null)
    {
        parent::__construct($options);

        if (null === $this->min && null === $this->max) {
            throw new MissingOptionsException('Either option "min" or "max" must be given for constraint ' . __CLASS__, array('min', 'max'));
        }

        if (null !== $this->min) {
            $this->min = new \DateTime($this->min);
        }

        if (null !== $this->max) {
            $this->max = new \DateTime($this->max);
        }
    }
}
