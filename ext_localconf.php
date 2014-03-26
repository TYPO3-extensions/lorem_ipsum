<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

// Configure Lorem Ipsum hook to insert nonsense in htmlArea RTE in wysiwyg mode
if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('rtehtmlarea') && TYPO3_MODE == 'BE') {
	$TYPO3_CONF_VARS['EXTCONF']['lorem_ipsum']['RTE_insert'][] = 'SJBR\\LoremIpsum\\Hook\\Rtehtmlarea\\RteHtmlAreaLoremIpsumInsertHook->loremIpsumInsert';
}
