<style type="text/css">
	#contenedor_eval_grupo{
		background-color: #FFF;
		font-size: 15px;
	}
	#contenedor_eval_grupo #encabezado{
		color:#FFF;

	}
	#contenedor_eval_grupo #encabezado #denominacion{
		background-color:#2d5280;
		box-sizing: border-box;
		font-size: 1.8em;
		padding: 10px 0px 10px 0px;
		text-align: center;
		text-shadow: 1px 2px 5px #000;

		

	}
	#contenedor_eval_grupo #encabezado #descripcion{
		background-color:#c9d0ff;
		box-sizing: border-box;
		color: #000;
		padding: 10px;
		text-align: justify;
		text-shadow: 1px 1px 2px #FFF;
	}
	#contenedor_eval_grupo #detalles{
		margin-top: 20px;
	}
	#contenedor_eval_grupo #detalles #area{
		box-sizing: border-box;
		text-align: center;
		padding: 5px 0px;
		background-color: #c4d8ea;
		color: #227;
	}
	#contenedor_eval_grupo #detalles #area span{
		margin-right: 50px;
	}

	.flotante{
		background-color: #c4d8ea;
		border-radius:5px;
		box-shadow: 5px 5px 5px #a9a9a9;
		box-sizing: border-box;
		float: left;
		margin: 20px 0px 40px 30px;
		padding: 10px;
		min-width: 500px;
		max-width: 50%;
	}
	#proyectos ul li{
		margin-bottom: 10px;
	}
	#contenedor_eval_grupo #detalles #integrantes{
		color: 	#101046;
	}
	#contenedor_eval_grupo #detalles #integrantes h3, #proyectos h3{
		color: #fff;
		margin: 0px;
		background-color: #2d5280;
		text-shadow: 1px 1px 1px #000;
		padding-left: 10px;
	}
	ul{
		list-style: circle;
	}
	
	table{
		border: 1px solid black;
		width: 98%;
		margin: 0px auto 40px auto;
	}
	table caption{
		color: #fff;
	    background-color: #2d5280;
	    font-size: 1.2em;
	    font-weight: bold;
	    padding: 3px 0px;
	    text-shadow: 1px 1px 1px #000;
	}
	table thead th{
		background-color: #47899c;
		color:#FFF;
		text-align: center;
		text-shadow: 1px 1px 1px black;

	}
	table tr{

	}
	table tr td{
		border-collapse: collapse;
		border: 1px solid grey;
	}
	.clear{
		clear:both;
	}

	#planes_trabajo{
		background-color: #f6faff;
		border: 2px solid #DDD;
		width: 97%;
		margin: 15px auto;
	}
	.item_plan{
		margin-bottom: 25px;
	}
	.item_plan h3{
		background-color: #cde0f9;
		box-sizing: border-box;
		margin:5px;
		padding:3px 15px;
		text-decoration: underline;

	}
	.item_plan p{
		line-height: 1.5em;
		margin: 3px;
		padding: 0px 15px;
	}
	.finalizado{
		color:red;
	}
	.vigente{
		color: green;
	}
	#seleccion_actividad{
		width: 600px;
	    margin: 25px auto;
	    background-color: #cde0f9;
	    box-sizing: border-box;
	    padding: 10px;
	}

</style>

<div id='contenedor_eval_grupo'>
	<div id='encabezado'>
		<div id='denominacion'><?php echo $datos['grupo']['denominacion']; ?></div>
		<?php if(isset($datos['grupo']['descripcion']) && $datos['grupo']['descripcion']): ?>
			<div id='descripcion'><?php echo $datos['grupo']['descripcion']; ?></div>
		<?php endif; ?>
	</div>
	<div id='detalles'>
		<div id='area'>
			<span><u><b>Facultad</b></u>: <?php echo $datos['grupo']['dependencia']; ?></span>
			<span><u><b>Área de Conocimiento</b></u>: <?php echo $datos['grupo']['area_conocimiento']; ?></span>
		</div>
		<div id='integrantes' title='Integrantes' class="flotante">
			<h3>Integrantes:</h3>
			<ul>
				<?php foreach($datos['integrantes'] as $integrante): ?>
					<li><b><?php echo strtoupper($integrante['apellido']); ?></b>, <?php echo $integrante['nombres']; ?> - <b>Rol</b>: <?php echo $integrante['rol']; ?></li>
				<?php endforeach; ?>
			</ul>
		</div>
		<div id='proyectos' class="flotante">
			<h3>Proyectos de investigación:</h3>
			<ul>
				<?php foreach($datos['proyectos'] as $proyecto): ?>
					<li>
						<b class="<?php echo strtolower($proyecto['estado']); ?>">(<?php echo $proyecto['estado']; ?>) 
						<?php echo $proyecto['codigo']; ?></b>: 
						<?php echo $proyecto['descripcion'];?></li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>
	<div class="clear"></div>
	<div id='actividades'>
		<div id='extension'>
			<table>
				<caption>Actividades de Extensión</caption>
				<thead>
					<tr>
						<th style="width:60%">Descripción</th>
						<th>Fecha Inicio</th>
						<th>Fecha Fin</th>
						<th>Nro. Resol.</th>	
					</tr>
				</thead>
				<tbody>
					<?php foreach($datos['extension'] as $actividad): ?>
					<tr>
						<td><?php echo $actividad['descripcion']; ?></td>
						<td class="centrado"><?php echo $this->fecha_dmy($actividad['fecha_inicio']); ?></td>
						<td class="centrado"><?php echo $this->fecha_dmy($actividad['fecha_fin']); ?></td>
						<td class="centrado"><?php echo $actividad['nro_resol']; ?></td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		<div id='publicaciones'>
			<table>
				<caption>Publicaciones</caption>
				<thead>
					<tr>
						<th>Título</th>
						<th>Tipo de Publicación</th>
						<th>Datos bibliográficos</th>
						<th>URL</th>
						<th>Año</th>	
					</tr>
				</thead>
				<tbody>
					<?php foreach($datos['publicacion'] as $publicacion): ?>
					<tr>
						<td><?php echo $publicacion['titulo']; ?></td>
						<td class="centrado"><?php echo $publicacion['tipo_publicacion']; ?></td>
						<td><?php echo $publicacion['datos_bibliograficos']; ?></td>
						<td class="centrado"><a href="<?php echo $publicacion['url_publicacion']; ?>" target="_BLANK"><?php echo $publicacion['url_publicacion']; ?></a></td>
						<td class="centrado"><?php echo $publicacion['anio']; ?></td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		<div id='transferencia'>
			<table>
				<caption>Transferencia</caption>
				<thead>
					<tr>
						<th style="width: 60%">Descripción</th>
						<th>Tipo</th>
						<th>Año</th>
						<th>Sector</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($datos['transferencia'] as $transferencia): ?>
					<tr>
						<td><?php echo $transferencia['descripcion']; ?></td>
						<td class="centrado"><?php echo $transferencia['tipo_transferencia']; ?></td>
						<td class="centrado"><?php echo $transferencia['anio']; ?></td>
						<td class="centrado"><?php echo $transferencia['sector_desc']; ?></td>
					</tr>
					<?php endforeach ?>
				</tbody>
			</table>
		</div>
		<div id='form_rrhh'>
			<table>
				<caption>Formación de Recursos Humanos</caption>
				<thead>
					<tr>
						<th>Apellido y Nombres</th>
						<th>Tipo de Formación</th>
						<th>Inicio</th>
						<th>Fin</th>
						<th>Beca</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($datos['form_rrhh'] as $formacion): ?>
					<tr>
						<td><?php echo $formacion['persona']; ?></td>
						<td class="centrado"><?php echo $formacion['tipo_formacion']; ?></td>
						<td class="centrado"><?php echo $this->fecha_dmy($formacion['fecha_inicio']); ?></td>
						<td class="centrado"><?php echo $this->fecha_dmy($formacion['fecha_fin']); ?></td>
						<td class="centrado"><?php echo $formacion['entidad_beca']; ?></td>
					</tr>
					<?php endforeach ?>
				</tbody>
			</table>
		</div>
		<div id='evento'>
			<table>
				<caption>Organización de Reuniones Científicas</caption>
				<thead>
					<tr>
						<th>Evento</th>
						<th>Alcance</th>
						<th>Año</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($datos['evento'] as $evento): ?>
					<tr>
						<td><?php echo $evento['evento']; ?></td>
						<td class="centrado"><?php echo $evento['alcance']; ?></td>
						<td class="centrado"><?php echo $evento['anio']; ?></td>
					</tr>
					<?php endforeach ?>
				</tbody>
			</table>
		</div>
	</div>
	<div id="seleccion_actividad">
		<h3>Seleccione una convocatoria para ver el plan de trabajo presentado</h3>
		<div style="margin-left: 30px;">
		<select id="id_convocatoria">
			<option value='nopar'>-- Seleccione --</option>
			<?php foreach ($datos['planes_trabajo'] as $plan): ?>
			<option value="<?php echo $plan['id_convocatoria']; ?>"><?php echo $plan['convocatoria']; ?></option>
			<?php endforeach ?>	
		</select>
		<?php /*foreach ($datos['planes_trabajo'] as $plan): ?>
			<input type="radio" 
					name="id_convocatoria" 
					value="<?php echo $plan['id_convocatoria']; ?>" 
					onclick='mostrar(<?php echo $plan['id_convocatoria']; ?>)'>
						<?php echo $plan['convocatoria']; ?><br>
		<?php endforeach */?>
			
		</div>
		
	</div>
	<div id='planes_trabajo' style="display:none">
		<div class='item_plan'>
			<h3>Proyectos</h3>
			<p id="plan_proyectos"></p>
		</div>
		<div class='item_plan'>
			<h3>Extensión</h3>
			<p id="plan_extension"></p>

		</div>
		<div class='item_plan'>
			<h3>Publicaciones</h3>
			<p id="plan_publicaciones"></p>
		</div>
		<div class='item_plan'>
			<h3>Transferencia</h3>
			<p id="plan_transferencia"></p>
		</div>
		<div class='item_plan'>
			<h3>Formación de Recursos Humanos</h3>
			<p id="plan_formacion"></p>
		</div>
		<div class='item_plan'>
			<h3>Organización de Reuniones Científicas</h3>
			<p id="plan_eventos"></p>
		</div>
	</div>
	<div id='evaluacion'>
		[dep id=form_eval_grupo]
		[dep id=form_eval_plan_trabajo]
	</div>
	
</div>

<script type="text/javascript">
	var informes = new Array();
	<?php foreach ($datos['planes_trabajo'] as $plan): ?>
		informes[<?php echo $plan['id_convocatoria']; ?>] = {
			"proyectos": "<?php echo limpiar_string($plan['proyectos']); ?>",
			"extension": "<?php echo limpiar_string($plan['extension']); ?>",
			"publicaciones": "<?php echo limpiar_string($plan['publicaciones']); ?>",
			"transferencia": "<?php echo limpiar_string($plan['transferencia']); ?>",
			"formacion": "<?php echo limpiar_string($plan['form_rrhh']); ?>",
			"eventos": "<?php echo limpiar_string($plan['org_eventos']); ?>"
			};
	<?php endforeach; ?>	

	$(document).ready(function(){
		$('html').animate({scrollTop:0});

		//Modificacion del select de convocatorias
		$('#id_convocatoria').on('change',function(e){
			if($('#id_convocatoria').prop('value') != 'nopar'){
				mostrar($('#id_convocatoria').prop('value'));	
			}else{
				limpiar();
			}
		})
	})
	
	
	
	function mostrar(id_convocatoria)
	{
		$('#planes_trabajo').css('display','block');
		$('#plan_proyectos').html(informes[id_convocatoria].proyectos);
		$('#plan_extension').html(informes[id_convocatoria]['extension']);
		$('#plan_publicaciones').html(informes[id_convocatoria]['publicaciones']);
		$('#plan_transferencia').html(informes[id_convocatoria]['transferencia']);
		$('#plan_formacion').html(informes[id_convocatoria]['formacion']);
		$('#plan_eventos').html(informes[id_convocatoria]['eventos']);
	}

	function limpiar()
	{
		$('#planes_trabajo').css('display','none');
		$('#plan_proyectos').html('');
		$('#plan_extension').html('');
		$('#plan_publicaciones').html('');
		$('#plan_transferencia').html('');
		$('#plan_formacion').html('');
		$('#plan_eventos').html('');
	}
</script>

<?php 
function limpiar_string($str)
{
	return str_replace(array(PHP_EOL,'"'),array("<br>",'\''),$str);
}
?>