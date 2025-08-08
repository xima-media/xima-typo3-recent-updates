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

namespace Xima\XimaTypo3RecentUpdates\Tests\Unit\Widgets\Provider;

use Doctrine\DBAL\Result;
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use Xima\XimaTypo3RecentUpdates\Widgets\Provider\RecentUpdatesDataProvider;

class RecentUpdatesDataProviderTest extends TestCase
{
    private ConnectionPool $connectionPool;
    private QueryBuilder $queryBuilder;
    private RecentUpdatesDataProvider $dataProvider;

    protected function setUp(): void
    {
        $this->connectionPool = $this->createMock(ConnectionPool::class);
        $this->queryBuilder = $this->createMock(QueryBuilder::class);

        $this->connectionPool
            ->method('getQueryBuilderForTable')
            ->with('sys_log')
            ->willReturn($this->queryBuilder);

        $this->dataProvider = new RecentUpdatesDataProvider($this->connectionPool);
    }

    public function testGetItemsReturnsEmptyArray(): void
    {
        $mockResult = $this->createMock(Result::class);
        $mockResult->method('fetchAllAssociative')->willReturn([]);

        // @phpstan-ignore-next-line
        $this->queryBuilder->method('select')->willReturnSelf();
        // @phpstan-ignore-next-line
        $this->queryBuilder->method('from')->willReturnSelf();
        // @phpstan-ignore-next-line
        $this->queryBuilder->method('leftJoin')->willReturnSelf();
        // @phpstan-ignore-next-line
        $this->queryBuilder->method('andWhere')->willReturnSelf();
        // @phpstan-ignore-next-line
        $this->queryBuilder->method('setMaxResults')->willReturnSelf();
        // @phpstan-ignore-next-line
        $this->queryBuilder->method('orderBy')->willReturnSelf();
        // @phpstan-ignore-next-line
        $this->queryBuilder->method('executeQuery')->willReturn($mockResult);

        $result = $this->dataProvider->getItems();

        self::assertCount(0, $result);
    }

    public function testDataProviderImplementsInterface(): void
    {
        self::assertInstanceOf(
            \TYPO3\CMS\Dashboard\Widgets\ListDataProviderInterface::class,
            $this->dataProvider
        );
    }
}
