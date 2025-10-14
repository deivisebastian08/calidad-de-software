<?php
	// Crear instancia de conexión
	require_once("script/conex.php");
	// Crear instancia de conexión
	$cn = new MySQLcn();

	// Consultar visitas agrupadas por fecha
	// --- Por fecha ---
	$sql = "SELECT fecha, COUNT(*) as total FROM visitas GROUP BY fecha ORDER BY fecha";
	$cn->Query($sql);
	$rowsFecha = $cn->Rows();

	// --- Por IP ---
	$sql = "SELECT ip, COUNT(*) as total FROM visitas GROUP BY ip ORDER BY total DESC LIMIT 10";
	$cn->Query($sql);
	$rowsIP = $cn->Rows();

	// --- Por Sistema Operativo ---
	$sql = "SELECT so, COUNT(*) as total FROM visitas GROUP BY so ORDER BY total DESC";
	$cn->Query($sql);
	$rowsSO = $cn->Rows();

	// --- Por Navegador ---
	$sql = "SELECT navegador, COUNT(*) as total FROM visitas GROUP BY navegador ORDER BY total DESC LIMIT 5";
	$cn->Query($sql);
	$rowsNav = $cn->Rows();

	$cn->Close();

	// Preparar arrays
	function preparar($rows, $campo) {
		$labels=[]; $totales=[];
		foreach($rows as $fila){
			$labels[] = $fila[$campo];
			$totales[] = $fila['total'];
		}
		return [$labels,$totales];
	}

	list($fechas,$totFecha) = preparar($rowsFecha,'fecha');
	list($ips,$totIP)       = preparar($rowsIP,'ip');
	list($sos,$totSO)       = preparar($rowsSO,'so');
	list($navs,$totNav)     = preparar($rowsNav,'navegador');
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Reporte de visitas</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
  <h1>Reportes de visitas</h1>

  <h2>Visitas por día</h2>
  <canvas id="grafFecha"></canvas>

  <h2>Top 10 IPs</h2>
  <canvas id="grafIP"></canvas>

  <h2>Visitas por Sistema Operativo</h2>
  <canvas id="grafSO"></canvas>

  <h2>Top 5 Navegadores</h2>
  <canvas id="grafNav"></canvas>

<script>
function crearGrafico(id, tipo, etiquetas, datos, color){
  new Chart(document.getElementById(id), {
    type: tipo,
    data: {
      labels: etiquetas,
      datasets: [{
        label: 'Visitas',
        data: datos,
        backgroundColor: color
      }]
    },
    options: { responsive:true, plugins:{legend:{display:false}}, scales:{y:{beginAtZero:true}} }
  });
}

crearGrafico("grafFecha","bar", <?php echo json_encode($fechas); ?>, <?php echo json_encode($totFecha); ?>, 'rgba(54,162,235,0.7)');
crearGrafico("grafIP","bar", <?php echo json_encode($ips); ?>, <?php echo json_encode($totIP); ?>, 'rgba(255,99,132,0.7)');
crearGrafico("grafSO","bar", <?php echo json_encode($sos); ?>, <?php echo json_encode($totSO); ?>, 'rgba(75,192,192,0.7)');
crearGrafico("grafNav","bar", <?php echo json_encode($navs); ?>, <?php echo json_encode($totNav); ?>, 'rgba(153,102,255,0.7)');
</script>
</body>
</html>
