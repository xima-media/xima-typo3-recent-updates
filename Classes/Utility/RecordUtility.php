<?php

declare(strict_types=1);

namespace Xima\XimaTypo3RecentUpdates\Utility;

use TYPO3\CMS\Backend\Utility\BackendUtility;

class RecordUtility
{
    public static function getRecordTitle(?string $table = null, ?int $uid = null): ?string
    {
        if ($table === null || $uid === null) {
            return '';
        }

        $record = BackendUtility::getRecord($table, $uid);
        if (empty($record)) {
            return '';
        }

        return isset($record[$GLOBALS['TCA'][$table]['ctrl']['label']]) ? (string)$record[$GLOBALS['TCA'][$table]['ctrl']['label']] : '';
    }
}
