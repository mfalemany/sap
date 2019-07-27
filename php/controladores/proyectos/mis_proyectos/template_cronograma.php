<style type="text/css">
	#tabla_cronograma{
		width:100%;
		text-align: center;
		border-collapse: collapse;
		font-size: 1.2em;

	}
	#tabla_cronograma tr{

	}
	#tabla_cronograma tr td{
		
		border: 2px solid #777;
		box-sizing: border-box;
		padding: 3px 0px;

	}
	#tabla_cronograma thead tr{
		background-color: #5a5e9c;
		color:#FFF;
		text-shadow: 1px 1px 1px #484747;
	}
	#tabla_cronograma tbody tr:nth-child(odd){
		background-color: #FFF;
	}

</style>
<table id="tabla_cronograma" border=1 cellspacing=0 cellpadding=2 bordercolor="666633">
	<thead>
		<!-- =========== CABECERA DE LA TABLA ============ -->
		<tr class="cabecera_tabla">
			<td rowspan=2>Meta</td>
			<?php for($i=1;$i<=$datos['duracion'];$i++): ?>
				<td colspan=2><?php echo $datos['ordinal'][$i]; ?> Año</td>
			<?php endfor; ?>
		</tr>
		<tr class="cabecera_tabla">
			<?php for($i=1;$i<=$datos['duracion'];$i++): ?>
				<td>Primer Semestre</td>
				<td>Segundo Semestre</td>
			<?php endfor; ?>
		</tr>
	</thead>
	<tbody>
	<!-- ============================================ -->
	<?php foreach($datos['objetivos'] as $objetivo): ?>
		<tr>
			<td style="text-align: left;"><?php echo $objetivo['obj_especifico']; ?></td>
			<?php foreach($datos['anios_proyecto'] as $anio): ?>

				<?php for ($semestre=1;$semestre<=2;$semestre++): ?> 
					<td>
					<?php echo ($objetivo['anio'] == $anio['anio'] && $objetivo['semestre'] == $semestre) ? "X" : ""; ?>
					</td>
				<?php endfor; ?>
			<?php endforeach; ?>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>