#!/bin/bash
# Script para corrigir permissões dos binários do node_modules

echo "Corrigindo permissões dos binários..."

# Corrigir permissões de todos os binários executáveis
chmod +x node_modules/.bin/* 2>/dev/null

# Corrigir permissões do esbuild
chmod +x node_modules/@esbuild/linux-x64/bin/esbuild 2>/dev/null
chmod +x node_modules/esbuild/bin/esbuild 2>/dev/null

# Corrigir permissões de arquivos .node (nativos)
find node_modules -type f -name "*.node" -exec chmod +x {} \; 2>/dev/null

# Corrigir permissões de outros binários
find node_modules -type f \( -name "esbuild" -o -name "vite" \) -exec chmod +x {} \; 2>/dev/null

echo "Permissões corrigidas!"
