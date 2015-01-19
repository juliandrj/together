<?php
defined('_JEXEC') or die('Restricted access');
$user = JFactory::getUser(); ?>
<script type="text/javascript" src="http://cdnjs.cloudflare.com/ajax/libs/knockout/3.1.0/knockout-min.js"></script>
<script type="text/javascript">
$(document).ready(function () {
	var togetherVM = {
		grasa: ko.observable(0),
		imc: ko.observable(0)
	};
	$('#calcular-btn').click(function () {
		$.ajax({
			url: '<?php echo JUri::root() ?>/index.php',
			data: 'option=com_together&task=calcularIMC&' + $('#imc-form').serialize(),
			type: 'post',
			dataType: 'json',
			success: function (data) {
				if (data.exception) {
					console.log(data.exception);
				} else {
					$('#grasa').val(data.grasa);
					$('#imc').val(data.imc);
				}
			},
			error: function (jqXHR, textStatus, errorThrown) {
				console.log(errorThrown)
			}
		});
	});
	ko.applyBindings(togetherVM);
});
</script>
<section>
<?php if (isset($user->groups["10"]) && !$user->guest) : ?>
<form id="imc-form" role="form" class="form-horizontal">
	<div class="form-group">
		<label for="genero" class="col-sm-1 control-label">Genero</label>
		<div class="col-sm-3">
			<select name="genero" class="form-control">
				<option value="*">---</option>
				<option value="M">Hombre</option>
				<option value="F">Mujer</option>
			</select>
		</div>
		<label for="edad" class="col-sm-1 control-label">Edad</label>
		<div class="col-sm-3">
			<input type="text" class="form-control" placeholder="edad" name="edad" id="edad" />
		</div>
		<label for="talla" class="col-sm-1 control-label">Talla</label>
		<div class="col-sm-3">
			<input type="text" class="form-control" placeholder="en centimetros" name="talla" id="talla" />
		</div>
	</div>
	<div class="form-group">
		<label id="cadmunLabel" for="cadmun" class="col-sm-1 control-label">Cadera / mu&ntilde;eca</label>
		<div class="col-sm-3">
			<input type="text" class="form-control" placeholder="en centimetros" name="cadmun" id="cadmun" />
		</div>
		<label for="cintura" class="col-sm-1 control-label">Cintura</label>
		<div class="col-sm-3">
			<input type="text" class="form-control" placeholder="en centimetros" name="cintura" id="cintura" />
		</div>
		<label for="peso" class="col-sm-1 control-label">Peso</label>
		<div class="col-sm-3">
			<input type="text" class="form-control" placeholder="en kilogramos" name="peso" id="peso" />
		</div>
	</div>
	<div class="form-group">
		<label for="grasa" class="col-sm-2 control-label">% de grasa</label>
		<div class="col-sm-4">
			<input type="text" class="form-control" name="grasa" id="grasa" readonly="readonly" data-bind="text: grasa" />
		</div>
		<label for="imc" class="col-sm-2 control-label">IMC</label>
		<div class="col-sm-4">
			<input type="text" class="form-control" name="imc" id="imc" readonly="readonly" data-bind="text: imc" />
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
