<?php

namespace Xima\XimaRecentUpdatesWidget\Domain\Model\Dto;

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class ListItem
{
    public array $log = [];

    public static function createFromV11Log(array $sysLogRow): static
    {
        $item = new static();
        $item->log = $sysLogRow;
        $item->log['log_data'] = unserialize($item->log['log_data']);
        $item->log['title'] = $item->log['log_data'][0] ?? '';
        return $item;
    }

    public static function createFromV12Log(array $sysLogRow): static
    {
        $item = new static();
        $item->log = $sysLogRow;
        $item->log['log_data'] = json_decode($item->log['log_data']);
        return $item;
    }

    public function getDetails(): string
    {
        return str_replace(['{', '}'], '', str_replace(array_keys($this->log['log_data']), $this->log['log_data'], $this->log['details']));
    }

    public function getType(): string
    {
        $table = $this->log['tablename'];
        $cType = $this->log['cType'];

        if ($table !== 'tt_content' || $cType === '') {
            return $table;
        } elseif ($cType === 'list') {
            return $this->log['listType'];
        } else {
            $label = '';
            $CTypeLabels = [];
            $contentGroups = BackendUtility::getPagesTSconfig($this->log['pid'])['mod.']['wizards.']['newContentElement.']['wizardItems.'] ?? [];
            foreach ($contentGroups as $group) {
                foreach ($group['elements.'] as $element) {
                    $CTypeLabels[$element['tt_content_defValues.']['CType']] = $element['title'];
                }
            }
            if (isset($CTypeLabels[$cType])) {
                $label = $CTypeLabels[$cType];
            }
            return LocalizationUtility::translate($label);
        }
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
