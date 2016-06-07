<?php  
namespace Tribuca\Bundle\MainBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;  
use Symfony\Component\Validator\ConstraintValidator;

class DateRangeValidator extends ConstraintValidator  
{
    /**
     * {@inheritDoc}
     */
    public function validate($value, Constraint $constraint)
    {           
        if (null === $value) {
            return;
        }
        
        $dateValue = $value->format('d-m-Y');
        
        if (!($value instanceof \DateTime)) {
            $this->context->addViolation($constraint->invalidMessage, array(
                '{{ value }}' => $dateValue,
            ));

            return;
        } 

        
        if (null !== $constraint->max && $value > $constraint->max) {
            $this->context->addViolation($constraint->maxMessage, array(
                '{{ value }}' => $dateValue,
                '{{ limit }}' => $this->formatDate($constraint->max),
            ));
        }

        if (null !== $constraint->min && $value < $constraint->min) {
            $this->context->addViolation($constraint->minMessage, array(
                '{{ value }}' => $dateValue,
                '{{ limit }}' => $this->formatDate($constraint->min),
            ));
        }
    }

    protected function formatDate($date)
    {
        $formatter = new \IntlDateFormatter(
            null,
            \IntlDateFormatter::SHORT,
            \IntlDateFormatter::NONE,
            date_default_timezone_get(),
            \IntlDateFormatter::GREGORIAN
        );

        return $this->processDate($formatter, $date);
    }

    /**
     * @param  \IntlDateFormatter $formatter
     * @param  \Datetime          $date
     * @return string
     */
    protected function processDate(\IntlDateFormatter $formatter, \Datetime $date)
    {
        return $formatter->format((int) $date->format('U'));
    }
}