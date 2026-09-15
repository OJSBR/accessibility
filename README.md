# Accessibility Block (Zoom & Contrast) — OJS plugin

[![OJS](https://img.shields.io/badge/OJS-3.3%20%7C%203.4%20%7C%203.5-brightgreen)](https://pkp.sfu.ca/ojs/)
[![Version](https://img.shields.io/badge/version-1.0.2.1-blue)](version.xml)
[![License](https://img.shields.io/badge/license-GPL--3.0-lightgrey)](LICENSE)

**⬇️ Install package:** [OJS 3.5](https://github.com/OJSBR/accessibility/releases/download/1.0.2.1/accessibility-1.0.2.1.tar.gz) · [OJS 3.4](https://github.com/OJSBR/accessibility/releases/download/1.0.2.1-ojs3.4/accessibility-1.0.2.1-ojs3.4.tar.gz) · [OJS 3.3](https://github.com/OJSBR/accessibility/releases/download/1.0.2.1-ojs3.3/accessibility-1.0.2.1-ojs3.3.tar.gz) — or browse all [Releases](../../releases).

A **block plugin** for **Open Journal Systems (OJS)** that adds a sidebar widget with
**reader accessibility controls**: **zoom in (A+)**, **zoom out (A−)**, a **high-contrast**
toggle and a **reset** button — no core patching, no external dependencies, and preferences
that **persist across pages**.

> **Developed and maintained by [OJSBR](https://ojsbr.com).** See the
> [Credits & authorship](#credits--authorship) section below.

## Compatibility & branches

| OJS version | Branch | Plugin release |
|-------------|--------|----------------|
| OJS 3.5.x   | [`stable-3_5_0`](../../tree/stable-3_5_0) *(default)* | 1.0.2.1 |
| OJS 3.4.x   | [`stable-3_4_0`](../../tree/stable-3_4_0) | 1.0.2.1 |
| OJS 3.3.x   | [`stable-3_3_0`](../../tree/stable-3_3_0) | 1.0.2.1 |

The three branches behave the same. The locale folders follow each OJS line: OJS 3.5 uses the
short codes (`fr`, `pt`, `nb_NO`, `sr_Latn`, `zh_Hans`), OJS 3.4 the codes `fr_FR`, `pt_PT`, `nb`,
`sr@latin`, `zh_CN`, and OJS 3.3 the five-letter codes of its registry (38 languages on 3.4 and
3.5; the 33 of them that OJS 3.3 knows on 3.3).

## The problem

Readers with low vision need larger text and stronger contrast, and many of them do not know
the browser's zoom shortcut or use a device where it breaks the layout. Journal themes rarely
offer either, and an accessibility statement that points to browser settings leaves the
reader on their own.

## What it does

- **Zoom in / out** — scales the whole page in 10% steps (100%–200%), so readers can enlarge
  the text without the browser's own zoom.
- **High contrast** — toggles a high-contrast theme (black background, white text, yellow
  links) over the entire site for low-vision readers.
- **Reset** — returns zoom and contrast to their defaults in one click.
- **Persistent** — the reader's choices are stored in `localStorage` and re-applied on every
  page that shows the block, until reset. Pages without the block are left alone, so a reader
  can always reach the reset button of whatever mode is on.
- **Accessible by design** — real `<button>` elements with `aria-label`, `aria-pressed`,
  a visually-hidden `aria-live` region announcing the zoom level, and 40&nbsp;px minimum
  touch targets.
- **On-theme** — the buttons reuse the active theme's own button style (the same dynamic
  colour as the journal galley buttons), with a safe fallback on themes that don't define it.
- **Multilingual** — ships in 38 languages (33 on OJS 3.3).

## Installation

1. Download the release for your OJS version (or clone the matching branch).
2. Install via **Settings → Website → Plugins → Upload A New Plugin**, or extract the folder
   into `plugins/blocks/` so you get `plugins/blocks/accessibility/`. Do not rename the folder:
   OJS derives the plugin from the directory name.
3. Enable **Accessibility Block (Zoom & Contrast)** under the *Block* plugins list.
4. Place the block in your sidebar under **Settings → Website → Appearance → Sidebar**
   (drag it into the sidebar column; the top is a good spot for accessibility controls).

## How it works (technical)

- A `BlockPlugin` renders `templates/block.tpl` into the sidebar. The styles and the script
  are files (`css/accessibility.css`, `js/accessibility.js`), cached by the browser like any
  other asset. Blocks are loaded while the sidebar is rendered, after the page head, so the
  script is queued with `addJavaScript` (printed at the end of the page) and the stylesheet is
  linked from the block itself.
- **Zoom** sets `document.documentElement.style.zoom`; **contrast** toggles a
  `html.ojsbr-a11y-contrast` class carrying the high-contrast rules. Both preferences are
  persisted in `localStorage`; storage blocked by the browser does not break the page.
- The control buttons carry the active theme's button class, so they inherit the journal's
  dynamic colour. A **zero-specificity `:where()` fallback** provides a sensible colour on
  themes that don't define that class, keeping the plugin portable.
- The class is `AccessibilityBlockPlugin`, loaded through `index.php` like PKP's own `browse` block; the
  name is kept because renaming it would reset the plugin's settings and its place in the sidebar.
- No core files are touched and no database schema is added, so it is fully
  upgrade-compatible; disabling the plugin removes the block entirely.

## Tests

- **PHPUnit** (`tests/*Test.php`, on PKP's `PKPTestCase`): the plugin class against the installed
  PKP, the script queued for reader pages only, a template without inline scripts or styles, the
  zoom level announced in the page language, escaped attributes, and the translations. From the
  OJS root:

  ```bash
  lib/pkp/lib/vendor/bin/phpunit --configuration lib/pkp/tests/phpunit.xml --no-coverage "$PWD/plugins/blocks/accessibility/tests"
  ```

  (On OJS 3.3 the PHPUnit configuration is `lib/pkp/tests/phpunit-env1.xml`.)

- **Cypress** (`cypress/tests/functional/AccessibilityBlock.cy.js`, run by
  [pkp-github-actions](https://github.com/pkp/pkp-github-actions) on every push to the OJS 3.4 and
  3.5 branches; PKP's CI no longer starts OJS 3.3 on current runners, so on 3.3 it is run on an
  installation): enables the
  plugin and places the block in the sidebar when needed (restoring the sidebar afterwards), then
  checks what a reader gets — four labelled controls, the assets loaded once, zoom within its
  limits, high contrast and zoom kept on the next page, and reset.
- Verified on OJS 3.5.0.3, 3.4.0.10 and 3.3.0.22.

Tests are kept in the repository and are not part of the release package.

## Credits & authorship

- **Developed and maintained by** [OJSBR](https://ojsbr.com) — original plugin.
- Distributed under the **GNU GPL v3**.

## AI use

Generative AI (Claude, by Anthropic) was used to write and run tests, improve the code and bring
it in line with PKP standards. Every change is reviewed and tested by OJSBR, which is responsible
for the published releases.

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
| OJS 3.5.x     | [`stable-3_5_0`](../../tree/stable-3_5_0) *(padrão)* | 1.0.2.1 |
| OJS 3.4.x     | [`stable-3_4_0`](../../tree/stable-3_4_0) | 1.0.2.1 |
| OJS 3.3.x     | [`stable-3_3_0`](../../tree/stable-3_3_0) | 1.0.2.1 |

As três branches se comportam igual. As pastas de idioma seguem cada linha do OJS: o 3.5 usa os
códigos curtos (`fr`, `pt`, `nb_NO`, `sr_Latn`, `zh_Hans`), o 3.4 os códigos `fr_FR`, `pt_PT`,
`nb`, `sr@latin`, `zh_CN`, e o 3.3 os códigos de cinco letras do seu registro (38 idiomas no 3.4
e no 3.5; os 33 que o OJS 3.3 conhece no 3.3).

### O problema

Leitores com baixa visão precisam de texto maior e contraste mais forte, e muitos não conhecem o
atalho de zoom do navegador ou usam um aparelho em que ele quebra o layout. Os temas das revistas
raramente oferecem uma coisa ou outra.

### O que faz

- **Aumentar / diminuir zoom** — amplia a página inteira em passos de 10% (100%–200%), para o
  leitor aumentar o texto sem depender do zoom do navegador.
- **Alto contraste** — ativa um tema de alto contraste (fundo preto, texto branco, links
  amarelos) sobre todo o site, para leitores com baixa visão.
- **Redefinir** — volta zoom e contraste ao padrão num clique.
- **Persistente** — as escolhas do leitor ficam salvas em `localStorage` e são reaplicadas em
  cada página que mostra o bloco, até que ele redefina. Páginas sem o bloco ficam intactas, para
  o leitor sempre alcançar o botão de redefinir.
- **Acessível por padrão** — botões `<button>` reais com `aria-label`, `aria-pressed`, uma
  região `aria-live` invisível que anuncia o nível de zoom e alvos de toque de no mínimo
  40&nbsp;px.
- **No tema** — os botões reutilizam o estilo de botão do tema ativo (a mesma cor dinâmica dos
  botões de galley da revista), com um fallback seguro em temas que não o definam.
- **Multilíngue** — vem em 38 idiomas (33 no OJS 3.3).

### Instalação

Instale em **Configurações → Website → Plugins → Enviar um novo plugin**, ou extraia a pasta
em `plugins/blocks/` (ficando `plugins/blocks/accessibility/`); não renomeie a pasta. Ative o **Accessibility Block
(Zoom & Contrast)** na lista de plugins de *Bloco* e posicione o bloco em **Configurações →
Website → Aparência → Barra lateral** (arraste para a coluna da barra lateral — o topo é um
bom lugar para os controles de acessibilidade).

### Como funciona (técnico)

- Um `BlockPlugin` renderiza `templates/block.tpl` na barra lateral. Estilos e script são
  arquivos (`css/accessibility.css`, `js/accessibility.js`), guardados em cache pelo navegador.
  Os blocos são carregados durante a renderização da barra lateral, depois do cabeçalho da
  página: o script entra pelo `addJavaScript` (impresso no fim da página) e a folha de estilos é
  ligada pelo próprio bloco.
- O **zoom** define `document.documentElement.style.zoom`; o **contraste** alterna a classe
  `html.ojsbr-a11y-contrast` com as regras de alto contraste. As duas preferências são salvas
  em `localStorage`; armazenamento bloqueado pelo navegador não quebra a página.
- Os botões recebem a classe de botão do tema ativo, herdando a cor dinâmica da revista. Um
  **fallback de especificidade zero (`:where()`)** garante uma cor sensata em temas que não
  definam essa classe, mantendo o plugin portável.
- A classe é `AccessibilityBlockPlugin`, carregada pelo `index.php` como o bloco `browse` da própria PKP;
  o nome fica porque renomear apagaria a configuração do plugin e a posição dele na barra lateral.
- Nenhum arquivo do núcleo é alterado e nenhum schema de banco é adicionado — é totalmente
  compatível com upgrades; desativar o plugin remove o bloco por completo.

### Testes

PHPUnit em `tests/*Test.php`, sobre o `PKPTestCase` do PKP (no OJS 3.3 com
`lib/pkp/tests/phpunit-env1.xml`), e Cypress em `cypress/tests/functional/`, rodado pelo
[pkp-github-actions](https://github.com/pkp/pkp-github-actions) a cada push nas branches do OJS 3.4 e
3.5 (o CI da PKP não sobe mais o OJS 3.3 nos runners atuais; no 3.3 ele roda numa instalação): liga o
plugin e põe o
bloco na barra lateral quando preciso (devolvendo a barra lateral como estava) e confere o que o
leitor recebe — quatro controles com nome acessível, arquivos carregados uma vez, zoom dentro dos
limites, contraste e zoom mantidos na página seguinte e redefinir. Verificado no OJS 3.5.0.3,
3.4.0.10 e 3.3.0.22. Os testes ficam no repositório e não vão no pacote de release.

### Créditos e autoria

- **Desenvolvido e mantido pela** [OJSBR](https://ojsbr.com) — plugin autoral.
- Distribuído sob a **GNU GPL v3**.

### Uso de IA

Foi usada IA generativa (Claude, da Anthropic) para escrever e rodar testes, melhorar o código e
alinhá-lo aos padrões da PKP. Toda mudança é revisada e testada pela OJSBR, que responde pelas
releases publicadas.

### Licença

Distribuído sob a **GNU GPL v3**. Veja [`LICENSE`](LICENSE) e `docs/COPYING`.
