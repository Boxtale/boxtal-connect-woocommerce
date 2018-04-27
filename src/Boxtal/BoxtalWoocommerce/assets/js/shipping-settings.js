document.addEventListener(
    "DOMContentLoaded", function() {
        var bwMapDisplay = document.querySelector(".bw-map-display-dropdown");
        var bwParcelPointOperators = document.querySelector(".bw-parcel-point-operators-dropdown");
        if (bwMapDisplay) {
            var initialValue = bwMapDisplay.value || bwMapDisplay.options[bwMapDisplay.selectedIndex].value;
            bwShowHideRelaySetting(bwParcelPointOperators, initialValue);

            bwMapDisplay.onchange = function() {
                var elem = (typeof this.selectedIndex === "undefined" ? window.event.srcElement : this);
                var value = elem.value || elem.options[elem.selectedIndex].value;
                bwShowHideRelaySetting(bwParcelPointOperators, value);
            }
        }
    }
);

function bwShowHideRelaySetting(bwParcelPointOperators, bwMapDisplayValue) {
    if (bwParcelPointOperators) {
        if (bwMapDisplayValue === "1") {
            bwParcelPointOperators.closest('tr').style.display = "table-row";
        } else {
            bwParcelPointOperators.closest('tr').style.display = "none";
        }
    }
}