<?php
require_once('lib/tracklogPhp.main.php');

$extensions = ['kml', 'tcx', 'gpx', 'csv', 'js'];
$files = scandir("test_files/external_files_diversos");
sort($files, SORT_NUMERIC);
foreach ($files as $key => $file) {
	$extension = pathinfo($file, PATHINFO_EXTENSION);
	if (in_array($extension, $extensions)) {
		$file_path = "test_files/external_files_diversos/".$file;
		try {
			($extension == "js") ? $extension = "geojson" : 0;
			$time_start = microtime(true);
			$tracklog = new $extension($file_path);
			?>
			<table border="1">
				<tr style="text-align: center; font-weight:bolder;">
					<td colspan="2"><?php echo $file?></td>
				</tr>
				<tr>
					<td>Informação</td>
					<td>Resultado</td>
				</tr>
				<tr>
					<td>Pontos de GPS</td>
					<td><?php echo count($tracklog->getPoints()) ?></td>
				</tr>				
				<tr>
					<td>Distância Total</td>
					<td><?php echo $tracklog->getTotalDistance("kilometers") ?></td>
				</tr>
				<tr>
					<td><b>Distância Total Ponto Inicial e Final</b></td>
					<td><?php echo $tracklog->getTotalDistanceFirstAndLast() ?></td>
				</tr>
				<?php try {	?>
						<tr>
							<td>Tempo Total</td>
							<td><?php echo $tracklog->getTotalTime() ?></td>
						</tr>
						<tr>
							<td>Pace</td>
							<td><?php echo $tracklog->getPace() ?></td>
						</tr>
						<tr>
							<td>Velocidade Média</td>
							<td><?php echo $tracklog->getAverageSpeed() ?></td>
						</tr>
					<?php } catch (Exception $e) { ?>
						<tr>
							<td>Tempo Total</td>
							<td><?php echo $e->getMessage() ?></td>
						</tr>
						<tr>
							<td>Pace</td>
							<td><?php echo $e->getMessage() ?></td>
						</tr>
						<tr>
							<td>Velocidade Média</td>
							<td><?php echo $e->getMessage() ?></td>
						</tr>
					<?php } ?>

					<?php try { ?>
						<tr>
							<td>Elevação Máxima</td>
							<td><?php echo $tracklog->getMaxElevation() ?></td>
						</tr>
						<tr>
							<td>Subida Acumulada</td>
							<td><?php echo $tracklog->getElevationGain() ?></td>
						</tr>
						<tr>
							<td><b>Subida Acumulada em Inteiros</b></td>
							<td><?php echo $tracklog->getElevationGainInts() ?></td>
						</tr>
						<tr>
							<td>Descida Acumulada</td>
							<td><?php echo $tracklog->getElevationLoss() ?></td>
						</tr>
						<tr>
							<td><b>Descida Acumulada em Inteiros</b></td>
							<td><?php echo $tracklog->getElevationLossInts() ?></td>
						</tr>
					<?php } catch (Exception $e) { ?>
						<tr>
							<td>Elevação Máxima</td>
							<td><?php echo $e->getMessage() ?></td>
						</tr>
						<tr>
							<td>Subida Acumulada</td>
							<td><?php echo $e->getMessage() ?></td>
						</tr>
						<tr>
							<td>Descida Acumulada</td>
							<td><?php echo $e->getMessage() ?></td>
						</tr>
					<?php }?>
					<tr>
						<td>Tempo de Execução</td>
						<td><?php  echo (microtime(true) - $time_start) ?></td>
					</tr>
			</table>
			<?php } catch (Exception $e) { ?>
				<table border="2">
					<tr>
						<td><?php echo $file ?></tr>
						<td><?php echo $e->getMessage() ?></tr>
					</tr>
					<tr>
						<td>Tempo de Execução</td>
						<td><?php  echo (microtime(true) - $time_start) ?></td>
					</tr>
				</table>

			<?php
		}
	}
}
?>