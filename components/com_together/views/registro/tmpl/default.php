<?php
defined('_JEXEC') or die('Restricted access');
$user = JFactory::getUser();
?>
<?php if (isset($user->groups["11"]) && !$user->guest) : ?>
<script type="text/javascript" src="http://cdnjs.cloudflare.com/ajax/libs/knockout/3.1.0/knockout-min.js"></script>
<script type="text/javascript" src="<?php echo JUri::root(); ?>templates/together/js/underscore-min.js"></script>
<script type="text/javascript">
$(document).ready(function () {
	var Opcion = function (nombre, valor) {
		this.nombre = nombre;
		this.valor = valor;
	};
	var togetherVM = {
		persona: ko.observable(),
		dieta: ko.observable(),
		found: ko.observable(false),
		verDieta: ko.observable(false),
		tipoDoc: ko.observable(),
		tiposDocumento: ko.observableArray([new Opcion('Cedula', 'CC'), new Opcion('Cedula extranjeria', 'CE'), new Opcion('Tarjeta de identidad', 'TI'), new Opcion('Registro civil', 'RC')]),
		genero:  ko.observable(),
		generos: ko.observableArray([new Opcion('Masculino', 'M'), new Opcion('Femenino', 'F')]),
		afisica: ko.observable(),
		afisicas: ko.observableArray([new Opcion('Sedentario', '1'), new Opcion('Ligera', '2'), new Opcion('Moderada', '3'), new Opcion('Activa', '4'), new Opcion('Muy Activa', '5'), new Opcion('Elite', '6')])
	};
	$('input.numero').blur(function () {
		$(this).val($(this).val().replace(/,/i, '.'));
		$(this).val($(this).val().replace(/[^0-9.]+/i, ''));
	});
	function validarCampo(campo) {
		var re = campo.attr('data-regexp');
		if (!_.isUndefined(re) && !_.isEmpty(re)) {
			var er = new RegExp('^'+re+'$',"g");
			if (!er.test(campo.val())) {
				if (!campo.parent().parent().hasClass('has-error')) {
					campo.parent().parent().addClass('has-error');
				}
			} else {
				campo.parent().parent().removeClass('has-error');
			}
		}
	}
	$('.form-control').blur(function () {
		validarCampo($(this));
	});
	$('#documento').blur(function () {
		if (_.isEmpty($(this).val()) || togetherVM.found()) {
			return;
		}
		$.ajax({
			url: '<?php echo JUri::root(); ?>index.php',
			data: 'option=com_together&task=buscar&documento=' + $('#documento').val(),
			type: 'post',
			dataType: 'json',
			success: function (data) {
				if (data.exception) {
					console.log(data.exception);
					togetherVM.found(false);
					togetherVM.verDieta(false);
				} else {
					var persona = data.persona;
					persona.antecedentes = _.indexBy(data.antecedentes, 'id_antecedente');
					persona.medidas = _.indexBy(data.medidas, 'id_medida');
					togetherVM.tipoDoc(persona.tipo_documento);
					togetherVM.genero(persona.genero);
					togetherVM.afisica(persona.afisica);
					togetherVM.persona(persona);
					togetherVM.found(true);
					togetherVM.verDieta(true);
					$('.antecedente').each(function (i) {
						var val = $('.si', this).val();
						if (persona.antecedentes[val]) {
							$('.si', this).prop('checked', true);
							$('.no', this).removeProp('checked');
							$('.cuales', this).val(persona.antecedentes[val].cuales);
						}
					});
					$('.medida').each(function (i) {
						var val = $('.id-medida', this).val();
						if (persona.medidas[val]) {
							$('.numero', this).val(persona.medidas[val].valor);
						}
					});
				}
			},
			error: function (jqXHR, textStatus, errorThrown) {
				console.log(errorThrown)
				togetherVM.found(false);
			}
		});
	});
	$('#btn-nuevo').click(function () {
		$('#reg-form')[0].reset();
		togetherVM.persona(null);
		togetherVM.found(false);
		togetherVM.verDieta(false);
	});
	$('#btn-enviar').click(function () {
		var $btn = $(this).button('loading');
		$.ajax({
			url: '<?php echo JUri::root(); ?>index.php',
			data: 'option=com_together&task=enviar&documento=' + $('#documento').val(),
			type: 'post',
			dataType: 'json',
			success: function (data) {
				if (data.exception) {
					alert(data.exception);
				} else {
					alert("Mensaje enviado con exito.");
				}
				$btn.button('reset');
			},
			error: function (jqXHR, textStatus, errorThrown) {
				alert(errorThrown)
				$btn.button('reset');
			}
		});
	});
	$('#btn-verDieta').click(function () {
		var $btn = $(this).button('loading');
		$.ajax({
			url: '<?php echo JUri::root(); ?>index.php',
			data: 'option=com_together&task=calcularIMC&documento=' + $('#documento').val(),
			type: 'post',
			dataType: 'json',
			success: function (data) {
				if (data.exception) {
					alert(data.exception);
				} else {
					togetherVM.dieta(data);
					$('#dietModal').modal();
				}
				$btn.button('reset');
			},
			error: function (jqXHR, textStatus, errorThrown) {
				alert(errorThrown)
				$btn.button('reset');
			}
		});
	});
	$('#btn-guardar').click(function () {
		var $btn = $(this).button('loading');
		$('.form-control').each(function () {
			validarCampo($(this));
		});
		if ($('.has-error').length > 0) {
			alert('Campos faltantes o mal digitados');
			$btn.button('reset');
		} else {
			$.ajax({
				url: '<?php echo JUri::root(); ?>index.php',
				data: $('#reg-form').serialize(),
				type: 'post',
				dataType: 'json',
				success: function (data) {
					if (data.exception) {
						alert(data.exception);
						togetherVM.verDieta(false);
					} else {
						alert(data.status);
						togetherVM.verDieta(true);
					}
					$btn.button('reset');
				},
				error: function (jqXHR, textStatus, errorThrown) {
					alert(errorThrown)
					togetherVM.found(false);
					$btn.button('reset');
				}
			});
		}
	});
	ko.applyBindings(togetherVM);
});
</script>
<section style="margin-bottom: 60px;" data-spy="scroll" data-target="#bar-together">
	<form id="reg-form" role="form" class="form-horizontal">
		<h1>Anamnesis</h1>
		<h2 id="personales">Datos personales</h2>
		<div class="form-group">
			<label for="tipo_documento" class="col-sm-2 control-label">Tipo de documento</label>
			<div class="col-sm-10">
				<select class="form-control" name="tipo_documento" id="tipo_documento" data-bind="options: tiposDocumento, optionsText: 'nombre', optionsValue: 'valor', value: tipoDoc"></select>
			</div>
		</div>
		<div class="form-group">
			<label for="documento" class="col-sm-2 control-label">N&uacute;mero de identificaci&oacute;n</label>
			<div class="col-sm-10">
				<input type="text" id="documento" class="form-control numero" data-regexp="[0-9.]+" placeholder="numero de documento de identidad" name="documento" id="documento" data-bind="attr: {readonly: found}"/>
			</div>
		</div>
		<div class="form-group">
			<label for="genero" class="col-sm-2 control-label">Genero</label>
			<div class="col-sm-10">
				<select class="form-control" name="genero" id="genero" data-bind="options: generos, optionsText: 'nombre', optionsValue: 'valor', value: genero"></select>
			</div>
		</div>
		<div class="form-group">
			<label for="nombre" class="col-sm-2 control-label">Nombres y apellidos</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" data-regexp="[\w\s]+" placeholder="nombre completo" name="nombre" id="nombre" data-bind="attr: {readonly: found}, value: persona() ? persona().nombres : ''" />
			</div>
		</div>
		<div class="form-group">
			<label for="email" class="col-sm-2 control-label">e-mail</label>
			<div class="col-sm-10">
				<input type="email" class="form-control" data-regexp="[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?" placeholder="correo electronico" name="email" id="email" data-bind="attr: {readonly: found, value: persona() ? persona().email : ''}" />
			</div>
		</div>
		<div class="form-group">
			<label for="nacimiento" class="col-sm-2 control-label">Fecha de nacimiento</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" data-regexp="(0[1-9]|[12][0-9]|3[01])[- /.](0[1-9]|1[012])[- /.](19|20)\d\d" placeholder="dd/mm/aaaa" name="nacimiento" id="nacimiento" data-bind="value: persona() ? persona().fecha_nacimiento : ''" />
			</div>
		</div>
		<div class="form-group">
			<label for="telefono" class="col-sm-2 control-label">Tel&eacute;fono</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" data-regexp="[\w]+" placeholder="fijo o celular" name="telefono" id="telefono" data-bind="value: persona() ? persona().telefono : ''" />
			</div>
		</div>
		<div class="form-group">
			<label for="profesion" class="col-sm-2 control-label">Profesi&oacute;n</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" data-regexp="[\w]+" placeholder="rango o cargo" name="profesion" id="profesion" data-bind="value: persona() ? persona().profesion : ''" />
			</div>
		</div>
		<div class="form-group">
			<label for="afisica" class="col-sm-2 control-label">Nivel de actividad f&iacute;sica</label>
			<div class="col-sm-10">
				<select id="afisica" name="afisica" class="form-control" data-bind="options: afisicas, optionsText: 'nombre', optionsValue: 'valor', value: afisica"></select>
			</div>
		</div>
		<h2 id="antecedentes" style="padding-top: 60px;">Antecedentes</h2>
		<?php
		$i = 0; 
		foreach ($this->antecedentes as $ant) : ?>
		<div class="form-group antecedente">
			<div class="col-sm-4">
				<?php echo $ant->antecedente; ?>
			</div>
			<div class="col-sm-2">
				<label class="radio-inline">
					<input type="radio" class="si" name="antecedente-<?php echo $i; ?>" value="<?php echo $ant->id; ?>"> Si
				</label>
				<label class="radio-inline">
					<input type="radio" class="no" name="antecedente-<?php echo $i; ?>" value="0" checked="checked"> No
				</label>
			</div>
			<div class="col-sm-6">
				<?php if ($ant->cuales) { ?><input type="text" class="form-control cuales" placeholder="cuales?" name="cuales-<?php echo $i; ?>" /><?php } ?>
			</div>
		</div>
		<?php 
		$i++;
		endforeach; ?>
		<h2 id="medidas" style="padding-top: 60px;">Medidas</h2>
		<?php 
		$i = 0;
		foreach ($this->medidas as $med) : ?>
		<div class="form-group medida">
			<label for="valor-<?php echo $i; ?>" class="col-sm-2 control-label"><?php echo $med->medida; ?></label>
			<div class="col-sm-10 input-group">
				<input type="text" class="form-control numero" data-regexp="[0-9.]+" name="valor-<?php echo $i; ?>" id="valor-<?php echo $i; ?>" />
				<span class="input-group-addon"><?php echo $med->unidad; ?></span>
			</div>
			<input type="hidden" class="id-medida" name="medida-<?php echo $i; ?>" value="<?php echo $med->id; ?>" />
		</div>
		<?php 
		$i ++;
		endforeach; ?>
		<input type="hidden" name="option" value="com_together"/>
		<input type="hidden" name="task" value="registrar"/>
	</form>
</section>
<div class="modal fade" id="dietModal" tabindex="-1" role="dialog" aria-labelledby="dietModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="dietModalLabel">Resultado del examen</h4>
			</div>
			<div class="modal-body" data-bind="if: dieta()">
				<h3>Indicadores</h3>
				<table class="table">
					<thead>
						<tr>
							<th>Grasa</th>
							<th>IMC</th>
							<th>Peso magro</th>
							<th>Proteina</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td data-bind="text: dieta().grasa"></td>
							<td data-bind="text: dieta().imc"></td>
							<td data-bind="text: dieta().magro"></td>
							<td data-bind="text: dieta().proteina"></td>
						</tr>
					</tbody>
				</table>
				<h3>Dieta propuesta</h3>
				<table class="table">
					<thead>
						<th>Comida</th>
						<th class="text-center" colspan="2">Proteinas</th>
						<th class="text-center" colspan="2">Carbohidratos</th>
						<th class="text-center" colspan="2">Grasas</th>
					</thead>
					<tbody data-bind="foreach: dieta().dieta">
						<tr>
							<td><strong><span data-bind="text: nombre"></strong></span> <span data-bind="text: k"></span></td>
							<td>
								<span data-bind="text: proteina.nombre"></span>
							</td>
							<td>
								<span data-bind="text: proteina.valor"></span><span data-bind="text: proteina.unidad"></span>
							</td>
							<td>
								<span data-bind="text: carbohidratos.nombre"></span>
							</td>
							<td>
								<span data-bind="text: carbohidratos.valor"></span><span data-bind="text: carbohidratos.unidad"></span>
							</td>
							<td>
								<span data-bind="text: grasas.nombre"></span>
							</td>
							<td>
								<span data-bind="text: grasas.valor"></span><span data-bind="text: grasas.unidad"></span>
							</td>
						</tr>
					</tbody>
				</table>
				<h3>Rutina de ejercicios</h3>
				<div role="tabpanel">
					<ul class="nav nav-tabs" role="tablist">
						<li role="presentation" class="active"><a href="#semana1" aria-controls="semana1" role="tab" data-toggle="tab">Semana 1</a></li>
						<li role="presentation"><a href="#semana2" aria-controls="semana2" role="tab" data-toggle="tab">Semana 2</a></li>
						<li role="presentation"><a href="#semana3" aria-controls="semana3" role="tab" data-toggle="tab">Semana 3</a></li>
						<li role="presentation"><a href="#semana4" aria-controls="semana4" role="tab" data-toggle="tab">Semana 4</a></li>
					</ul>
					<div id="rutina" data-bind="html: dieta().rutina"></div>
				</div>
			</div>
		</div>
	</div>
</div>
<nav id="bar-together" class="navbar navbar-inverse navbar-fixed-bottom">
	<div class="container-fluid">
		<ul class="nav navbar-nav">
			<li class="active">
				<a href="#inicio">
					<span class="glyphicon glyphicon-home" aria-hidden="true"></span>
				</a>
			</li>
			<li>
				<a href="#personales">
					<span class="glyphicon glyphicon-user" aria-hidden="true"></span> Datos personales
				</a>
			</li>
			<li>
				<a href="#antecedentes">
					<span class="glyphicon glyphicon-glass" aria-hidden="true"></span> Antecedentes
				</a>
			</li>
			<li>
				<a href="#medidas">
					<span class="glyphicon glyphicon-dashboard" aria-hidden="true"></span> Medidas
				</a>
			</li>
		</ul>
		<ul class="nav navbar-nav navbar-right">
			<li>
				<button id="btn-nuevo" type="button" class="btn btn-danger navbar-btn" data-loading-text="cargando..." data-bind="enable: found">
					<span class="glyphicon glyphicon-file" aria-hidden="true"></span> Nuevo
				</button>
			</li>
			<li>
				<p class="navbar-text">|</p>
			</li>
			<li>
				<button id="btn-verDieta" type="button" class="btn btn-primary navbar-btn" data-loading-text="cargando..." data-bind="enable: verDieta">
					<span class="glyphicon glyphicon-print" aria-hidden="true"></span> Ver dieta
				</button>
			</li>
			<li>
				<p class="navbar-text">|</p>
			</li>
			<li>
				<button id="btn-enviar" type="button" class="btn btn-warning navbar-btn" data-loading-text="enviando..." data-bind="enable: verDieta">
					<span class="glyphicon glyphicon-envelope" aria-hidden="true"></span> Enviar reporte
				</button>
			</li>
			<li>
				<p class="navbar-text">|</p>
			</li>
			<li>
				<button id="btn-guardar" type="button" class="btn btn-success navbar-btn" data-loading-text="guardando...">
					<span class="glyphicon glyphicon-floppy-save" aria-hidden="true"></span> Guardar
				</button>
			</li>
			<li>
				<p class="navbar-text">&nbsp;</p>
			</li>
		</ul>
	</div>
</nav>
<?php else: ?>
<h1>Sin acceso</h1>
<?php endif; ?>