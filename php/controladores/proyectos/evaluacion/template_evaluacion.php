<div id="evaluacion_proyecto">
	<div id="titulo_proyecto">
		<h1>{{TITULO_PROYECTO}}</h1>	
	</div>
	<div id="cuerpo_proyecto">
		<table>
			<tr>
				<td><b>Código de {{TIPO_PROYECTO}}:</b></td>
				<td>{{CODIGO_PROYECTO}} <span style="font-size: 0.6em;">(ID: {{ID_PROYECTO}})</span></td>
				<td><b>Vigencia:</b> {{FECHA_DESDE}} al {{FECHA_HASTA}}</td>
			</tr>
			<tr>
				<td><b>Entidad Financiadora:</b></td>
				<td colspan=2>{{ENTIDAD_FINANCIADORA}}</td>
			</tr>
		</table>
		<div></div>
		<table id="direccion_proyecto">
			<caption>DIRECCIÓN DEL <span style="text-transform:uppercase;">{{TIPO_PROYECTO}}</span></caption>
			<tr>
				<td class="cabecera_tabla">Apellido y Nombre</td>
				<td class="cabecera_tabla">Rol</td>
				<td class="cabecera_tabla">Cat. Incentivos</td>
				<td class="cabecera_tabla">CVAr</td>
			</tr>	
			{{DIRECTOR}}
			{{CODIRECTOR}}
			{{SUBDIRECTOR}}
		</table>
		<div id="archivo">
			<a href="{{ARCHIVO_PROYECTO}}" target="_BLANK">Descargar contenido del {{TIPO_PROYECTO}}</a>
		</div>
		<div class="form_evaluacion_proyecto">
			<div class='importante'>Los puntajes de cada ítem van desde 0 a 20 puntos.</div>
			{{FORMULARIO_EVALUACION}}
			<div id="puntaje_total_contenedor">
				<div>Puntaje Total: <span id="puntaje_total_numero"></span> (<span id="puntaje_total_descripcion"></span>)
				</div>
				<div style="font-size: 0.6em; margin-top:15px; width: 50%; text-align: left;">El puntaje total del proyecto tiene un máximo en 100 pts., valorándose al proyecto con el resultado de dividir dicho puntaje en 10 y redondeando a un número entero:
					<ul style="text-align: left;">
						<li>1 - 5: No aprobado</li>
						<li>6 - 7: Aprobado - Bueno</li>
						<li>8 - 9: Aprobado - Muy bueno</li>
						<li>10: Aprobado - Excelente</li>
					</ul>

				</div>
			</div>
		</div>
	</div>
	

</div>