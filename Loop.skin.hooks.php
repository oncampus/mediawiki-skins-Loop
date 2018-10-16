<?php 
class LoopSkinHooks {
	
	/**
	 * Show edit links only in LoopEditMode
	 *
     * This is attached to the MediaWiki 'SkinEditSectionLinks' hook.
	 *
	 * @param Skin $skin 
	 * @param Title $title 
	 * @param string $section 
	 * @param string $tooltip 
     * @param array $result 
	 * @param Language $lang 
	 * @return bool true
	 */	
	public static function onSkinEditSectionLinks( Skin $skin, Title $title, $section, $tooltip, &$result, $lang ) {
		
		$loopeditmode = $skin->getUser()->getOption( 'loopeditmode', false, true );
		$looprendermode = $skin->getUser()->getOption( 'looprendermode' );
		
		if ( $loopeditmode && $looprendermode == "default" ) {
			$result[ 'editsection' ][ 'text' ] = '<span class="ic ic-edit"></span>';
		} else {
			$result[ 'editsection' ][ 'text' ] = '';
		}
		return true;
	}
	
	
}