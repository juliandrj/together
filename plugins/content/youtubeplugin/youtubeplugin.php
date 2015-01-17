<?php
/**
* @ 2013 Benj Golding. All rights reserved.
* @GNU/GPL licence
*
*/

// Assert file included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

/**
* YouTube Content Plugin
*
*/
class plgContentYoutubePlugin extends JPlugin
{

	/**
	* Ctor
	*
	* @param object $subject The object to observe
	* @param object $params The object that holds the plugin parameters
	*/
	function PluginYoutube( &$subject, $params )
	{
		parent::__construct( $subject, $params );
	}

	/**
	* Example prepare content method
	*
	* Method is called by the view
	*
	* @param object The article object. Note $article->text is also available
	* @param object The article params
	* @param int The 'page' number
	*/
	function onContentPrepare( $context, &$article, &$params, $page = 0)
		{
		global $mainframe;
		

		if ( JString::strpos( $article->text, '{youtube}' ) === false ) {
		return true;
		}
		
		$article->text = preg_replace('|{youtube}(.*){\/youtube}|e', '$this->embedVideo("\1")', $article->text);
		
			

		return true;
	
	}

	function embedVideo($vCode)
	{

	 	$params = $this->params;

		$width = $params->get('width', 100);
		$height = $params->get('height', 344);
	
		return '<iframe src="//www.youtube.com/embed/'.$vCode.'" allowfullscreen style="width: '.$width.'%; height: '.$height.'px; border: none;"></iframe>';
		//return '<object width="'.$width.'" height="'.$height.'"><param name="movie" value="http://www.youtube.com/v/'.$vCode.'"></param><param name="allowFullScreen" value="true"></param><embed src="http://www.youtube.com/v/'.$vCode.'" type="application/x-shockwave-flash" allowfullscreen="true" width="'.$width.'" height="'.$height.'"></embed></object>';
	}

}
