/**
 * @file cypress/tests/functional/AccessibilityBlock.cy.js
 *
 * Copyright (c) 2026 OJSBR (https://ojsbr.com)
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * Functional tests: what a reader gets from the block.
 *
 * Parameters (--env): contextPath (default publicknowledge), pagePath and
 * otherPagePath (two reader pages that show the block; default the journal
 * home page and "about"). The block must be enabled and placed in the sidebar.
 * Assertions use data attributes and ARIA state, never labels, so the spec runs
 * against a journal in any language. Nothing is changed on the server: the
 * preferences live in the browser.
 */

describe('Accessibility block plugin', function() {
	const contextPath = Cypress.env('contextPath') || 'publicknowledge';
	const pagePath = Cypress.env('pagePath') || '';
	const otherPagePath = Cypress.env('otherPagePath') || 'about';

	const url = (path) => '/index.php/' + contextPath + (path ? '/' + path : '');
	// A session cookie gets past an edge cache that serves anonymous pages.
	const visit = (path) => cy.visit(url(path), {headers: {Cookie: 'OJSSID=cypress' + Date.now()}});
	const control = (action) => cy.get('.block_accessibility [data-a11y-action="' + action + '"]');
	// Once the page is zoomed, the Electron build Cypress ships (Chrome 118) hit-tests
	// with unzoomed coordinates and reports the button as covered: click it directly.
	const press = (action) => control(action).should('be.visible').click({force: true});
	const zoom = () => cy.document().then((doc) => parseFloat(doc.documentElement.style.zoom || '1'));

	beforeEach(function() {
		cy.clearLocalStorage();
	});

	it('Shows four labelled controls and loads its assets once', function() {
		visit(pagePath);
		cy.get('.block_accessibility').should('have.length', 1);
		['zoom-out', 'zoom-in', 'contrast', 'reset'].forEach((action) => {
			control(action)
				.should('have.attr', 'type', 'button')
				.invoke('attr', 'aria-label')
				.should('match', /\S/)
				.and('not.contain', '##');
		});
		control('contrast').should('have.attr', 'aria-pressed', 'false');
		cy.get('link[href*="/accessibility/css/accessibility.css"]').should('have.length', 1);
		cy.get('.block_accessibility .ojsbr-a11y-btn').should('have.css', 'min-height', '40px');
		cy.get('script[src*="/accessibility/js/accessibility.js"]').should('have.length', 1);
		cy.get('.block_accessibility script, .block_accessibility style').should('have.length', 0);
	});

	it('Zooms within its limits and announces the level', function() {
		visit(pagePath);
		zoom().should('eq', 1);
		press('zoom-out');
		zoom().should('eq', 1);
		for (let i = 0; i < 12; i++) {
			press('zoom-in');
		}
		zoom().should('eq', 2);
		cy.get('.block_accessibility .ojsbr-a11y-status').should('contain', '200%');
		press('zoom-out');
		zoom().should('be.closeTo', 1.9, 0.001);
	});

	it('Keeps high contrast and zoom on the next page, and resets both', function() {
		visit(pagePath);
		press('zoom-in');
		press('contrast');
		cy.get('html').should('have.class', 'ojsbr-a11y-contrast');
		control('contrast').should('have.attr', 'aria-pressed', 'true');
		// The glyph of the pressed button stays readable on its yellow background.
		control('contrast').find('span').should('have.css', 'color', 'rgb(0, 0, 0)');

		visit(otherPagePath);
		cy.get('html').should('have.class', 'ojsbr-a11y-contrast');
		control('contrast').should('have.attr', 'aria-pressed', 'true');
		zoom().should('be.closeTo', 1.1, 0.001);

		press('reset');
		cy.get('html').should('not.have.class', 'ojsbr-a11y-contrast');
		control('contrast').should('have.attr', 'aria-pressed', 'false');
		zoom().should('eq', 1);
	});
});
