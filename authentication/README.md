# Sistema de autenticação com PHP Slim e Twig.

Este projeto é um sistema de autenticação simples desenvolvido em PHP, utilizando o microframework Slim (versão 4) e o mecanismo de templates Twig.

## Funcionalidades
- Cadastro de usuários com validação e hash de senhas.
- Login seguro com limitação de tentativas.
- Logout funcional para encerrar sessões.
- Exibição de mensagens de erro amigáveis.
- Home page com saudação personalizada ao usuário logado.
- Rotas seguras com gerenciamento de sessões.

## Requisitos
- **PHP**: Versão 8.1 ou superior.
- **Composer**: Para gerenciar as dependências.
- Servidor Web (XAMPP, Apache, Nginx ou PHP embutido).

## Instalação

1. Clone o repositório:
   ```bash
   git clone https://github.com/Daniel5008/php_projects.git
   cd seu-repositorio

3. Instale as dependências do projeto:

composer install

3. Configure o ambiente:
- Crie um arquivo .env na raiz do projeto e adicione as credenciais do banco de dados:
  DB_HOST=dbhost
  DB_NAME=dbname
  DB_USER=dbuser
  DB_PASSWORD=dbpasword

4. Configure o banco de dados:
- Crie o banco de dados authentication_db e execute o script SQL fornecido na pasta src/database.

5. Inicie o servidor:
   ````bash
   php -S localhost:8080 -t public

7. Acesse o sistema: Abra seu navegador e vá para http://localhost:8080.

## Estrutura do projeto
project/
├── public/                # Arquivos acessíveis publicamente
│   ├── index.php          # Arquivo principal do Slim
│   ├── css/               # Arquivos CSS externos
├── src/                   # Código fonte
│   ├── Database/          # Banco de dados
│   ├── Models/            # Classes de manipulação de dados
│   └── functions/         # Utilitários em geral
├── templates/             # Arquivos Twig (views)
├── .env                   # Configurações do ambiente (ignorado pelo Git)
├── composer.json          # Dependências do projeto
└── README.md              # Este arquivo

