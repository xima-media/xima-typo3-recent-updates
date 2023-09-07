<?php

declare(strict_types=1);

namespace Xima\XimaRecentUpdatesWidget\Widgets\Provider;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Dashboard\Widgets\ListDataProviderInterface;
use Xima\XimaRecentUpdatesWidget\Domain\Model\Dto\ListItem;

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
