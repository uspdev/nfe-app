# nfe-app
App demo/teste para consumir o nfe-ws

O APP é uma aplicação em angularjs e utiliza boostrap. font-awesome, jquery e jqueri-ui.
Roda direto no navegador do cliente consultando a API.

Desenvolvido no php 5.5 mas com compatibilidade do php 5.3

### Requisitos necessários ###

A necessidade de processamento e armazenamento é relativamente baixa. Arquivos XML e DANFES PDF são pequenos e não é necessário um grande storage.

- SO: Linux
- Servidor apache
- PHP
- Certificado ICP-BRASIL


### Backup ###

É necessário realizar backup da pasta data, onde ficam todos os arquivos personalizados..

Fazendo backup da pasta da aplicação é o suficiente.


### Deploy ###

O deploy não é automatizado e carece de cuidaos.

Tem de criar a pasta data/ e dentro dela ficam asmazenados todos os arquivos enviados e gerados. 

- A pasta data tem de pertencer ao usuário do apache
- chown www-data:www-data -R ./data

Para realizar consultas na SEFAZ é necessário ter um certificado digital ICP-BRASIL válido.
A senha do webservice fica em config/passwd.txt e pode ser gerada pela sintaxe:

- php passwd.php



### Pessoas envolvidas ###

Masaki Kawabata Neto - kawabata em sc.usp.br