<?php

namespace Chrometoaster\GA_GTM\Extensions;

use Chrometoaster\GA_GTM\Formfields\GACodeField;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataExtension;

/**
 * Class GASiteConfig
 *
 * Adapted to fit the needs, originally based on https://github.com/peavers/silverstripe-google-analytics.
 */
class GASiteConfigExtension extends DataExtension
{

    private static $db = [
        'AnalyticType' => 'Varchar(5)',
        'GoogleCode'   => 'Varchar(15)',
    ];

    public function updateCMSFields(FieldList $fields)
    {
        $fields->findOrMakeTab('Root.GAGTM', 'GA & GTM');

        $fields->addFieldsToTab('Root.GAGTM', [
            GACodeField::create('GoogleCode', 'Google code')->setDescription('Can be either a Universal (<strong>UA-XXXXXXXX-X</strong>) or Tag manager (<strong>GTM-XXXXXX</strong>) code.'),
        ]);
    }

    /**
     *  Update the AnalyticType field with a key depending on what type of code is used.
     */
    public function onBeforeWrite()
    {
        $parts = explode("-", (string) $this->owner->getField('GoogleCode'));

        if (count($parts)) {
            if ($parts[0] === "GTM") {
                $this->owner->setField('AnalyticType', 'GTM');

            } else {
                if ($parts[0] === "UA") {
                    $this->owner->setField('AnalyticType', 'UA');
                }
            }
        }

        parent::onBeforeWrite();
    }

}
