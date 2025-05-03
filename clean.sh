#!/bin/bash

echo "⚠️  Isto vai apagar TODOS os containers, volumes e imagens não utilizados!"
read -p "Tens a certeza? (s/n): " confirm

if [ "$confirm" != "s" ]; then
    echo "❌ Cancelado."
    exit 1
fi

echo "🧹 A parar containers..."
docker ps -aq | xargs -r docker stop

echo "🗑️  A remover containers..."
docker ps -aq | xargs -r docker rm

echo "🧼 A remover volumes..."
docker volume ls -q | xargs -r docker volume rm

echo "🔥 A remover imagens..."
docker images -q | xargs -r docker rmi -f

echo "✅ Docker limpo com sucesso."

