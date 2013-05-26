<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

if (TYPO3_MODE == 'BE') {

	// Create wizard configuration
	$wizConfig = array(
		'type' => 'userFunc',
		'userFunc' => 'SJBR\LoremIpsum\Controller\Wizard\LoremIpsumController->main',
		'params' => array()
	);

	// Load affected tables (except "pages")
	$typo3Version = \TYPO3\CMS\Core\Utility\VersionNumberUtility::convertVersionNumberToInteger(TYPO3_version);
	if ($typo3Version < 6001000) {
		\TYPO3\CMS\Core\Utility\GeneralUtility::loadTCA('tt_content');
		\TYPO3\CMS\Core\Utility\GeneralUtility::loadTCA('sys_file_reference');
		\TYPO3\CMS\Core\Utility\GeneralUtility::loadTCA('pages_language_overlay');
		\TYPO3\CMS\Core\Utility\GeneralUtility::loadTCA('sys_language');
	}

	// *********************
	// Apply wizards to:
	// *********************

	// Titles
	$GLOBALS['TCA']['pages']['columns']['title']['config']['wizards']['tx_loremipsum'] =
	$GLOBALS['TCA']['pages']['columns']['nav_title']['config']['wizards']['tx_loremipsum'] =
	$GLOBALS['TCA']['pages_language_overlay']['columns']['title']['config']['wizards']['tx_loremipsum'] =
	$GLOBALS['TCA']['pages_language_overlay']['columns']['nav_title']['config']['wizards']['tx_loremipsum'] =
	$GLOBALS['TCA']['sys_file_reference']['columns']['title']['config']['wizards']['tx_loremipsum'] =
	$GLOBALS['TCA']['sys_file_reference']['columns']['alternative']['config']['wizards']['tx_loremipsum'] =
		array_merge($wizConfig, array('params' => array(
			'type' => 'title'
		)));

	// Subheaders
	$GLOBALS['TCA']['pages']['columns']['subtitle']['config']['wizards']['tx_loremipsum'] =
	$GLOBALS['TCA']['pages_language_overlay']['columns']['subtitle']['config']['wizards']['tx_loremipsum'] =
	$GLOBALS['TCA']['tt_content']['columns']['header']['config']['wizards']['tx_loremipsum'] =
	$GLOBALS['TCA']['tt_content']['columns']['subheader']['config']['wizards']['tx_loremipsum'] =
		array_merge($wizConfig, array('params' => array(
			'type' => 'header'
		)));

	// Description / Abstract
	$GLOBALS['TCA']['pages']['columns']['description']['config']['wizards']['tx_loremipsum'] =
	$GLOBALS['TCA']['pages']['columns']['abstract']['config']['wizards']['tx_loremipsum'] =
	$GLOBALS['TCA']['pages_language_overlay']['columns']['description']['config']['wizards']['tx_loremipsum'] =
	$GLOBALS['TCA']['pages_language_overlay']['columns']['abstract']['config']['wizards']['tx_loremipsum'] =
	$GLOBALS['TCA']['tt_content']['columns']['imagecaption']['config']['wizards']['tx_loremipsum'] =
	$GLOBALS['TCA']['sys_file_reference']['columns']['description']['config']['wizards']['tx_loremipsum'] =
		array_merge($wizConfig, array('params' => array(
			'type' => 'description',
			'endSequence' => '46,32',
			'add' => TRUE
		)));

	// Keywords field
	$GLOBALS['TCA']['pages']['columns']['keywords']['config']['wizards']['tx_loremipsum'] =
	$GLOBALS['TCA']['pages_language_overlay']['columns']['keywords']['config']['wizards']['tx_loremipsum'] =
		array_merge($wizConfig, array('params' => array(
			'type' => 'word',
			'endSequence' => '44,32',
			'add' => TRUE,
			'count' => 30
		)));

	// Bodytext field in Content Elements
	$GLOBALS['TCA']['tt_content']['columns']['bodytext']['config']['wizards']['_VERTICAL'] = 1;
	$GLOBALS['TCA']['tt_content']['columns']['bodytext']['config']['wizards']['tx_loremipsum_2'] =
		array_merge($wizConfig, array('params' => array(
			'type' => 'loremipsum',
			'endSequence' => '32',
			'add'=> TRUE
		)));
	$GLOBALS['TCA']['tt_content']['columns']['bodytext']['config']['wizards']['tx_loremipsum'] =
		array_merge($wizConfig, array('params' => array(
			'type' => 'paragraph',
			'endSequence' => '10',
			'add'=> TRUE
		)));

	// Adding type selector to languages records
	$tempColumns = array(
		'tx_loremipsum_type' => array(
			'label' => 'LLL:EXT:lorem_ipsum/Resources/Private/Language/locallang.xlf:selectDummyContentType',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array('LLL:EXT:lorem_ipsum/Resources/Private/Language/locallang.xlf:traditionalLoremIpsum', '0'),
					array('LLL:EXT:lorem_ipsum/Resources/Private/Language/locallang.xlf:russianSampleText', '1'),
					array('LLL:EXT:lorem_ipsum/Resources/Private/Language/locallang.xlf:chineseSampleText', '2'),
				),
				'default' => '0'
			)
		),
	);
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('sys_language', $tempColumns, 1);
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('sys_language', 'tx_loremipsum_type');

	// CSH:
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('xEXT_loremipsum', 'EXT:lorem_ipsum/Resources/Private/Language/locallang_csh.xlf');
}
?>
