<?php 
class LoopSkinHooks {
	
	public static function onSkinEditSectionLinks(Skin $skin, Title $title, $section, $tooltip, &$result, $lang	) {
		
		$result['editsection']['text'] = '<i class="ic ic-edit"></i>';
		return true;
	}
	
}