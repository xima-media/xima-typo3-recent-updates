<?php

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

namespace Xima\XimaTypo3RecentUpdates\Domain\Model\Dto;

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use Xima\XimaTypo3RecentUpdates\Utility\RecordUtility;

final class ListItem
{
    public array $log = [];

    public static function createFromV11Log(array $sysLogRow): static
    {
        $item = new ListItem();
        $item->log = $sysLogRow;
        $item->log['log_data'] = unserialize($item->log['log_data']);
        $item->log['title'] = $item->log['log_data'][0] ?? RecordUtility::getRecordTitle($item->log['log_data']['table'], $item->log['log_data']['uid']);
        return $item;
    }

    public static function createFromV12Log(array $sysLogRow): static
    {
        $item = new ListItem();
        $item->log = $sysLogRow;
        $item->log['log_data'] = json_decode($item->log['log_data'], true);
        $item->log['title'] = $item->log['log_data']['title'] ?? RecordUtility::getRecordTitle($item->log['log_data']['table'], $item->log['log_data']['uid']);
        return $item;
    }

    public function getDetails(): string
    {
        return str_replace(['{', '}'], '', str_replace(array_keys($this->log['log_data']), $this->log['log_data'], $this->log['details']));
    }

    public function getType(): string|null
    {
        $table = $this->log['tablename'];
        $cType = $this->log['cType'];

        if ($table !== 'tt_content' || $cType === '') {
            $label = $GLOBALS['TCA'][$table]['ctrl']['title'];
        } elseif ($cType === 'list') {
            $label = $this->log['listType'];
        } else {
            $label = BackendUtility::getLabelFromItemlist('tt_content', 'CType', $cType);
        }
        return str_starts_with($label, 'LLL') ? LocalizationUtility::translate($label) : $label;
    }

    public function getTitle(): string
    {
        return self::truncate($this->log['title'] ?? '');
    }

    public static function truncate(string $string, int $length = 50, string $append = '&hellip;'): string
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
