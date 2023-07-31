<h1 align="center">
    Api Laravel Saúde
</h1>

<p align="center">

  <img alt="Repository size" src="https://img.shields.io/github/languages/count/yhanndaniel/api-laravel-saude">
  
  <a href="https://github.com/yhanndaniel/api-laravel-saude/commits/master">
    <img alt="GitHub last commit" src="https://img.shields.io/github/last-commit/yhanndaniel/api-laravel-saude">
  </a>

  <a href="https://github.com/yhanndaniel/api-laravel-saude/issues">
    <img alt="Repository issues" src="https://img.shields.io/github/issues/yhanndaniel/api-laravel-saude">
  </a>

  <img alt="License" src="https://img.shields.io/badge/license-MIT-brightgreen">
</p>

<p align="center">
  <a href="#rocket-tecnologias">Tecnologias</a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;
  <a href="#-projeto">Projeto</a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;  
</p>

<br>
## :rocket: Tecnologias

Esse projeto foi desenvolvido com as seguintes tecnologias:

- [PHP](https://www.php.net/)
- [Laravel](https://laravel.com/)
- [MySQL](https://www.mysql.com/)
- [Docker](https://www.docker.com/)

## 💻 Projeto

Uma api com os recursos de Cidades, Medicos e Pacientes Utilizando Laravel mais as melhores praticas pra desenvolver e testes de integração, para rodar o projeto utilize os seguintes comandos:

```
./vendor/bin/sail up -d
./vendor/bin/sail composer install
./vendor/bin/sail artisan migrate:fresh --seed 
./vendor/bin/sail artisan test

```

Depois é só clicar no botão do postman [![Run in Postman](https://run.pstmn.io/button.svg)](https://god.gw.postman.com/run-collection/9544205-3ebcf897-05b9-4249-899e-4e6e17837a89?action=collection%2Ffork&source=rip_markdown&collection-url=entityId%3D9544205-3ebcf897-05b9-4249-899e-4e6e17837a89%26entityType%3Dcollection%26workspaceId%3D3ae1ce10-5edd-46e0-a9b1-400b5b0daf33)

e testa a aplicação
