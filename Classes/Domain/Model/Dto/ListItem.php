<?php

namespace Xima\XimaTypo3RecentUpdates\Domain\Model\Dto;

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

final class ListItem
{
    public array $log = [];

    public static function createFromV11Log(array $sysLogRow): static
    {
        $item = new ListItem();
        $item->log = $sysLogRow;
        $item->log['log_data'] = unserialize($item->log['log_data']);
        $item->log['title'] = $item->log['log_data'][0] ?? '';
        return $item;
    }

    public static function createFromV12Log(array $sysLogRow): static
    {
        $item = new ListItem();
        $item->log = $sysLogRow;
        $item->log['log_data'] = json_decode($item->log['log_data'], true);
        $item->log['title'] = $item->log['log_data']['title'] ?? '';
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
            return $table;
        }
        if ($cType === 'list') {
            return $this->log['listType'];
        }
        $label = '';
        $CTypeLabels = [];
        $contentGroups = BackendUtility::getPagesTSconfig($this->log['pageId'])['mod.']['wizards.']['newContentElement.']['wizardItems.'] ?? [];
        foreach ($contentGroups as $group) {
            foreach ($group['elements.'] as $element) {
                $CTypeLabels[$element['tt_content_defValues.']['CType']] = $element['title'];
            }
        }
        if (isset($CTypeLabels[$cType])) {
            $label = $CTypeLabels[$cType];
        }

        return str_starts_with($label, 'LLL') ? LocalizationUtility::translate($label) : $label;
    }

    public function getTitle(): string
    {
        return self::truncate($this->log['title'] ?? '');
    }

    public static function truncate(string $string, int $length=50, string $append='&hellip;'): string
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
