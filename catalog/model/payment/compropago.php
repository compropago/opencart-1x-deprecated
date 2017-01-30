<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

class ModelPaymentCompropago extends Model 
{
    public function getMethod($address, $total) 
    {
        $this->load->language('payment/compropago');
        $this->load->model('setting/setting');
        
        $method_data = array(
            'code'       => 'compropago',
            'title'      => !empty($this->config->get('compropago_title')) ? $this->config->get('compropago_title') : 'ComproPago - Pagos en efectivo.',
            'terms'      => true,
            'sort_order' => $this->config->get('compropago_sort_order')
        );
        
        return $method_data;
    }
}