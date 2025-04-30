<?php

declare(strict_types=1);

namespace juni\twighelper\twigextensions;

use Craft;
use craft\elements\Entry;
use craft\helpers\DateTimeHelper;
use craft\helpers\StringHelper;
use craft\helpers\Template as TemplateHelper;
use craft\i18n\Locale;
use juni\twighelper\services\Formatter;
use juni\twighelper\TwigHelper;
use Twig\Environment as TwigEnvironment;
use Twig\Extension\AbstractExtension;
use Twig\Extension\CoreExtension;
use Twig\Markup;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * Twig can be extended in many ways; you can add extra tags, filters, tests, operators,
 * global variables, and functions. You can even extend the parser itself with
 * node visitors.
 *
 * http://twig.sensiolabs.org/doc/advanced.html
 *
 * @author    Jurjen Nieuwenhuis
 * @package   Themehelper
 * @since     1.0.0
 */
class TwigExtension extends AbstractExtension
{
    // Public Methods
    // =========================================================================

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName(): string
    {
        return 'TwigHelper';
    }

    /**
     * Returns an array of Twig filters, used in Twig templates via:
     *
     *      {{ 'something' | someFilter }}
     *
     * @return array
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('scrub', [$this, 'scrub']),
            new TwigFilter('inline', [$this, 'inline']),
            new TwigFilter('monthIndex', [$this, 'monthIndex']),
            new TwigFilter('startsWith', [$this, 'startsWith']),
            new TwigFilter('navTitle', [$this, 'navTitle']),
            new TwigFilter('lead', [$this, 'lead']),
            new TwigFilter('typography', [$this, 'typography']),
            new TwigFilter('readingTime', [$this, 'readingTime']),
            new TwigFilter('timeAgo', [$this, 'timeAgo'], ['needs_environment' => true]),
        ];
    }

    /**
     * Returns an array of Twig functions, used in Twig templates via:
     *
     *      {% set this = someFunction('something') %}
     *
    * @return array
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('scrub', [$this, 'scrub']),
            new TwigFunction('inline', [$this, 'inline']),
            new TwigFunction('monthIndex', [$this, 'monthIndex']),
            new TwigFunction('startsWith', [$this, 'startsWith']),
            new TwigFunction('navTitle', [$this, 'navTitle']),
            new TwigFunction('lead', [$this, 'lead']),
            new TwigFunction('typography', [$this, 'typography']),
            new TwigFunction('readingTime', [$this, 'readingTime']),
            new TwigFunction('timeAgo', [$this, 'timeAgo'], ['needs_environment' => true]),
        ];
    }

    /**
     * Removes empty <p/> tags from the html text. It also removes any non-breaking spaces.
     */
    public function scrub(?string $text = null): Markup|string
    {
        if (null === $text || '' === $text) {
            return '';
        }

        $newText = preg_replace('/<p>\xc2\xa0<\/p>/i', '', $text);
        $newText = preg_replace('/<p[^>]*?><\/p>/i', '', $newText);
        $newText = preg_replace('/<(p|h[1-6])><br \/><\/(p|h[1-6])>/i', '', $newText);
        $newText = str_replace('&nbsp;', ' ', $newText);

        return TemplateHelper::raw($newText);
    }

    /**
     * Strip <p> tags from rich text field
     */
    public function inline(?string $var): Markup|string
    {
        if ('' === $var || null === $var) {
            return '';
        }

        $newVar = preg_replace('/<p[^>]*?>/i', '', $var);
        $newVar = str_replace('</p>', '<br>', $newVar);
        $newVar = preg_replace('/<br>$/', '', $newVar);

        return TemplateHelper::raw($newVar);
    }

    public function monthIndex(string $monthName): int
    {
        return (int) date('m', strtotime($monthName));
    }

    public function startsWith($string, $needle): bool
    {
        return str_starts_with($string, $needle);
    }

    public function navTitle(Entry $entry): Markup|string
    {
        $title = $entry->navigationTitle;

        if (empty($title)) {
            $title = $entry->title;
        }

        return TemplateHelper::raw($title);
    }

    /**
     * Add the class 'lead' to the paragraph elements
     */
    public function lead(?string $var): Markup|string
    {
        if (null === $var || '' === $var) {
            return '';
        }

        return TemplateHelper::raw($this->_addCssClass($var, 'lead'));
    }

    public function typography(?string $var): Markup|string
    {
        if (null === $var || '' === $var) {
            return '';
        }

        $var = $this->_addCssClass($var, 'unordered-list', 'ul');
        $var = $this->_addCssClass($var, 'ordered-list', 'ol');
        $var = $this->_addCssClass($var, 'table table-striped table-hover', 'table');

        return TemplateHelper::raw($var);
    }

    public function readingTime(?string $var): string
    {
        if (null === $var) {
            return Craft::t('juni-twig-helper', '{num, number} {num, plural, =1{minute} other{minutes}} read', ['num' => 0]);
        }

        $text = preg_replace('@<(script|style)[^>]*?>.*?</\\1>@si', '', $var);
        $text = strip_tags($text);
        $text = trim($text);

        $words = count(preg_split("/[\n\r\t ]+/", $text));

        if (!empty($words)) {
            $wordsPerMinute = 200; // TODO: create setting
            $timeInMinutes = ceil($words / $wordsPerMinute);

            return Craft::t('juni-twig-helper', '{num, number} {num, plural, =1{minute} other{minutes}} read', ['num' => $timeInMinutes]);
        }

        return Craft::t('juni-twig-helper', '{num, number} {num, plural, =1{minute} other{minutes}} read', ['num' => 0]);
    }

    /**
     * Formats the value as `7 days ago`, etc.
     *
     * @param TwigEnvironment $env
     * @param mixed $date
     * @param string $format
     * @param $agoLabel
     * @param mixed|null $timezone
     * @param string|null $locale
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function timeAgo(TwigEnvironment $env, mixed $date, string $format, $agoLabel = 'ago', mixed $timezone = null, ?string $locale = null): string
    {
        // Is this a custom PHP date format?
        if ($format !== null && !in_array($format, [Locale::LENGTH_SHORT, Locale::LENGTH_MEDIUM, Locale::LENGTH_LONG, Locale::LENGTH_FULL], true)) {
            if (str_starts_with($format, 'icu:')) {
                $format = substr($format, 4);
            } else {
                $format = StringHelper::ensureLeft($format, 'php:');
            }
        }

        $date = $env->getExtension(CoreExtension::class)->convertDate($date, $timezone);

        $measureTypes = [
            'minutes' => Formatter::MINUTE_IN_SECONDS,
            'hours' => Formatter::HOUR_IN_SECONDS,
            'days' => Formatter::DAY_IN_SECONDS,
            'months' => Formatter::YEAR_IN_SECONDS / 12,
        ];

        $now = DateTimeHelper::now()->getTimestamp();

        // TODO: make configurable
        $timeNumber = 12;
        $timeMeasure = 'months';

        $limit = $timeNumber * $measureTypes[$timeMeasure];
        $timeDiff = $now - $date->getTimestamp();

        if ($timeDiff <= $limit) {
            return sprintf('%s %s',
                TwigHelper::$plugin->formatter->toHumanTimeAgo($date->getTimestamp(), $now),
                $agoLabel
            );
        }

        $formatter = $locale ? Craft::$app->getI18n()->getLocaleById($locale)->getFormatter() : Craft::$app->getFormatter();
        $fmtTimeZone = $formatter->timeZone;
        $formatter->timeZone = $timezone !== null ? $date->getTimezone()->getName() : $formatter->timeZone;
        $formatted = $formatter->asDateTime(\DateTime::createFromInterface($date), $format);
        $formatter->timeZone = $fmtTimeZone;

        return $formatted;
    }

    // Private Methods
    // =========================================================================

    private function _addCssClass(string $html, string $class, string $element = 'p'): string
    {
        $replace = sprintf('<%s class="%s">', $element, $class);
        $pattern = sprintf('/<%s[^>]*?>/i', $element);

        return preg_replace($pattern, $replace, $html);
    }
}
