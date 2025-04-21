# GaragePro Mercado Pago Plugin

## Descrição
O **GaragePro Mercado Pago** é um plugin para WordPress que integra o sistema de pagamentos do Mercado Pago, permitindo a criação e gestão de cobranças, controle de inadimplência e integração com membros do site.

---

## Funcionalidades
- **Gestão de Cobranças**: Criação, atualização e consulta de cobranças via API do Mercado Pago.
- **Webhooks**: Manipulação de notificações de pagamento do Mercado Pago.
- **Controle de Acesso**: Restringe acesso a páginas para usuários inadimplentes.
- **Gestão de Membros**: Registro, login e edição de dados de usuários.
- **Integração com ACF (Advanced Custom Fields)**: Popula campos personalizados com dados do usuário.
- **REST API Personalizada**: Endpoints para integração com sistemas externos.
- **Logs Personalizados**: Registra logs em um arquivo para debug e auditoria.

---

## Instalação

1. Faça o upload da pasta do plugin para o diretório `wp-content/plugins` do seu site WordPress.
2. Ative o plugin no painel administrativo do WordPress.
3. Configure o arquivo `config.php` com o token da API do Mercado Pago e URLs apropriadas.

---

## Configurações

O arquivo `config.php` contém as configurações centrais do plugin:

- **`mercadopago_access_token`**: O token de autenticação da API do Mercado Pago.
- **`callback_url`**: Endpoint para notificações de pagamento (webhook).
- **`return_url`**: Página para redirecionar usuários após o pagamento.
- **`log_enabled`**: Ativa ou desativa o registro de logs.

---

## Shortcodes Disponíveis

- `[garagepro_register_form]`: Exibe o formulário de registro de usuário.
- `[garagepro_login_form]`: Exibe o formulário de login de usuário.
- `[garagepro_edit_profile_form]`: Exibe o formulário de edição de perfil do usuário.

---

## Endpoints REST API

### 1. Webhook do Mercado Pago
**URL**: `/wp-json/garagepro-mercadopago/v1/webhook`  
**Método**: POST  
**Descrição**: Manipula notificações enviadas pelo Mercado Pago para atualizar o status de cobranças.

### 2. Listar Cobranças de um Usuário
**URL**: `/wp-json/garagepro-mercadopago/v1/cobrancas/{user_id}`  
**Método**: GET  
**Descrição**: Retorna todas as cobranças associadas a um usuário.  
**Autenticação**: Obrigatório enviar um token no cabeçalho `Authorization`.

### 3. Criar Cobrança
**URL**: `/wp-json/garagepro-mercadopago/v1/cobrancas`  
**Método**: POST  
**Body**:
```json
{
  "referenceId": "123456",
  "userId": 1,
  "value": 100.50,
  "status": "pendente"
}
```
**Descrição**: Cria uma nova cobrança no sistema.  
**Autenticação**: Obrigatório enviar um token no cabeçalho `Authorization`.

---

## Diretórios e Arquivos

- **`garagepro-mercadopago.php`**: Arquivo principal do plugin.
- **`/includes`**:
  - `api-mercadopago.php`: Integração com a API do Mercado Pago.
  - `cobrancas.php`: Gestão de cobranças.
  - `webhook-handler.php`: Manipulação de webhooks.
  - `controle-acesso.php`: Controle de acesso para inadimplentes.
  - `membros.php`: Registro, login e edição de membros.
  - `acf-integration.php`: Integração com ACF.
  - `config.php`: Configurações do plugin.
  - `helper-functions.php`: Funções utilitárias.
- **`/logs`**:
  - `garagepro-mercadopago.log`: Arquivo de logs.

---

## Requisitos

- WordPress 5.0 ou superior.
- PHP 7.4 ou superior.
- Plugin **Advanced Custom Fields (ACF)** (opcional, para integração de campos personalizados).

---

## Suporte
Para dúvidas ou problemas, entre em contato com o autor do plugin ou consulte a documentação oficial do WordPress.