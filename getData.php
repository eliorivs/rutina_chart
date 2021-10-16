<?php

header("Content-Type: application/json; charset=UTF-8");
require ('conexion/conexion.php');
require ('utils.php');
require ('caudales.php');
mysqli_set_charset($conn, "utf8");

$minusalldata = [];
$estaciones = ['53'];
$parametros = ['55'];
$normas = [];
$hitos = [];
$programas = ['1'];



$caudal_bombeo_column = array('name'=>'Bombeo Rec','color'=>'navy','type'=>'column','yAxis'=>1,'data'=>GetCaudales());
$caudal_bombeo_line = array('name'=>'Bombeo Rec l',  'lineWidth' => 1,'color'=>'red','type'=>'line','markers'=>array('enable'=>'false'),'yAxis'=>1,'data'=>GetCaudales());

//print json_encode($caudal_bombeo);



$resultado = primary_object($estaciones, $parametros, $normas, $hitos,$programas);
$rules = SetNormas($normas);
$series = GetNormas($rules, $parametros, $estaciones, $resultado, $hitos);

//print json_encode($series);


array_push($series,$caudal_bombeo_column);
array_push($series,$caudal_bombeo_line);
//print json_encode($series);

$plotlines = getHitos($hitos);
foreach ($all_min as $key => $value)
if (empty($value)) unset($all_min[$key]);

$response = array('series' => $series, 'plotlines' => $plotlines, 'min' => ConvertMilis(min($all_min))-(86400000*15) , 'minimos' => ($all_min));

print json_encode($response);

function primary_object($estaciones, $parametros, $normas, $hitos,$programas)
{
	global $conn;
	$j = 0;
	$series = 0;
	$arr = array();
	
	$resultado = array();
	$programas = '';

	if ($programas == '') {
		$programas = [' '];
	}

	$color_counter = 0;

	foreach ($estaciones as $estacion) {
		$pto = GetNameEstacion($estacion);

		foreach ($parametros as $parametro) {
			$elemento = $parametro;
			$parametro = 'parametro_' . $parametro;

			foreach ($programas as $programa) {
				$busqueda = search_program($programa);
				$define_program = define_program($programa);
				$color_counter++;

				$j = 0;
				unset($arr);
				$series++;

				$sql = "select count(*) as cantidad  from muestras  where estacion ='" . $pto . "' and " . $parametro . " !='' and " . $parametro . " !='-' " . $busqueda . " ";

				$result = $conn->query($sql);
				while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
					$cantidad = $row['cantidad'];
				}
				if ($cantidad > 0) {
					$sql = " select " . $parametro . " as parametro, fecha as date from muestras where estacion ='" . $pto . "' and " . $parametro . " !='' and " . $parametro . " !='-' " . $busqueda . " order by date";
        			$result = $conn->query($sql);
					while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
						if ($j == 0) {
							if ($define_program == '') {
								$arr['name'] = GetNameSerie($elemento) . "-" . $pto;
							} else {
								$arr['name'] = GetNameSerie($elemento) . " [" . $define_program . "]";
							}
							$arr['lineWidth'] = 2;
							$time = $row['date'];
							if ($color_counter <= 18) {
								$arr['color'] = GetColorArray($color_counter);
							} else {
								$arr['color'] = GetColorArray(rand(1, 18));
							}
							$j++;
						}
						$time = $row['date'];
						$date = new DateTime($time);
						$timeArray = $date->getTimestamp() * 1000;
						$band= NotShowDisabled($parametro,$pto,$time);

						//echo $row['parametro']."<br>";



						if($band=='0')
						{
					
						 $date = new DateTime($time);
						 $timeArray = $date->getTimestamp() * 1000;
						 $arr['data'][] = [$timeArray, ChangePosition($row['parametro']),$band];
						 //$arr['marker']['symbol']='circle';						
						 //$arr['marker']['fillColor']='#FFFFFF';
						 //$arr['marker']['lineWidth']='2';
						 //$arr['marker']['lineColor']=null; 
  
					   }
					   else
					   {
					   
						$date = new DateTime($time);
						$timeArray = $date->getTimestamp() * 1000;
						$arr['data'][]= array('x'=>$timeArray,'y'=>ChangePosition($row['parametro']),'color'=>'#FF0000');
						//$arr['marker']['symbol']='circle';
						//$arr['marker']['fillColor']='#FFFFFF';
						//$arr['marker']['lineWidth']='2';
					//	$arr['marker']['lineColor']=null; 
						   
					   }


						//$arr['data'][] = [$timeArray, 1 * ChangePosition($row['parametro'])];
						//$arr['marker']['symbol']='circle';
							//$arr['marker']['fillColor']='#FFFFFF';
							//$arr['marker']['lineWidth']='1';
							//$arr['marker']['lineColor']=null; 
							$arr['marker']['enabled']='true';
							
							//$arr['marker']['symbol']='circle';
							$arr['tooltip'] = array('valueDecimals' => GetDecimals($elemento), 'valueSuffix' => " " . GetUnidadSerie($elemento));
							
					

					}
					array_push($resultado, $arr);
				}
			}
		}
	}

	return ($resultado);
}

function DateMinMax($estaciones)
{
	foreach ($estaciones as $estacion) {
		$datemin[] = DateValueMin(GetNameEstacion($estacion));
		$datemax[] = DateValueMax(GetNameEstacion($estacion));
	}
	$inicio = min($datemin);
	$fin   = max($datemax);
	global $all_min;
	$all_min = array();
	array_push($all_min, $inicio);

	$data = array('min' => $inicio, 'max' => $fin);
	return ($data);
}

function SetNormas($normas)
{
	global $conn;

	$series = ['maxima', 'minima'];
	$alias = ['max', 'min'];
	$color = ['#190c59', '#ff0000'];
	$i = 0;
	foreach ($normas as $norma) {
		$i = 0;
		foreach ($series as $serie) {
			$sql = "select " . $serie . " as serie , norma_description  from normas where id_norma='" . $norma . "'";
                //echo $sql;
			$result = $conn->query($sql);
			while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
				$rules[] = array('color' => $color[$i], 'serie' => $row['serie'], 'alias' => $alias[$i], 'description' => $row['norma_description']);
				$i++;
			}
		}
	}
	return ($rules);
}

function GetNormas($normas, $parametros, $estaciones, $resultado, $hitos)
{
	global $conn;
	global $all_min;

	$fechahitomenor = getMenorHito($hitos);
	$fechahitomayor = getMaximoHito($hitos);

	$MinMax = DateMinMax($estaciones);
	$inicio = $MinMax['min'];
	$fin   = $MinMax['max'];

	$minimos = [$fechahitomenor, $inicio];
	$maximos = [$fechahitomayor, $fin];

	foreach ($minimos as $key => $value)
	if (empty($value)) unset($minimos[$key]);

	foreach ($maximos as $key => $value)
	if (empty($value)) unset($maximos[$key]);

	array_push($all_min, min($minimos));

	$fechas_serie = array('datemin' => min($minimos), 'datemax' => max($maximos));
	$plotline = -1;

	foreach ($parametros as $parametro) {
		$unidad = GetUnidadSerie($parametro);
		$parametro_result = GetNameSerie($parametro);

		foreach ($normas as $norma) {
			$sql = "select " . $norma['serie'] . " as value  from normas_config where id_parameter='" . $parametro . "'";

            //echo $sql ;
			$result = $conn->query($sql);
			while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
				if ($row['value'] != '') {
					$plotline++;

					$forserie = array(
						'name' => $norma['description'] . " " . $norma['alias'] . "[" . GetNameSerie($parametro) . " :" . $row['value'] . " " . GetUnidadSerie($parametro) . " ]",
						'lineWidth' => '1.5',
						'color' => $norma['color'],
						'tooltip' => array('valueDecimals' => 2, 'valueSuffix' => 'u.pH'),
						'dashStyle' => 'shortdash',
						'parametro' => $parametro,
						'fechas' => array($fechas_serie['datemin'], $fechas_serie['datemax']),
						'data' => array(
							array(
								ConvertMilis($fechas_serie['datemin']),
								$row['value'] * 1
							),
							array(
								ConvertMilis($fechas_serie['datemax']),
								$row['value'] * 1
							)
						)

					);

					array_push($resultado, $forserie);
				}
			}
		}
	}
	return $resultado;
}

function getMenorHito($hitos)
{
	global $conn;
	global $all_min;
	if (count($hitos) > 0) {
		foreach ($hitos as $hito) {
			$sql = "select color, comentario, fecha, id_hito, width from hitos where id_hito='" . $hito . "'";
			$result = $conn->query($sql);
			while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
				$datemin[] = $row['fecha'];
			}
		}
		if (min($datemin) != '') {
			array_push($all_min, min($datemin));
		}

		return min($datemin);
	} else {
		return '';
	}
}

function getMaximoHito($hitos)
{
	global $conn;

	if (count($hitos) > 0) {
		foreach ($hitos as $hito) {
			$sql = "select color, comentario, fecha, id_hito, width from hitos where id_hito='" . $hito . "'";
			$result = $conn->query($sql);
			while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
				$datemax[] = $row['fecha'];
			}
		}

		return max($datemax);
	} else {
		return '';
	}
}

function getHitos($hitos)
{
	global $conn;
	$resultado = [];

	foreach ($hitos as $hito) {
		$sql = "select color, comentario, fecha, id_hito, width from hitos where id_hito='" . $hito . "'";

		$result = $conn->query($sql);

		while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
			$color = $row['color'];
			$comentario = $row['comentario'];
			$fecha = $row['fecha'];
			$id_hito = $row['id_hito'];
			$width = $row['width'];
			$date = DateTime::createFromFormat("Y-m-d", $fecha);
			$time = $row['fecha'];
			$date = new DateTime($time);
			date_add($date, date_interval_create_from_date_string('1 day'));
			$timeArray = $date->getTimestamp() * 1000;

			$resultado[] = array(
				/*'value'=> 'Date.UTC('.$date->format("Y").','.$date->format("m").','.$date->format("d").')',*/
																				'value' => $timeArray,
				'width' => $row['width'],
				'color' => $color,
				'dashStyle' => 'dash',
				'year' => $date->format("Y"),
				'month' => $date->format("m"),
				'day' => $date->format("d"),
				'label' => array(
					'text' => $comentario,
					'rotation' => 90,
					'textAlign' => 'left', 'style' => array('font' => '11px "Poppins"')
				)
			);

    


         // $hitos_response[]=array('miliseconds'=>$timeArray,'color'=>$color,'nombre_hito'=>$comentario,'fecha'=>$fecha, 'id_hito'=>$id_hito,'width'=>$width,'year'=>$date->format("Y"),'month'=>$date->format("m"),'day'=>$date->format("d"));;
		}
	}
	return ($resultado);
}


