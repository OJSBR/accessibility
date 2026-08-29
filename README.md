# Accessibility Block (Zoom & Contrast) — OJS plugin

[![OJS](https://img.shields.io/badge/OJS-3.3%20%7C%203.4%20%7C%203.5-brightgreen)](https://pkp.sfu.ca/ojs/)
[![Version](https://img.shields.io/badge/version-1.0.1.2-blue)](version.xml)
[![License](https://img.shields.io/badge/license-GPL--3.0-lightgrey)](LICENSE)

**⬇️ Install package:** [OJS 3.5](https://github.com/OJSBR/accessibility/releases/download/1.0.1.2/accessibility-1.0.1.2.tar.gz) · [OJS 3.4](https://github.com/OJSBR/accessibility/releases/download/1.0.0.1-ojs3.4/accessibility-1.0.0.1-ojs3.4.tar.gz) · [OJS 3.3](https://github.com/OJSBR/accessibility/releases/download/1.0.0.1-ojs3.3/accessibility-1.0.0.1-ojs3.3.tar.gz) — or browse all [Releases](../../releases).

A **block plugin** for **Open Journal Systems (OJS)** that adds a sidebar widget with
**reader accessibility controls**: **zoom in (A+)**, **zoom out (A−)**, a **high-contrast**
toggle and a **reset** button — no core patching, no external dependencies, and preferences
that **persist across pages**.

> **Developed and maintained by [OJSBR](https://ojsbr.com).** See the
> [Credits & authorship](#credits--authorship) section below.

## Compatibility & branches

| OJS version | Branch | Plugin release |
|-------------|--------|----------------|
| OJS 3.5.x   | [`stable-3_5_0`](../../tree/stable-3_5_0) *(default)* | 1.0.1.2 |
| OJS 3.4.x   | [`stable-3_4_0`](../../tree/stable-3_4_0) | 1.0.0.0-ojs3.4 |
| OJS 3.3.x   | [`stable-3_3_0`](../../tree/stable-3_3_0) | 1.0.0.0-ojs3.3 |

## What it does

- **Zoom in / out** — scales the whole page in 10% steps (100%–200%), so readers can enlarge
  the text without the browser's own zoom.
- **High contrast** — toggles a high-contrast theme (black background, white text, yellow
  links) over the entire site for low-vision readers.
- **Reset** — returns zoom and contrast to their defaults in one click.
- **Persistent** — the reader's choices are stored in `localStorage` and re-applied on every
  page of the journal until reset.
- **Accessible by design** — real `<button>` elements with `aria-label`, `aria-pressed`,
  a visually-hidden `aria-live` region announcing the zoom level, and 40&nbsp;px minimum
  touch targets.
- **On-theme** — the buttons reuse the active theme's own button style (the same dynamic
  colour as the journal galley buttons), with a safe fallback on themes that don't define it.
- **Multilingual** — ships in 6 languages: English, Portuguese (BR), Spanish, French, Italian
  and German.

## Installation

1. Download the release for your OJS version (or clone the matching branch).
2. Install via **Settings → Website → Plugins → Upload A New Plugin**, or extract the folder
   into `plugins/blocks/` so you get `plugins/blocks/accessibility/`.
3. Enable **Accessibility Block (Zoom & Contrast)** under the *Block* plugins list.
4. Place the block in your sidebar under **Settings → Website → Appearance → Sidebar**
   (drag it into the sidebar column; the top is a good spot for accessibility controls).

## How it works (technical)

- A `BlockPlugin` renders `templates/block.tpl` into the sidebar. The template is
  **self-contained**: the CSS and JavaScript are inlined inside a Smarty `{literal}` block,
  so there are no extra asset requests and nothing to register in the theme.
- **Zoom** sets `document.documentElement.style.zoom`; **contrast** toggles a
  `html.ojsbr-a11y-contrast` class carrying the high-contrast rules. Both preferences are
  persisted in `localStorage` and re-applied inline on load (no flash).
- The control buttons carry the active theme's button class, so they inherit the journal's
  dynamic colour. A **zero-specificity `:where()` fallback** provides a sensible colour on
  themes that don't define that class, keeping the plugin portable.
- No core files are touched and no database schema is added, so it is fully
  upgrade-compatible; disabling the plugin removes the block entirely.

## Tests

A functional [Cypress](https://www.cypress.io/) test lives in
`cypress/tests/functional/AccessibilityBlock.cy.js`. It enables the plugin, places the block
in the sidebar and exercises the controls end to end (zoom in, high contrast, persistence
across pages and reset), following the same conventions as the plugins shipped with OJS.

## Credits & authorship

- **Developed and maintained by** [OJSBR](https://ojsbr.com) — original plugin.
- Distributed under the **GNU GPL v3**.

## Contributing

Issues and pull requests are welcome. Please target the branch matching the OJS version you
are working against. See [`CONTRIBUTING.md`](CONTRIBUTING.md).

## License

Distributed under the **GNU GPL v3**. See [`LICENSE`](LICENSE) and `docs/COPYING`.

---

## 🇧🇷 Português

Um **plugin de bloco** para o **Open Journal Systems (OJS)** que adiciona, na barra lateral,
**controles de acessibilidade para o leitor**: **aumentar zoom (A+)**, **diminuir zoom (A−)**,
alternar **alto contraste** e um botão de **redefinir** — sem alterar o núcleo, sem
dependências externas, e com as preferências **persistindo entre as páginas**.

> **Desenvolvido e mantido pela [OJSBR](https://ojsbr.com).** Veja a seção
> [Créditos e autoria](#créditos-e-autoria) abaixo.

### Compatibilidade e branches

| Versão do OJS | Branch | Release do plugin |
|---------------|--------|-------------------|
| OJS 3.5.x     | `stable-3_5_0` *(padrão)* | 1.0.1.0 |
| OJS 3.4.x     | `stable-3_4_0` | 1.0.0.0-ojs3.4 |
| OJS 3.3.x     | `stable-3_3_0` | 1.0.0.0-ojs3.3 |

### O que faz

- **Aumentar / diminuir zoom** — amplia a página inteira em passos de 10% (100%–200%), para o
  leitor aumentar o texto sem depender do zoom do navegador.
- **Alto contraste** — ativa um tema de alto contraste (fundo preto, texto branco, links
  amarelos) sobre todo o site, para leitores com baixa visão.
- **Redefinir** — volta zoom e contraste ao padrão num clique.
- **Persistente** — as escolhas do leitor ficam salvas em `localStorage` e são reaplicadas em
  cada página da revista até que ele redefina.
- **Acessível por padrão** — botões `<button>` reais com `aria-label`, `aria-pressed`, uma
  região `aria-live` invisível que anuncia o nível de zoom e alvos de toque de no mínimo
  40&nbsp;px.
- **No tema** — os botões reutilizam o estilo de botão do tema ativo (a mesma cor dinâmica dos
  botões de galley da revista), com um fallback seguro em temas que não o definam.
- **Multilíngue** — vem em 6 idiomas: inglês, português (BR), espanhol, francês, italiano e
  alemão.

### Instalação

Instale em **Configurações → Website → Plugins → Enviar um novo plugin**, ou extraia a pasta
em `plugins/blocks/` (ficando `plugins/blocks/accessibility/`). Ative o **Accessibility Block
(Zoom & Contrast)** na lista de plugins de *Bloco* e posicione o bloco em **Configurações →
Website → Aparência → Barra lateral** (arraste para a coluna da barra lateral — o topo é um
bom lugar para os controles de acessibilidade).

### Como funciona (técnico)

- Um `BlockPlugin` renderiza `templates/block.tpl` na barra lateral. O template é
  **autossuficiente**: CSS e JavaScript ficam embutidos num bloco Smarty `{literal}`, sem
  requisições extras nem nada a registrar no tema.
- O **zoom** define `document.documentElement.style.zoom`; o **contraste** alterna a classe
  `html.ojsbr-a11y-contrast` com as regras de alto contraste. As duas preferências são salvas
  em `localStorage` e reaplicadas na carga (sem piscar).
- Os botões recebem a classe de botão do tema ativo, herdando a cor dinâmica da revista. Um
  **fallback de especificidade zero (`:where()`)** garante uma cor sensata em temas que não
  definam essa classe, mantendo o plugin portável.
- Nenhum arquivo do núcleo é alterado e nenhum schema de banco é adicionado — é totalmente
  compatível com upgrades; desativar o plugin remove o bloco por completo.

### Testes

Um teste funcional [Cypress](https://www.cypress.io/) fica em
`cypress/tests/functional/AccessibilityBlock.cy.js`. Ele habilita o plugin, posiciona o bloco
na barra lateral e exercita os controles de ponta a ponta (aumentar zoom, alto contraste,
persistência entre páginas e redefinir), seguindo as mesmas convenções dos plugins que
acompanham o OJS.

### Créditos e autoria

- **Desenvolvido e mantido pela** [OJSBR](https://ojsbr.com) — plugin autoral.
- Distribuído sob a **GNU GPL v3**.

### Licença

Distribuído sob a **GNU GPL v3**. Veja [`LICENSE`](LICENSE) e `docs/COPYING`.
