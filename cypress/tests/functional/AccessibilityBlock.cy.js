/**
 * @file cypress/tests/functional/AccessibilityBlock.cy.js
 *
 * Copyright (c) 2026 OJSBR (https://ojsbr.com)
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 */

describe('Accessibility block plugin tests', function() {
	it('Enables the plugin, adds the block to the sidebar and exercises the controls', function() {
		cy.login('admin', 'admin', 'publicknowledge');

		cy.get('nav').contains('Settings').click();
		// Ensure submenu item click despite animation
		cy.get('nav').contains('Website').click({ force: true });
		cy.get('button[id="plugins-button"]').click();

		// Find and enable the plugin
		cy.get('input[id^="select-cell-accessibilityblockplugin-enabled"]').click();
		cy.contains('has been enabled');
		cy.waitJQuery();

		// Reload so the newly enabled block shows up in the sidebar options
		cy.reload();
		cy.waitJQuery();

		// Place the block in the sidebar
		cy.get('button[id="appearance-button"]').click();
		cy.get('#appearance-setup-button').click();
		cy.get('#appearance-setup span:contains("Accessibility Block"):first').click();
		cy.get('#appearance-setup button:contains("Save")').click();
		cy.waitJQuery();

		// The block and its four controls are rendered on the front end
		cy.visit('/index.php/publicknowledge');
		cy.get('.block_accessibility').should('exist');
		cy.get('.block_accessibility [data-a11y-action="zoom-out"]').should('exist');
		cy.get('.block_accessibility [data-a11y-action="zoom-in"]').should('exist');
		cy.get('.block_accessibility [data-a11y-action="contrast"]').should('exist');
		cy.get('.block_accessibility [data-a11y-action="reset"]').should('exist');

		// Zooming in increases the page zoom
		cy.get('.block_accessibility [data-a11y-action="zoom-in"]').click();
		cy.document().then(doc => {
			expect(parseFloat(doc.documentElement.style.zoom)).to.be.greaterThan(1);
		});

		// High contrast toggles the document class and the button's pressed state
		cy.get('.block_accessibility [data-a11y-action="contrast"]').click();
		cy.get('html').should('have.class', 'ojsbr-a11y-contrast');
		cy.get('.block_accessibility [data-a11y-action="contrast"]')
			.should('have.attr', 'aria-pressed', 'true');

		// The preference persists on the next page
		cy.visit('/index.php/publicknowledge/about');
		cy.get('html').should('have.class', 'ojsbr-a11y-contrast');

		// Reset restores both zoom and contrast
		cy.get('.block_accessibility [data-a11y-action="reset"]').click();
		cy.get('html').should('not.have.class', 'ojsbr-a11y-contrast');
		cy.document().then(doc => {
			expect(parseFloat(doc.documentElement.style.zoom)).to.equal(1);
		});
	});
})
