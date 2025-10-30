# TravelCurator - Guia de Shortcodes

Este documento descreve todos os shortcodes disponíveis no plugin TravelCurator.

## 📦 Shortcodes Disponíveis

### 1. Grid de Pacotes
Exibe uma grade de pacotes de viagem.

```
[travelcurator_packages limit="6" columns="3" category="" destination="" purpose="" orderby="date" order="DESC"]
```

**Parâmetros:**
- `limit` - Número de pacotes a exibir (padrão: 6)
- `columns` - Número de colunas (1-4, padrão: 3)
- `category` - Filtrar por categoria (slug da categoria)
- `destination` - Filtrar por destino (slug do destino)
- `purpose` - Filtrar por propósito emocional (slug do propósito)
- `orderby` - Ordenar por: date, title, menu_order, rand (padrão: date)
- `order` - Ordem: ASC ou DESC (padrão: DESC)

**Exemplos:**
```
[travelcurator_packages limit="9" columns="3"]
[travelcurator_packages limit="4" columns="2" category="aventura"]
[travelcurator_packages limit="6" columns="3" destination="europa" orderby="title"]
```

---

### 2. Pacote Específico
Exibe um pacote específico por ID.

```
[travelcurator_package id="123"]
```

**Parâmetros:**
- `id` - ID do pacote (obrigatório)

**Exemplo:**
```
[travelcurator_package id="45"]
```

---

### 3. Filtro de Busca
Exibe um formulário de busca e filtros para pacotes.

```
[travelcurator_search show_category="yes" show_destination="yes" show_price="yes"]
```

**Parâmetros:**
- `show_category` - Mostrar filtro de categoria (yes/no, padrão: yes)
- `show_destination` - Mostrar filtro de destino (yes/no, padrão: yes)
- `show_price` - Mostrar filtro de preço (yes/no, padrão: yes)

**Exemplos:**
```
[travelcurator_search]
[travelcurator_search show_price="no"]
[travelcurator_search show_category="yes" show_destination="yes" show_price="no"]
```

---

### 4. Formulário de Lead
Exibe um formulário para capturar leads interessados em um pacote.

```
[travelcurator_lead_form package_id="0" title="Tenho Interesse!"]
```

**Parâmetros:**
- `package_id` - ID do pacote (0 para formulário genérico)
- `title` - Título do formulário (padrão: "Tenho Interesse!")

**Exemplos:**
```
[travelcurator_lead_form package_id="45"]
[travelcurator_lead_form package_id="0" title="Fale Conosco"]
```

---

### 5. Pacotes em Destaque
Exibe apenas os pacotes marcados como destaque.

```
[travelcurator_featured limit="4" columns="2"]
```

**Parâmetros:**
- `limit` - Número de pacotes a exibir (padrão: 4)
- `columns` - Número de colunas (1-4, padrão: 2)

**Exemplos:**
```
[travelcurator_featured]
[travelcurator_featured limit="6" columns="3"]
```

---

## 🎨 Widgets do Elementor

O plugin também fornece widgets nativos para o Elementor:

1. **Pacotes de Viagem - Grid** - Grid configurável de pacotes
2. **Detalhes do Pacote** - Exibe detalhes de um pacote específico
3. **Filtro de Busca** - Formulário de busca com filtros
4. **Formulário de Lead** - Formulário de captura de leads

Para usar os widgets, edite sua página no Elementor e procure pela categoria "TravelCurator" no painel de widgets.

---

## 📋 Notas Importantes

### Status dos Pacotes
Para que um pacote seja exibido publicamente, ele deve:
1. Estar **Publicado** (não em rascunho)
2. Ter o status definido como **"Ativo"** no meta box "Status do Pacote"

Os pacotes com status "Rascunho", "Inativo" ou "Esgotado" não serão exibidos nos shortcodes e widgets.

### Personalização
Todos os shortcodes incluem estilos CSS inline que podem ser sobrescritos pelo tema. Para personalizar completamente, adicione seus estilos customizados no CSS do seu tema.

### Suporte
Para mais informações e suporte, visite: https://viajareclicar.com.br

---

**Versão:** 1.0.0
**Última atualização:** Outubro 2024
