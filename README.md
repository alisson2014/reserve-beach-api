# Reserve Beach Api

## Configurando o projeto

### Clonar repositório
Em um diretório escolhido, rode o seguinte comando para baixar o projeto via GitHub
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

Caso for utilizar o script é necessário conceder permissão de execução para o mesmo:
```
sudo chmod +x up.sh
```

Dentro da pasta do projeto, rode::
```
./up.sh
```

### Baixar as dependências
Dentro do container do php execute o seguinte comando do composer para baixar todas as dependências do projeto:
```
composer install
```