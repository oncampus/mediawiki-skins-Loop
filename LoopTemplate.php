<?php
/**
 * BaseTemplate class for the Loop skin
 *
 * @ingroup Skins
 */
class LoopTemplate extends BaseTemplate {
	/**
	 * Outputs the entire contents of the page
	 */
	public function execute() {
		$this->html( 'headelement' );
		$this->html( 'bodycontent' );
		$this->printTrail();

		
	}

}

