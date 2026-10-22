<?php

namespace Chrometoaster\GA_GTM\Tests;

use Chrometoaster\GA_GTM\Formfields\GACodeField;
use SilverStripe\Core\Validation\ValidationResult;
use SilverStripe\Dev\SapphireTest;

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
     * @param string $code
     * @return ValidationResult
     */
    private function validateCode($code)
    {
        return GACodeField::create('GoogleCode', 'Google code')->setValue($code)->validate();
    }
}
