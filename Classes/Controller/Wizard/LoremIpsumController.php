<?php
namespace SJBR\LoremIpsum\Controller\Wizard;
/***************************************************************
*  Copyright notice
*
*  (c) 1999-2004 Kasper Skaarhoj <kasper@typo3.com>
*  (c) 2013 Stanislas Rolland <typo3@sjbr.ca>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*  A copy is found in the textfile GPL.txt and important notices to the license
*  from the author is found in LICENSE.txt distributed with these scripts.
*
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * Lorem Ipsum dummy text wizard
 */
class LoremIpsumController {

	protected $str_loremIpsum = 'Lorem ipsum dolor sit amet.';
	protected $str_loremIpsum_extended = 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit.';
	protected $LRfileMap = array(
		0 => 'Resources/Private/LoremIpsum/Text/lorem_ipsum.txt',
		1 => 'Resources/Private/LoremIpsum/Text/lorem_ipsum.ru.txt',
		2 => 'Resources/Private/LoremIpsum/Text/lorem_ipsum.cn.txt'
	);
	protected $LRSentenceEndMap = array(
		0 => '.',
		1 => '.',
		2 => '。'
	);
	protected $loremIpsumSource = array();
	protected $lindex = '';
	protected $paragraphSentences = 5;
	protected $LRfile = 'Resources/Private/LoremIpsum/Text/lorem_ipsum.txt';
	protected $LRSentenceEnd = '.';
	protected $backPath = '';

	/**
	 * Main function for TCEforms wizard.
	 *
	 * @param array	 $PA: parameter array for "userFunc" wizard type
	 * @param object $pObj:	parent object
	 * @return string HTML for the wizard
	 */
	public function main($PA, $pObj) {

		// Detect proper LR file source
		$this->setLRfile($PA);

		// Load Lorem Ipsum sources from text file
		$this->loadLoremIpsumArray();

		switch ($PA['params']['type']) {
			case 'title':
			case 'header':
			case 'description':
			case 'word':
			case 'paragraph':
			case 'loremipsum':
				$onclick = $this->getHeaderTitleJS(
								"document." . $PA['formName'] . "['" . $PA['itemName'] . "'].value",
								$PA['params']['type'],
								$PA['params']['endSequence'],
								$PA['params']['add'],
								\TYPO3\CMS\Core\Utility\MathUtility::forceIntegerInRange($PA['params']['count'], 2, 100, 10),
								"document." . $PA['formName'] . "['" . $PA['itemName'] . "']"
							) . ';' .
							implode('', $PA['fieldChangeFunc']) .		// Necessary to tell TCEforms that the value is updated.
							'return false;';

				$output .= '<a href="#" onclick="' . htmlspecialchars($onclick) . '">' .
							$this->getIcon($PA['params']['type'], $this->backPath) .
							'</a>';
				break;
		}
		return $output;
	}

	/**
	 * Create rotating Lipsum text for JS variable
	 * Can be used by other non TCEform fields as well.
	 *
	 * @param string $varName: JavaScript variable name, eg. a form field value property reference
	 * @param string $type: key from $this->lindex array
	 * @param string $endSequence: list of character numbers to end sequence with
	 * @param integer Number of options to cycle through
	 * @param integer $count: number of texts to cycle through
	 * @param string $varElement: reference to the form field
	 * @return string JavaScript applying a lipsum string to input javascript variable
	 */
	protected function getHeaderTitleJS($varName, $type, $endSequence = '', $add = FALSE, $count = 10, $varElement = '') {

		// Load data
		$this->loadLoremIpsumArray();

		// Type must exist
		if (is_array($this->lindex[$type])) {

			// Shuffle index
			shuffle($this->lindex[$type]);

			// Init vars: Creates pointer. Currently random based on time value. Can it be cycling through 0-$count instead?
			if ($type == 'loremipsum') {
				$code = "
				var lipsum_temp_pointer = 0;
				var lipsum_temp_strings = new Array();";
			} else {
				$code = "
				var lipsum_temp_date = new Date();
				var lipsum_temp_pointer = lipsum_temp_date.getTime()%".$count.";
				var lipsum_temp_strings = new Array();";
			}

			// End-sequence
			$chars = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(',', $endSequence, 1);
			$addJS = '';
			foreach ($chars as $charVal) {
				if (intval($charVal) >0 ) {
					$addJS .= "+unescape('" . rawurlencode(chr(intval($charVal))) . "')";
				}
			}

			// Add Lipsum content to array
			for ($a = 0; $a < $count; $a++) {
				$code .= "
				lipsum_temp_strings[" . $a . "]='" . $this->lindex[$type][$a] . "'" . $addJS . ";";
			}

			// Set variable value
			$code .= "
				" . $varName . ($add ? '+' : '') . "=lipsum_temp_strings[lipsum_temp_pointer];
			";
				
			// Hook for insertion into RTE
			if ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['lorem_ipsum']['RTE_insert'] && $varElement) {
				$_params = array (
					'element' => &$varElement,
				);
				foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['lorem_ipsum']['RTE_insert'] as $_funcRef) {
					if ($_funcRef) {
						$code .= \TYPO3\CMS\Core\Utility\GeneralUtility::callUserFunction($_funcRef, $_params, $this);
					}
				}
			}
			return $code;
		}
	}

	/**
	 * Return icon for Lipsum Wizard
	 *
	 * @param string $type: see keyword list in other functions
	 * @param string $backPath
	 * @return string Text/Icon
	 */
	protected function getIcon($type, $backPath = '') {
		switch ($type) {
			case 'loremipsum':
				return '<img src="' . $backPath . \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('lorem_ipsum') . 'Resources/Public/Images/lorem_ipsum.gif" style="margin-left:2px;margin-top:3px;" title="' . $this->str_loremIpsum . '..." alt="' . $this->str_loremIpsum . '..." />';
				break;
			default:
				$imageTitle = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('insertDummyContent', 'LoremIpsum');
				return '<img src="' . $backPath . \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('lorem_ipsum') . 'Resources/Public/Images/lipsum.gif" style="margin-left:2px;margin-top:3px;" title="' . $imageTitle . '" alt="' . $imageTitle . '" />';
				break;
		}
	}

	/**
	 * Initialize LoremIpsum sources
	 *
	 * @return void
	 */
	protected function loadLoremIpsumArray() {

		if (!is_array($GLOBALS['T3_VAR']['ext']['lorem_ipsum'][$this->LRfile]['lindex'])) {
			// Init
			$pCounter = 0;

			// Load text
			$lipsumText = \TYPO3\CMS\Core\Utility\GeneralUtility::getUrl(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('lorem_ipsum') . $this->LRfile);
			$lipsumText = preg_replace('/[' . CR . LF . ']/u', '', $lipsumText);

			// Split into sentencies
			$this->loremIpsumSource = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode($this->LRSentenceEnd, $lipsumText, 1);

			// Make unique and sort
			$this->loremIpsumSource = array_unique($this->loremIpsumSource);
			sort($this->loremIpsumSource);

			// Create index of title/header/sentence length strings
			$this->lindex = array(
				'title' => array(),
				'header' => array(),
				'description' => array(),
				'word' => array(),
				'paragraph' => array(),
				'loremipsum' => array($this->str_loremIpsum)
			);
			foreach ($this->loremIpsumSource as $lipsumStr) {
				if (in_array('mbstring', get_loaded_extensions())) {
					$strlen = mb_strlen($lipsumStr);
				} else {
					$strlen = strlen($lipsumStr);
				}
				if ($strlen < 20) {
					$this->lindex['title'][] = $lipsumStr;
					$this->lindex['word'][] = preg_replace('/^.*[ ]([[:alnum:]]+)$/u', '\1', $lipsumStr);
				} else if ($strlen < 60) {
					$this->lindex['header'][] = $lipsumStr;
				} else {
					$this->lindex['description'][] = $lipsumStr;
					$this->lindex['paragraph'][round($pCounter/$this->paragraphSentences)] .= $lipsumStr . $this->LRSentenceEnd . ' ';
					$pCounter++;
				}
			}

			$this->lindex['word'] = array_unique($this->lindex['word']);

			$GLOBALS['T3_VAR']['ext']['lorem_ipsum'][$this->LRfile]['lindex'] = $this->lindex;
		} else {
			$this->lindex = $GLOBALS['T3_VAR']['ext']['lorem_ipsum'][$this->LRfile]['lindex'];
		}
	}

	/**
	 * Set Lorem Ipsum source file.
	 *
	 * @param array	$PA: input array
	 * @return void
	 */
	protected function setLRfile(&$PA) {
		if ($GLOBALS['TCA'][$PA['table']] && $GLOBALS['TCA'][$PA['table']]['ctrl']['languageField']) {
			$lField = $GLOBALS['TCA'][$PA['table']]['ctrl']['languageField'];
			$lValue = intval($PA['row'][$lField]);
			if ($lValue > 0) {
				list($row) = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('*','sys_language', 'uid=' . $lValue);
				if (isset($this->LRfileMap[$row['tx_loremipsum_type']])) {
					$this->LRfile = $this->LRfileMap[$row['tx_loremipsum_type']];
				}
				$this->LRSentenceEnd = $this->LRSentenceEndMap[$row['tx_loremipsum_type']];
			}
		}
	}
}

?>