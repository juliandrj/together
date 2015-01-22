<?php
defined('_JEXEC') or die('Restricted access');
$user = JFactory::getUser(); ?>
<script type="text/javascript" src="http://cdnjs.cloudflare.com/ajax/libs/knockout/3.1.0/knockout-min.js"></script>
<script type="text/javascript" src="<?php echo JUri::root(); ?>templates/together/js/underscore-min.js"></script>
<script type="text/javascript">
$(document).ready(function () {
	var togetherVM = {
		grasa: ko.observable(0),
		imc: ko.observable(0),
		proteina: ko.observable(0),
		magro: ko.observable(0)
	};
	$('#calcular-btn').click(function () {
		var hasErrors = false;
		$('input.numero').parent().parent().removeClass('has-error');
		$('input.numero').each(function (i) {
			if (_.isEmpty($(this).val()) || _.isNaN(parseFloat($(this).val()))) {
				hasErrors = true;
				if (!$(this).parent().parent().hasClass('has-error')) {
					$(this).parent().parent().addClass('has-error');
				}
			} else {
				$(this).val($(this).val().replace(/,/i, '.'));
				$(this).val($(this).val().replace(/[^0-9.]+/i, ''));
			}
		});
		if (!hasErrors) {
			$.ajax({
				url: '<?php echo JUri::root(); ?>index.php',
				data: 'option=com_together&task=calcularIMC&' + $('#imc-form').serialize(),
				type: 'post',
				dataType: 'json',
				success: function (data) {
					if (data.exception) {
						console.log(data.exception);
					} else {
						$('#grasa').val(data.grasa);
						$('#imc').val(data.imc);
						$('#proteina').val(data.proteina);
						$('#magro').val(data.magro);
					}
				},
				error: function (jqXHR, textStatus, errorThrown) {
					console.log(errorThrown)
				}
			});
		}
	});
	ko.applyBindings(togetherVM);
});
</script>
<section>
<?php if (isset($user->groups["10"]) && !$user->guest) : ?>
<form id="imc-form" role="form" class="form-horizontal">
	<div class="row">
		<div class="form-group col-md-6">
			<label for="genero" class="col-sm-4 control-label">Genero</label>
			<div class="col-sm-8">
				<select name="genero" class="form-control">
					<option value="M">Hombre</option>
					<option value="F">Mujer</option>
				</select>
			</div>
		</div>
		<div class="form-group col-md-6">
			<label for="actividad" class="col-sm-4 control-label">Nivel de actividad f&iacute;sica</label>
			<div class="col-sm-8">
				<select id="actividad" name="actividad" class="form-control">
					<option value="1">Sedentario</option>
					<option value="1.32">Ligera</option>
					<option value="1.54">Moderada</option>
					<option value="1.76">Activa</option>
					<option value="1.98">Muy Activa</option>
					<option value="2">Elite</option>
				</select>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="form-group col-md-4">
			<label for="edad" class="col-sm-4 control-label">Edad</label>
			<div class="col-sm-8">
				<input type="text" class="form-control numero" placeholder="edad" name="edad" id="edad" />
			</div>
		</div>
		<div class="form-group col-md-4">
			<label for="talla" class="col-sm-4 control-label">Altura</label>
			<div class="col-sm-8">
				<input type="text" class="form-control numero" placeholder="en centimetros" name="talla" id="talla" />
			</div>
		</div>
		<div class="form-group col-md-4">
			<label for="peso" class="col-sm-4 control-label">Peso</label>
			<div class="col-sm-8">
				<input type="text" class="form-control numero" placeholder="en kilogramos" name="peso" id="peso" />
			</div>
		</div>
	</div>
	<div class="row">
		<div class="form-group col-md-6">
			<label id="cadmunLabel" for="cadmun" class="col-sm-4 control-label">Cadera / mu&ntilde;eca</label>
			<div class="col-sm-8">
				<input type="text" class="form-control numero" placeholder="en centimetros" name="cadmun" id="cadmun" />
			</div>
		</div>
		<div class="form-group col-md-6">
			<label for="cintura" class="col-sm-4 control-label">Cintura</label>
			<div class="col-sm-8">
				<input type="text" class="form-control numero" placeholder="en centimetros" name="cintura" id="cintura" />
			</div>
		</div>
	</div>
	<div class="row">
		<div class="form-group col-md-6">
			<label for="proteina" class="col-sm-4 control-label">Proteina</label>
			<div class="col-sm-8">
				<input type="text" class="form-control" name="proteina" id="proteina" readonly="readonly" data-bind="text: proteina" />
			</div>
		</div>
		<div class="form-group col-md-6">
			<label for="magro" class="col-sm-4 control-label">Peso magro</label>
			<div class="col-sm-8">
				<input type="text" class="form-control" name="magro" id="magro" readonly="readonly" data-bind="text: magro" />
			</div>
		</div>
	</div>
	<div class="row">
		<div class="form-group col-md-6">
			<label for="grasa" class="col-sm-4 control-label">% de grasa</label>
			<div class="col-sm-8">
				<input type="text" class="form-control" name="grasa" id="grasa" readonly="readonly" data-bind="text: grasa" />
			</div>
		</div>
		<div class="form-group col-md-6">
			<label for="imc" class="col-sm-4 control-label">IMC</label>
			<div class="col-sm-8">
				<input type="text" class="form-control" name="imc" id="imc" readonly="readonly" data-bind="text: imc" />
			</div>
		</div>
	</div>
	<div class="form-group">
		<div class="col-sm-12 text-center">
			<button id="calcular-btn" type="button" class="btn btn-primary">
				Calcular
			</button>
		</div>
	</div>
</form>
<?php else : ?>
	<p>Debe tener una cuenta para poder usar el aplicativo.</p>
<?php endif; ?>
</section>
