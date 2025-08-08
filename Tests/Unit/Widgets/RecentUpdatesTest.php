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

namespace Xima\XimaTypo3RecentUpdates\Tests\Unit\Widgets;

use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Dashboard\Widgets\ButtonProviderInterface;
use TYPO3\CMS\Dashboard\Widgets\ListDataProviderInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetConfigurationInterface;
use Xima\XimaTypo3RecentUpdates\Widgets\RecentUpdates;

class RecentUpdatesTest extends TestCase
{
    private WidgetConfigurationInterface $configuration;
    private ListDataProviderInterface $dataProvider;
    private ButtonProviderInterface $buttonProvider;
    private RecentUpdates $widget;

    protected function setUp(): void
    {
        $this->configuration = $this->createMock(WidgetConfigurationInterface::class);
        $this->dataProvider = $this->createMock(ListDataProviderInterface::class);
        $this->buttonProvider = $this->createMock(ButtonProviderInterface::class);

        $this->widget = new RecentUpdates(
            $this->configuration,
            $this->dataProvider,
            $this->buttonProvider,
            ['option1' => 'value1']
        );
    }

    public function testGetOptions(): void
    {
        $expectedOptions = ['option1' => 'value1'];
        self::assertSame($expectedOptions, $this->widget->getOptions());
    }

    public function testGetOptionsWithEmptyOptions(): void
    {
        $widget = new RecentUpdates($this->configuration, $this->dataProvider);
        self::assertSame([], $widget->getOptions());
    }

    public function testRenderWidgetContentMethodExists(): void
    {
        // Test that the widget implements the interface correctly
        self::assertInstanceOf(\TYPO3\CMS\Dashboard\Widgets\WidgetInterface::class, $this->widget);

        // We skip the actual rendering test as it requires TYPO3 environment
        // In integration tests, this would be fully tested
    }
}
