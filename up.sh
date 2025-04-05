#!/bin/bash

# Subir os serviços principais
docker compose up -d --build

# Subir o serviço adicional (phpMyAdmin)
docker compose -f docker-compose.phpmyadmin.yml up -d --build

# Abrir o vscode na pasta atual
code .