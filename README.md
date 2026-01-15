# Tehokas MVP - Gestão de Projetos Kanban

Sistema de gestão de projetos focado em visualização de saúde e fluxo de tarefas via Kanban. O projeto foi desenvolvido como uma Single Page Application (SPA) utilizando o ecossistema Laravel e Vue.js, priorizando a experiência do usuário e a integridade dos dados.

## Tecnologias Utilizadas

* **Backend:** PHP 8.2, Laravel 11
* **Frontend:** Vue.js 3 (Composition API), Tailwind CSS
* **Fullstack Glue:** Inertia.js
* **Banco de Dados:** MySQL 8.0
* **Ambiente de Desenvolvimento:** Docker (via Laravel Sail)

## Requisitos

* Docker Desktop instalado e rodando.
* Git.

## Instruções de Instalação

Siga os passos abaixo para rodar o projeto em ambiente local utilizando o Laravel Sail.

1. **Clone o repositório:**

```bash
git clone https://github.com/matheusmmandrade/tehokas-mvp.git
cd tehokas-mvp
```

2. **Configure as variáveis de ambiente:**

```bash
cp .env.example .env
```

3. **Instale as dependências do Composer:**

Caso tenha PHP instalado localmente:

```bash
composer install
```

Caso não tenha PHP instalado, utilize um container temporário:

```bash
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php82-composer:latest \
    composer install --ignore-platform-reqs
```

4. **Suba os containers do Docker (Sail):**

```bash
./vendor/bin/sail up -d
```

5. **Gere a chave da aplicação e rode as migrações do banco de dados:**

```bash
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate
```

6. **Instale as dependências do Frontend e inicie o servidor de desenvolvimento:**

```bash
./vendor/bin/sail npm install
./vendor/bin/sail npm run dev
```

7. **Acesse a aplicação no navegador:** http://localhost

## Desafio Técnico e Solução

A maior dificuldade técnica encontrada durante o desenvolvimento foi a implementação da funcionalidade de Drag and Drop no quadro Kanban garantindo a persistência dos dados sem comprometer a experiência do usuário. O desafio principal era sincronizar a atualização visual imediata da interface com a requisição ao banco de dados, evitando que a tela recarregasse ou apresentasse instabilidade visual ("flicker") durante a movimentação dos cards.

Para superar isso, utilizei a API nativa de Drag and Drop do HTML5 integrada ao sistema de roteamento do Inertia.js. A solução consistiu em disparar requisições do tipo PATCH utilizando as opções manuais do Inertia (com a diretiva `preserveScroll: true`), o que permitiu atualizar o status da tarefa no backend e re-renderizar apenas os componentes necessários da interface, mantendo a posição de rolagem e o estado da página intactos.