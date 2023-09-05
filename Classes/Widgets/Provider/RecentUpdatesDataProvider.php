<?php

declare(strict_types=1);

namespace Xima\XimaRecentUpdatesWidget\Widgets\Provider;

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Dashboard\Widgets\ListDataProviderInterface;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class RecentUpdatesDataProvider implements ListDataProviderInterface
{
    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function getItems(): array
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('sys_log');

        $results =  $queryBuilder
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
            $logData = json_decode($result['log_data'], true);
            $table = $logData['table'];

            if ($table !== 'tt_content' || $result['cType'] === '') {
                $type = $table;
            } elseif ($result['cType'] === 'list') {
                $type = $result['listType'];
            } else {
                $type = $this->getCTypeTranslationString($result['cType'], $result['pageId']);
            }

            $items[] = [
                'uid' => $result['uid'],
                'title' => $this->truncate($logData['title']),
                'table' => $logData['table'],
                'pageId' => $result['pageId'],
                'pageTitle' => $result['pageTitle'],
                'type' => $type,
                'updated' => $result['updated'],
                'userName' => $result['username'],
                'userId' => $result['userId'],
                'details' => str_replace(['{', '}'], '', str_replace(array_keys($logData), $logData, $result['details'])),
            ];
        }

        return $items;
    }

    protected function getCTypeTranslationString(string $key, int $pid): string
    {
        $label = '';
        $CTypeLabels = [];
        $contentGroups = BackendUtility::getPagesTSconfig($pid)['mod.']['wizards.']['newContentElement.']['wizardItems.'] ?? [];
        foreach ($contentGroups as $group) {
            foreach ($group['elements.'] as $element) {
                $CTypeLabels[$element['tt_content_defValues.']['CType']] = $element['title'];
            }
        }
        if (isset($CTypeLabels[$key])) {
            $label = $CTypeLabels[$key];
        }

        if (str_starts_with($label, 'LLL:')) {
            $label = LocalizationUtility::translate($label);
        }

        return $label;
    }

    protected function truncate(string $string, int $length=50, string $append='&hellip;'): string
    {
        $string = trim($string);

        if (strlen($string) > $length) {
            $string = wordwrap($string, $length);
            $string = explode("\n", $string, 2);
            $string = $string[0] . $append;
        }

        return $string;
    }
}
