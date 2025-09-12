jQuery(document).ready(function ($) {

    // Add Gear Row
    $(".add-row").on("click", function () {
        let index = $("#extra-gear-table > tbody > tr.gear-row").length; // main tbody only

        // Build renting options HTML
        let rentingHtml = "";
        if (srsExtraGear.rentingOptions && srsExtraGear.rentingOptions.length > 0) {
            rentingHtml = `<select class="form-select" name="srs_extra_gear[extra_gear][${index}][renting_options][]" multiple>`;
            srsExtraGear.rentingOptions.forEach(function (opt) {
                rentingHtml += `<option value="${opt}">${opt}</option>`;
            });
            rentingHtml += `</select>`;
        } else {
            rentingHtml = `<div class="alert alert-warning p-2">${srsExtraGear.noOptionsMsg}</div>`;
        }

        // Build type options HTML
        let typeHtml = "";
        if (srsExtraGear.type_options && srsExtraGear.type_options.length > 0) {
            typeHtml = `<select class="form-select" name="srs_extra_gear[extra_gear][${index}][type_options][]" multiple>`;
            srsExtraGear.type_options.forEach(function (type) {
                typeHtml += `<option value="${type}">${type}</option>`;
            });
            typeHtml += `</select>`;
        }

        let row = `
        <tr class="gear-row" data-gear-index="${index}">
            <td>
                <input type="text" class="form-control" name="srs_extra_gear[extra_gear][${index}][name]">
                <input type="hidden" name="srs_extra_gear[extra_gear][${index}][product_id]" value="0">
            </td>
            <td>
                <div class="form-check form-switch mb-2">
                    <input class="form-check-input rental-toggle" type="checkbox" role="switch"
                           id="eg-rental-toggle-${index}" name="srs_extra_gear[extra_gear][${index}][is_rental]" value="1" checked>
                    <label class="form-check-label" for="eg-rental-toggle-${index}">Rental (multi-day pricing)</label>
                </div>

                <div class="base-price-wrapper mb-2" style="display:none;">
                    <label class="form-label mb-1">Base Price</label>
                    <input type="number" step="0.01" class="form-control base-price-input"
                           name="srs_extra_gear[extra_gear][${index}][base_price]">
                </div>

                <table class="table table-sm table-bordered day-prices-table mb-2">
                    <thead>
                        <tr>
                            <th>Day</th>
                            <th>Price</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
                <button type="button" class="btn btn-sm btn-primary add-day">Add Day</button>
            </td>
            <td>
                <input type="number" step="0.01" class="form-control extra-price-input" name="srs_extra_gear[extra_gear][${index}][prices][extra]">
            </td>
            <td>${rentingHtml}</td>
            <td>${typeHtml}</td>
            <td>
                <button type="button" class="btn btn-danger btn-sm remove-row">Remove</button>
            </td>
        </tr>`;
        $("#extra-gear-table > tbody").append(row);
        $("#extra-gear-table > tbody tr.gear-row:last .rental-toggle").trigger("change");
    });

    // Remove Gear Row
    $(document).on("click", ".remove-row", function () {
        $(this).closest("tr.gear-row").remove();
        reindexGears();
    });

    // Add Day Row
    $(document).on("click", ".add-day", function () {
        let gearRow = $(this).closest("tr.gear-row");
        let gearIndex = gearRow.data("gear-index");
        let tbody = gearRow.find("table.day-prices-table > tbody");
        let row = `
        <tr>
            <td><input type="number" min="1" class="form-control"
                       name="srs_extra_gear[extra_gear][${gearIndex}][prices][day][]"></td>
            <td><input type="number" step="0.01" class="form-control"
                       name="srs_extra_gear[extra_gear][${gearIndex}][prices][value][]"></td>
            <td><button type="button" class="btn btn-danger btn-sm remove-day">×</button></td>
        </tr>`;
        tbody.append(row);
    });

    // Remove Day Row
    $(document).on("click", ".remove-day", function () {
        $(this).closest("tr").remove();
    });

    // Rental toggle behavior
    $(document).on("change", ".rental-toggle", function () {
        const gearRow = $(this).closest("tr.gear-row");
        const isChecked = $(this).is(":checked");
        const dayTable = gearRow.find("table.day-prices-table");
        const addDayBtn = gearRow.find(".add-day");
        const basePriceWrap = gearRow.find(".base-price-wrapper");
        const extraPriceInput = gearRow.find(".extra-price-input");

        if (isChecked) {
            dayTable.show();
            addDayBtn.show();
            basePriceWrap.hide();
            extraPriceInput.prop("disabled", false);
        } else {
            dayTable.hide();
            addDayBtn.hide();
            basePriceWrap.show();
            extraPriceInput.prop("disabled", true);
        }
    });

    // Initialize toggle state on page load for existing rows
    $("#extra-gear-table > tbody > tr.gear-row").each(function () {
        const toggle = $(this).find(".rental-toggle");
        if (toggle.length) toggle.trigger("change");
    });

    // Reindex gears after removal to keep names consistent
    function reindexGears() {
        $("#extra-gear-table > tbody > tr.gear-row").each(function (i) {
            $(this).attr("data-gear-index", i);

            // Update inputs and selects inside this gear row
            $(this).find("input, select").each(function () {
                let name = $(this).attr("name");
                if (name) {
                    name = name.replace(/srs_extra_gear\[extra_gear]\[\d+]/, `srs_extra_gear[extra_gear][${i}]`);
                    $(this).attr("name", name);
                }
            });
        });
    }
});
