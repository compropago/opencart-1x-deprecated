<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

require __DIR__ . '/../../../vendor/autoload.php';

use CompropagoSdk\Client;
use CompropagoSdk\Factory\Factory;

class ControllerPaymentCompropago extends Controller
{
    public function index()
    {
        $this->language->load('payment/compropago');
        $this->load->model('setting/setting');
        $this->load->model('checkout/order');
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

        $this->data['text_title'] = $this->language->get('text_title');
        $this->data['button_confirm'] = $this->language->get('button_confirm');

        if ($this->config->get('compropago_active_mode') == 'yes') {
            $auth = true;
        } else {
            $auth = false;
        }

        $client = new Client(
            $this->config->get('compropago_public_key'),
            $this->config->get('compropago_secret_key'),
            $auth
        );

        $providers = $client->api->listProviders(true, floatval($order_info['total']), $order_info['currency_code']);
        $active = explode(',', $this->config->get('compropago_active_providers'));
        $final = [];

        foreach ($providers as $provider) {
            foreach ($active as $key => $value) {
                if ($provider->internal_name == $value) {
                    $final[] = $provider;
                    break;
                }
            }
        }

        $this->data['providers'] = $final;
        $this->data['action'] = $this->url->link('payment/compropago/send');

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/compropago.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/payment/compropago.tpl';
        } else {
            $this->template = 'default/template/payment/compropago.tpl';
        }

        $this->render();
    }

    public function send()
    {
        $this->load->model('setting/setting');
        $this->load->model('checkout/order');
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

        $products = $this->cart->getProducts();
        $order_name = '';

        foreach ($products as $product) {
            $order_name .= $product['name'];
        }

        $data = array(
            'order_id'           => $order_info['order_id'],
            'order_price'        => floatval($order_info['total']),
            'order_name'         => $order_name,
            'customer_name'      => $order_info['payment_firstname'] . ' ' . $order_info['payment_lastname'],
            'customer_email'     => $order_info['email'],
            'payment_type'       => $this->request->post['provider_cp'],
            'currency'           => $order_info['currency_code'],
            'app_client_name'    => 'opencart',
            'app_client_version' => VERSION
        );

        $order = Factory::getInstanceOf('PlaceOrderInfo', $data);

        if ($this->config->get('compropago_active_mode') == 'yes') {
            $auth = true;
        } else {
            $auth = false;
        }

        $client = new Client(
            $this->config->get('compropago_public_key'),
            $this->config->get('compropago_secret_key'),
            $auth
        );

        $new_order = $client->api->placeOrder($order);
        $this->model_checkout_order->confirm($this->session->data['order_id'], $this->config->get('compropago_status'));
        $this->redirect($this->url->link('payment/compropago/success', 'id='.base64_encode($new_order->id)));
    }

    public function success()
    {
        $this->language->load('payment/compropago');
        $this->cart->clear();

        $this->data['cp_order_id'] = base64_decode($this->request->get['id']);
        $this->data['continue'] = $this->url->link('common/home');
        $this->data['button_continue'] = $this->language->get('button_continue');

        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home'),
            'separator' => '::'
        );

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_basket'),
            'href' => $this->url->link('checkout/cart'),
            'separator' => '::'
        );

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_checkout'),
            'href' => $this->url->link('checkout/checkout', '', 'SSL'),
            'separator' => '::'
        );

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_success'),
            'href' => $this->url->link('payment/compropago/success', 'id='.$this->request->get['id']),
            'separator' => '::'
        );

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/compropago_success.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/payment/compropago_success.tpl';
        } else {
            $this->template = 'default/template/payment/compropago_success.tpl';
        }

        $this->children = array(
            'common/column_left',
            'common/column_right',
            'common/content_top',
            'common/content_bottom',
            'common/footer',
            'common/header'
        );

        $this->response->setOutput($this->render());
    }

    public function webhook()
    {
        $this->load->model('setting/setting');
        $this->load->model('checkout/order');
        $request = @file_get_contents('php://input');

        if(!$resp_webhook = Factory::getInstanceOf('CpOrderInfo', $request)){
            die('Invalid Request');
        }

        $publickey     = $this->config->get('compropago_public_key');
        $privatekey    = $this->config->get('compropago_secret_key');
        $live          = ($this->config->get('compropago_active_mode') == 'yes' ? true : false);

        try{
            $client = new Client($publickey, $privatekey, $live );

            if($resp_webhook->id == "ch_00000-000-0000-000000"){
                die("Probando el WebHook?, Ruta correcta.");
            }
        
            $response = $client->api->verifyOrder($resp_webhook->id);

            switch ($response->type){
                case 'charge.success':
                $comment='compropago_order_status_approve_id XXX'.$response->order_info->order_id.'XXX'.$this->config->get('compropago_order_status_approve_id');
                    $this->model_checkout_order->confirm(
                        $response->order_info->order_id,
                        $this->config->get('compropago_order_status_approve_id'),
                        $comment
                    );
                    $this->db->query("UPDATE " . DB_PREFIX . "order SET order_status_id = '" . (int)$this->config->get('compropago_order_status_approve_id') . "' WHERE order_id = '" . (int)$response->order_info->order_id . "'");
                    printf("[%s]\n",      $comment);
                    break;
                case 'charge.pending':
                $comment='compropago_order_status_new_id XXX'.$response->order_info->order_id.'XXX'.$this->config->get('compropago_order_status_new_id');
                    $this->model_checkout_order->confirm(
                        $response->order_info->order_id,
                        $this->config->get('compropago_order_status_new_id'),
                        $comment
                    );
                    
                    $this->db->query("UPDATE " . DB_PREFIX . "order SET order_status_id = '" . (int)$this->config->get('compropago_order_status_new_id') . "' WHERE order_id = '" . (int)$response->order_info->order_id . "'" );
                    printf("[%s]\n",      $comment);


                    break;
                case 'charge.declined':
                    $this->model_checkout_order->confirm(
                        $response->order_info->order_id,
                        $this->config->get('compropago_order_status_declined_id'),
                        $comment
                    );
                    printf("[%s]\n",      $comment);

                    break;
                case 'charge.expired':
                    $this->model_checkout_order->confirm(
                        $response->order_info->order_id,
                        $this->config->get('compropago_order_status_cancel_id'),
                        $comment
                    );
                    $comment = "COMPROPAGO_EXPIRED";
                    printf("[%s]\n",      $comment);
                    printf("[%10s]\n",    $comment);
                    break;
                case 'charge.deleted':
                    $this->model_checkout_order->confirm(
                        $response->order_info->order_id,
                        $this->config->get('compropago_order_status_cancel_id'),
                        $comment
                    );
                    $comment = "COMPROPAGO_DELETED";
                    printf("[%s]\n",      $comment);
                    printf("[%10s]\n",    $comment);

                    break;
                case 'charge.canceled':
                    $this->model_checkout_order->confirm(
                        $response->order_info->order_id,
                        $this->config->get('compropago_order_status_cancel_id'),
                        $comment
                    );
                    $comment = "COMPROPAGO_CANCELED";
                    printf("[%s]\n",      $comment);
                    printf("[%10s]\n",    $comment);

                    break;
                default:
                    die('Invalid Response type');
                }

        }catch (Exception $e) {
            die($e->getMessage());
        }
    }
}
