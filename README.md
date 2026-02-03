# Task API

Uma API RESTful para gerenciamento de tarefas desenvolvida com Laravel, featuring autenticação JWT, autorização por roles, testes abrangentes e suporte Docker.

## 🚀 Tecnologias

- **PHP 8.4+**
- **Laravel 12.0**
- **MySQL 8.0**
- **JWT Authentication (tymon/jwt-auth)**
- **Docker & Docker Compose**
- **PHPUnit** para testes
- **Nginx** como web server

## 📋 Funcionalidades

- ✅ Autenticação via JWT
- ✅ Autorização baseada em roles (admin/user)
- ✅ CRUD completo de tarefas
- ✅ Validação de dados via Form Requests
- ✅ Policies para controle de acesso
- ✅ Testes unitários e feature tests
- ✅ Containerização Docker
- ✅ API RESTful bem estruturada

## 🛠️ Pré-requisitos

- PHP >= 8.2
- Composer
- Docker & Docker Compose (opcional)
- MySQL (se não usar Docker)

## 🏃‍♂️ Como Rodar o Projeto

### Opção 1: Composer (Local)

```bash
# Clone o repositório
git clone <repository-url>
cd task-api

# Instale dependências
composer install

# Configure o ambiente
cp .env.example .env
php artisan key:generate

# Execute as migrações
php artisan migrate

# Inicie o servidor de desenvolvimento
composer run dev
```

### Opção 2: Docker (Recomendado)

```bash
# Clone o repositório
git clone <repository-url>
cd task-api

# Suba os containers
docker-compose up -d

# Execute as migrações no container
docker-compose exec app php artisan migrate
```

A API estará disponível em `http://localhost:8000`

## 📡 Endpoints da API

### Autenticação
- `POST /api/auth/login` - Login e geração de token JWT
- `POST /api/auth/logout` - Logout (invalida token)
- `POST /api/auth/refresh` - Refresh token
- `GET /api/auth/me` - Informações do usuário autenticado

### Tarefas
- `GET /api/tasks` - Listar tarefas do usuário
- `POST /api/tasks` - Criar nova tarefa
- `PUT /api/tasks/{id}` - Atualizar tarefa existente
- `DELETE /api/tasks/{id}` - Excluir tarefa

## 🧪 Testes

Para executar a suíte de testes:

```bash
# Via Composer
composer test

# Ou diretamente com PHPUnit
php artisan test
```

## 📁 Estrutura do Projeto

```
task-api/
├── app/
│   ├── Http/
│   │   ├── Controllers/     # Controllers da API
│   │   └── Requests/       # Form Requests
│   ├── Models/              # Models Eloquent
│   ├── Policies/            # Authorization Policies
│   └── Services/            # Lógica de negócio
├── database/
│   ├── migrations/         # Migrações do banco
│   └── factories/          # Factories para testes
├── docker/                 # Configurações Docker
├── tests/                  # Testes unitários e feature
└── docker-compose.yml      # Orquestração Docker
```

## 🔐 Autorização

O sistema implementa controle de acesso baseado em:

- **Roles**: `admin` e `user`
- **Policies**: Regras específicas para operações em tarefas
- **JWT**: Tokens para autenticação stateless

Regras implementadas:
- Apenas usuários autenticados podem acessar endpoints de tarefas
- Usuários só podem visualizar/editar/excluir suas próprias tarefas
- Administradores podem gerenciar todas as tarefas

## 📝 Variáveis de Ambiente

Configure as seguintes variáveis no `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=task_db
DB_USERNAME=root
DB_PASSWORD=root

JWT_SECRET=your-secret-key
```

## 🤝 Como Contribuir

1. Fork o projeto
2. Crie uma branch para sua feature (`git checkout -b feature/nova-funcionalidade`)
3. Commit suas mudanças (`git commit -am 'Add new feature'`)
4. Push para a branch (`git push origin feature/nova-funcionalidade`)
5. Abra um Pull Request

## 📄 Licença

Este projeto está licenciado sob a licença MIT. Veja o arquivo [LICENSE](LICENSE) para mais detalhes.