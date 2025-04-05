# Reserve Beach Api

__Rest API__ que servirá todos os metódos e dados necessários para o funcionamento do sistema web Reserve Beach, que será um 
gerenciador de quadras esportivas, com foco na área do beach tennis.

## Configurando o projeto

### Clonar repositório
Em um diretório escolhido, rode o seguinte comando para __baixar__ o projeto via __GitHub__:
1. Navegar até o diretório
```
cd caminho/Do/Seu/Diretorio
```

2. Clonar o repositório:
```
git clone https://github.com/alisson2014/reserve-beach-api.git
```

### Criar o arquivo .env e .env.dev
Para criar os arquivos `.env` e `.env.dev` a partir dos arquivos de exemplo, execute os seguintes comandos no terminal:

```
cp .env.example .env
cp .env.dev.example .env.dev
```

Não é necessário mudar nada nesses arquivos para o projeto funcionar corretamente, mas você pode personalizar as variáveis, se preferir.

### Subir os serviços do projeto
Para subir os serviços necessários para o projeto funcionar pode ser usado o script up.sh, que é um alias para os comandos do docker compose.

- Caso for utilizar o script é necessário conceder permissão de execução para o mesmo:
```
sudo chmod +x up.sh
```

- Dentro da pasta do projeto, rode::
```
./up.sh
```

### Acessar terminal Linux dentro container do php
Caso queira ter acesso ao composer ou ao php, os dois estão instalados no container do php que pode ser acessado via Docker:
```
docker compose exec -it php sh
```

### Baixar as dependências
- Caso já esteja dentro do container do php, execute o seguinte comando do composer para baixar todas as dependências do projeto:
```
composer install
```

- Fora do container:
```
docker compose exec -it php composer install
```

### Executar migrations
- Caso já esteja dentro do container do php, execute o seguinte comando do doctrine para atualizar o banco de dados:
```
php bin/console doctrine:migrations:migrate
```

- Fora do container:
```
docker compose exec -it php php bin/console doctrine:migrations:migrate
```