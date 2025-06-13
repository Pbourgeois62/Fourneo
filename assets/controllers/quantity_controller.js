// assets/controllers/quantity_controller.js
import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['quantityInput'];

    connect() {
        if (!this.hasQuantityInputTarget) {
            console.error('Quantity input target not found for quantity_controller.');
        }
    }

    /**
     * @param {Event} event L'événement du clic sur le bouton.
     */
    increment(event) {
        if (!this.hasQuantityInputTarget) {
            return; 
        }

        const valueToAdd = parseInt(event.currentTarget.dataset.value, 10);
        
        let currentQuantity = parseInt(this.quantityInputTarget.value, 10);

        if (isNaN(currentQuantity)) {
            currentQuantity = 0;
        }

        const newQuantity = currentQuantity + valueToAdd;
        this.quantityInputTarget.value = newQuantity;
        this.quantityInputTarget.dispatchEvent(new Event('change', { bubbles: true }));
    }
}