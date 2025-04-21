# Changelog - GaragePro Mercado Pago Plugin

## [1.1.0] - 2025-04-21
### Alterado
- Substituída a integração com o PicPay pela integração com o Mercado Pago.
- Atualização dos endpoints REST API para usar a API do Mercado Pago.
- Adicionado suporte para autenticação e configuração do token do Mercado Pago.

## [1.0.0] - 2025-04-20
### Adicionado
- Criação do plugin GaragePro PicPay.
- Integração com a API do PicPay para criar, consultar e cancelar cobranças.
- Registro de cobranças no banco de dados via Custom Post Type (CPT).
- Webhook para manipular notificações de pagamento do PicPay.
- Controle de acesso para usuários inadimplentes.
- Registro, login e edição de membros via shortcodes.
- Integração com ACF para popular campos personalizados com dados do usuário.
- Endpoints REST API personalizados para integração com sistemas externos.
- Sistema de logs para depuração e auditoria.
- Configuração centralizada no arquivo `config.php`.