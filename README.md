## 📃 Sobre
Este repositório contém os arquivos do teste prático para vaga de programador PHP Júnior.
Contém uma API REST em **PHP** como back-end e **Mysql** como banco de dados, uma aplicação em **HTML/CSS/Javascript** como front-end.
Esta plicação permite abrir chamados, editar um chamado já cadastrado, excluir um chamado e listar os chamados cadastrados.

**PHP**: Foi utilizado para criar uma API REST que faz todo o CRUD da aplicação, sendo responsável pela regra de negócio e pela persistência no banco dados, além de tratar as exceções e servir os dados tanto ao front-end web quanto a qualquer API Client.

## 📚 Requisitos
- [**Git**](https://git-scm.com/) para clonar o projeto.
- [**PHP**](https://www.php.net/downloads.php) instalado.
- [**Mysql**](https://dev.mysql.com/downloads/) para persistir os dados.
- Um navegador

O projeto pode ser visto em execução aqui: [http://desafiovdt.x10.mx/](http://desafiovdt.x10.mx/)

## ▶ Começando
``` bash
  # Clonar o projeto:
  $ git clone https://github.com/jojsj/desafioVerdanatech

  # Entrar no diretório:
  $ cd desafioVerdanatech
```

## ⚙️ Iniciando back-end

```bash
  # Entrar com seu usuário do mysql e inserir a senha
  # Esta configuração pode ser alterada em server/conf/database.php
  # Padrão do projeto: user:admin pass:CAFEBABE?i{^8
  $ mysql -u [user] -p

  # Rodar o script sql:
  mysql> source ./server/conf/database.sql

  # Sair do mysql:
  mysql> exit

  # Rodar a aplicação:
  $ php -S localhost:8000
```

Pronto! Agora é só abrir o navegador e ir em  [http://localhost:8000](http://localhost:8000)
