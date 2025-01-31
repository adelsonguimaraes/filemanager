# File Manager
Projeto simples para gerenciamento de arquivos.

## Tecnologias
* PHP 7+
* AngularJS
* Bootstrap 3.3.7
* Docker
* Docker Compose

## Como executar
Para rodar esse projeto após fazer o clone, é necessário ter o ambiente PHP configurado ou docker.

* Em caso de ter o ambiente PHP configurado basta apenas colocar o projeto dentro de seu htdocs e acessar via navegador.
* Em caso de docker entre na pasta do projeto e rode o seguinte comando.
    ```bash
    docker-compose up --build        
    ```
* Com o comando anterior seu projeto irá baixar a imagem base e buildar sua imagem.
* Ao fim basta acessar o navegador no endereço.
    > http://localhost:8080

## Permissões
Importante que o local onde você colocar seu projeto tenha permissões para escrever nas pastas, pois o PHP precisa para manipulação dos arquivos.

## Configurações
Altere em `app.php` as informações de senha e o nome da sua pasta de projeto.

```php
// altere para o nome da pasta do seu projeto
$raiz = $_SERVER['DOCUMENT_ROOT'] . '/my_project';
```

```php
// altere para o usuário e senha desejado
$user = "admin";
$pass = "admin";
```

## Preview
![](/docs/file_manager.JPG)