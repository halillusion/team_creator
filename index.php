<?php
// definition
$title = 'Takım Oluşturucu v1.0';
$body = '';
if (isset($_POST['staff_number']) === false) {
	$body = '
	<div class="jumbotron">
		<p class="display-4">Kaç adamımız var?</p>
		<div class="form-group">
			<label for="staff_number">Adam Sayısı</label>
			<input type="number" class="form-control" name="staff_number" id="staff_number" />
		</div>
		<p class="lead">
			<button type="submit" class="btn btn-primary btn-lg">Tamam, gönder!</button>
		</p>
	</div>';
} elseif (isset($_POST['staffs']) !== false) {
	$totalStaff = count($_POST['staffs']);
	$staffs = [];
	foreach ($_POST['staffs'] as $staff) {
		$staffs[$staff['name']] = $staff['point'];
	}
	$_staffs = $staffs;
	$totalPoint = array_sum($staffs);
	$balance = round($totalPoint / 2);
	asort($staffs);
	$teams = [];

	$totalA = 0;
	$totalB = 0;
	for ($i=0; $i < $totalStaff; $i++) {
		
		if (($i % 2) == 0) {

			$popA = array_pop($staffs);
			$popB = array_pop($staffs);

			$totalA = $totalA + (is_null($popA) ? 0 : $popA);
			$totalB = $totalB + (is_null($popB) ? 0 : $popB);

			if ($totalA > $totalB) {

				if (! is_null($popA)) array_push($staffs, $popA);
				if (! is_null($popB)) array_push($staffs, $popB);
				$totalA = $totalA - (is_null($popA) ? 0 : $popA);
				$totalB = $totalB - (is_null($popB) ? 0 : $popB);

				$popB = array_pop($staffs);
				$popA = array_pop($staffs);

				if (! is_null($popB)) $teams['B'][] = $popB;
				if (! is_null($popA)) $teams['A'][] = $popA;

			} else {

				if (! is_null($popA)) $teams['A'][] = $popA;
				if (! is_null($popB)) $teams['B'][] = $popB;
			}
			

		} else {

			$shiftA = array_shift($staffs);
			$shiftB = array_shift($staffs);

			$totalA = $totalA + (is_null($shiftA) ? 0 : $shiftA);
			$totalB = $totalB + (is_null($shiftB) ? 0 : $shiftB);

			if ($totalA > $totalB) {

				if (! is_null($shiftA)) array_unshift($staffs, $shiftA);
				if (! is_null($shiftB)) array_unshift($staffs, $shiftB);
				$totalA = $totalA - (is_null($shiftA) ? 0 : $popA);
				$totalB = $totalB - (is_null($shiftB) ? 0 : $shiftB);

				$shiftB = array_shift($staffs);
				$shiftA = array_shift($staffs);

				if (! is_null($shiftB)) $teams['B'][] = $popA;
				if (! is_null($shiftA)) $teams['A'][] = $shiftA;

			} else {

				if (! is_null($shiftA)) $teams['A'][] = $shiftA;
				if (! is_null($shiftB)) $teams['B'][] = $shiftB;
			}
		}
	}

	foreach ($teams['A'] as $i => $p) {
		$search = array_search($p, $_staffs);
		if ($search !== false) {
			unset($teams['A'][$i]);
			unset($_staffs[$search]);
			$teams['A'][$search] = $p;
		}
	}

	foreach ($teams['B'] as $i => $p) {
		$search = array_search($p, $_staffs);
		if ($search !== false) {
			unset($teams['B'][$i]);
			unset($_staffs[$search]);
			$teams['B'][$search] = $p;
		}
	}

	$body .= '
	<div class="jumbotron">
		<p class="display-4">Ve takımlar hazır!</p>
		<div class="row">';

		$statistics = [
			'total' => 0,
			'A' => 0,
			'B' => 0
		];

		ksort($teams);
		
		foreach ($teams as $teamName => $persons) {
			
			$body .= '
			<div class="col-6">
				<h2>Takım '.$teamName.'</h2>
				<ul class="list-group">';

				ksort($persons);
				foreach ($persons as $personName => $point) {

					$statistics[$teamName] = $statistics[$teamName] + $point;
					$body .= '
					<li class="list-group-item d-flex justify-content-between align-items-center">
						'.$personName.'
						<span class="badge badge-primary badge-pill">'.$point.'</span>
					</li>';
				}

				$statistics['total'] = $statistics['total'] + $statistics[$teamName];

			$body = str_replace('Takım '.$teamName, 'Takım '.$teamName.' <span class="badge '.($teamName == 'A' ? 'badge-success' : 'badge-info').'">'.$statistics[$teamName].'</span>', $body);

			$body .= '
				</ul>
			</div>';
		}

	function getPerc($total, $number) {

		if ( $total > 0 ) {
			return round($number / ($total / 100),2);
		} else {
			return 0;
		}
	}

	$statistics['balanceA'] = getPerc($statistics['total'], $statistics['A']);
	$statistics['balanceB'] = getPerc($statistics['total'], $statistics['B']);

	$body .= '
			<div class="col-12 mt-5">
				<div class="progress">
					<div class="progress-bar progress-bar-striped bg-success" role="progressbar" style="width: '.$statistics['balanceA'].'%" aria-valuenow="'.$statistics['balanceA'].'" aria-valuemin="0" aria-valuemax="100">%'.$statistics['balanceA'].'</div>
					<div class="progress-bar progress-bar-striped bg-info" role="progressbar" style="width: '.$statistics['balanceB'].'%" aria-valuenow="'.$statistics['balanceB'].'" aria-valuemin="0" aria-valuemax="100">%'.$statistics['balanceB'].'</div>
				</div>
			</div>
		</div>
	</div>';
	
} else {

	$body .= '
	<input type="hidden" name="staff_number" value="'.$_POST['staff_number'].'" />
	<div class="jumbotron">
		<p class="display-4">Bu adamların isimleri ve puanları peki ?</p>';
	for ($i=0; $i < $_POST['staff_number']; $i++) {
		
		$body .= '
		<div class="form-row">
			<div class="form-group col-6">
				<label for="staff_'.$i.'">'.($i+1).'. Adamın İsmi</label>
				<input type="text" class="form-control" name="staffs['.$i.'][name]" id="staff_'.$i.'" />
			</div>
			<div class="form-group col-6">
				<label for="staff_n'.$i.'">'.($i+1).'. Adamın Puanı</label>
				<input type="number" value="1" min="1" max="5" class="form-control" name="staffs['.$i.'][point]" id="staff_n'.$i.'" />
			</div>
		</div>';
	}
	$body .= '
		<p class="lead">
			<button type="submit" class="btn btn-primary btn-lg">Her şey tamam, takımları kur!</button>
		</p>
	</div>';
}	?>
<!doctype html>
<html lang="tr">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<meta name="author" content="halillusion">
		<title><?php echo $title; ?></title>
		<link href="https://bootswatch.com/4/sketchy/bootstrap.min.css" rel="stylesheet" >
		<link href="https://fonts.googleapis.com/css2?family=Shadows+Into+Light+Two&display=swap" rel="stylesheet">
		<style type="text/css">
			body,
			p,
			h1,
			h2,
			h3,
			h4,
			h5,
			h6,
			a,
			button {
				font-family: 'Shadows Into Light Two', cursive;
			}
		</style>
	</head>
	<body>
		<div class="container">
			<div class="row aligh-items-center">
				<form method="post" class="col-12 my-5">
					<h1 class="display-2"><?php echo $title; ?></h1>
					<?php echo $body; ?>
				</form>
			</div>
		</div>
	</body>
</html>
