<?php

namespace Chrometoaster\GA_GTM\Tests;

use Chrometoaster\GA_GTM\Extensions\GASiteConfigExtension;
use Chrometoaster\GA_GTM\Formfields\GACodeField;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\SiteConfig\SiteConfig;

/**
 * Class GASiteConfigExtensionTest
 *
 * The settings field, the analytic type derived from the code on write,
 * and the head and body snippets rendered for each type.
 */
class GASiteConfigExtensionTest extends SapphireTest
{
    protected $usesDatabase = true;

    public function testExtensionIsApplied()
    {
        $this->assertTrue(SiteConfig::has_extension(GASiteConfigExtension::class));
    }

    public function testCMSFieldsHaveTheCodeFieldInItsOwnTab()
    {
        $fields = SiteConfig::current_site_config()->getCMSFields();

        $tab = $fields->findTab('Root.GAGTM');
        $this->assertNotNull($tab);
        $this->assertSame('GA & GTM', $tab->Title());

        $field = $tab->Fields()->dataFieldByName('GoogleCode');
        $this->assertInstanceOf(GACodeField::class, $field);
        $this->assertSame('Google code', $field->Title());
    }

    public function testWriteSetsTheAnalyticTypeFromTheCode()
    {
        $codes = [
            'GTM-ABC1234'   => 'GTM',
            'UA-12345678-1' => 'UA',
        ];

        foreach ($codes as $code => $type) {
            $config = SiteConfig::current_site_config();
            $config->GoogleCode = $code;
            $config->write();

            $this->assertSame($type, SiteConfig::get()->byID($config->ID)->AnalyticType, $code);
        }
    }

    public function testWriteWithoutACodeLeavesTheAnalyticTypeEmpty()
    {
        $config = SiteConfig::current_site_config();
        $config->GoogleCode = '';
        $config->write();

        $this->assertEmpty(SiteConfig::get()->byID($config->ID)->AnalyticType);
    }

    public function testSnippetsForATagManagerCode()
    {
        $config = $this->writeCode('GTM-ABC1234');

        $head = (string) $config->renderWith('Chrometoaster\\GA_GTM\\GA_GTM_head');
        $this->assertStringContainsString('https://www.googletagmanager.com/gtm.js?id=', $head);
        $this->assertStringContainsString("'dataLayer','GTM-ABC1234'", $head);
        $this->assertStringNotContainsString('google-analytics.com', $head);

        $body = (string) $config->renderWith('Chrometoaster\\GA_GTM\\GA_GTM_body');
        $this->assertStringContainsString('https://www.googletagmanager.com/ns.html?id=GTM-ABC1234', $body);
    }

    public function testSnippetsForAUniversalAnalyticsCode()
    {
        $config = $this->writeCode('UA-12345678-1');

        $head = (string) $config->renderWith('Chrometoaster\\GA_GTM\\GA_GTM_head');
        $this->assertStringContainsString('https://www.google-analytics.com/analytics.js', $head);
        $this->assertStringContainsString("ga('create', 'UA-12345678-1', 'auto');", $head);
        $this->assertStringNotContainsString('googletagmanager.com', $head);

        $body = (string) $config->renderWith('Chrometoaster\\GA_GTM\\GA_GTM_body');
        $this->assertStringNotContainsString('googletagmanager.com', $body);
        $this->assertStringNotContainsString('UA-12345678-1', $body);
    }

    public function testNoSnippetsWithoutACode()
    {
        $config = $this->writeCode('');

        $this->assertSame('', trim((string) $config->renderWith('Chrometoaster\\GA_GTM\\GA_GTM_head')));
        $this->assertSame('', trim((string) $config->renderWith('Chrometoaster\\GA_GTM\\GA_GTM_body')));
    }

    /**
     * Write a code to the current site config, which the snippets read through the $SiteConfig global.
     *
     * @param string $code
     * @return SiteConfig
     */
    private function writeCode($code)
    {
        $config = SiteConfig::current_site_config();
        $config->GoogleCode = $code;
        $config->write();

        return $config;
    }
}
