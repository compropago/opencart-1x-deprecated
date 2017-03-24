<?php if($flags == 0) {?> 
<div class="checkout-content">
  <div class="providers-content">
  <h1>¡Servicio temporalmente fuera de servicio!</h1>
  <p>Favor de seleccionar otro método de pago</p>
  </div>
</div>
<?php }else{ ?> 
<link rel="stylesheet" type="text/css" href="catalog/view/theme/default/stylesheet/compropago.css">
<div class="checkout-heading">Seleccione el establecimiento</div>
<div class="checkout-content">
  <div class="providers-content">
    <form id="cp-form" action="<?php echo $action; ?>" method="post">
      <?php foreach ($providers as $provider) { ?>
        <label class="cp-provider" for="prov_<?php echo $provider->internal_name; ?>" data-provider="<?php echo $provider->internal_name; ?>">
          <img src="<?php echo $provider->image_medium; ?>" alt="<?php echo $provider->name; ?>">
        </label>
      <?php } ?>
      <input type="hidden" value="" name="provider_cp" id="provider_cp">
    </form>
  </div>
</div>
<div class="buttons">
  <div class="right"><input type="submit" id="button_confirm" value="<?php echo $button_confirm; ?>" class="button" /></div>
</div>
<script src="catalog/view/theme/default/javascript/compropago.js"></script>

<?php } ?>

