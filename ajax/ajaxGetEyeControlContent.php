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

	// State options
	$stateOptions = array(
		"Normal" => 0,
		"Obtunded" => 1,
		"Miotic" => 2,
		"Dilated" => 3
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
		"Partial Infrequent" => 2,
		"None" => 3
	);

	$blinkSelect = '<select id="eye-blink-select" class="modal-select">';
	foreach($blinkOptions as $label => $value) {
		$selected = ($blink == $value) ? ' selected="selected"' : '';
		$blinkSelect .= '<option value="' . $value . '"' . $selected . '>' . $label . '</option>';
	}
	$blinkSelect .= '</select>';

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
				<button id="eye-set-defaults" class="red-button modal-button-small">Set Defaults</button>
			</div>
		</div>

		<div class="control-modal-div">
			<h2 class="modal-section-title">Lid Position</h2>
			' . $lidSelect . '
		</div>

		<div class="control-modal-div">
			<h2 class="modal-section-title">Movement</h2>
			' . $moveSelect . '
		</div>

		<div class="control-modal-div">
			<h2 class="modal-section-title">Eye Position</h2>
			' . $posSelect . '
		</div>

		<div class="control-modal-div">
			<h2 class="modal-section-title">Blink Pattern</h2>
			' . $blinkSelect . '
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
