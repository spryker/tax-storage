<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Functional\Spryker\Zed\Glossary\Business;

use Codeception\TestCase\Test;
use Generated\Shared\Transfer\KeyTranslationTransfer;
use Generated\Shared\Transfer\LocaleTransfer;
use Spryker\Zed\Glossary\Business\GlossaryFacade;
use Spryker\Zed\Locale\Business\LocaleFacade;

/**
 * @group Spryker
 * @group Zed
 * @group Glossary
 * @group GlossaryFacade
 * @group Business
 */
class GlossaryFacadeTest extends Test
{

    const GLOSSARY_KEY = 'glossary_key';

    /**
     * @var \Orm\Zed\Glossary\Persistence\Base\SpyGlossaryKeyQuery
     */
    private $query;

    /**
     * @var \Symfony\Component\HttpFoundation\Request
     */
    private $request;

    /**
     * @var array
     */
    private $locales = [];

    /**
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->getAvailableLocales();
    }

    /**
     * @return void
     */
    private function getAvailableLocales()
    {
        $this->locales = $this->getLocaleFacade()->getAvailableLocales();
    }

    /**
     * @return \Spryker\Zed\Locale\Business\LocaleFacade
     */
    private function getLocaleFacade()
    {
        return new LocaleFacade();
    }

    /**
     * @return \Spryker\Zed\Glossary\Business\GlossaryFacade
     */
    private function getGlossaryFacade()
    {
        return new GlossaryFacade();
    }

    /**
     * @param array $locales
     *
     * @return \Generated\Shared\Transfer\LocaleTransfer
     */
    private function buildLocaleTransferObject(array $locales)
    {
        $locale = new LocaleTransfer();
        $locale->setIdLocale($locales[0]);
        $locale->setLocaleName($this->locales[$locales[0]]);
        $locale->setIsActive(true);

        return $locale;
    }

    /**
     * @return void
     */
    public function testAddTranslation()
    {
        $facade = $this->getGlossaryFacade();

        $formData = [
            self::GLOSSARY_KEY => 'form.button.save',
        ];
        foreach ($this->locales as $localeId => $localeName) {
            $formData['locales'][$localeName] = 'save ' . $localeId;
        }

        $translationTransfer = (new KeyTranslationTransfer())->fromArray($formData);

        $action = $facade->saveGlossaryKeyTranslations($translationTransfer);

        $this->assertTrue($action);
    }

    /**
     * @return void
     */
    public function testUpdateTranslation()
    {
        $facade = $this->getGlossaryFacade();
        $localesIds = array_keys($this->locales);

        $locale = $this->buildLocaleTransferObject($localesIds);

        $formData = [
            self::GLOSSARY_KEY => 'form.button.save',
        ];
        foreach ($this->locales as $localeId => $localeName) {
            $formData['locales'][$localeName] = 'save ' . $localeId;
        }

        $translationTransfer = (new KeyTranslationTransfer())->fromArray($formData);

        $facade->saveGlossaryKeyTranslations($translationTransfer);

        $translatedKey = $facade->getTranslation($formData[self::GLOSSARY_KEY], $locale);

        $changedLocales = [];
        foreach ($this->locales as $localeId => $localeName) {
            $changedLocales[$localeName] = 'save-changed-' . $localeId;
        }

        $translationTransfer->setLocales($changedLocales);

        $facade->saveGlossaryKeyTranslations($translationTransfer);
        $translatedKeyChanged = $facade->getTranslation($formData[self::GLOSSARY_KEY], $locale);

        $this->assertNotSame($translatedKey->getValue(), $translatedKeyChanged->getValue());
    }

}
