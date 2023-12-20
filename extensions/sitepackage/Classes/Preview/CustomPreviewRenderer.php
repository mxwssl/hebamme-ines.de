<?php

namespace Mxw\Sitepackage\Preview;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

use Psr\Log\LoggerAwareTrait;
use TYPO3\CMS\Backend\Preview\StandardContentPreviewRenderer;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Backend\View\BackendLayout\Grid\GridColumnItem;
use TYPO3\CMS\Backend\View\PageLayoutView;
use TYPO3\CMS\Backend\View\PageLayoutViewDrawFooterHookInterface;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class CustomPreviewRenderer.
 */
class CustomPreviewRenderer extends StandardContentPreviewRenderer
{
    use LoggerAwareTrait;

    /**
     * Menu content types defined by TYPO3.
     *
     * @var string[]
     */
    private const MENU_CONTENT_TYPES = [
        'menu_abstract',
        'menu_categorized_content',
        'menu_categorized_pages',
        'menu_pages',
        'menu_recently_updated',
        'menu_related_pages',
        'menu_section',
        'menu_section_pages',
        'menu_sitemap',
        'menu_sitemap_pages',
        'menu_subpages',
    ];

    public function renderPageModulePreviewContent(GridColumnItem $item): string
    {
        $out = '';
        $record = $item->getRecord();

        $contentTypeLabels = $item->getContext()->getContentTypeLabels();
        $languageService = $this->getLanguageService();
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);

        // Check if a Fluid-based preview template was defined for this CType
        // and render it via Fluid. Possible option:
        // mod.web_layout.tt_content.preview.media = EXT:site_mysite/Resources/Private/Templates/Preview/Media.html
        $infoArr = [];
        $this->getProcessedValue($item, 'header_position,header_layout,header_link', $infoArr);
        $tsConfig = BackendUtility::getPagesTSconfig($record['pid'])['mod.']['web_layout.']['tt_content.']['preview.'] ?? [];
        if (!empty($tsConfig[$record['CType']]) || !empty($tsConfig[$record['CType'] . '.'])) {
            $fluidPreview = $this->renderContentElementPreviewFromFluidTemplate($record);
            if ($fluidPreview !== null) {
                return $fluidPreview;
            }
        }

        // Draw preview of the item depending on its CType
        switch ($record['CType']) {
            case 'header':
                if ($record['subheader']) {
                    $out .= $this->linkEditContent($this->renderText($record['subheader']), $record) . '<br />';
                }
                break;
            case 'uploads':
                if ($record['media']) {
                    $out .= $this->linkEditContent($this->getThumbCodeUnlinked($record, 'tt_content', 'media'), $record) . '<br />';
                }
                break;
            case 'shortcut':
                if (!empty($record['records'])) {
                    $shortcutContent = [];
                    $recordList = explode(',', $record['records']);
                    foreach ($recordList as $recordIdentifier) {
                        $split = BackendUtility::splitTable_Uid($recordIdentifier);
                        $tableName = empty($split[0]) ? 'tt_content' : $split[0];
                        $shortcutRecord = BackendUtility::getRecord($tableName, $split[1]);
                        if (is_array($shortcutRecord)) {
                            $shortcutRecord = $this->translateShortcutRecord($record, $shortcutRecord, $tableName, (int) $split[1]);
                            $icon = $this->getIconFactory()->getIconForRecord($tableName, $shortcutRecord, Icon::SIZE_SMALL)->render();
                            $icon = BackendUtility::wrapClickMenuOnIcon(
                                $icon,
                                $tableName,
                                $shortcutRecord['uid'],
                                '1'
                            );
                            $shortcutContent[] = $icon
                                . htmlspecialchars(BackendUtility::getRecordTitle($tableName, $shortcutRecord));
                        }
                    }
                    $out .= implode('<br />', $shortcutContent) . '<br />';
                }
                break;
            case 'list':
                $hookOut = '';
                if (!empty($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['list_type_Info'])) {
                    $pageLayoutView = PageLayoutView::createFromPageLayoutContext($item->getContext());
                    $_params = ['pObj' => &$pageLayoutView, 'row' => $record];
                    foreach (
                        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['list_type_Info'][$record['list_type']] ??
                        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['list_type_Info']['_DEFAULT'] ??
                        [] as $_funcRef
                    ) {
                        $hookOut .= GeneralUtility::callUserFunction($_funcRef, $_params, $pageLayoutView);
                    }
                }

                if ((string) $hookOut !== '') {
                    $out .= $hookOut;
                } elseif (!empty($record['list_type'])) {
                    $label = BackendUtility::getLabelFromItemListMerged($record['pid'], 'tt_content', 'list_type', $record['list_type']);
                    if (!empty($label)) {
                        $out .= $this->linkEditContent('<p style="font-family:Menlo,Monaco,Consolas,\'Courier New\',monospace">' . htmlspecialchars($languageService->sL($label)) . '</p>', $record) . '<br />';
                    } else {
                        $message = sprintf($languageService->sL('LLL:EXT:core/Resources/Private/Language/locallang_core.xlf:labels.noMatchingValue'), $record['list_type']);
                        $out .= '<span class="label label-warning">' . htmlspecialchars($message) . '</span>';
                    }
                } else {
                    $out .= '<p style="font-family:Menlo,Monaco,Consolas,\'Courier New\',monospace">' . $languageService->getLL('noPluginSelected') . '</p>';
                }
                $out .= htmlspecialchars($languageService->sL(BackendUtility::getLabelFromItemlist('tt_content', 'pages', $record['pages']))) . '<br />';
                break;
            default:
                $contentTypeLabel = (string) ($contentTypeLabels[$record['CType']] ?? '');
                if ($contentTypeLabel === '') {
                    $message = sprintf(
                        $languageService->sL('LLL:EXT:core/Resources/Private/Language/locallang_core.xlf:labels.noMatchingValue'),
                        $record['CType']
                    );
                    $out .= '<span class="label label-warning">' . htmlspecialchars($message) . '</span>';
                    break;
                }
                // Handle menu content types
                if (in_array($record['CType'], self::MENU_CONTENT_TYPES, true)) {
                    $out .= $this->linkEditContent('<strong>' . htmlspecialchars($contentTypeLabel) . '</strong>', $record);
                    if ($record['CType'] !== 'menu_sitemap' && (($record['pages'] ?? false) || ($record['selected_categories'] ?? false))) {
                        // Show pages/categories if menu type is not "Sitemap"
                        $out .= ':' . $this->linkEditContent($this->generateListForMenuContentTypes($record), $record) . '<br />';
                    }
                    break;
                }
                $out .= $this->linkEditContent('<p style="font-family:Menlo,Monaco,Consolas,\'Courier New\',monospace">' . htmlspecialchars($contentTypeLabel) . '</p>', $record) . '<br />';
                if ($record['bodytext']) {
                    $out .= $this->linkEditContent($this->renderText($record['bodytext']), $record) . '<br />';
                }
                if ($record['image']) {
                    $out .= $this->linkEditContent($this->getThumbCodeUnlinked($record, 'tt_content', 'image'), $record) . '<br />';
                }
        }

        return $out;
    }

    /**
     * Render a footer for the record.
     */
    public function renderPageModulePreviewFooter(GridColumnItem $item): string
    {
        $content = '';
        $info = [];
        $record = $item->getRecord();
        $this->getProcessedValue(
            $item,
            'nav_title,starttime,endtime,fe_group,space_before_class,space_after_class',
            $info
        );

        if (
            !empty($GLOBALS['TCA']['tt_content']['ctrl']['descriptionColumn'])
            && !empty($record[$GLOBALS['TCA']['tt_content']['ctrl']['descriptionColumn']])
        ) {
            $info[] = htmlspecialchars($record[$GLOBALS['TCA']['tt_content']['ctrl']['descriptionColumn']]);
        }

        // Call drawFooter hooks
        if (!empty($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['tt_content_drawFooter'])) {
            $pageLayoutView = PageLayoutView::createFromPageLayoutContext($item->getContext());
            foreach ($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['tt_content_drawFooter'] ?? [] as $className) {
                $hookObject = GeneralUtility::makeInstance($className);
                if (!$hookObject instanceof PageLayoutViewDrawFooterHookInterface) {
                    throw new \UnexpectedValueException($className . ' must implement interface ' . PageLayoutViewDrawFooterHookInterface::class, 1582574541);
                }
                $hookObject->preProcess($pageLayoutView, $info, $record);
            }
            $item->setRecord($record);
        }

        if (!empty($info)) {
            $content = implode('<br>', $info);
        }

        if (!empty($content)) {
            $content = '<div class="t3-page-ce-footer">' . $content . '</div>';
        }

        return $content;
    }
}
