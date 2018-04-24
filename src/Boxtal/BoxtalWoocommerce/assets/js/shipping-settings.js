document.addEventListener(
    "DOMContentLoaded", function() {
        var bwCategoryTag = document.querySelectorAll(".bw-tag-category-dropdown");
        var relayElement = document.querySelectorAll(".bw-tag-relay-operators-dropdown");
        if (bwCategoryTag.length > 0) {
            var initialValue = bwCategoryTag[0].value || bwCategoryTag[0].options[bwCategoryTag[0].selectedIndex].value;
            bwShowHideRelaySetting(relayElement, initialValue);

            bwCategoryTag[0].onchange = function() {
                var elem = (typeof this.selectedIndex === "undefined" ? window.event.srcElement : this);
                var value = elem.value || elem.options[elem.selectedIndex].value;
                bwShowHideRelaySetting(relayElement, value);
            }
        }
    }
);

function bwShowHideRelaySetting(relayElement, categoryTagValue) {
    if (relayElement.length > 0) {
        if (categoryTagValue === "relay") {
            relayElement[0].closest('tr').style.display = "table-row";
        } else {
            relayElement[0].closest('tr').style.display = "none";
        }
    }
}