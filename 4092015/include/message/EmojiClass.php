<?php 




class EmojiClass {
	
	/** EMOJI functions **/
	
	/**
	 * Replaces emoji codes with the corresponding html tag.
	 * @param string $value
	 * @return string
	 */
	function getEmojiHtml($value) {
		$html = preg_replace_callback('/\[i:\d{3}\]/', array($this,'replaceEmojiCode'), $value);
		// reconvert html special chars input
		return str_replace('&amp;#', '&#', $html);
	}
	
	/**
	 * Replaces emoji img tags with the corresponding code.
	 * @param string $value
	 * @return string
	 */
	function getEmojiCode($value) {
		return preg_replace_callback('/<img src="[\.\.]*\/img(s)*\/emoji\/\d{3}.gif"([^<])*>/', array($this,'replaceEmojiHtml'), $value);
	}
	
	
	/**
	 *
	 * @global string $blogImgUrl
	 * @param array $matches
	 * @return string
	 */
	function replaceEmojiCode($matches) {
	
		$code = substr($matches[0], 3, 3);
		$emoji = "/var/www/livechat/htdocs/imgs/emoji/$code.gif";
		if (file_exists($emoji)) {
			return "<img src='/imgs/emoji/$code.gif' />";
		} else {
			return "[i:$code]";
		}
	}
	
	/**
	 *
	 * @global string $blogImgUrl
	 * @param array $matches
	 * @return string
	 */
	function replaceEmojiHtml($matches) {
		$firstMatch = $matches[0];
	
		$code = substr($firstMatch, strpos($firstMatch, '/emoji/') + 7, 3);
	
		return '[i:' . $code. ']';
	}
	
	function hasEmoji($value) {
		$result = preg_replace_callback('/\[i:\d{3}\]/', array($this,'confirmEmoji'), $value);
		return $result;
	}
	
	function confirmEmoji($matches){
		$code = substr($matches[0], 3, 3);
		$emoji = "/var/www/livechat/htdocs/imgs/emoji/$code.gif";
		if (file_exists($emoji)) {
			return true;
		}
		return false;
	}
	
	
	
	
	
	
	
	
	
	
	
}

