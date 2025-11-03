# Changelog - TravelCurator Plugin v1.0.2

## [1.0.2] - 2024-11-03

### 🎨 Redesign Completo da Interface

#### 1. Menu Administrativo Corrigido
- **Problema:** Item "Todos os Pacotes" aparecia duplicado no menu administrativo
- **Solução:** Removida criação manual de submenus em `admin/class-travelcurator-admin.php` (linhas 92-107)
- **Motivo:** WordPress já cria automaticamente estes itens quando o post type tem `'show_in_menu' => 'travelcurator'`

#### 2. Herança de Fontes e Cores do Tema
- **Implementação:** Todos os componentes agora herdam fontes e cores do tema ativo
- **Variáveis CSS utilizadas:**
  - `var(--wp--preset--font-family--heading)` para títulos
  - `var(--wp--preset--font-family--body)` para texto
  - `var(--wp--preset--color--text)` para cor do texto
  - `var(--wp--preset--color--heading)` para cor dos títulos
- **Fallbacks:** Valores padrão definidos caso o tema não forneça as variáveis
- **Arquivo:** `includes/elementor/assets/widgets-new.css`

#### 3. Novo Header com Filtros por Propósito Emocional
- **Design:** Seção com gradiente terracota/dourado no topo
- **Filtros em Pills:**
  - "Todas as Experiências" (ativo por padrão)
  - Filtros dinâmicos baseados na taxonomia `travel_purpose`
  - Estilo pill com bordas arredondadas
  - Filtro ativo destacado em dourado (#D4B254)
- **Conteúdo:**
  - Título: "Nossas Experiências Curadas"
  - Subtítulo explicativo sobre curadoria emocional
- **Funcionalidade:** Filtro AJAX sem recarregar página

#### 4. Cards de Pacotes Redesenhados
Novo design baseado no mockup sc-001.png:

**Estrutura Visual:**
- Badge de propósito emocional no topo esquerdo (dourado)
- Ícone emoji centralizado sobre a imagem (🍷, 🎉, 🧭, etc.)
- Gradiente diagonal sobre imagem (azul/dourado)
- Badge de categoria em vermelho terracota
- Título em fonte serifada grande e bold
- Descrição do pacote
- Meta informações: duração e preço
- Dois botões lado a lado:
  - "Ver Detalhes" (outline) → Abre modal
  - "Tenho Interesse" (preenchido) → Abre WhatsApp

**Especificações Técnicas:**
- Altura da imagem: 280px
- Bordas arredondadas: 16px
- Sombra suave com elevação ao hover
- Transição suave de 0.4s
- Elevação de -12px ao hover
- Grid responsivo com auto-fill

**Integração WhatsApp:**
- Link direto para WhatsApp com mensagem pré-preenchida
- Formato: "Olá! Tenho interesse no pacote: [Nome do Pacote]"
- Abre em nova aba
- Número configurável em TravelCurator > Configurações

#### 5. Modal Popup de Detalhes
Design baseado no mockup sc-003.png:

**Estrutura:**
- Overlay escuro com blur (backdrop-filter)
- Modal centralizado com max-width 700px
- Botão X de fechar no canto superior direito
- Título do pacote em fonte serifada
- Badge de categoria
- Descrição completa do pacote
- Seção "Destaques da Experiência" com fundo bege (#F9F7F4)
- Lista com checkmarks dourados
- Rodapé com duração, preço e botões
- Dois botões: "Fechar" (outline) e "Tenho Interesse" (preenchido)

**Comportamento:**
- Animação de escala ao abrir/fechar
- Fecha com tecla ESC
- Fecha ao clicar no overlay
- Bloqueia scroll do body quando aberto
- Transição suave de 0.3s

**Destaques:**
- Lê campo `_travelcurator_highlights` (separado por quebras de linha)
- Destaques padrão caso não configurado:
  - Hospedagem em acomodação premium
  - Passeios e experiências exclusivas
  - Guia especializado em português
  - Traslados inclusos
  - Seguro viagem completo

#### 6. Ícones por Propósito Emocional
Mapeamento de emojis por propósito:
- **Reconexão:** 🍷 (taça de vinho)
- **Celebração:** 🎉 (confete)
- **Descoberta:** 🧭 (bússola)
- **Transformação:** 🦋 (borboleta)
- **Descanso:** 🌴 (palmeira)
- **Padrão:** ✨ (estrelas)

### 📝 Arquivos Modificados

1. **admin/class-travelcurator-admin.php**
   - Removidos submenus duplicados "Todos os Pacotes" e "Adicionar Pacote"

2. **includes/class-travelcurator.php**
   - Adicionados hooks para enqueue de scripts no Elementor
   - Linhas 215-216: enqueue_scripts para frontend e preview

3. **includes/elementor/class-travelcurator-elementor.php**
   - Método `enqueue_styles()` atualizado para carregar novos estilos
   - Método `enqueue_scripts()` atualizado com localização de dados
   - Passa número do WhatsApp e configurações para JavaScript

4. **includes/elementor/widgets/travel-packages-grid.php**
   - Método `render()` completamente reescrito
   - Novo header com filtros de propósito emocional
   - Cards redesenhados conforme mockup
   - Modais HTML renderizados para cada pacote
   - JavaScript inline para funcionalidade de modal
   - Integração direta com WhatsApp

### 📁 Arquivos Adicionados

1. **includes/elementor/assets/widgets-new.css** (Nova)
   - Estilos modernos com herança de tema
   - Design de header com gradiente
   - Pills de filtro interativos
   - Cards com layout aprimorado
   - Modal responsivo e acessível
   - Animações suaves
   - Media queries para mobile/tablet
   - Total: ~800 linhas

2. **includes/elementor/assets/widgets-new.js** (Nova)
   - Funcionalidade de filtros por propósito
   - Sistema de modal (abrir/fechar)
   - Integração WhatsApp
   - Smooth scroll
   - Lazy loading de imagens
   - Hooks para Elementor frontend
   - Total: ~150 linhas

3. **CHANGELOG-1.0.2.md** (Este arquivo)

### 🎯 Funcionalidades Novas

1. **Filtros por Propósito Emocional**
   - Interface visual com pills
   - Filtro AJAX sem reload
   - Todos os propósitos cadastrados aparecem automaticamente

2. **Sistema de Modal**
   - Popup para cada pacote
   - Conteúdo completo do pacote
   - Destaques em formato de lista
   - Botões de ação (Fechar / Tenho Interesse)

3. **Integração WhatsApp Direta**
   - Links em cards E modais
   - Mensagem pré-preenchida
   - Configurável via admin

4. **Design Responsivo**
   - Mobile first
   - Breakpoints: 768px, 480px
   - Grid adaptativo
   - Botões empilhados em mobile
   - Modal otimizado para telas pequenas

### 🔧 Melhorias Técnicas

1. **Performance:**
   - Lazy loading de imagens
   - Transições com GPU (transform)
   - CSS otimizado com seletores específicos

2. **Acessibilidade:**
   - Botões com labels claros
   - Fechamento de modal com ESC
   - Contraste adequado de cores
   - Links com rel="noopener" para segurança

3. **Manutenibilidade:**
   - CSS com variáveis do tema
   - Código comentado
   - Nomes de classe semânticos
   - Separação de concerns (CSS/JS)

4. **Compatibilidade:**
   - Mantidos estilos antigos para backward compatibility
   - Novos arquivos carregados em paralelo
   - Fallbacks para temas sem variáveis CSS

### 🐛 Bugs Corrigidos

1. Menu "Todos os Pacotes" duplicado
2. Widgets não herdavam cores/fontes do tema
3. Interface não seguia identidade visual da agência

### 📱 Responsividade

**Desktop (> 768px):**
- Grid de 3 colunas (padrão)
- Cards com hover elevation
- Modal centralizado

**Tablet (768px - 480px):**
- Grid de 1 coluna
- Cards full-width
- Botões horizontais

**Mobile (< 480px):**
- Header compacto (40px padding)
- Título menor (1.6em)
- Pills menores
- Cards otimizados
- Botões empilhados verticalmente
- Modal com padding reduzido

### ✅ Como Testar

1. **Menu Administrativo:**
   - Acesse WordPress Admin > TravelCurator
   - Verifique que "Todos os Pacotes" aparece apenas UMA vez

2. **Herança de Tema:**
   - Ative diferentes temas
   - Verifique que os widgets adotam as fontes e cores do tema

3. **Header com Filtros:**
   - Adicione o widget "Pacotes de Viagem - Grid" em uma página
   - Ative a opção "Mostrar Filtros"
   - Verifique o header rosa/terracota
   - Teste os filtros por propósito emocional

4. **Cards:**
   - Verifique visual dos cards (badge, ícone, layout)
   - Teste botão "Ver Detalhes" (deve abrir modal)
   - Teste botão "Tenho Interesse" (deve abrir WhatsApp)

5. **Modal:**
   - Clique em "Ver Detalhes" em qualquer card
   - Verifique abertura suave do modal
   - Teste fechar com X, botão "Fechar" e tecla ESC
   - Teste botão "Tenho Interesse" no modal

6. **WhatsApp:**
   - Configure número em TravelCurator > Configurações > WhatsApp
   - Teste links de WhatsApp nos cards e modais
   - Verifique mensagem pré-preenchida

7. **Responsividade:**
   - Teste em mobile (< 480px)
   - Teste em tablet (768px)
   - Teste em desktop (> 1024px)

### 🔮 Próximas Melhorias Sugeridas

1. Adicionar campo de highlights no meta box de edição de pacotes
2. Permitir personalizar ícones por propósito no admin
3. Adicionar mais opções de customização via Elementor
4. Criar variações de cor do header (configurável)
5. Implementar sistema de wishlist (favoritos)
6. Adicionar animações de carregamento AJAX
7. Criar shortcode com mesma funcionalidade

### 📞 Suporte

Para dúvidas ou problemas:
- **Site:** https://viajareclicar.com.br
- **Plugin:** TravelCurator v1.0.2
- **WordPress:** 6.8.3+
- **Elementor:** 3.35+
- **PHP:** 7.4+

---

**Desenvolvido com ❤️ para Viajar & Clicar**
