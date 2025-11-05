# Changelog - TravelCurator Plugin

## [1.0.1] - 2024-10-30

### 🔧 Correções

#### 1. Widgets do Elementor Corrigidos
- **Problema:** Widgets não apareciam no editor do Elementor
- **Solução:** Atualizado hook do Elementor de `elementor/widgets/widgets_registered` para `elementor/widgets/register` (compatível com Elementor 3.35+)
- **Arquivo alterado:** `includes/class-travelcurator.php` (linha 211)

#### 2. Menu Consolidado
- **Problema:** Menu "Travel Packages" aparecia duplicado separado do menu principal
- **Solução:** Configurado o post type para aparecer dentro do menu "TravelCurator"
- **Arquivo alterado:** `includes/class-travelcurator-post-types.php` (linha 80)
- **Mudança:** `'show_in_menu' => true` → `'show_in_menu' => 'travelcurator'`

#### 3. Interface de Status dos Pacotes
- **Problema:** Todos os pacotes ficavam em "Draft" sem interface para alterar
- **Solução:** Adicionados dois novos meta boxes na sidebar do editor:
  - **Status do Pacote:** Permite escolher entre Ativo, Inativo, Rascunho ou Esgotado
  - **Nível de Dificuldade:** Permite escolher entre Fácil, Moderado ou Difícil
- **Arquivos alterados:**
  - `includes/class-travelcurator-meta-boxes.php` (linhas 28-238, 726-755)
- **Como usar:** Ao editar um pacote, use o meta box "Status do Pacote" na sidebar direita

#### 4. Textos Traduzidos para Português
- **Problema:** Vários textos estavam em inglês no admin
- **Solução:** Traduzidos todos os labels, filtros e mensagens
- **Textos corrigidos:**
  - Labels do post type (Travel Package → Pacote de Viagem)
  - Colunas do admin (Image → Imagem, Price → Preço, etc.)
  - Status (Active → Ativo, Draft → Rascunho, etc.)
  - Filtros (All Statuses → Todos os Status, etc.)
  - Níveis de dificuldade (Easy → Fácil, Moderate → Moderado, Hard → Difícil)
- **Arquivo alterado:** `includes/class-travelcurator-post-types.php`

#### 5. Shortcodes Implementados
- **Problema:** Shortcodes não estavam funcionando
- **Solução:** Implementados 5 shortcodes completos com funcionalidade AJAX
- **Arquivo alterado:** `public/class-travelcurator-public.php`

**Shortcodes disponíveis:**
1. `[travelcurator_packages]` - Grid de pacotes com filtros
2. `[travelcurator_package id="123"]` - Pacote específico
3. `[travelcurator_search]` - Formulário de busca
4. `[travelcurator_lead_form]` - Formulário de captura de leads
5. `[travelcurator_featured]` - Pacotes em destaque

**Documentação:** Ver arquivo `SHORTCODES.md` para detalhes completos

#### 6. Filtros Duplicados Corrigidos
- **Problema:** Interface mostrava filtros e textos duplicados
- **Solução:**
  - Removidas duplicações de código
  - Consolidados filtros em uma única seção
  - Traduzidos todos os termos para português

### ✨ Melhorias

#### Layouts Padrão
- CSS existente em `includes/elementor/assets/widgets.css` mantém layouts consistentes
- Todos os widgets e shortcodes usam o mesmo design system
- Responsivo para mobile, tablet e desktop

#### Estrutura de URLs
- URLs atualizadas para português:
  - `travel-packages` → `pacotes-de-viagem`
  - `travel-package` → `pacote-de-viagem`

### 📝 Arquivos Modificados

1. `includes/class-travelcurator.php`
2. `includes/class-travelcurator-post-types.php`
3. `includes/class-travelcurator-meta-boxes.php`
4. `public/class-travelcurator-public.php`

### 📚 Arquivos Adicionados

1. `SHORTCODES.md` - Documentação completa dos shortcodes
2. `CHANGELOG.md` - Este arquivo

### 🔍 Como Testar as Correções

1. **Widgets do Elementor:**
   - Edite uma página com Elementor
   - Procure pela categoria "TravelCurator" no painel esquerdo
   - Os 4 widgets devem aparecer e funcionar corretamente

2. **Menu Consolidado:**
   - Acesse o admin do WordPress
   - Verifique que existe apenas o menu "TravelCurator"
   - Dentro dele deve ter "Todos os Pacotes"

3. **Status dos Pacotes:**
   - Edite qualquer pacote
   - Na sidebar direita, procure o meta box "Status do Pacote"
   - Selecione "Ativo" e salve
   - O pacote agora aparecerá no site

4. **Shortcodes:**
   - Crie uma página ou post
   - Adicione qualquer shortcode, exemplo: `[travelcurator_packages limit="6"]`
   - Visualize a página para ver o grid funcionando

5. **Traduções:**
   - Acesse "TravelCurator > Todos os Pacotes"
   - Verifique que todos os textos estão em português

---

## [1.0.0] - 2024-10-29

### 🎉 Lançamento Inicial
- Plugin base criado
- Custom Post Type para pacotes
- Taxonomias (Categorias, Destinos, Propósitos)
- Meta boxes para detalhes dos pacotes
- Sistema de leads
- Integração básica com Elementor

---

**Desenvolvido para:** WordPress 6.8.3 + Elementor 3.35
**Compatibilidade PHP:** 7.4+
