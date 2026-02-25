# API Agenda Pro

API REST para o sistema Agenda Pro, construída em Slim 4 com PHP‑DI.

## Requisitos

- PHP 8.0+
- Composer
- Banco de dados MySQL/MariaDB (ou compatível)

## Configuração

1. Instale as dependências:
	- `composer install`
2. Crie o arquivo de ambiente:
	- copie `.env.example` para `.env`
3. Ajuste as variáveis de banco, token e SMTP em `.env`.

Se houver migrations, execute conforme o seu fluxo (ex.: Phinx).

## Executar

- `composer start`
- Acesse: http://127.0.0.1:8080

## Testes

- `composer test`

## Autenticação

- Fluxo principal: sessão por cookie (`HttpOnly`).
- Endpoint de sessão atual: `GET /auth/me`.
- Logout de sessão: `POST /auth/logout`.
- Compatibilidade legada por bearer pode ser habilitada via:
	- `AUTH_ALLOW_BEARER_FALLBACK=true`

## CORS

Configure as variáveis abaixo em `.env`:

- `CORS_ALLOWED_ORIGINS` (lista separada por vírgula)
- `CORS_ALLOW_CREDENTIALS`
- `CORS_ALLOWED_HEADERS`
- `CORS_ALLOWED_METHODS`

## Sessão (cookie)

- `SESSION_COOKIE_NAME`
- `SESSION_COOKIE_SECURE`
- `SESSION_COOKIE_HTTP_ONLY`
- `SESSION_COOKIE_SAME_SITE`
- `SESSION_COOKIE_PATH`
- `SESSION_COOKIE_DOMAIN`
- `SESSION_COOKIE_LIFETIME`

## Observabilidade

- Todas as respostas incluem `X-Request-Id`.
- Logs de acesso são gravados em `var/logs/app.log` com `request_id`, status e latência.

## WhatsApp (aprovação de solicitação)

Quando uma solicitação de agendamento é aprovada, a API tenta enviar mensagem WhatsApp para o telefone do cliente (`client_phone`).

Se a integração estiver desabilitada ou falhar, a aprovação continua normalmente e o evento é registrado em log.
