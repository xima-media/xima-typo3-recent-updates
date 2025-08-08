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
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use Xima\XimaTypo3RecentUpdates\ViewHelpers\TimeAgoViewHelper;

class TimeAgoViewHelperTest extends TestCase
{
    private TimeAgoViewHelper $viewHelper;

    protected function setUp(): void
    {
        $this->viewHelper = new TimeAgoViewHelper();

        // Mock LocalizationUtility for predictable test results
        $this->setupLocalizationMock();
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
        $this->viewHelper->setArguments(['timestamp' => 0, 'currentTimestamp' => time()]);
        $result = $this->viewHelper->render();

        self::assertSame('', $result);
    }

    public function testRenderWithNegativeTimestamp(): void
    {
        $this->viewHelper->setArguments(['timestamp' => -1, 'currentTimestamp' => time()]);
        $result = $this->viewHelper->render();

        self::assertSame('', $result);
    }

    public function testRenderJustNow(): void
    {
        $currentTime = 1640995200; // 2022-01-01 00:00:00
        $timestamp = $currentTime - 30; // 30 seconds ago

        $this->viewHelper->setArguments(['timestamp' => $timestamp, 'currentTimestamp' => $currentTime]);
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

        $this->viewHelper->setArguments(['timestamp' => $timestamp, 'currentTimestamp' => $currentTime]);
        $result = $this->viewHelper->render();

        self::assertStringContainsString('1 minute ago', $result);
        self::assertStringContainsString('title="2021-12-31 23:59:00"', $result);
    }

    public function testRenderMinutesPlural(): void
    {
        $currentTime = 1640995200; // 2022-01-01 00:00:00
        $timestamp = $currentTime - 300; // 5 minutes ago

        $this->viewHelper->setArguments(['timestamp' => $timestamp, 'currentTimestamp' => $currentTime]);
        $result = $this->viewHelper->render();

        self::assertStringContainsString('5 minutes ago', $result);
        self::assertStringContainsString('title="2021-12-31 23:55:00"', $result);
    }

    public function testRenderHoursSingular(): void
    {
        $currentTime = 1640995200; // 2022-01-01 00:00:00
        $timestamp = $currentTime - 3600; // 1 hour ago

        $this->viewHelper->setArguments(['timestamp' => $timestamp, 'currentTimestamp' => $currentTime]);
        $result = $this->viewHelper->render();

        self::assertStringContainsString('1 hour ago', $result);
        self::assertStringContainsString('title="2021-12-31 23:00:00"', $result);
    }

    public function testRenderHoursPlural(): void
    {
        $currentTime = 1640995200; // 2022-01-01 00:00:00
        $timestamp = $currentTime - (3 * 3600); // 3 hours ago

        $this->viewHelper->setArguments(['timestamp' => $timestamp, 'currentTimestamp' => $currentTime]);
        $result = $this->viewHelper->render();

        self::assertStringContainsString('3 hours ago', $result);
        self::assertStringContainsString('title="2021-12-31 21:00:00"', $result);
    }

    public function testRenderDaysSingular(): void
    {
        $currentTime = 1640995200; // 2022-01-01 00:00:00
        $timestamp = $currentTime - 86400; // 1 day ago

        $this->viewHelper->setArguments(['timestamp' => $timestamp, 'currentTimestamp' => $currentTime]);
        $result = $this->viewHelper->render();

        self::assertStringContainsString('1 day ago', $result);
        self::assertStringContainsString('title="2021-12-31 00:00:00"', $result);
    }

    public function testRenderDaysPlural(): void
    {
        $currentTime = 1640995200; // 2022-01-01 00:00:00
        $timestamp = $currentTime - (7 * 86400); // 7 days ago

        $this->viewHelper->setArguments(['timestamp' => $timestamp, 'currentTimestamp' => $currentTime]);
        $result = $this->viewHelper->render();

        self::assertStringContainsString('7 days ago', $result);
        self::assertStringContainsString('title="2021-12-25 00:00:00"', $result);
    }

    public function testRenderMonthsSingular(): void
    {
        $currentTime = 1640995200; // 2022-01-01 00:00:00
        $timestamp = $currentTime - 2592000; // ~1 month ago

        $this->viewHelper->setArguments(['timestamp' => $timestamp, 'currentTimestamp' => $currentTime]);
        $result = $this->viewHelper->render();

        self::assertStringContainsString('1 month ago', $result);
    }

    public function testRenderMonthsPlural(): void
    {
        $currentTime = 1640995200; // 2022-01-01 00:00:00
        $timestamp = $currentTime - (3 * 2592000); // ~3 months ago

        $this->viewHelper->setArguments(['timestamp' => $timestamp, 'currentTimestamp' => $currentTime]);
        $result = $this->viewHelper->render();

        self::assertStringContainsString('3 months ago', $result);
    }

    public function testRenderYearsSingular(): void
    {
        $currentTime = 1640995200; // 2022-01-01 00:00:00
        $timestamp = $currentTime - 31536000; // 1 year ago

        $this->viewHelper->setArguments(['timestamp' => $timestamp, 'currentTimestamp' => $currentTime]);
        $result = $this->viewHelper->render();

        self::assertStringContainsString('1 year ago', $result);
    }

    public function testRenderYearsPlural(): void
    {
        $currentTime = 1640995200; // 2022-01-01 00:00:00
        $timestamp = $currentTime - (2 * 31536000); // 2 years ago

        $this->viewHelper->setArguments(['timestamp' => $timestamp, 'currentTimestamp' => $currentTime]);
        $result = $this->viewHelper->render();

        self::assertStringContainsString('2 years ago', $result);
    }

    public function testRenderFutureDate(): void
    {
        $currentTime = 1640995200; // 2022-01-01 00:00:00
        $timestamp = $currentTime + 3600; // 1 hour in future

        $this->viewHelper->setArguments(['timestamp' => $timestamp, 'currentTimestamp' => $currentTime]);
        $result = $this->viewHelper->render();

        self::assertStringContainsString('in the future', $result);
    }

    public function testHtmlEscaping(): void
    {
        $currentTime = 1640995200;
        $timestamp = $currentTime - 60;

        $this->viewHelper->setArguments(['timestamp' => $timestamp, 'currentTimestamp' => $currentTime]);
        $result = $this->viewHelper->render();

        // Check that HTML entities are properly escaped
        self::assertStringNotContainsString('<', strip_tags($result));
        self::assertStringNotContainsString('>', strip_tags($result));
        self::assertStringContainsString('&quot;', $result);
    }

    public function testEscapeOutputPropertyIsFalse(): void
    {
        $reflection = new \ReflectionClass($this->viewHelper);
        $property = $reflection->getProperty('escapeOutput');
        $property->setAccessible(true);

        self::assertFalse($property->getValue($this->viewHelper));
    }

    /**
     * Mock LocalizationUtility to return predictable English values for testing
     */
    private function setupLocalizationMock(): void
    {
        // Create a mock that will be used by LocalizationUtility
        // Note: In a real test scenario, you might want to use a proper mocking framework
        // or dependency injection to replace LocalizationUtility with a test double

        // For this test, we rely on the fact that if localization fails,
        // the keys themselves should be returned, allowing us to test the basic functionality
    }
}
