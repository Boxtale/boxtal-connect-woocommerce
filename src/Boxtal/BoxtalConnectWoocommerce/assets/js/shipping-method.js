(function () {
    var Components = {};

    Components.ratesTable = {
        trigger: '#bw-rates-table',

        init: function() {
            const triggerEl = document.querySelector(this.trigger);
            const self = this;

            if (null !== triggerEl) {
				self.on("body", "click", ".bw-add-rate-line", function(e) {
                	e.preventDefault();
					const httpRequest = new XMLHttpRequest();
					httpRequest.onreadystatechange = function(data) {
						if (httpRequest.readyState === 4) {
							if (httpRequest.status === 200) {
								const data = typeof httpRequest.response === 'object' && httpRequest.response !== null ? httpRequest.response.data : JSON.parse(httpRequest.response).data;
								const tableBody = self.getTableBody();
								tableBody.insertAdjacentHTML('beforeend', data);
								self.initSelect('#bw-rates-table tbody tr:last-child .bw-tail-select');
							} else {
								console.log("Error: " + httpRequest.status);
							}
						}
					};
					httpRequest.open("POST", ajaxurl);
					httpRequest.setRequestHeader(
						"Content-Type",
						"application/x-www-form-urlencoded"
					);
					httpRequest.responseType = "json";
					let params = "action=bw_add_rate_line&security=" + encodeURIComponent(bwShippingMethodAjaxNonce);
					const tableRows = self.getTableRows();
					if (null !== tableRows) {
						const lastLine = tableRows[tableRows.length - 1];
						const values = [];
						self.parseNodeChildrenSpecificTypeAction(lastLine, ['SELECT', 'INPUT'], function(item) {
							if ('SELECT' === item.tagName) {
								const name = item.getAttribute('name');
								const options = Array.from(item.selectedOptions);
								if (options.length > 0) {
									options.map(option => {
										values.push(name + "=" + encodeURIComponent(option.value));
									});
								} else {
									values.push(name + "=");
								}
							} else {
								values.push(item.getAttribute('name') + "=" + encodeURIComponent(item.value));
							}
						});
						params += '&' + values.join('&');
					}
					httpRequest.send(params);
                });

				self.on("body", "click", ".bw-remove-rate-line", function(e) {
					e.preventDefault();
					const selectedLine = document.querySelector('.pricing-item.last_selected');
					if (null !== selectedLine);
					selectedLine.remove();
				});

				self.initSelect(".bw-tail-select");

				self.submitForm = self.submitForm.bind(self);

				document.getElementById("mainform").addEventListener("submit", self.submitForm);
            }
        },

		submitForm: function(e) {
        	const self = this;
			e.preventDefault();
			const pricingItems = [];
			const pricingRows = document.querySelectorAll('.pricing-item');
			if (pricingRows.length > 0) {
				for (let i = 0; i < pricingRows.length; i++) {
					const element = pricingRows[i];
					const pricingItem = {
						'price-from': element.querySelector("[name='pricing-items["+i+"][\"price-from\"]']").value,
						'price-to': element.querySelector("[name='pricing-items["+i+"][\"price-to\"]']").value,
						'weight-from': element.querySelector("[name='pricing-items["+i+"][\"weight-from\"]']").value,
						'weight-to': element.querySelector("[name='pricing-items["+i+"][\"weight-to\"]']").value,
						'shipping-class': Array.apply(null, element.querySelector("[name='pricing-items["+i+"][\"shipping-class\"][]']").options).filter(option => option.selected).map(option => option.value),
						'parcel-point-network': Array.apply(null, element.querySelector("[name='pricing-items["+i+"][\"parcel-point-network\"][]']").options).filter(option => option.selected).map(option => option.value),
						'pricing': element.querySelector("[name='pricing-items["+i+"][\"pricing\"]']").value,
						'flat-rate': element.querySelector("[name='pricing-items["+i+"][\"flat-rate\"]']").value
					};
					pricingItems.push(pricingItem);
				}
			}

			// disable inputs to prevent them from being posted in PHP
			const inputs = document.querySelectorAll('.pricing-item input, .pricing-item select');
			if (inputs.length > 0) {
				for (let i = 0; i < inputs.length; i++) {
					const input = inputs[i];
					input.disabled = true;
				}
			}

			const el = document.createElement("input");
			el.setAttribute("type", "hidden");
			el.setAttribute("name", "pricing-items");
			el.value = JSON.stringify(pricingItems);

			const saveInput = document.createElement("input");
			saveInput.setAttribute("type", "hidden");
			saveInput.setAttribute("name", "save");
			saveInput.value = document.querySelector('button[type="submit"]').value;

			document.querySelector(self.trigger).appendChild(el);
			document.querySelector(self.trigger).appendChild(saveInput); // necessary for WC submission to work.

			document.getElementById("mainform").removeEventListener("submit", self.submitForm);
			document.getElementById("mainform").submit();

			return true;
		},

		initSelect: function(selector) {
        	const self = this;

			const selects = tail.select(selector, {
				locale: bwLocale,
				multiShowCount: false,
				multiSelectAll: true,
				cbComplete: function(container) {
					const text = self.getPlaceholderText(this);
					const label = container.querySelector('.label-inner');
					if (null !== label) {
						label.innerHTML = text;
					}
				}
			});

			for (let i = 0; i < selects.length; i++) {
				const select = selects[i];
				select.on("close", function() {
					self.refreshSelectText(this);
				});

				if (select.e.name.indexOf('["pricing"]') > -1) {
					select.on("change", function(item, state) {
						const regex = /pricing-items\[(\d+)\]\["pricing"\]/g;
						const matches = regex.exec(select.e.name);
						if (null !== matches) {
							const index = matches[1];
							const rateInput = document.getElementById('flat-rate-' + index);
							if (null !== rateInput) {
								if ("rate" === item.key) {
									rateInput.removeAttribute('disabled');
									if ("" === rateInput.value) {
										rateInput.value = 0;
									}
								} else {
									rateInput.setAttribute('disabled', 'disabled');
								}
							}
						}
					});
				}
			}
		},

		getPlaceholderText: function(select) {
			if (select.options.length > 0) {

				let placeholderArray = [];
				for (let j = 0; j < select.options.length; j++) {
					const option = select.options[j];
					if (true === option.selected) {
						placeholderArray.push(option.value);
					}
				}
				if (placeholderArray.length > 0) {
					if (placeholderArray.length === select.options.length) {
						return tail.select.strings[bwLocale].all;
					} else {
						return placeholderArray.join(', ');
					}
				} else {
					return tail.select.strings[bwLocale].none;
				}
			}
			return tail.select.strings[bwLocale].emptySearch;
		},

		refreshSelectText: function(select) {
        	const text = this.getPlaceholderText(select);
			select.config('placeholder', text);
		},

		getTableBody: function() {
			const table = document.querySelector(this.trigger);
			const tableBody = table.querySelector('tbody');
			if (null === tableBody) {
				return null;
			}
			return tableBody;
		},

		getTableRows: function() {
			const tableBody = this.getTableBody();
			if (null === tableBody) {
				return null;
			}
			if (0 === tableBody.childElementCount) {
				return null;
			}
			return tableBody.children;
		},

		parseNodeChildrenSpecificTypeAction: function(element, types, action) {
			if (element.hasChildNodes()) {
				const children = element.childNodes;
				for (let i = 0; i < children.length; i++) {
					const child = children[i];
					if (-1 !== types.indexOf(child.tagName)) {
						action(child);
					}
					this.parseNodeChildrenSpecificTypeAction(child, types, action);
				}
			}
			return element;
		},

        on: function(elSelector, eventName, selector, fn) {
            const element = document.querySelector(elSelector);

            element.addEventListener(eventName, function(event) {
                const possibleTargets = element.querySelectorAll(selector);
                const target = event.target;

                for (let i = 0, l = possibleTargets.length; i < l; i++) {
                    let el = target;
                    const p = possibleTargets[i];

                    while(el && el !== element) {
                        if (el === p) {
                            return fn.call(p, event);
                        }

                        el = el.parentNode;
                    }
                }
            });
        }
    };

    document.addEventListener(
        "DOMContentLoaded", function() {
            Components.ratesTable.init();
        }
    );

})();
