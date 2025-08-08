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
        if ($record === null || $record === []) {
            return '';
        }

        return isset($record[$GLOBALS['TCA'][$table]['ctrl']['label']]) ? (string)$record[$GLOBALS['TCA'][$table]['ctrl']['label']] : '';
    }
}
