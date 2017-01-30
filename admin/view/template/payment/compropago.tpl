<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
?>

<link rel="stylesheet" href="view/stylesheet/compropago.css">

<?php echo $header; ?>
<div id="content">
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>

  <?php if ($error_warning) { ?>
  <div class="warning"><?php echo $error_warning; ?></div>
  <?php } ?>

  <?php if (isset($compropago_retro_hook)) { ?>
  <div class="warning"><?php echo $compropago_retro_text; ?></div>
  <?php } ?>

  <div class="box">
    <div class="heading">
      <h1><img src="//cdn.compropago.com/cp-assets/ui-compropago/logo.svg" alt="ComproPago" style="height:20px;"/> <?php echo $heading_title; ?></h1>
      <div class="buttons">
        <button id="send-config" class="button"><?php echo $button_save; ?></button>
        <a href="<?php echo $cancel; ?>" class="button"><?php echo $button_cancel; ?></a>
      </div>
    </div>

    <div class="content">
      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
        <table class="form">
          <tr>
            <td><span class="required">*</span> <?php echo $entry_status; ?><br /></td>
            <td>
              <select name="compropago_status">
                <?php if ($compropago_status) { ?>
                <option value="1" selected="selected">Activar</option>
                <option value="0">Desactivar</option>
                <?php } else { ?>
                <option value="1">Activar</option>
                <option value="0" selected="selected">Desactivar</option>
                <?php } ?>
              </select>
            </td>
          </tr>
          <tr>
            <td><span class="required">*</span> <?php echo $entry_title; ?><br></td>
            <td><input type="text" name="compropago_title" value="<?php echo $compropago_title; ?>"></td>
          </tr>
          <tr>
            <td><span class="required">*</span> <?php echo $entry_public_key; ?><br /></td>
            <td><input type="text" name="compropago_public_key" value="<?php echo $compropago_public_key; ?>" /></td>
          </tr>
          <tr>
            <td><span class="required">*</span> <?php echo $entry_secret_key; ?><br /></td>
            <td><input type="text" name="compropago_secret_key" value="<?php echo $compropago_secret_key; ?>" /></td>
          </tr>
          <tr>
            <td><span class="required">*</span> <?php echo $entry_active_mode; ?><br></td>
            <td>
              <select name="compropago_active_mode">
                <?php if ($compropago_active_mode == 'yes') { ?>
                  <option value="yes" selected>Yes</option>
                  <option value="no">No</option>
                <?php } else { ?>
                  <option value="yes">Yes</option>
                  <option value="no" selected>No</option>
                <?php } ?>
              </select>
            </td>
          </tr>
          <tr>
            <td><span class="required">*</span> <?php echo $entry_active_providers; ?></td>
            <td>
                <?php 
                $list = explode(',', $selected_providers);
                foreach ($all_providers  as $provider) {
                  foreach ($list as $key => $value) {
                    if ($value == $provider->internal_name) {
                      $check = 'cp-selected';
                      break;
                    } else {
                      $check = '';
                    }
                  } 
                ?>
                <label class="cp-provider <?php echo $check; ?>" data-provider="<?php echo $provider->internal_name; ?>">
                  <img src="<?php echo $provider->image_medium; ?>" alt="<?php echo $provider->name; ?>">
                </label>
                <?php } ?><br>

                <input type="hidden" value=" <?php echo $selected_providers; ?>" name="compropago_active_providers" id="compropago_active_providers">
            </td>
          </tr>
          <tr>
            <td><span class="required">*</span> <?php echo $entry_order_status_new; ?><br /></td>
            <td>
              <select name="compropago_order_status_new_id">
                <?php foreach ($order_statuses as $order_status) { ?>
                  <?php if ($order_status['order_status_id'] == $compropago_order_status_new_id) { ?>
                    <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                  <?php } else { ?>
                    <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                  <?php } ?>
                <?php } ?>
              </select>
            </td>
          </tr>
          <tr>
            <td><span class="required">*</span> <?php echo $entry_order_status_pending; ?><br /></td>
            <td>
              <select name="compropago_order_status_pending_id">
                <?php foreach ($order_statuses as $order_status) { ?>
                  <?php if ($order_status['order_status_id'] == $compropago_order_status_pending_id) { ?>
                    <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                  <?php } else { ?>
                    <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                  <?php } ?>
                <?php } ?>
              </select>
            </td>
          </tr>
          <tr>
            <td><span class="required">*</span> <?php echo $entry_order_status_approve; ?><br /></td>
            <td>
              <select name="compropago_order_status_approve_id">
                <?php foreach ($order_statuses as $order_status) { ?>
                  <?php if ($order_status['order_status_id'] == $compropago_order_status_approve_id) { ?>
                    <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                  <?php } else { ?>
                    <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                  <?php } ?>
                <?php } ?>
              </select>
            </td>
          </tr>
          <tr>
            <td><span class="required">*</span> <?php echo $entry_order_status_declined; ?><br /></td>
            <td>
              <select name="compropago_order_status_declined_id">
                <?php foreach ($order_statuses as $order_status) { ?>
                  <?php if ($order_status['order_status_id'] == $compropago_order_status_declined_id) { ?>
                    <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                  <?php } else { ?>
                    <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                  <?php } ?>
                <?php } ?>
              </select>
            </td>
          </tr>
          <tr>
            <td><span class="required">*</span> <?php echo $entry_order_status_cancel; ?><br /></td>
            <td>
              <select name="compropago_order_status_cancel_id">
                <?php foreach ($order_statuses as $order_status) { ?>
                  <?php if ($order_status['order_status_id'] == $compropago_order_status_cancel_id) { ?>
                    <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                  <?php } else { ?>
                    <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                  <?php } ?>
                <?php } ?>
              </select>
            </td>
          </tr>
          <tr>
            <td><span class="required">*</span> <?php echo $entry_sort_order; ?><br /></td>
            <td><input type="text" name="compropago_sort_order" value="<?php echo $compropago_sort_order; ?>" /></td>
          </tr>
        </table>
      </form>
    </div>
  </div>
</div>
<script src="view/javascript/compropago.js"></script>
<?php echo $footer; ?>