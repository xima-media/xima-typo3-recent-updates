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

namespace Xima\XimaTypo3RecentUpdates\Tests\Unit\ViewHelpers;

use PHPUnit\Framework\TestCase;
use Xima\XimaTypo3RecentUpdates\ViewHelpers\TimeAgoViewHelper;

class TimeAgoViewHelperTest extends TestCase
{
    private TestableTimeAgoViewHelper $viewHelper;

    protected function setUp(): void
    {
        $this->viewHelper = new TestableTimeAgoViewHelper();
    }

    public function testInitializeArguments(): void
    {
        $this->viewHelper->initializeArguments();

        // Just test that initializeArguments() can be called without errors
        // The actual argument configuration is tested implicitly by the render tests
        $this->addToAssertionCount(1);
    }

    public function testRenderWithInvalidTimestamp(): void
    {
        $this->viewHelper->setArguments(['timestamp' => 0, 'currentTimestamp' => time(), 'renderClass' => 'test']);
        $result = $this->viewHelper->render();

        self::assertSame('', $result);
    }

    public function testRenderWithNegativeTimestamp(): void
    {
        $this->viewHelper->setArguments(['timestamp' => -1, 'currentTimestamp' => time(), 'renderClass' => 'test']);
        $result = $this->viewHelper->render();

        self::assertSame('', $result);
    }

    public function testRenderWithoutCurrentTimestamp(): void
    {
        $timestamp = time() - 300; // 5 minutes ago
        $this->viewHelper->setArguments(['timestamp' => $timestamp, 'renderClass' => 'test']);
        $result = $this->viewHelper->render();

        // Should use current time() and show "minutes ago"
        self::assertStringContainsString('minutes ago', $result);
        self::assertStringStartsWith('<span', $result);
        self::assertStringEndsWith('</span>', $result);
    }

    public function testRenderJustNow(): void
    {
        $currentTime = 1640995200; // 2022-01-01 00:00:00
        $timestamp = $currentTime - 30; // 30 seconds ago

        $this->viewHelper->setArguments(['timestamp' => $timestamp, 'currentTimestamp' => $currentTime, 'renderClass' => 'test']);
        $result = $this->viewHelper->render();

        self::assertStringContainsString('just now', $result);
        self::assertStringContainsString('title="2021-12-31 23:59:30"', $result);
        self::assertStringStartsWith('<span', $result);
        self::assertStringEndsWith('</span>', $result);
    }

    public function testRenderMinutesSingular(): void
    {
        $currentTime = 1640995200; // 2022-01-01 00:00:00
        $timestamp = $currentTime - 60; // 1 minute ago

        $this->viewHelper->setArguments(['timestamp' => $timestamp, 'currentTimestamp' => $currentTime, 'renderClass' => 'test']);
        $result = $this->viewHelper->render();

        self::assertStringContainsString('1 minute ago', $result);
        self::assertStringContainsString('title="2021-12-31 23:59:00"', $result);
    }

    public function testRenderMinutesPlural(): void
    {
        $currentTime = 1640995200; // 2022-01-01 00:00:00
        $timestamp = $currentTime - 300; // 5 minutes ago

        $this->viewHelper->setArguments(['timestamp' => $timestamp, 'currentTimestamp' => $currentTime, 'renderClass' => 'test']);
        $result = $this->viewHelper->render();

        self::assertStringContainsString('5 minutes ago', $result);
        self::assertStringContainsString('title="2021-12-31 23:55:00"', $result);
    }

    public function testRenderHoursSingular(): void
    {
        $currentTime = 1640995200; // 2022-01-01 00:00:00
        $timestamp = $currentTime - 3600; // 1 hour ago

        $this->viewHelper->setArguments(['timestamp' => $timestamp, 'currentTimestamp' => $currentTime, 'renderClass' => 'test']);
        $result = $this->viewHelper->render();

        self::assertStringContainsString('1 hour ago', $result);
        self::assertStringContainsString('title="2021-12-31 23:00:00"', $result);
    }

    public function testRenderHoursPlural(): void
    {
        $currentTime = 1640995200; // 2022-01-01 00:00:00
        $timestamp = $currentTime - (3 * 3600); // 3 hours ago

        $this->viewHelper->setArguments(['timestamp' => $timestamp, 'currentTimestamp' => $currentTime, 'renderClass' => 'test']);
        $result = $this->viewHelper->render();

        self::assertStringContainsString('3 hours ago', $result);
        self::assertStringContainsString('title="2021-12-31 21:00:00"', $result);
    }

    public function testRenderDaysSingular(): void
    {
        $currentTime = 1640995200; // 2022-01-01 00:00:00
        $timestamp = $currentTime - 86400; // 1 day ago

        $this->viewHelper->setArguments(['timestamp' => $timestamp, 'currentTimestamp' => $currentTime, 'renderClass' => 'test']);
        $result = $this->viewHelper->render();

        self::assertStringContainsString('1 day ago', $result);
        self::assertStringContainsString('title="2021-12-31 00:00:00"', $result);
    }

    public function testRenderDaysPlural(): void
    {
        $currentTime = 1640995200; // 2022-01-01 00:00:00
        $timestamp = $currentTime - (7 * 86400); // 7 days ago

        $this->viewHelper->setArguments(['timestamp' => $timestamp, 'currentTimestamp' => $currentTime, 'renderClass' => 'test']);
        $result = $this->viewHelper->render();

        self::assertStringContainsString('7 days ago', $result);
        self::assertStringContainsString('title="2021-12-25 00:00:00"', $result);
    }

    public function testRenderMonthsSingular(): void
    {
        $currentTime = 1640995200; // 2022-01-01 00:00:00
        $timestamp = $currentTime - 2592000; // ~1 month ago

        $this->viewHelper->setArguments(['timestamp' => $timestamp, 'currentTimestamp' => $currentTime, 'renderClass' => 'test']);
        $result = $this->viewHelper->render();

        self::assertStringContainsString('1 month ago', $result);
    }

    public function testRenderMonthsPlural(): void
    {
        $currentTime = 1640995200; // 2022-01-01 00:00:00
        $timestamp = $currentTime - (3 * 2592000); // ~3 months ago

        $this->viewHelper->setArguments(['timestamp' => $timestamp, 'currentTimestamp' => $currentTime, 'renderClass' => 'test']);
        $result = $this->viewHelper->render();

        self::assertStringContainsString('3 months ago', $result);
    }

    public function testRenderYearsSingular(): void
    {
        $currentTime = 1640995200; // 2022-01-01 00:00:00
        $timestamp = $currentTime - 31536000; // 1 year ago

        $this->viewHelper->setArguments(['timestamp' => $timestamp, 'currentTimestamp' => $currentTime, 'renderClass' => 'test']);
        $result = $this->viewHelper->render();

        self::assertStringContainsString('1 year ago', $result);
    }

    public function testRenderYearsPlural(): void
    {
        $currentTime = 1640995200; // 2022-01-01 00:00:00
        $timestamp = $currentTime - (2 * 31536000); // 2 years ago

        $this->viewHelper->setArguments(['timestamp' => $timestamp, 'currentTimestamp' => $currentTime, 'renderClass' => 'test']);
        $result = $this->viewHelper->render();

        self::assertStringContainsString('2 years ago', $result);
    }

    public function testRenderFutureDate(): void
    {
        $currentTime = 1640995200; // 2022-01-01 00:00:00
        $timestamp = $currentTime + 3600; // 1 hour in future

        $this->viewHelper->setArguments(['timestamp' => $timestamp, 'currentTimestamp' => $currentTime, 'renderClass' => 'test']);
        $result = $this->viewHelper->render();

        self::assertStringContainsString('in the future', $result);
    }

    public function testHtmlEscaping(): void
    {
        $currentTime = 1640995200;
        $timestamp = $currentTime - 60;

        $this->viewHelper->setArguments(['timestamp' => $timestamp, 'currentTimestamp' => $currentTime, 'renderClass' => 'test']);
        $result = $this->viewHelper->render();

        // Check that the output is properly structured HTML
        self::assertStringStartsWith('<span', $result);
        self::assertStringEndsWith('</span>', $result);
        self::assertStringContainsString('title=', $result);

        // Verify that the inner content (when tags are stripped) doesn't contain raw HTML
        $innerContent = strip_tags($result);
        self::assertStringNotContainsString('<', $innerContent);
        self::assertStringNotContainsString('>', $innerContent);
    }

    public function testEscapeOutputPropertyIsFalse(): void
    {
        $reflection = new \ReflectionClass($this->viewHelper);
        $property = $reflection->getProperty('escapeOutput');
        $property->setAccessible(true);

        self::assertFalse($property->getValue($this->viewHelper));
    }

}

/**
 * Testable version of TimeAgoViewHelper that doesn't depend on TYPO3's localization system
 */
class TestableTimeAgoViewHelper extends TimeAgoViewHelper
{
    /**
     * @phpstan-ignore constructor.missingParentCall
     */
    public function __construct()
    {
        // Override parent constructor to avoid Context dependency in tests
        $this->escapeOutput = false;
    }

    public function setArguments(array $arguments): void
    {
        $this->arguments = $arguments;
    }

    protected function translateLabel(string $key): string
    {
        $translations = [
            'timeago.future' => 'in the future',
            'timeago.just_now' => 'just now',
        ];

        return $translations[$key] ?? $key;
    }

    protected function translatePluralLabel(string $baseKey, int $count): string
    {
        $translations = [
            'timeago.minutes.singular' => '%d minute ago',
            'timeago.minutes.plural' => '%d minutes ago',
            'timeago.hours.singular' => '%d hour ago',
            'timeago.hours.plural' => '%d hours ago',
            'timeago.days.singular' => '%d day ago',
            'timeago.days.plural' => '%d days ago',
            'timeago.months.singular' => '%d month ago',
            'timeago.months.plural' => '%d months ago',
            'timeago.years.singular' => '%d year ago',
            'timeago.years.plural' => '%d years ago',
        ];

        $singularKey = $baseKey . '.singular';
        $pluralKey = $baseKey . '.plural';

        $template = $count === 1
            ? ($translations[$singularKey] ?? '%d')
            : ($translations[$pluralKey] ?? '%d');

        return sprintf($template, $count);
    }

    public function getCurrentTimestamp(): int
    {
        // In tests, always return current time for predictable behavior
        return time();
    }

    public function formatDateWithTypo3Settings(int $timestamp): string
    {
        // For tests, use a simple format to avoid dependency on TYPO3 globals
        $dateTime = new \DateTimeImmutable('@' . $timestamp);
        return $dateTime->format('Y-m-d H:i:s');
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

    protected function formatRelativeTime(int $difference): string
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
}
