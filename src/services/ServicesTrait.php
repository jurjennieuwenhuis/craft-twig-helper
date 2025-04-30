<?php

declare(strict_types=1);

namespace juni\twighelper\services;

use yii\base\InvalidConfigException;

/**
 * Trait ServicesTrait
 *
 * @property-read Formatter $formatter
 */
trait ServicesTrait
{

    // Static Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public static function config(): array
    {
        return [
            'components' => [
                'formatter' => Formatter::class,
            ]
        ];
    }

    // Public methods
    // =========================================================================

    /**
     * Returns the formatter service.
     *
     * @return Formatter
     * @throws InvalidConfigException
     */
    public function getFormatter(): Formatter
    {
        return $this->get('formatter');
    }
}
