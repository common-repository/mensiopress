<?php
class mensio_Activator {
	private function CheckIfFirstActivation() {
		$PgnState = '';
		global $wpdb;
		$name = 'mensio_installed';
		$query = 'SELECT * FROM '.$wpdb->prefix.'options WHERE option_name = %s';
		$DataRows = $wpdb->get_results($wpdb->prepare($query,$name));
		foreach ( $DataRows as $Data ) {
			$PgnState = $Data->option_value;
		}
		if ($PgnState == '') {
			$PgnState = 'NewInstall';
		} elseif ($PgnState == 'NewInstall') {
			$PgnState = 'ReInstall';
		}
		return $PgnState;
	}
	private function SetActivationSwitch ( $value ) {
		global $wpdb;
		switch ($value) {
			case 'NewInstall':
				$wpdb->insert( $wpdb->prefix.'options',
					array( 'option_name' => 'mensio_installed', 'option_value' => 'NewInstall' ),
					array( '%s', '%s' )
				);
				break;
			case 'Active':
				$wpdb->update( $wpdb->prefix.'options',
					array( 'option_value' => 'Active' ),
					array( 'option_name' => 'mensio_installed' ),
					array( '%s' )
				);
				break;
			case 'ReInstall':
				break;
		}
	}
	public function activate() {
		$PlgState = $this->CheckIfFirstActivation();
		switch ($PlgState) {
			case 'NewInstall':
				$this->SetActivationSwitch('NewInstall');
				$PlgState = 'FrmOptions';
				break;
			case 'Deactivated':
				$this->SetActivationSwitch('Active');
				$PlgState = 'FrmQuestion';
				break;
			case 'Active':
				break;
		}
		return $PlgState;
	}
}
