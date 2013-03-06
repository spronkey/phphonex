<?php
namespace Spronkware\PHPhonex;

/**
 * Phonex Algorithm class for calculating four character code used to identify equivalent names.
 *
 * The algorithm is described in a paper from the University of Utah, and is a combination
 * of the Soundex and Metaphone algorithms. Phonex is further customised toward each specific language,
 * in this case English.
 *
 * http://www.cs.utah.edu/contest/2005/NameMatching.pdf
 * 
 * @author Keith Humm <www.github.com/spronkey>
 * @version 0.1
 * @date March 6, 2013
 * @copyright 
 */
class PHPhonex
{
	protected static function isVowelOrY($char)
	{
		return (
			($char == 'a') ||
			($char == 'e') ||
			($char == 'i') ||
			($char == 'o') ||
			($char == 'u') ||
			($char == 'y')
		);
	}

	/**
	 * Calculates the Phonex Algorithm four-character code for the supplied string.
	 *
	 * @param string $name String to calculate - strip unwanted chars i.e. newlines *before* passing
	 * @return string Four-character Phonex code i.e. B302
	 */
	public static function phonex($name)
	{
		// PREPROCESSING
		// Perform all ops on lowercase name
		$name = mb_strtolower($name);

		// 1: Remove all trailing S characters. Using preg_ here vs rtrim for multibyte safety
		$name = preg_replace('/(s+)$/u', '', $name);

		/*
		 2: Convert leading letter pairs:
			KN -> N
			WR -> R
			PH -> F
		*/
		$match =	array('/^kn/u',	'/^wr/u',	'/^ph/u');
		$replace =	array('n',		'r',		'f');
		$name = preg_replace($match, $replace, $name);
		unset($match, $replace);

		/*
		 3: Convert leading single letters:
			H -> remove
			E I O U Y -> A
			K Q -> C
			P -> B
			J -> G
			V -> Z
			F -> S
		*/
		// first do multiples with regex
		$replaced = false;
		$replacements =  array(
			'/^h/u' 			=>	'',
			'/^(e|i|o|u|y)/u'	=>	'a',
			'/^(k|q)/u'			=>	'c',
			'/^p/u'				=>	'b',
			'/^j/u'				=>	'g',
			'/^v/u'				=>	'f',
			'/^z/u'				=>	's',
		);
		foreach($replacements as $match => $replace) {
			$name = preg_replace($match, $replace, $name, 1);
		}
		unset($replacements);

		// CODING RULES
		/*
		 1: Retain first letter of the name, drop all occurrences of A E H I O U W Y in other positions
		 2: Assign following numbers to remaining letters after first:
			B F P V -> 1
			C G J K Q S X Z -> 2
			D T -> 3 (if not followed by C)
			L -> 4 (if followed by vowel or end of name)
			M N -> 5 (Ignore next letter if either D or G)
			R -> 6 (if followed by vowel or end of name)
		 3: Ignore the current letter if it has the same code digit as the last character of the code
		 */
		$lookup = array(
			'b' => 1, 'f' => 1, 'p' => 1, 'v' => 1,
			'c' => 2, 'g' => 2, 'j' => 2, 'k' => 2, 'q' => 2, 's' => 2, 'x' => 2, 'z' => 2,
			'd' => 3, 't' => 3,
			'l' => 4,
			'm' => 5, 'n' => 5,
			'r' => 6
		);

		// split into character array
		$ca = preg_split('//u', $name, -1, PREG_SPLIT_NO_EMPTY);
		$lastcode = null;
		$count = 0;
		$output = array();
		for($i = 0; $i < count($ca); $i++)
		{
			if(isset($lookup[$ca[$i]])) {
				$code = $lookup[$ca[$i]];
			} else {
				$code = 0;
			}
			
			$outCode = null;
			$atEnd = (count($ca) - $i <= 1);

			// check lookahead rules
			switch($code) {
				// D T -> 3 - if not followed by C
				case 3:
					if($atEnd || $ca[$i+1] != 'c') {
						$outCode = $code;
					}
					break;
				// L -> 4 - if followed by vowel or end of name
				case 4:
					if($atEnd || static::isVowelOrY($ca[$i+1])) {
						$outCode = $code;
					}
					break;
				// M N -> 5 (Ignore next letter if either D or G)
				case 5:
					if(!$atEnd && ($ca[$i+1] == 'd' || $ca[$i+1] == 'g')) {
						$ca[$i+1] = $ca[$i]; // override next to current and it will be ignored
					}
					$outCode = $code;
					break;
				// R -> 6 (if followed by vowel or end of name)
				case 6:
					if($atEnd || static::isVowelOrY($ca[$i+1])) {
						$outCode = $code;
					}
					break;
				default:
					$outCode = $code;
					break;
			}

			// check not the same as last code used, don't include vowels (0), and not the first char
			if(	$lastcode != $outCode &&
				isset($code) && $outCode != 0 &&
				$i != 0)
			{
				$output[] = $code;
			}
			if(count($output) > 0) {
				$lastcode = $output[count($output)-1];
			}

			// first case - output the letter, and set code to whatever it calculated as
			if($i == 0) {
				$output[] = $ca[$i];
				$lastcode = 0;//$outCode;
				continue;
			}
		}
		/*
		 4: Convert to the form Letter Digit Digit Digit by:
		 	- adding trailing zeros (if there are less than 3 digits), or
		 	- dropping the rightmost digits if there are more than 3
		 */
		 $code = '';
		 for($i = 0; $i < 4; $i++) {
		 	if(isset($output[$i])) {
		 		$code .= $output[$i];
		 	} else {
		 		$code .= 0;
		 	}
		 }
		 unset($output);

		 return ucfirst($code);
	}
}