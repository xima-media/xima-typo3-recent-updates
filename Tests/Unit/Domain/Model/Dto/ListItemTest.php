<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS extension "xima_typo3_recent_updates".
 *
 * Copyright (C) 2023-2026 Konrad Michalik <hej@konradmichalik.dev>
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

namespace Xima\XimaTypo3RecentUpdates\Tests\Unit\Domain\Model\Dto;

use PHPUnit\Framework\TestCase;
use Xima\XimaTypo3RecentUpdates\Domain\Model\Dto\ListItem;

/**
 * ListItemTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0
 */
class ListItemTest extends TestCase
{
    public function testCreateFromV12LogWithValidData(): void
    {
        $logData = [
            'uid' => 1,
            'updated' => 1234567890,
            'tablename' => 'tt_content',
            'details' => 'Test details',
            'log_data' => '{"table":"tt_content","uid":1,"title":"Test Item"}',
            'cType' => 'text',
        ];

        $listItem = ListItem::createFromV12Log($logData);

        self::assertInstanceOf(ListItem::class, $listItem);
        self::assertSame(1, $listItem->log['uid']);
        self::assertSame('tt_content', $listItem->log['tablename']);
        self::assertIsArray($listItem->log['log_data']);
    }

    public function testCreateFromV11LogWithValidData(): void
    {
        $logData = [
            'uid' => 1,
            'updated' => 1234567890,
            'tablename' => 'tt_content',
            'details' => 'Test details',
            'log_data' => serialize(['table' => 'tt_content', 'uid' => 1, 'Test Item']),
            'cType' => 'text',
        ];

        $listItem = ListItem::createFromV11Log($logData);

        self::assertInstanceOf(ListItem::class, $listItem);
        self::assertSame(1, $listItem->log['uid']);
        self::assertSame('tt_content', $listItem->log['tablename']);
        self::assertIsArray($listItem->log['log_data']);
    }

    public function testCreateFromV12LogWithoutTitleAndRecordReference(): void
    {
        $logData = [
            'uid' => 1,
            'updated' => 1234567890,
            'tablename' => 'tt_content',
            'details' => 'Test details',
            'log_data' => '{"history":"12"}',
            'cType' => 'text',
        ];

        $listItem = ListItem::createFromV12Log($logData);

        self::assertSame('', $listItem->log['title']);
    }

    public function testCreateFromV11LogWithoutTitleAndRecordReference(): void
    {
        $logData = [
            'uid' => 1,
            'updated' => 1234567890,
            'tablename' => 'tt_content',
            'details' => 'Test details',
            'log_data' => serialize(['history' => '12']),
            'cType' => 'text',
        ];

        $listItem = ListItem::createFromV11Log($logData);

        self::assertSame('', $listItem->log['title']);
    }

    public function testCreateFromLogWithNonArrayLogData(): void
    {
        $logData = [
            'uid' => 1,
            'updated' => 1234567890,
            'tablename' => 'tt_content',
            'details' => 'Test details',
            'cType' => 'text',
        ];

        self::assertSame([], ListItem::createFromV12Log(['log_data' => 'not json'] + $logData)->log['log_data']);
        self::assertSame([], ListItem::createFromV11Log(['log_data' => serialize(false)] + $logData)->log['log_data']);
    }

    public function testTruncate(): void
    {
        $shortString = 'Short text';
        self::assertSame('Short text', ListItem::truncate($shortString));

        $longString = 'This is a very long string that should be truncated because it exceeds the maximum length';
        $truncated = ListItem::truncate($longString, 30);

        self::assertStringContainsString('&hellip;', $truncated);
        self::assertLessThan(strlen($longString), strlen($truncated));
    }

    public function testGetTitleWithEmptyData(): void
    {
        $listItem = new ListItem();
        $listItem->log = ['title' => ''];

        self::assertSame('', $listItem->getTitle());
    }

    public function testGetTitleWithValidData(): void
    {
        $listItem = new ListItem();
        $listItem->log = ['title' => 'Test Title'];

        self::assertSame('Test Title', $listItem->getTitle());
    }
}
