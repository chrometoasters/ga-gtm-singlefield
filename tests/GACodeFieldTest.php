<?php

namespace Chrometoaster\GA_GTM\Tests;

use Chrometoaster\GA_GTM\Formfields\GACodeField;
use ReflectionMethod;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\Forms\FormField;

/**
 * Class GACodeFieldTest
 *
 * Which codes the field accepts, and the error it records for the others.
 */
class GACodeFieldTest extends SapphireTest
{
    public function testFieldType()
    {
        $this->assertSame('text', GACodeField::create('GoogleCode')->Type());
    }

    public function testAcceptsAnEmptyValueAndValidCodes()
    {
        $codes = [
            '',
            'GTM-ABCD',
            'GTM-ABCD1234',
            'UA-12345678-1',
            'UA-123456789-12',
        ];

        foreach ($codes as $code) {
            $result = $this->validateCode($code);

            $this->assertTrue($result->isValid(), "'$code'");
            $this->assertSame([], $result->getMessages(), "'$code'");
        }
    }

    public function testRejectsInvalidCodes()
    {
        $codes = [
            'GTM-ABC',
            'GTM-ABCDEFGHI',
            'UA-1234567-1',
            'UA-12345678-123',
            'XY-12345678-1',
        ];

        foreach ($codes as $code) {
            $result = $this->validateCode($code);
            $this->assertFalse($result->isValid(), "'$code'");

            $messages = $result->getMessages();
            $this->assertCount(1, $messages, "'$code'");

            $message = reset($messages);
            $this->assertSame('GoogleCode', $message['fieldName'], "'$code'");
            $this->assertSame('validation', $message['messageType'], "'$code'");
            $this->assertSame(
                "ERROR: This isn't a valid Universal or Tag Manager code. Must be in either UA-XXXXXXXX-X or GTM-XXXXXX format.",
                $message['message'],
                "'$code'"
            );
        }
    }

    /**
     * Validate a code with the field and return the validation result.
     *
     * FormField::validate() returns the result itself on CMS 6; on CMS 5 it
     * records errors on the validator it is given.
     *
     * @param string $code
     * @return \SilverStripe\Core\Validation\ValidationResult|\SilverStripe\ORM\ValidationResult
     */
    private function validateCode($code)
    {
        $field = GACodeField::create('GoogleCode', 'Google code')->setValue($code);

        if ((new ReflectionMethod(FormField::class, 'validate'))->getNumberOfParameters() === 0) {
            return $field->validate();
        }

        $validatorClass = 'SilverStripe\\Forms\\RequiredFields';
        $validator = new $validatorClass();
        $field->validate($validator);

        return $validator->getResult();
    }
}
