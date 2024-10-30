<?php
class mensio_Deactivator {
	private function DeactivatePlugin() {
		global $wpdb;
		$wpdb->update( $wpdb->prefix.'options',
			array( 'option_value' => 'Deactivated' ),
			array( 'option_name' => 'mensio_installed', 'option_value' => 'Active' ),
			array( '%s' )
		);
	}
	public function deactivate() {
		$this->DeactivatePlugin();
	}
}
