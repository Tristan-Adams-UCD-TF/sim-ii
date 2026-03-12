<?php
/*
sim-ii

Copyright (C) 2024  VetSim, Cornell University College of Veterinary Medicine Ithaca, NY

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program. If not, see <http://www.gnu.org/licenses/>
*/

	// ajaxGetEyeControlContent.php: AJAX call to fetch the modal for Eye control

	// init
	require_once("../init.php");
	$returnVal = array();

	// is user logged in
	if(adminClass::isUserLoggedIn() === FALSE) {
		$returnVal['status'] = AJAX_STATUS_LOGIN_FAIL;
		echo json_encode($returnVal);
		exit();
	}

	$side = dbClass::valuesFromPost('side');
	if($side == '' || ($side != 'left' && $side != 'right') ) {
		$side = 'right';
	}

	// Get current values from POST
	$state = dbClass::valuesFromPost('state');
	$lid = dbClass::valuesFromPost('lid');
	$move = dbClass::valuesFromPost('move');
	$position = dbClass::valuesFromPost('position');
	$blink = dbClass::valuesFromPost('blink');
	$pupil = dbClass::valuesFromPost('pupil');
	$plr = dbClass::valuesFromPost('plr');
	$menace = dbClass::valuesFromPost('menace');
	$palpebral = dbClass::valuesFromPost('palpebral');
	$nystagmus = dbClass::valuesFromPost('nystagmus');

	// State options
	$stateOptions = array(
		"Normal" => 0,
		"Obtunded" => 1,
		"Stuporous" => 2,  // aka "Miotic"
		"Comatose" => 3    // aka "Dilated"
	);

	$stateSelect = '<select id="eye-state-select" class="modal-select">';
	foreach($stateOptions as $label => $value) {
		$selected = ($state == $value) ? ' selected="selected"' : '';
		$stateSelect .= '<option value="' . $value . '"' . $selected . '>' . $label . '</option>';
	}
	$stateSelect .= '</select>';

	// Lid options
	$lidOptions = array(
		"Open" => 0,
		"Partial" => 1,
		"Closed" => 2
	);

	$lidSelect = '<select id="eye-lid-select" class="modal-select">';
	foreach($lidOptions as $label => $value) {
		$selected = ($lid == $value) ? ' selected="selected"' : '';
		$lidSelect .= '<option value="' . $value . '"' . $selected . '>' . $label . '</option>';
	}
	$lidSelect .= '</select>';

	// Movement options
	$moveOptions = array(
		"Normal" => 0,
		"Infrequent Slow" => 1,
		"None" => 2
	);

	$moveSelect = '<select id="eye-move-select" class="modal-select">';
	foreach($moveOptions as $label => $value) {
		$selected = ($move == $value) ? ' selected="selected"' : '';
		$moveSelect .= '<option value="' . $value . '"' . $selected . '>' . $label . '</option>';
	}
	$moveSelect .= '</select>';

	// Position options
	$posOptions = array(
		"Center" => 0,
		"Right" => 1,
		"Left" => 2,
		"Up" => 3,
		"Down" => 4
	);

	$posSelect = '<select id="eye-position-select" class="modal-select">';
	foreach($posOptions as $label => $value) {
		$selected = ($position == $value) ? ' selected="selected"' : '';
		$posSelect .= '<option value="' . $value . '"' . $selected . '>' . $label . '</option>';
	}
	$posSelect .= '</select>';

	// Blink options
	$blinkOptions = array(
		"Normal" => 0,
		"Infrequent Slow" => 1,
		"Partial Infrequent Slow" => 2,
		"None" => 3
	);

	$blinkSelect = '<select id="eye-blink-select" class="modal-select">';
	foreach($blinkOptions as $label => $value) {
		$selected = ($blink == $value) ? ' selected="selected"' : '';
		$blinkSelect .= '<option value="' . $value . '"' . $selected . '>' . $label . '</option>';
	}
	$blinkSelect .= '</select>';

	// PLR options (PlrResponse: 0=Normal, 1=Partial, 2=None)
	$plrOptions = array(
		"Normal" => 0,
		"Partial" => 1,
		"None" => 2
	);

	$plrSelect = '<select id="eye-plr-select" class="modal-select">';
	foreach($plrOptions as $label => $value) {
		$selected = ($plr == $value) ? ' selected="selected"' : '';
		$plrSelect .= '<option value="' . $value . '"' . $selected . '>' . $label . '</option>';
	}
	$plrSelect .= '</select>';

	// Menace options (BlinkEvent: 0=None, 1=Normal, 2=SlowPartial)
	$menaceOptions = array(
		"Normal" => 1,
		"Slow Partial" => 2,
		"None" => 0
	);

	$menaceSelect = '<select id="eye-menace-select" class="modal-select">';
	foreach($menaceOptions as $label => $value) {
		$selected = ($menace == $value) ? ' selected="selected"' : '';
		$menaceSelect .= '<option value="' . $value . '"' . $selected . '>' . $label . '</option>';
	}
	$menaceSelect .= '</select>';

	// Palpebral options (BlinkEvent: same as menace)
	$palpebralSelect = '<select id="eye-palpebral-select" class="modal-select">';
	foreach($menaceOptions as $label => $value) {
		$selected = ($palpebral == $value) ? ' selected="selected"' : '';
		$palpebralSelect .= '<option value="' . $value . '"' . $selected . '>' . $label . '</option>';
	}
	$palpebralSelect .= '</select>';

	// Nystagmus options (NystagmusResponse: 0=Normal, 1=Slow, 2=Very Slow, 3=None)
	$nystagmusOptions = array(
		"Normal"    => 0,
		"Slow"      => 1,
		"Very Slow" => 2,
		"None"      => 3
	);

	$nystagmusSelect = '<select id="eye-nystagmus-select" class="modal-select">';
	foreach($nystagmusOptions as $label => $value) {
		$selected = ($nystagmus == $value) ? ' selected="selected"' : '';
		$nystagmusSelect .= '<option value="' . $value . '"' . $selected . '>' . $label . '</option>';
	}
	$nystagmusSelect .= '</select>';

	// Default pupil value
	if($pupil == '' || $pupil < 5 || $pupil > 90) {
		$pupil = 70;
	}

	$content = '
		<h1 id="modal-title">' . ucfirst($side) . ' Eye Control</h1>
		<hr class="modal-divider">

		<div class="control-modal-div">
			<h2 class="modal-section-title">Neurological State</h2>
			<div class="eye-state-controls">
				' . $stateSelect . '
			</div>
		</div>

		<div class="eye-col-container">
			<div class="eye-two-col">
				<div class="eye-col">
					<h2 class="modal-section-title">Direct Outputs</h2>
					<div class="eye-col-row"><span class="eye-col-label">Lid:</span>' . $lidSelect . '</div>
					<div class="eye-col-row"><span class="eye-col-label">Movement:</span>' . $moveSelect . '</div>
					<div class="eye-col-row"><span class="eye-col-label">Position:</span>' . $posSelect . '</div>
					<div class="eye-col-row"><span class="eye-col-label">Blink:</span>' . $blinkSelect . '</div>
				</div>
				<div class="eye-col">
					<h2 class="modal-section-title">Input Responses</h2>
					<div class="eye-col-row"><span class="eye-col-label">PLR:</span>' . $plrSelect . '</div>
					<div class="eye-col-row"><span class="eye-col-label">Menace:</span>' . $menaceSelect . '</div>
					<div class="eye-col-row"><span class="eye-col-label">Palpebral:</span>' . $palpebralSelect . '</div>
					<div class="eye-col-row"><span class="eye-col-label">Nystagmus:</span>' . $nystagmusSelect . '</div>
				</div>
			</div>
		</div>

		<hr class="modal-divider">
		<div class="control-modal-div eye-pupil-control">
			<p id="pupil-title">Pupil Size: <span id="pupil-value">' . $pupil . '</span>%</p>
			<div value="' . $pupil . '" id="pupil-slider" class="control-slider-1" data-highlight="true"></div>
			<div class="clearer"></div>
		</div>

		<hr class="modal-divider">
		<div class="control-modal-div eye-button-row">
			<button class="red-button modal-button apply">Apply</button>
			<button class="red-button modal-button sync">Apply Both</button>
			<button class="red-button modal-button cancel">Close</button>
		</div>
	';

	$returnVal['status'] = AJAX_STATUS_OK;
	$returnVal['html'] = $content;
	$returnVal['side'] = $side;
	$returnVal['pupil'] = $pupil;
	echo json_encode($returnVal);
	exit();

?>
