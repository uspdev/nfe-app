# nfe-app
App demo/teste para consumir o [nfe-ws](https://github.com/uspdev/nfe-ws) utilizando a biblioteca [nfe-ws-consumer](https://github.com/uspdev/nfe-ws-consumer).

A aplicação é desenvolvida em PHP e utiliza Bootstrap para a interface.

### Instalação

Instale as dependências via Composer:

```bash
composer install
```

Copie o arquivo `config.sample.php` para `config.php` e ajuste conforme necessário:

```php
$ws_url = 'http://servidor/nfe-ws/api';
$ws_api_key = 'sua-api-key-gerada';
```

### Utilização

Aponte o servidor web (Apache, Nginx ou servidor embutido do PHP) para a pasta `www/`.

Exemplo com o servidor embutido do PHP para testes:

```bash
php -S localhost:8000 -t www/
```

### Pessoas envolvidas

Masaki Kawabata Neto - kawabata em sc.usp.br
