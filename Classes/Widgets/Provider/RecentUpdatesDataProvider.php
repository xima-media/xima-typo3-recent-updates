<?php

declare(strict_types=1);

namespace Xima\XimaRecentUpdatesWidget\Widgets\Provider;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Dashboard\Widgets\ListDataProviderInterface;

class RecentUpdatesDataProvider implements ListDataProviderInterface
{
    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function getItems(): array
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('sys_log');

        return $queryBuilder
            ->select(
                'sl.uid',
                'sl.recuid',
                'sl.tstamp',
                'sl.tablename',
                'tt.header',
                'tt.CType',
                'tt.pid',
                'tt.list_type',
                'sl.userid',
                'bu.username',
                'p.title',
                'ptt.title as page'
            )
            ->from('sys_log', 'sl')
            ->leftJoin('sl', 'be_users', 'bu', 'sl.userid = bu.uid')
            ->leftJoin('sl', 'tt_content', 'tt', 'sl.recuid = tt.uid')
            ->leftJoin('sl', 'pages', 'p', 'sl.recuid = p.uid')
            ->leftJoin('sl', 'pages', 'ptt', 'tt.pid = ptt.uid')
            ->andWhere('channel = "content" OR channel = "pages"')
            ->setMaxResults(20)
            ->orderBy('sl.tstamp', 'DESC')
            ->executeQuery()
            ->fetchAllAssociative();
    }
}
