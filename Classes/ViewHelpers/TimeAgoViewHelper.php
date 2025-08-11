<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS extension "xima_typo3_recent_updates".
 *
 * Copyright (C) 2023-2025 Konrad Michalik <hej@konradmichalik.dev>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

namespace Xima\XimaTypo3RecentUpdates\ViewHelpers;

use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use Xima\XimaTypo3RecentUpdates\Configuration;

/**
 * ViewHelper for displaying relative time differences in human-readable format
 *
 * Usage:
 * <xru:timeAgo timestamp="{item.updated}" />
 *
 * Output:
 * <span title="2025-01-27 10:30:00">5 minutes ago</span>
 */
class TimeAgoViewHelper extends AbstractViewHelper
{
    protected $escapeOutput = false;

    public function __construct(private readonly Context $context) {}

    public function initializeArguments(): void
    {
        $this->registerArgument('timestamp', 'int', 'Unix timestamp to format', true);
        $this->registerArgument('currentTimestamp', 'int', 'Current timestamp for comparison (default: now)', false, null);
        $this->registerArgument('renderClass', 'string', 'Adds this class to the rendered <span> element', false, 'badge badge-secondary');
    }

    public function render(): string
    {
        $arguments = $this->arguments;
        $timestamp = (int)$arguments['timestamp'];
        $currentTimestamp = (int)($arguments['currentTimestamp'] ?? $this->getCurrentTimestamp());
        $renderClass = $arguments['renderClass'] ?? 'badge badge-secondary';

        if ($timestamp <= 0) {
            return '';
        }

        $difference = $currentTimestamp - $timestamp;
        $formattedDate = $this->formatDateWithTypo3Settings($timestamp);
        $relativeTime = $this->formatRelativeTime($difference);

        return sprintf(
            '<span class="%s" title="%s">%s</span>',
            $renderClass,
            htmlspecialchars($formattedDate, ENT_QUOTES),
            htmlspecialchars($relativeTime, ENT_QUOTES)
        );
    }

    private function formatRelativeTime(int $difference): string
    {
        // Handle future dates
        if ($difference < 0) {
            return $this->translateLabel('timeago.future');
        }

        // Less than 1 minute
        if ($difference < 60) {
            return $this->translateLabel('timeago.just_now');
        }

        // Minutes
        if ($difference < 3600) {
            $minutes = (int)floor($difference / 60);
            return $this->translatePluralLabel('timeago.minutes', $minutes);
        }

        // Hours
        if ($difference < 86400) {
            $hours = (int)floor($difference / 3600);
            return $this->translatePluralLabel('timeago.hours', $hours);
        }

        // Days
        if ($difference < 2592000) { // 30 days
            $days = (int)floor($difference / 86400);
            return $this->translatePluralLabel('timeago.days', $days);
        }

        // Months
        if ($difference < 31536000) { // 365 days
            $months = (int)floor($difference / 2592000);
            return $this->translatePluralLabel('timeago.months', $months);
        }

        // Years
        $years = (int)floor($difference / 31536000);
        return $this->translatePluralLabel('timeago.years', $years);
    }

    protected function translateLabel(string $key): string
    {
        return LocalizationUtility::translate($key, Configuration::EXT_NAME) ?? $key;
    }

    protected function translatePluralLabel(string $baseKey, int $count): string
    {
        $singularKey = $baseKey . '.singular';
        $pluralKey = $baseKey . '.plural';

        $template = LocalizationUtility::translate(
            $count === 1 ? $singularKey : $pluralKey,
            Configuration::EXT_NAME
        ) ?? '%d';

        return sprintf($template, $count);
    }

    /**
     * Get current timestamp using TYPO3's Context API for timezone awareness
     */
    private function getCurrentTimestamp(): int
    {
        try {
            return $this->context->getPropertyFromAspect('date', 'timestamp');
        } catch (\Exception $e) {
            // Fallback to regular time() if Context is not available
            return time();
        }
    }

    /**
     * Format date using TYPO3's system date format configuration
     */
    private function formatDateWithTypo3Settings(int $timestamp): string
    {
        // Get TYPO3 system date format from global configuration
        $dateFormat = $GLOBALS['TYPO3_CONF_VARS']['SYS']['ddmmyy'] ?? 'd-m-Y';
        $timeFormat = $GLOBALS['TYPO3_CONF_VARS']['SYS']['hhmm'] ?? 'H:i';
        $fullFormat = $dateFormat . ' ' . $timeFormat;

        // Create DateTime object with proper timezone handling
        $dateTime = new \DateTimeImmutable('@' . $timestamp);

        // Apply server timezone for display (TYPO3 backend typically uses server timezone)
        try {
            $serverTimezone = new \DateTimeZone(date_default_timezone_get());
            $dateTime = $dateTime->setTimezone($serverTimezone);
        } catch (\Exception $e) {
            // Continue with UTC if timezone setting fails
        }

        return $dateTime->format($fullFormat);
    }
}
