describe('PrestaShop Back Office Login', () => {
    // Define the URL and credentials (use environment variables in a real project!)
    const adminUrlPath = 'http://localhost:8080/admin_xxx'; // !! REPLACE THIS WITH YOUR UNIQUE ADMIN PATH !!
    const username = 'admin@admin.com'; // !! REPLACE THIS WITH YOUR EMAIL !!
    const password = 'admin123'; // !! REPLACE THIS WITH YOUR PASSWORD !!

    beforeEach(() => {
        cy.viewport(1920, 1080);
        cy.visit(adminUrlPath);
        cy.get('#email').should('be.visible').type(username);
        cy.get('#passwd').should('be.visible').type(password);
        cy.get('#submit_login').click();
        cy.url().should('include', 'controller=AdminDashboard');
    });

    afterEach(() => {
        cy.get("#subtab-AdminDashboard").should('be.visible').click();
        cy.get("#employee_infos").should('be.visible').click();
        cy.get("#header_logout").should('be.visible').click();
        cy.url().should('include', 'admin_xxx/login');
    });

    it('Install IPG Checkout', () => {
        cy.get('#subtab-AdminParentModulesSf').should('be.visible').click();
        cy.get('#subtab-AdminModulesSf').should('be.visible').click();
        cy.get('#page-header-desc-configuration-add_module').should('be.visible').click();
        cy.wait(1000);
        cy.get('#importDropzone').should('be.visible').selectFile(__dirname + '/../../../ipgcheckout.zip', { action: 'drag-drop' });
        cy.wait(10000);
        cy.get('#importDropzone > div.module-import-success > a').should('be.visible').click();
        cy.wait(1000);
    });

    it('Uninstall IPG Checkout', () => {
        cy.get('#subtab-AdminParentModulesSf').should('be.visible').click();
        cy.get('#subtab-AdminModulesSf').should('be.visible').click();
        cy.get('[data-tech-name="ipgcheckout"] .btn-group.module-actions button.dropdown-toggle-split').click();
        cy.get('[data-confirm_modal="module-modal-confirm-ipgcheckout-uninstall"]').click();
        cy.get('#force_deletion[data-tech-name="ipgcheckout"]').click();
        cy.wait(1000);
        cy.get('#module-modal-confirm-ipgcheckout-uninstall > div > div > div.modal-footer > a').click();
        cy.wait(10000);
    })
});