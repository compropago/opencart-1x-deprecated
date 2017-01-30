<?php 
error_reporting(E_ALL);
ini_set('display_errors', '1');
?>
<?php echo $header; ?><?php echo $column_left; ?><?php echo $column_right; ?>
<div class="content">
    <?php echo $content_top; ?>
    <div class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
        <?php } ?>
    </div>
    <div class="compropagoDivFrame" id="compropagodContainer" style="width: 100%;">
        <iframe style="width: 100%;"
            id="compropagodFrame"
            src="https://www.compropago.com/comprobante/?confirmation_id=<?php echo $cp_order_id; ?>"
            frameborder="0"
            scrolling="yes"> </iframe>
    </div>
    <script type="text/javascript">
        function resizeIframe() {
            var container=document.getElementById("compropagodContainer");
            var iframe=document.getElementById("compropagodFrame");
            if(iframe && container){
                var ratio=585/811;
                var width=container.offsetWidth;
                var height=(width/ratio);
                if(height>937){ height=937;}
                iframe.style.width=width + 'px';
                iframe.style.height=height + 'px';
            }
        }
        window.onload = function(event) {
            resizeIframe();
        };
        window.onresize = function(event) {
            resizeIframe();
        };
    </script>

    <div class="buttons">
        <div class="right"><a href="<?php echo $continue; ?>" class="button"><?php echo $button_continue; ?></a></div>
    </div>
    <?php echo $content_bottom; ?>
</div>
<?php echo $footer; ?>