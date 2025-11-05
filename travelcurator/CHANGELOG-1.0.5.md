# Changelog - TravelCurator Plugin v1.0.5

## [1.0.5] - 2025-11-04

### 🐛 Bug Fixes & Enhancements

#### 1. Menu Administrativo - Propósitos Agora Visível
- **Problema:** O item "Propósitos Emocionais" não aparecia no menu administrativo do TravelCurator
- **Solução:** Adicionado parâmetro `'show_in_menu' => 'travelcurator'` em todas as taxonomias
- **Taxonomias corrigidas:**
  - `travel_category` (Categorias de Viagem)
  - `travel_destination` (Destinos)
  - `travel_purpose` (já estava correto)
- **Arquivo:** `includes/class-travelcurator-taxonomies.php`
- **Linhas modificadas:** 60, 154

#### 2. Tradução de Dificuldade para Português
- **Problema:** Níveis de dificuldade dos pacotes apareciam em inglês (easy, moderate, difficult)
- **Solução:** Implementado sistema de tradução automática
- **Mapeamento de traduções:**
  - `easy` → Fácil
  - `moderate` → Moderado
  - `difficult` / `hard` → Difícil
  - `extreme` → Extremo
  - `challenging` → Desafiador
- **Aplicado em:** Cards de pacotes E modais de detalhes
- **Arquivo:** `includes/elementor/widgets/travel-packages-grid.php`
- **Linhas modificadas:** 774-786, 833, 893-904, 957

#### 3. Estado Vazio com Call-to-Action WhatsApp
- **Problema:** Ao filtrar por propósito sem pacotes, aparecia apenas uma lista vazia
- **Solução:** Redesenhado estado vazio com mensagem amigável e CTA para WhatsApp
- **Novo design inclui:**
  - Ícone emoji emotivo (😔)
  - Título: "Nenhuma experiência encontrada"
  - Mensagem explicativa
  - Call-to-action: "Mas podemos criar uma experiência personalizada para você!"
  - Botão verde do WhatsApp: "💬 Fale Conosco no WhatsApp"
  - Mensagem pré-preenchida: "Olá! Gostaria de uma experiência personalizada. Podem me ajudar?"
- **Arquivos:**
  - `includes/elementor/widgets/travel-packages-grid.php` (linhas 860-869) - HTML
  - `includes/elementor/assets/widgets-new.css` (linhas 668-721) - Estilos

#### 4. Filtros por Parâmetros de URL
- **Problema:** Não era possível linkar para a página com filtro pré-aplicado
- **Solução:** Implementado sistema completo de URL parameters
- **Funcionalidades:**
  - Leitura de parâmetro `?purpose=` na URL ao carregar página
  - Aplicação automática do filtro se parâmetro existir
  - Scroll suave até a seção de pacotes
  - Atualização da URL ao clicar nos pills de filtro (sem reload)
- **Exemplo de uso:**
  - Link na homepage: `https://site.com/experiencias/?purpose=descanso`
  - Ao carregar, filtra automaticamente por "Descanso"
- **Tecnologia:** URLSearchParams API + window.history.pushState
- **Arquivo:** `includes/elementor/assets/widgets-new.js`
- **Linhas modificadas:** 10-62

#### 5. Customização Completa do Hero via Elementor
- **Problema:** Não era possível remover sombra do título ou customizar bordas/margens
- **Solução:** Adicionados novos controles de customização no Elementor
- **Novos controles adicionados:**
  1. **Sombra do Título (Text Shadow)**
     - Controle completo de sombra do texto
     - Permite adicionar, remover ou personalizar sombra
     - Seletor: `.tc-header-title`
  2. **Margem Externa (Margin)**
     - Controle responsivo de margens (top/bottom)
     - Unidades: px, em, %
     - Seletor: `.tc-experiences-header`
  3. **Borda (Border)**
     - Controle de tipo, largura e cor da borda
     - Seletor: `.tc-experiences-header`
  4. **Arredondamento das Bordas (Border Radius)**
     - Controle responsivo dos cantos
     - Unidades: px, %
     - Seletor: `.tc-experiences-header`
- **Arquivo:** `includes/elementor/widgets/travel-packages-grid.php`
- **Linhas modificadas:** 323-330, 372-403

### 📝 Arquivos Modificados

1. **travelcurator.php**
   - Atualizado versão de 1.0.4 para 1.0.5
   - Linha 6: Comentário de versão
   - Linha 25: Constante TRAVELCURATOR_VERSION

2. **includes/class-travelcurator-taxonomies.php**
   - Linha 60: Adicionado `'show_in_menu' => 'travelcurator'` em travel_category
   - Linha 154: Adicionado `'show_in_menu' => 'travelcurator'` em travel_destination

3. **includes/elementor/widgets/travel-packages-grid.php**
   - Linhas 323-330: Controle de Text Shadow para título do hero
   - Linhas 372-403: Controles de Margin, Border, Border Radius para hero
   - Linhas 774-786: Array de tradução de dificuldade nos cards
   - Linha 833: Aplicação da tradução de dificuldade nos cards
   - Linhas 860-869: Novo HTML do estado vazio com WhatsApp CTA
   - Linhas 893-904: Array de tradução de dificuldade nos modais
   - Linha 957: Aplicação da tradução de dificuldade nos modais

4. **includes/elementor/assets/widgets-new.css**
   - Linhas 668-721: Estilos completos do estado vazio
   - Inclui: .no-packages, .no-packages-icon, .no-packages-title, .no-packages-text, .no-packages-cta, .btn-whatsapp-cta

5. **includes/elementor/assets/widgets-new.js**
   - Linhas 10-62: Função initPurposeFilters() reescrita
   - Implementação de applyFilter() helper
   - Leitura de parâmetros URL na carga
   - Atualização de URL ao clicar (pushState)
   - Scroll suave para pacotes filtrados

### 📁 Arquivos Adicionados

1. **CHANGELOG-1.0.5.md** (Este arquivo)
   - Documentação completa das mudanças da versão 1.0.5

### 🎯 Funcionalidades Novas

1. **Deep Linking com Filtros**
   - Permite compartilhar links com filtros pré-aplicados
   - Exemplo: `?purpose=reconexao`, `?purpose=aventura`
   - Scroll automático para a seção de pacotes

2. **Estado Vazio Inteligente**
   - Mensagem amigável quando não há pacotes
   - Call-to-action direto para WhatsApp
   - Design consistente com identidade visual

3. **Customização Total do Hero**
   - Controles avançados no Elementor
   - Permite remoção completa de sombras
   - Controle de espaçamentos e bordas

### 🔧 Melhorias Técnicas

1. **UX/UI:**
   - Mensagens em português para melhor compreensão
   - Estado vazio com ação clara para o usuário
   - Navegação via URL intuitiva

2. **Flexibilidade:**
   - Hero totalmente customizável via Elementor
   - Não requer edição de código para personalização

3. **Internacionalização:**
   - Sistema de tradução extensível
   - Fácil adicionar novos níveis de dificuldade

4. **SEO e Compartilhamento:**
   - URLs amigáveis com parâmetros descritivos
   - Facilita compartilhamento de filtros específicos

### ✅ Como Testar

#### 1. Menu Administrativo
- Acesse WordPress Admin > TravelCurator
- Verifique que "Propósitos Emocionais" agora aparece no menu
- Clique para acessar a lista de propósitos

#### 2. Tradução de Dificuldade
- Edite um pacote e defina a dificuldade (easy, moderate, etc.)
- Visualize o pacote no front-end
- Verifique que aparece traduzido (Fácil, Moderado, etc.)
- Confira tanto no card quanto no modal

#### 3. Estado Vazio
- Acesse a página com o widget de pacotes
- Clique em um propósito que não tenha pacotes cadastrados
- Verifique mensagem amigável com emoji
- Teste o botão "Fale Conosco no WhatsApp"
- Verifique que abre WhatsApp com mensagem pré-preenchida

#### 4. Filtros por URL
- Crie um link: `sua-url.com/experiencias/?purpose=descanso`
- Acesse o link
- Verifique que o filtro "Descanso" já está ativo
- Verifique scroll automático para os pacotes
- Clique em outro filtro
- Verifique que a URL é atualizada sem recarregar

#### 5. Customização do Hero
- Edite a página com Elementor
- Selecione o widget "Pacotes de Viagem - Grid"
- Vá em "Estilo"
- Teste os novos controles:
  - Sombra do Título (adicione/remova sombras)
  - Margem Externa (ajuste espaçamentos)
  - Borda (adicione bordas)
  - Arredondamento das Bordas (arredonde cantos)

### 🔄 Compatibilidade

- **WordPress:** 6.8.3+
- **Elementor:** 3.35+
- **PHP:** 7.4+
- **Navegadores:** Chrome, Firefox, Safari, Edge (últimas versões)
- **Responsivo:** Mobile, Tablet, Desktop

### 📞 Suporte

Para dúvidas ou problemas:
- **Site:** https://viajareclicar.com.br
- **Plugin:** TravelCurator v1.0.5
- **Repositório:** fuka65doug/viajareclicar
- **Branch:** claude/fix-wordpress-plugin-011CUbgzfH5V9sVf4sjKQRP5

---

**Desenvolvido com ❤️ para Viajar & Clicar**
