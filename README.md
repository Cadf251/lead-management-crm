# new_crm
New version of and old CRM project.

## Requisitos

* PHP 8.3 ou superior;
* Apache;
* rewrite_module ativo no Apache;
* MySQL 8.0 ou superior;
* Composer;

## Como rodar o projeto baixado

Duplicar o arquivo ".env.example" e renomear para ".env".<br>
Alterar no arquivo .env as credenciais do banco de dados.<br>

# Sequência para instalar o projeto
Criar o composer
```
composer init
```

Instalar as dependências do composer "vendor"
```
composer install
```

Instalar a biblioteca PHP para criar logs
```
composer require monolog/monolog
```

Instala as dependências para usar variáveis de ambiente
```
composer require vlucas/phpdotenv
```

Instala o PHP Mailer
```
composer require phpmailer/phpmailer
```

### Como usar o Github
Criar uma vez
### git init

Baixar os arquivos do Git
## git clone --branch 1.0.0 https://github.com/Cadf251/new_crm.git

Verifica se há alterações no github
### git pull

Adiciona um arquivo específico
### git add <file>
### git add

Adiciona todos os arquivos modificados
### git add .

Verifica as alterações atuais e se foram adicionadas
### git status

Registra um conjunto de alterações na história do projeto com uma mensagem
### git commit -m "Mensagem"

Empurra os arquivos para o github
### git push <remote> <branch>
### git push origin 1.0.0