<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once __DIR__ . '/../../../vendor/autoload.php';

use CompropagoSdk\Client;
use CompropagoSdk\Factory\Factory;
use CompropagoSdk\Extern\TransactTables;
use CompropagoSdk\Tools\Validations;

class ControllerPaymentCompropago extends Controller 
{
    private $error = array();

    public function install()
    {
        $sql_drop = TransactTables::sqlDropTables(DB_PREFIX);

        foreach($sql_drop as $query){
            $this->db->query($query);
        }

        $sql_create = TransactTables::sqlCreateTables(DB_PREFIX);

        foreach($sql_create as $query){
            $this->db->query($query);
        }
    }

    private function retro()
    {
        $flagerror = false;
        $public_key = $this->config->get('compropago_public_key');
        $private_key = $this->config->get('compropago_secret_key');

        if ($this->config->get('compropago_status') == 1) {
            if (!empty($public_key) && !empty($private_key)) {
                if ($this->config->get('compropago_active_mode')=='yes') {
                    $moduleLive=true;
                } else {
                    $moduleLive=false;
                }
                try {
                    $client = new Client( $public_key, $private_key, $moduleLive);
                    //eval keys
                    if (!$compropagoResponse = Validations::evalAuth($client)) {
                        $this->error[] = 'Invalid Keys, The Public Key and Private Key must be valid before using this module.';
                        $flagerror = true;
                    } else {
                        if ($compropagoResponse->mode_key != $compropagoResponse->livemode) {
                            // compropagoKey vs compropago Mode
                            $this->error[] = 'Your Keys and Your ComproPago account are set to different Modes.';
                            $flagerror = true;
                        } else {
                            if ($moduleLive != $compropagoResponse->livemode) {
                                // store Mode vs compropago Mode
                                $this->error[] = 'Your Store and Your ComproPago account are set to different Modes.';
                                $flagerror = true;
                            } else {
                                if ($moduleLive != $compropagoResponse->mode_key) {
                                    // store Mode vs compropago Keys
                                    $this->error[] = 'ComproPago ALERT:Your Keys are for a different Mode.';
                                    $flagerror = true;
                                } else {
                                    if (!$compropagoResponse->mode_key && !$compropagoResponse->livemode) {
                                        //can process orders but watch out, NOT live operations just testing
                                        $this->error[] = 'WARNING: ComproPago account is Running in TEST Mode, NO REAL OPERATIONS';
                                        $flagerror = true;
                                    } else {
                                        $this->errmsg = '';
                                    }
                                }
                            }
                        }
                    }
                } catch (\Exception $e) {
                    //something went wrong on the SDK side
                    $this->error[] = $e->getMessage(); //may not be show or translated
                    $flagerror = true;
                }
            } else {
                $this->error[] = 'The Public Key and Private Key must be set before using ComproPago';
                $flagerror = true;
            }
        } else {
            $this->error[] = 'ComproPago is not Enabled';
            $this->controlVision='no';
            $flagerror = true;
        }

        return $flagerror;
    }
 
    public function index() 
    {
        $this->language->load('payment/compropago');

        $this->document->setTitle('Compropago Payment Method Configuration');

        $this->load->model('setting/setting');
 
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('compropago', $this->request->post);				
			
			$this->session->data['success'] = $this->language->get('text_success');

			$this->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
		}
 
        $this->data['heading_title'] = $this->language->get('heading_title');

        $this->data['text_edit'] = $this->language->get('text_edit');
        $this->data['text_enabled'] = $this->language->get('text_enabled');
        $this->data['text_disabled'] = $this->language->get('text_disabled');
        $this->data['text_yes'] = $this->language->get('text_yes');
        $this->data['text_no'] = $this->language->get('text_no');

        $this->data['entry_title'] = $this->language->get('entry_title');
        $this->data['entry_secret_key'] = $this->language->get('entry_secret_key');
        $this->data['entry_public_key'] = $this->language->get('entry_public_key');
    
        $this->data['entry_order_status_new'] = $this->language->get('entry_order_status_new');
        $this->data['entry_order_status_pending'] = $this->language->get('entry_order_status_pending');
        $this->data['entry_order_status_approve'] = $this->language->get('entry_order_status_approve');
        $this->data['entry_order_status_declined'] = $this->language->get('entry_order_status_declined');
        $this->data['entry_order_status_cancel'] = $this->language->get('entry_order_status_cancel');
        $this->data['entry_status'] = $this->language->get('entry_status');
        $this->data['entry_sort_order'] = $this->language->get('entry_sort_order');
        $this->data['entry_active_mode'] = $this->language->get('entry_active_mode');
        $this->data['entry_active_providers'] = $this->language->get('entry_active_providers');

        $this->data['help_secret_key'] = $this->language->get('help_secret_key');
        $this->data['help_public_key'] = $this->language->get('help_public_key');

        $this->data['button_save'] = $this->language->get('text_button_save');
        $this->data['button_cancel'] = $this->language->get('text_button_cancel');

        $client = new Client('', '', false);

        $this->data['all_providers'] = $client->api->listProviders();
        $this->data['selected_providers'] = $this->config->get('compropago_active_providers');


        /**
         * Validaciones de error
         */
        if (isset($this->error['warning'])) {
            $this->data['error_warning'] = $this->error['warning'];
        } else {
            $this->data['error_warning'] = '';
        }

        if (isset($this->error['secret_key'])) {
            $this->data['error_secret_key'] = $this->error['secret_key'];
        } else {
            $this->data['error_secret_key'] = '';
        }

        if (isset($this->error['public_key'])) {
            $this->data['error_public_key'] = $this->error['public_key'];
        } else {
            $this->data['error_public_key'] = '';
        }

        /**
         * Generación de breadcrums
         */
        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_home'),
            'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => false
        );

        $this->data['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_payment'),
            'href'      => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );

        $this->data['breadcrumbs'][] = array(
            'text'      => $this->language->get('heading_title'),
            'href'      => $this->url->link('payment/compropago', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );

        $this->data['action'] = $this->url->link('payment/compropago', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');
 

        /**
         * Recuperacion de información
         */
        $this->data['compropago_title'] = isset($this->request->post['compropago_title']) ?
            $this->request->post['compropago_title'] : !empty($this->config->get('compropago_title')) ? 
                $this->config->get('compropago_title') : 'ComproPago - Pagos en efectivo.';

        $this->data['compropago_secret_key'] = isset($this->request->post['compropago_secret_key']) ?
            $this->request->post['compropago_secret_key'] : $this->config->get('compropago_secret_key');

        $this->data['compropago_public_key'] = isset($this->request->post['compropago_public_key']) ?
            $this->request->post['compropago_public_key'] : $this->config->get('compropago_public_key');
        
        $this->data['compropago_order_status_new_id'] = isset($this->request->post['compropago_order_status_new_id']) ?
            $this->request->post['compropago_order_status_new_id'] : $this->config->get('compropago_order_status_new_id');
            
        $this->data['compropago_order_status_approve_id'] = isset($this->request->post['compropago_order_status_approve_id']) ?
            $this->request->post['compropago_order_status_approve_id'] : $this->config->get('compropago_order_status_approve_id');
        
        $this->data['compropago_order_status_pending_id'] = isset($this->request->post['compropago_order_status_pending_id']) ?
            $this->request->post['compropago_order_status_pending_id'] : $this->config->get('compropago_order_status_pending_id');

        $this->data['compropago_order_status_declined_id'] = isset($this->request->post['compropago_order_status_declined_id']) ?
            $this->request->post['compropago_order_status_declined_id'] : $this->config->get('compropago_order_status_declined_id');

        $this->data['compropago_order_status_cancel_id'] = isset($this->request->post['compropago_order_status_cancel_id']) ?
            $this->request->post['compropago_order_status_cancel_id'] : $this->config->get('compropago_order_status_cancel_id');
        
        $this->data['compropago_sort_order'] = isset($this->request->post['compropago_sort_order']) ?
            $this->request->post['compropago_sort_order'] : $this->config->get('compropago_sort_order');
        
        $this->data['compropago_status'] = isset($this->request->post['compropago_status']) ?
            $this->request->post['compropago_status'] : $this->config->get('compropago_status');

        $this->data['compropago_active_mode'] = isset($this->request->post['compropago_active_mode']) ?
            $this->request->post['compropago_active_mode'] : $this->config->get('compropago_active_mode');

        /**
         * Retroalimentación de configuración
         */
        if ($this->retro()){
            $data['compropago_retro_hook'] = true;
            $text = $this->getErrorText();
            $data['compropago_retro_text'] = $text;
            $this->session->data['retro_error'] = $text;
        }

        $this->load->model('localisation/order_status');

        $this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

        $this->template = 'payment/compropago.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
 
        $this->response->setOutput($this->render());
    }

    private function validate() 
    {
        if (!$this->user->hasPermission('modify', 'payment/compropago')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

        if (!$this->request->post['compropago_secret_key']) {
            $this->error['secret_key'] = $this->language->get('error_secret_key');
        }

        if (!$this->request->post['compropago_public_key']) {
            $this->error['public_key'] = $this->language->get('error_public_key');
        }

        return !$this->error;
    }

    private function getErrorText()
    {
        $final = "";
        foreach($this->error as $text){
            $final .= $text;
        }
        return $final;
    }
}