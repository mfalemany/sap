<style type="text/css">
	.evaluacion{
		margin-bottom: 30px;
	}
	.evaluacion table{
		width: 100%;
	}
	.evaluacion table caption{
		background-color:#030c90;
		color:#FFF;
		font-size: 1.4em;
		font-weight: bold;
		padding: 10px 0px 10px 0px;
		text-shadow: 1px 1px 1px #222;

	}
	.evaluacion table tr{

	}
	.evaluacion table th{
		font-size: 1.2em;
		background-color: #575b97;
		color:#FFF;
		text-align: center;
	}
	.evaluacion table tr td{
		border-collapse: collapse;
		box-sizing: border-box;
		border-bottom: 1px solid #222;
		font-size: 1.3em;
	}
	.evaluacion table tr td:nth-child(1){
		text-align: center;
		font-weight: bold;
	}
	.evaluacion table tr:nth-child(even){
		background-color: #bdbcbc;
	}
	.evaluacion table tfoot tr td{
		background-color: #da7373;;
		color:#FFF;
		font-weight: bold;
		text-align: center;
		padding:5px;
		font-size: 2em;
		text-shadow: 1px 1px 1px black;
		


	}


</style>
<?php 
$resultados = array('M'=>'Aprobado - Muy Bueno','B'=>'Aprobado - Bueno','E'=>'Aprobado - Excelente','N'=>'No aprobado','A'=>'Aprobado'); 	
?>

<div id="contenedor_evaluaciones">
	<?php foreach($datos as $evaluacion): ?>
		<?php //ei_arbol($datos) ?>
		<div class='evaluacion'>
			<table>
				<?php $fecha_eval = strtotime($evaluacion['fecha_eval']); ?>
				<caption><?php echo $evaluacion['evaluador']; ?> </caption>
				<th width="20%">Concepto</th>
				<th width=80%>Evaluación/Puntaje</th>
				<?php 
					$instancia = $evaluacion['instancia'];
					$tipo = ($evaluacion['tipo'] == '0') ? 'pi' : 'pdts';
					$metodo = $tipo.'_'.$instancia;
					$metodo($evaluacion);
				?>
				<?php if($instancia == 'inicial'): ?>
					<?php $color = ($evaluacion['result_final_evaluacion'] == 'N') ? "#dc0000": "#00c040";?>
					<tfoot>
						<tr><td colspan=2 style="background-color: <?php echo $color; ?>">Resultado final de la evaluación: <?php echo $resultados[$evaluacion['result_final_evaluacion']];?></td></tr>
					</tfoot>
				<?php else: ?>
					<?php $color = ($evaluacion['satisfactorio'] == 'S') ? "#00c040" : "#dc0000";?>
					<tfoot>
						<tr><td colspan=2 style="background-color: <?php echo $color; ?>"><?php echo ($evaluacion['satisfactorio'] == 'S') ? 'Satisfactorio' : 'No Satisfactorio' ;?></td></tr>
					</tfoot>
				<?php endif; ?>
			</table>

			
		</div>
	<?php endforeach; ?>
</div>


<?php function pi_inicial($evaluacion){ ?>
	<tr>
		<td>Contenido tecnológico-Científico</td>
		<td><?php echo $evaluacion['cont_tec_cientif_punt']; ?> puntos.</td>
	</tr>
	<tr>
		<td>Justificación</td>
		<td><?php echo $evaluacion['cont_tec_cientif_justif']; ?></td>
	</tr>
	<tr>
		<td>Director/Co-Director</td>
		<td><?php echo $evaluacion['dir_codir_punt']; ?> puntos.</td>
	</tr>
	<tr>
		<td>Justificación</td>
		<td><?php echo $evaluacion['dir_codir_justif']; ?></td>
	</tr>
	<tr>
		<td>Conformación del Grupo</td>
		<td><?php echo $evaluacion['conf_grupo_punt']; ?> puntos.</td>
	</tr>
	<tr>
		<td>Justificación</td>
		<td><?php echo $evaluacion['conf_grupo_justif']; ?></td>
	</tr>
	<tr>
		<td>Factibilidad</td>
		<td><?php echo $evaluacion['factibilidad_punt']; ?> puntos.</td>
	</tr>
	<tr>
		<td>Justificación</td>
		<td><?php echo $evaluacion['factibilidad_justif']; ?></td>
	</tr>
	<tr>
		<td>Resultados Esperados</td>
		<td><?php echo $evaluacion['result_esp_punt']; ?> puntos.</td>
	</tr>
	<tr>
		<td>Justificación</td>
		<td><?php echo $evaluacion['result_esp_justif']; ?></td>
	</tr>
	<tr>
		<td>Observaciones generales</td>
		<td><?php echo $evaluacion['observaciones']; ?></td>
	</tr>
<?php } ?>

<?php function pdts_inicial($evaluacion){ ?>
	<?php 
		$resultados = array('M'=>'Aprobado - Muy Bueno','B'=>'Aprobado - Bueno','E'=>'Aprobado - Excelente','N'=>'No aprobado','A'=>'Aprobado'); 
	?>
	<tr>
		<td>Novedad/Originalidad en el conocimiento</td>
		<td><?php echo $resultados[$evaluacion['nov_orig_conc_punt']]; ?> </td>
	</tr>
	<tr>
		<td>Justificación</td>
		<td><?php echo $evaluacion['nov_orig_conc_justif']; ?></td>
	</tr>
	<tr>
		<td>Relevancia</td>
		<td><?php echo $resultados[$evaluacion['relevancia_punt']]; ?> </td>
	</tr>
	<tr>
		<td>Justificación</td>
		<td><?php echo $evaluacion['relevancia_justif']; ?></td>
	</tr>
	<tr>
		<td>Demanda</td>
		<td><?php echo  $resultados[$evaluacion['demanda_punt']]; ?> </td>
	</tr>
	<tr>
		<td>Justificación</td>
		<td><?php echo $evaluacion['demanda_justif']; ?></td>
	</tr>
	<tr>
		<td>Factibilidad Técnica</td>
		<td><?php echo $resultados[$evaluacion['factib_tecnica_punt']]; ?> </td>
	</tr>
	<tr>
		<td>Justificación</td>
		<td><?php echo $evaluacion['factib_tecnica_justif']; ?></td>
	</tr>
	<tr>
		<td>Factibilidad Económica</td>
		<td><?php echo  $resultados[$evaluacion['factib_econom_punt']]; ?> </td>
	</tr>
	<tr>
		<td>Justificación</td>
		<td><?php echo $evaluacion['factib_econom_justif']; ?></td>
	</tr>
	<tr>
		<td>Observaciones generales</td>
		<td><?php echo $evaluacion['observaciones']; ?></td>
	</tr>
<?php } ?>

<?php function pi_informe($evaluacion){ ?>
	<tr>
		<td>Producción</td>
		<td><?php echo $evaluacion['produccion']; ?> </td>
	</tr>
	<tr>
		<td>Transferencia y Divulgación</td>
		<td><?php echo $evaluacion['transf_divulgacion']; ?></td>
	</tr>
	<tr>
		<td>Formación de Recursos Humanos</td>
		<td><?php echo $evaluacion['form_rec_hum']; ?> </td>
	</tr>
	<tr>
		<td>Satisfactorio</td>
		<td><?php echo ($evaluacion['satisfactorio'] == 'S') ? 'Si' : 'No'; ?></td>
	</tr>
<?php } ?>

<?php function pdts_informe($evaluacion){ ?>
	<tr>
		<td>Avance del desarrollo</td>
		<td><?php echo $evaluacion['avance_desarrollo']; ?> </td>
	</tr>
	<tr>
		<td>Producción</td>
		<td><?php echo $evaluacion['produccion']; ?> </td>
	</tr>
	<tr>
		<td>Transferencia y Divulgación</td>
		<td><?php echo $evaluacion['transf_divulgacion']; ?></td>
	</tr>
	<tr>
		<td>Formación de Recursos Humanos</td>
		<td><?php echo $evaluacion['form_rec_hum']; ?> </td>
	</tr>
	<tr>
		<td>Satisfactorio</td>
		<td><?php echo ($evaluacion['satisfactorio'] == 'S') ? 'Si' : 'No'; ?></td>
	</tr>
<?php } ?>