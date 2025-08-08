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

namespace Xima\XimaTypo3RecentUpdates\Widgets\Provider;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Dashboard\Widgets\ListDataProviderInterface;
use Xima\XimaTypo3RecentUpdates\Domain\Model\Dto\ListItem;

class RecentUpdatesDataProvider implements ListDataProviderInterface
{
    /**
    * @throws \Doctrine\DBAL\Exception
    */
    public function getItems(): array
    {
        $typo3Version = GeneralUtility::makeInstance(Typo3Version::class)->getMajorVersion();
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('sys_log');

        $results = $queryBuilder
            ->select(
                'sl.recuid as uid',
                'sl.tstamp as updated',
                'sl.tablename',
                'sl.details',
                'sl.log_data',
                'tt.CType as cType',
                'sl.event_pid as pageId',
                'tt.list_type as listType',
                'sl.userid as userId',
                'bu.username as username',
                'p.title as pageTitle'
            )
            ->from('sys_log', 'sl')
            ->leftJoin('sl', 'be_users', 'bu', 'sl.userid = bu.uid')
            ->leftJoin('sl', 'tt_content', 'tt', 'sl.recuid = tt.uid')
            ->leftJoin('sl', 'pages', 'p', 'sl.event_pid = p.uid')
            ->andWhere('channel = "content" OR channel = "pages"')
            ->setMaxResults(20)
            ->orderBy('sl.tstamp', 'DESC')
            ->executeQuery()
            ->fetchAllAssociative();

        $items = [];

        foreach ($results as $result) {
            try {
                $items[] = $typo3Version === 11 ? ListItem::createFromV11Log($result) : ListItem::createFromV12Log($result);
            } catch (\Exception $e) {
            }
        }

        return $items;
    }
}
