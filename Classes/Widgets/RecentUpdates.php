<?php

declare(strict_types=1);

namespace Xima\XimaTypo3RecentUpdatesWidget\Widgets;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Dashboard\Widgets\ButtonProviderInterface;
use TYPO3\CMS\Dashboard\Widgets\ListDataProviderInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetConfigurationInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetInterface;
use TYPO3\CMS\Fluid\View\StandaloneView;

class RecentUpdates implements WidgetInterface
{
    protected ServerRequestInterface $request;

    public function __construct(
        protected readonly WidgetConfigurationInterface $configuration,
        protected readonly ListDataProviderInterface $dataProvider,
        protected readonly ?ButtonProviderInterface $buttonProvider = null,
        protected array $options = []
    ) {
    }

    public function renderWidgetContent(): string
    {
        $template = GeneralUtility::getFileAbsFileName('EXT:xima_typo3_recent_updates_widget/Resources/Private/Templates/List.html');

        // preparing view
        $view = GeneralUtility::makeInstance(StandaloneView::class);
        $view->setFormat('html');
        $view->setTemplateRootPaths(['EXT:xima_typo3_recent_updates_widget/Resources/Private/Templates/']);
        $view->setPartialRootPaths(['EXT:xima_typo3_recent_updates_widget/Resources/Private/Partials/']);
        $view->setTemplatePathAndFilename($template);

        $view->assignMultiple([
            'configuration' => $this->configuration,
            'records' => $this->dataProvider->getItems(),
            'button' => $this->buttonProvider,
            'options' => $this->options,
        ]);
        return $view->render();
    }

    public function getOptions(): array
    {
        return $this->options;
    }
}
