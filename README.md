# API Agenda Pro

![PHP Version](https://img.shields.io/badge/PHP-7.4%20%7C%208.0%2B-blue)
![License](https://img.shields.io/badge/License-MIT-green)

API REST para o sistema Agenda Pro, construída em Slim 4 com PHP-DI para gerenciamento de agendas profissionais.

## Índice

- [Tecnologias](#tecnologias)
- [Arquitetura](#arquitetura)
- [Pré-requisitos](#pré-requisitos)
- [Instalação](#instalação)
- [Configuração](#configuração)
- [Executar](#executar)
- [Testes](#testes)
- [Análise de Código](#análise-de-código)
- [Estrutura do Projeto](#estrutura-do-projeto)
- [Endpoints da API](#endpoints-da-api)
- [Como Contribuir](#como-contribuir)
- [Licença](#licença)

## Tecnologias

Este projeto utiliza as seguintes tecnologias:

- **PHP 7.4+ / 8.0+** - Linguagem de programação
- **Slim Framework 4.10** - Micro-framework PHP para APIs REST
- **PHP-DI 7** - Container de injeção de dependências
- **Illuminate Database 12.49** - Eloquent ORM para acesso a dados
- **PHPUnit 9.5.26** - Framework de testes unitários
- **PHPStan 1.8** - Ferramenta de análise estática
- **PHP_CodeSniffer 3.7** - Verificador de padrões de código (PSR-12)
- **Phinx 0.16.10** - Gerenciador de migrations de banco de dados
- **Monolog 2.8** - Biblioteca de logging
- **Symfony Mailer 8.0** - Envio de e-mails
- **Stripe PHP 19.3** - Integração com pagamentos Stripe
- **Composer** - Gerenciador de dependências PHP

## Arquitetura

A aplicação segue uma arquitetura em camadas utilizando:

- **Slim 4**: Framework leve para roteamento HTTP e middleware
- **PHP-DI**: Container de injeção de dependências para gerenciar serviços
- **Eloquent ORM**: Object-Relational Mapping para interação com banco de dados
- **PSR-7**: Padrão para mensagens HTTP (request/response)
- **PSR-15**: Padrão para middleware HTTP

A estrutura segue princípios de separação de responsabilidades, com camadas distintas para aplicação, domínio e infraestrutura.

## Pré-requisitos

Antes de começar, certifique-se de ter instalado:

- PHP 8.0 ou superior
- Composer
- MySQL 8.0 ou MariaDB (ou banco de dados compatível)
- Extensões PHP: mbstring, xml, ctype, json, mysql

## Instalação

1. Clone o repositório:
   ```bash
   git clone https://github.com/VK-Tech-software/backend-agenda-pro.git
   cd backend-agenda-pro
   ```

2. Instale as dependências do projeto:
   ```bash
   composer install
   ```

## Configuração

1. Crie o arquivo de ambiente:
   ```bash
   cp .env.example .env
   ```

2. Configure as variáveis de ambiente no arquivo `.env`:
   
   **Banco de Dados:**
   ```env
   DB_HOST=localhost
   DB_PORT=3306
   DB_DATABASE=agenda_pro
   DB_USERNAME=root
   DB_PASSWORD=
   ```

   **SMTP (Email):**
   ```env
   SMTP_HOST=smtp.example.com
   SMTP_PORT=587
   SMTP_USERNAME=user@example.com
   SMTP_PASSWORD=password
   SMTP_FROM=noreply@example.com
   ```

   **CORS:**
   ```env
   CORS_ALLOWED_ORIGINS=http://localhost:3000
   CORS_ALLOW_CREDENTIALS=true
   CORS_ALLOWED_HEADERS=Content-Type,Authorization
   CORS_ALLOWED_METHODS=GET,POST,PUT,DELETE,OPTIONS
   ```

3. Execute as migrations para criar as tabelas do banco de dados:
   ```bash
   vendor/bin/phinx migrate
   ```

## Executar

Para iniciar o servidor de desenvolvimento:

```bash
composer start
```

A API estará disponível em: **http://127.0.0.1:8080**

## Testes

Execute a suíte de testes unitários:

```bash
composer test
```

Para executar testes com cobertura de código:

```bash
vendor/bin/phpunit --coverage-html coverage
```

## Análise de Código

### Análise Estática com PHPStan

```bash
vendor/bin/phpstan analyse
```

### Verificação de Padrões de Código (PSR-12)

```bash
vendor/bin/phpcs
```

Para corrigir automaticamente problemas de estilo:

```bash
vendor/bin/phpcbf
```

## Estrutura do Projeto

```
backend-agenda-pro/
├── app/                  # Configurações da aplicação
│   ├── Bootstrap/        # Inicialização da aplicação
│   ├── Dependencies/     # Configuração do container DI
│   ├── Route/           # Definição de rotas
│   └── settings.php     # Configurações gerais
├── src/                 # Código-fonte principal
│   ├── Application/     # Camada de aplicação (use cases)
│   ├── Domain/          # Camada de domínio (entidades, interfaces)
│   └── Infrastructure/  # Camada de infraestrutura (repositórios, serviços)
├── public/              # Ponto de entrada público
│   └── index.php        # Front controller
├── tests/               # Testes automatizados
├── var/                 # Arquivos temporários e logs
├── composer.json        # Dependências do projeto
├── phpunit.xml          # Configuração do PHPUnit
├── phpstan.neon.dist    # Configuração do PHPStan
└── phinx.php           # Configuração do Phinx (migrations)
```

## Endpoints da API

A API fornece endpoints para gerenciamento de agendas. Documentação detalhada dos endpoints disponíveis:

*(Documentação completa dos endpoints será adicionada em breve)*

### Endpoints Base

- `GET /` - Verificação de saúde da API
- `GET /api/v1/*` - Rotas principais da API

Para mais detalhes, consulte o arquivo `requests.http` no repositório.

## Como Contribuir

Contribuições são bem-vindas! Por favor, leia o [CONTRIBUTING.md](CONTRIBUTING.md) para detalhes sobre nosso código de conduta e processo de envio de pull requests.

## Licença

Este projeto está licenciado sob a Licença MIT - veja o arquivo [LICENSE](LICENSE) para mais detalhes.
