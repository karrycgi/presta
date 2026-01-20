const SCREENSHOT_OPTIONS = {
    capture: 'fullPage',
    scale: false
}

describe('PrestaShop Back Office Login', () => {
    // Define the URL and credentials (use environment variables in a real project!)
    const adminUrlPath = 'http://localhost:8080/admin_xxx'; // !! REPLACE THIS WITH YOUR UNIQUE ADMIN PATH !!
    const username = 'admin@admin.com'; // !! REPLACE THIS WITH YOUR EMAIL !!
    const password = 'admin123'; // !! REPLACE THIS WITH YOUR PASSWORD !!

    beforeEach(() => {
        //cy.viewport(1920, 1080);
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
        cy.screenshot("install-01_start_install_ipg_checkout_dashboard", SCREENSHOT_OPTIONS);
        cy.get('#subtab-AdminParentModulesSf').should('be.visible').click();
        cy.wait(1000);
        cy.screenshot("install-02_opened_modules", SCREENSHOT_OPTIONS);
        cy.get('#subtab-AdminModulesSf').should('be.visible').click();
        cy.screenshot("install-03_module_manager", SCREENSHOT_OPTIONS);
        cy.get('#page-header-desc-configuration-add_module').should('be.visible').click();
        cy.wait(1000);
        cy.screenshot("install-04_upload_a_module", SCREENSHOT_OPTIONS);
        cy.get('#importDropzone').should('be.visible').selectFile(__dirname + '/../../../ipgcheckout.zip', { action: 'drag-drop' });
        cy.wait(1000);
        cy.screenshot("install-05_installing_ipg_checkout", SCREENSHOT_OPTIONS);
        cy.wait(9000);
        cy.screenshot("install-06_installing_ipg_checkout_completed", SCREENSHOT_OPTIONS);
        cy.get('#importDropzone > div.module-import-success > a').should('be.visible').click();
        cy.wait(1000);
        cy.screenshot("install-07_configure_ipg_checkout", SCREENSHOT_OPTIONS);
    });

    it('Uninstall IPG Checkout', () => {
        cy.screenshot("uninstall-01_start_uninstall_ipg_checkout_dashboard", SCREENSHOT_OPTIONS);
        cy.get('#subtab-AdminParentModulesSf').should('be.visible').click();
        cy.wait(1000);
        cy.screenshot("uninstall-02_opened_modules", SCREENSHOT_OPTIONS);
        cy.get('#subtab-AdminModulesSf').should('be.visible').click();
        cy.screenshot("uninstall-03_module_manager", SCREENSHOT_OPTIONS);
        cy.get('[data-tech-name="ipgcheckout"] .btn-group.module-actions button.dropdown-toggle-split').click();
        cy.wait(1000);
        cy.screenshot("uninstall-03_configuration_drop_down", SCREENSHOT_OPTIONS);
        cy.get('[data-confirm_modal="module-modal-confirm-ipgcheckout-uninstall"]').click();
        cy.wait(1000);
        cy.screenshot("uninstall-04_open_uninstall_ipg_checkout", SCREENSHOT_OPTIONS);
        cy.get('#force_deletion[data-tech-name="ipgcheckout"]').click();
        cy.wait(1000);
        cy.screenshot("uninstall-05_check_delete_all_data", SCREENSHOT_OPTIONS);
        cy.get('#module-modal-confirm-ipgcheckout-uninstall > div > div > div.modal-footer > a').click();
        cy.wait(10000);
        cy.screenshot("uninstall-06_ipg_checkout_uninstalled", SCREENSHOT_OPTIONS);
    })
});