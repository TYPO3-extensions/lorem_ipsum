<?php
namespace SJBR\LoremIpsum\Hook\Rtehtmlarea;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Stanislas Rolland <typo3(arobas)sjbr.ca>
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
 *  A copy is found in the text file GPL.txt and important notices to the license
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
 * Insert lorem ipsum content into the RTE in wysiwyg mode
 *
 */
class RteHtmlAreaLoremIpsumInsertHook {

	/**
	 * Returns Javascript code to do insertion in htmlArea RTE
	 *
	 * @param array $params: array with form element name
	 * @return string Javascript code
	 */
	public function loremIpsumInsert($params) {
		return '
				if (typeof(lorem_ipsum) == \'function\' && ' . $params['element'] . '.tagName.toLowerCase() == \'textarea\' ) lorem_ipsum(' . $params['element'] . ', lipsum_temp_strings[lipsum_temp_pointer]);
				';
	}
}
