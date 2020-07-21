<?php
/**
 * Ares (https://ares.to)
 *
 * @license https://gitlab.com/arescms/ares-backend/LICENSE (MIT License)
 */

namespace App\Model;

use App\Helper\LocaleHelper;

/**
 * Class Locale
 */
class Locale
{
    /**
     * @var string
     */
    private string $locale = '';

    /**
     * @var string
     */
    private string $fallbackLocale = '';

    /**
     * @var array
     */
    private array $messages = [];

    /**
     * @var array
     */
    private array $fallbackMessages = [];

    /**
     * @var LocaleHelper
     */
    private LocaleHelper $localeHelper;

    /**
     * Locale constructor.
     *
     * @param LocaleHelper $localeHelper
     */
    public function __construct(
        LocaleHelper $localeHelper
    ) {
        $this->localeHelper = $localeHelper;
    }

    /**
     * Takes message and placeholder to translate them in given locale.
     *
     * @param string $message
     * @return string
     */
    public function translate(string $message): string
    {
        $messages = $this->getMessages();

        if (!array_key_exists($message, $messages)) {
            $messages = $this->getFallbackMessages();

            if (!array_key_exists($message, $messages)) {
                return $message;
            } else {
                return $messages[$message];
            }
        }

        return $messages[$message];
    }

    /**
     * @return string
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * @param string $locale
     */
    public function setLocale(string $locale): void
    {
        $this->locale = $locale;
    }

    /**
     * @return string
     */
    public function getFallbackLocale(): string
    {
        return $this->fallbackLocale;
    }

    /**
     * @param string $fallbackLocale
     */
    public function setFallbackLocale(string $fallbackLocale): void
    {
        $this->fallbackLocale = $fallbackLocale;
    }

    /**
     * @return array
     */
    public function getMessages(): array
    {
        if ($this->messages) {
            return $this->messages;
        }

        $messages = $this->localeHelper->getMessages($this->getLocale());

        $this->setMessages($messages);

        return $this->messages;
    }

    /**
     * @param array $messages
     */
    public function setMessages(array $messages): void
    {
        $this->messages = $messages;
    }

    /**
     * @return array
     */
    public function getFallbackMessages(): array
    {
        if ($this->fallbackMessages) {
            return $this->fallbackMessages;
        }

        $messages = $this->localeHelper->getMessages($this->getFallbackLocale());

        $this->setFallbackMessages($messages);

        return $this->fallbackMessages;
    }

    /**
     * @param array $fallbackMessages
     */
    public function setFallbackMessages(array $fallbackMessages): void
    {
        $this->fallbackMessages = $fallbackMessages;
    }
}
