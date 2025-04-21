<?php
// Impede acesso direto
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Classe central para integrar diferentes gateways de pagamento.
 */
class GaragePro_Payment_Gateway {
    private $gateway;

    // Inicializa o gateway com base na configuração
    public function __construct($gateway_type = 'picpay') {
        if ($gateway_type === 'paypal') {
            require_once 'api-paypal.php';
            $this->gateway = new GaragePro_PayPal();
        } else {
            require_once 'api-picpay.php';
            $this->gateway = new GaragePro_PicPay();
        }
    }

    // Método para criar uma cobrança
    public function create_charge($referenceId, $value, $buyer) {
        return $this->gateway->create_charge($referenceId, $value, $buyer);
    }

    // Método para consultar o status de pagamento
    public function get_payment_status($referenceId) {
        return $this->gateway->get_payment_status($referenceId);
    }

    // Método para cancelar uma cobrança
    public function cancel_charge($referenceId) {
        return $this->gateway->cancel_charge($referenceId);
    }
}