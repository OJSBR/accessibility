#!/bin/bash

set -e

npx cypress run  --headless --browser chrome  --config '{"specPattern":["plugins/blocks/accessibility/cypress/tests/functional/*.cy.js"]}'
