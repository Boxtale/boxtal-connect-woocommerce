(function () {
    var Components = {};

    Components.ratesTable = {
        trigger: 'select[name="BW_ORDER_DELIVERED"]',

        init: function() {
            const triggerEl = document.querySelector(this.trigger);
            const self = this;

            if (null !== triggerEl) {
				self.initSelect('select');
            }
        },

		initSelect: function(selector) {
        	tail.select(selector, {
				locale: bwLocale
			});
		},
    };

    document.addEventListener(
        "DOMContentLoaded", function() {
            Components.ratesTable.init();
        }
    );

})();
