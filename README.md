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

## CORS

Configure as variáveis abaixo em `.env`:

- `CORS_ALLOWED_ORIGINS` (lista separada por vírgula)
- `CORS_ALLOW_CREDENTIALS`
- `CORS_ALLOWED_HEADERS`
- `CORS_ALLOWED_METHODS`
