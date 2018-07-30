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
		
		?>
		<section>
			<div class="container">
				<div class="row col-12">
					<?php $this->html( 'bodytext' ); ?>
				</div>
			</div> 
		</section>
		<section>
			<div class="col-12 text-center">
				<div id="page-footer-links" class="text-center p-3">
						<?php $this->getUserMenu(); ?> |
						<a href="?action=edit">edit</a> |
						<a href="?action=purge">purge</a> |
						<a href="?debug=true">debug</a> |
						<a href="?debug=false"><span style="text-decoration:line-through;">debug</span></a> |
						<a href="https://www.oncampus.de/impressum">Impressum</a> 
				</div>
			</div>
			<?php $this->printTrail(); ?>
		</section>
	<?php 
	}
	
	function getUserMenu() {
		global $wgUser;
		
		$personTools = $this->getPersonalTools ();

		if($wgUser->isLoggedIn ()) {
			if (! $userName = $wgUser->getRealName ()) {
				$userName = $wgUser->getName ();
			}
			$loggedin = true;
		
			if (isset ( $personTools ['logout'] )) {
				echo '<a href="' . $personTools ['logout'] ['links'] [0] ['href'] . '"><span class="ic ic-logout pr-1"></span> ' . $personTools ['logout'] ['links'] [0] ['text'] . '</a>';
			}
			
		} else {
			
			$loggedin = false;
			
			if (isset ( $personTools ['login'] )) {
				echo '<a href="' . $personTools ['login'] ['links'] [0] ['href'] . '"><span class="ic ic-login pr-1"></span>  ' . $personTools ['login'] ['links'] [0] ['text'] . '</a>';
			}

		}

	}
}

?>