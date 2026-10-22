<?php

namespace Chrometoaster\GA_GTM\Formfields;

use SilverStripe\Core\Validation\ValidationResult;
use SilverStripe\Forms\TextField;

/**
 * Class GACodeField
 */
class GACodeField extends TextField
{
    /**
     * @return string
     */
    public function Type()
    {
        return 'text';
    }

    /**
     * Simple validation to make sure the input matches the expected pattern.
     *
     * @return ValidationResult
     */
    public function validate(): ValidationResult
    {
        $this->beforeExtending('updateValidate', function (ValidationResult $result) {
            // No value set so accept
            if (empty($this->value)) {
                return;
            } else {
                $parts = explode("-", $this->value);
            }

            if ($parts[0] === "GTM" && (mb_strlen($parts[1]) >= 4) && (mb_strlen($parts[1]) <= 8)) {
                return;

            } else if (($parts[0] === "UA")
                && (mb_strlen($parts[1]) >= 8 && mb_strlen($parts[1]) < 10)
                && (mb_strlen($parts[2]) >= 1 && mb_strlen($parts[2]) < 3)) {
                return;

            } else {
                $result->addFieldError($this->name, "ERROR: This isn't a valid Universal or Tag Manager code. Must be in either UA-XXXXXXXX-X or GTM-XXXXXX format.", "validation");
            }
        });

        return parent::validate();
    }
}
