describe('PrestaShop 9.0.3 Full Checkout Flow', () => {
    const baseUrl = '/'; // Replace with your URL
    
    it('should add item, create shipment data, and pay via Bank Wire', () => {
        // 1. Add Product to Cart
        cy.visit(baseUrl);
        cy.get('.product-miniature').first().find('a').first().click();
        cy.get('.add-to-cart').click();
        cy.get('.cart-content-btn .btn-primary').contains('Proceed to checkout').click();
        cy.get('.cart-detailed-actions a.btn.btn-primary').first().contains('Proceed to checkout').click();

        // 2. Personal Information (Guest Registration)
        cy.get('input[name="firstname"]').type('John');
        cy.get('input[name="lastname"]').type('Doe');
        cy.get('input[name="email"]').first().type(`test-${Date.now()}@example.com`);
        cy.get('input[name="psgdpr"]').check(); // Accept privacy policy if enabled
        cy.get('input[name="customer_privacy"]').check(); // Accept privacy policy if enabled
        cy.get('button[data-link-action="register-new-customer"]').click();
        
        // 3. Address Creation (Shipment Data)
        cy.get('input[name="address1"]').type('123 Cypress Lane');
        cy.get('input[name="city"]').type('Miami');
        cy.get('input[name="postcode"]').type('33101');
        cy.get('select[name="id_state"]').select('10'); // Select Florida or valid state ID
        cy.get('select[name="id_country"]').select('United States');
        cy.get('button[name="confirm-addresses"]').click();

        // 4. Shipping Method
        // Standard PrestaShop usually defaults to the first available carrier
        cy.get('button[name="confirmDeliveryOption"]').click();

        // 5. Payment Method (Bank Wire / ps_wirepayment)
        // Select the radio button for Bank Wire (typically #payment-option-2)
        cy.get('#payment-option-2').check();

        // Accept Terms of Service
        cy.get('#conditions_to_approve\\[terms-and-conditions\\]').check();

        // 6. Place Order
        cy.get('#payment-confirmation button').click();

        // 7. Verify Order Confirmation
        cy.get('.h1.card-title').should('contain', 'Your order is confirmed');
    });
});