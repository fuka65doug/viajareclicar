# TravelCurator WordPress Plugin

**Versão:** 1.0.0  
**Autor:** Seu Nome  
**Licença:** GPL v2 or later  

## 📋 Descrição

TravelCurator é um plugin WordPress completo para agências de viagem, oferecendo gerenciamento de pacotes turísticos, captura de leads, integração com Elementor e muito mais.

## ✨ Funcionalidades Principais

### 🎯 Gerenciamento de Pacotes
- Custom Post Type para Pacotes de Viagem
- Taxonomias: Destinos e Categorias
- Meta boxes personalizados (preço, duração, dificuldade, destaques, roteiro, galeria)
- Templates customizados para exibição

### 📊 Sistema de Leads
- Formulário de captura de leads integrado
- Dashboard completo para gerenciamento
- Sistema de status (Pendente, Contatado, Convertido, Perdido)
- Exportação de leads
- Integração com WhatsApp

### 📧 Notificações
- E-mail automático para novos leads
- Auto-resposta para clientes
- Notificações personalizáveis
- Central de notificações no admin

### 🎨 Integração Elementor
- **4 Widgets Personalizados:**
  1. Package Grid - Grade de pacotes
  2. Package Carousel - Carrossel de pacotes
  3. Package Search - Busca avançada
  4. Featured Package - Pacote em destaque

### 🔧 Configurações Avançadas
- Configuração de e-mail
- Integração WhatsApp
- CSS personalizado
- Opções de moeda
- E muito mais...

## 📦 Estrutura de Arquivos

```
travelcurator/
├── admin/
│   ├── css/
│   │   ├── admin.css
│   │   └── jquery-ui.css
│   ├── js/
│   │   └── admin.js
│   ├── partials/
│   │   ├── dashboard.php
│   │   ├── leads.php
│   │   ├── settings.php
│   │   └── notifications.php
│   └── class-travelcurator-admin.php
├── public/
│   ├── css/
│   │   └── public.css
│   ├── js/
│   │   └── public.js
│   └── class-travelcurator-public.php
├── includes/
│   ├── class-travelcurator.php (Principal)
│   ├── class-travelcurator-loader.php
│   ├── class-travelcurator-i18n.php
│   ├── class-travelcurator-post-types.php
│   ├── class-travelcurator-taxonomies.php
│   ├── class-travelcurator-meta-boxes.php
│   ├── class-travelcurator-leads.php
│   ├── class-travelcurator-notifications.php
│   └── class-travelcurator-api.php
├── elementor/
│   ├── class-travelcurator-elementor.php
│   └── widgets/
│       ├── package-grid-widget.php
│       ├── package-carousel-widget.php
│       ├── package-search-widget.php
│       └── featured-package-widget.php
├── templates/
│   ├── single-travel_package.php
│   └── archive-travel_package.php
├── languages/
│   └── travelcurator.pot
├── travelcurator.php (Arquivo principal)
├── uninstall.php
└── README.md
```

## 🚀 Instalação

### Método 1: Upload pelo WordPress
1. Faça download do arquivo `travelcurator.zip`
2. No WordPress, vá em **Plugins > Adicionar Novo > Enviar Plugin**
3. Selecione o arquivo ZIP
4. Clique em **Instalar Agora**
5. Ative o plugin

### Método 2: Upload via FTP
1. Descompacte o arquivo `travelcurator.zip`
2. Envie a pasta `travelcurator` para `/wp-content/plugins/`
3. Ative o plugin no painel do WordPress

### Método 3: WP-CLI
```bash
wp plugin install travelcurator.zip --activate
```

## ⚙️ Configuração Inicial

### Passo 1: Configurações Básicas
1. Vá em **TravelCurator > Configurações**
2. Aba **Geral**: Configure nome da empresa, e-mail, telefone
3. Aba **E-mail**: Configure notificações e auto-resposta
4. Aba **WhatsApp**: Configure o botão flutuante
5. Clique em **Salvar Alterações**

### Passo 2: Criar Destinos e Categorias
1. Vá em **Pacotes > Destinos** e adicione destinos
2. Vá em **Pacotes > Categorias** e adicione categorias

### Passo 3: Criar Primeiro Pacote
1. Vá em **Pacotes > Adicionar Novo**
2. Preencha título, descrição e conteúdo
3. Configure preço, duração e dificuldade
4. Adicione destaques e roteiro
5. Selecione destino e categoria
6. Adicione imagem destacada
7. Publique!

### Passo 4: Configurar Elementor (Opcional)
1. Edite uma página com Elementor
2. Procure por widgets "TravelCurator"
3. Arraste e configure os widgets desejados

## 📊 Usando o Dashboard

### Visão Geral
O dashboard mostra:
- Total de pacotes publicados
- Total de leads recebidos
- Leads pendentes
- Taxa de conversão
- Leads recentes
- Pacotes mais procurados

### Gerenciando Leads
1. Vá em **TravelCurator > Gerenciar Leads**
2. Visualize todos os leads recebidos
3. Altere status clicando no dropdown
4. Entre em contato via WhatsApp
5. Exporte leads para CSV

### Filtros Disponíveis
- Todos
- Pendentes
- Contatados
- Convertidos
- Perdidos

## 🎨 Widgets Elementor

### 1. Package Grid
Exibe pacotes em grade com filtros por destino e categoria.

**Opções:**
- Número de pacotes
- Colunas (1-4)
- Ordenação
- Filtros de taxonomia
- Estilo do card

### 2. Package Carousel
Carrossel responsivo de pacotes.

**Opções:**
- Slides visíveis
- Autoplay
- Velocidade
- Setas e pontos
- Loop infinito

### 3. Package Search
Formulário de busca avançada.

**Opções:**
- Filtros (destino, categoria, data, preço)
- Estilo (inline/empilhado)
- Cores personalizáveis

### 4. Featured Package
Destaque um pacote específico.

**Opções:**
- Seleção de pacote
- Layout (lado a lado/overlay/card)
- Informações exibidas
- Personalização visual

## 🔌 REST API

O plugin expõe endpoints REST API:

```
GET /wp-json/travelcurator/v1/packages
GET /wp-json/travelcurator/v1/packages/{id}
POST /wp-json/travelcurator/v1/leads
GET /wp-json/travelcurator/v1/destinations
```

### Exemplo de uso:
```javascript
fetch('/wp-json/travelcurator/v1/packages')
  .then(response => response.json())
  .then(data => console.log(data));
```

## 🎯 Shortcodes

### Formulário de Lead
```
[travelcurator_lead_form package_id="123"]
```

### Grid de Pacotes
```
[travelcurator_packages columns="3" posts_per_page="9"]
```

### Busca
```
[travelcurator_search]
```

## 🌍 Internacionalização

O plugin está pronto para tradução. Arquivo POT incluído em `/languages/`.

### Para traduzir:
1. Use Poedit ou Loco Translate
2. Abra o arquivo `travelcurator.pot`
3. Crie tradução para seu idioma
4. Salve como `travelcurator-{locale}.po` e `.mo`

## 🔧 Hooks e Filtros

### Actions
```php
do_action('travelcurator_before_lead_form');
do_action('travelcurator_after_lead_form');
do_action('travelcurator_lead_submitted', $lead_id);
do_action('travelcurator_package_viewed', $package_id);
```

### Filters
```php
apply_filters('travelcurator_lead_notification_email', $email);
apply_filters('travelcurator_package_price_format', $price);
apply_filters('travelcurator_search_results', $results);
```

## 🐛 Solução de Problemas

### Leads não estão sendo salvos
- Verifique permissões do banco de dados
- Ative o modo debug: `define('WP_DEBUG', true);`
- Verifique o log de erros

### E-mails não estão sendo enviados
- Teste com plugin SMTP (WP Mail SMTP)
- Verifique configurações do servidor
- Use o botão "Testar E-mail" nas configurações

### Templates não aparecem
- Verifique se o tema suporta custom post types
- Copie templates para o tema se necessário
- Limpe o cache

### Widgets Elementor não aparecem
- Certifique-se que Elementor está instalado
- Verifique a categoria "TravelCurator"
- Limpe cache do Elementor

## 📝 Requisitos

- WordPress 5.0 ou superior
- PHP 7.2 ou superior
- MySQL 5.6 ou superior
- Elementor 3.0+ (opcional)

## 🔐 Segurança

- Todas as entradas são sanitizadas
- Proteção contra SQL Injection
- Nonces para verificação de formulários
- Escape de saída
- Permissões verificadas

## 📄 Licença

Este plugin é licenciado sob GPL v2 ou posterior.

## 🤝 Suporte

Para suporte, documentação adicional ou reportar bugs:
- E-mail: seu@email.com
- Website: https://seusite.com
- GitHub: https://github.com/seuusuario/travelcurator

## 📈 Changelog

### Versão 1.0.0 (2025-01-01)
- 🎉 Lançamento inicial
- ✅ Custom Post Type para pacotes
- ✅ Sistema de leads completo
- ✅ 4 Widgets Elementor
- ✅ Dashboard administrativo
- ✅ Sistema de notificações
- ✅ Integração WhatsApp
- ✅ REST API
- ✅ Templates customizados
- ✅ Internacionalização

## 🎉 Créditos

Desenvolvido com ❤️ para agências de viagem.

**Bibliotecas utilizadas:**
- jQuery UI Datepicker
- WordPress Core APIs
- Elementor SDK

---

**Boa sorte com seu site de viagens! ✈️🌍**
