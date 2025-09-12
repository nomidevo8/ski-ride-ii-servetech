jQuery(document).ready(function ($) {

    // Add Boot Row
    $(".add-row").on("click", function () {
        let index = $("#boots-table > tbody > tr.boot-row").length;

        let rentingHtml = "";
        if (srsBoots && srsBoots.rentingOptions && srsBoots.rentingOptions.length > 0) {
            rentingHtml = `<select class="form-select" name="srs_boots[boots][${index}][renting_options][]" multiple>`;
            srsBoots.rentingOptions.forEach(function (opt) {
                rentingHtml += `<option value="${opt}">${opt}</option>`;
            });
            rentingHtml += `</select>`;
        } else {
            rentingHtml = `<div class="alert alert-warning p-2">${srsBoots.noOptionsMsg}</div>`;
        }

        
        // Type Options (Child / Adult)
        let typeHtml = `<select class="form-select" name="srs_boots[boots][${index}][type_options][]" multiple>`;
        if (srsBoots && srsBoots.typeOptions && srsBoots.typeOptions.length) {
            srsBoots.typeOptions.forEach(function (opt) {
                typeHtml += `<option value="${opt}">${opt}</option>`;
            });
        } else {
            typeHtml += `<option value="Child">Child</option><option value="Adult">Adult</option>`;
        }
        typeHtml += `</select>`;

        let row = `
        <tr class="boot-row" data-boot-index="${index}">
            <td>
                <input type="text" class="form-control" name="srs_boots[boots][${index}][name]">
                <input type="hidden" name="srs_boots[boots][${index}][product_id]" value="0">
            </td>

            <td>
                <div class="form-check form-switch mb-2">
                    <input class="form-check-input rental-toggle" type="checkbox" role="switch"
                           id="rental-toggle-${index}" name="srs_boots[boots][${index}][is_rental]" value="1" checked>
                    <label class="form-check-label" for="rental-toggle-${index}">Rental (multi-day pricing)</label>
                </div>

                <div class="base-price-wrapper mb-2" style="display:none;">
                    <label class="form-label mb-1">Base Price</label>
                    <input type="number" step="0.01" class="form-control base-price-input"
                           name="srs_boots[boots][${index}][base_price]">
                </div>

                <table class="table table-sm table-bordered day-prices-table mb-2" style="">
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
                <input type="number" step="0.01" class="form-control extra-price-input" name="srs_boots[boots][${index}][prices][extra]">
            </td>
            <td>
                ${rentingHtml}
            </td>

            <td>
                ${typeHtml}
            </td>

            <td>
                <button type="button" class="btn btn-danger btn-sm remove-row">Remove</button>
            </td>
        </tr>`;

        $("#boots-table > tbody").append(row);
        // ensure initial toggle behavior applied
        $("#boots-table > tbody tr.boot-row:last .rental-toggle").trigger("change");
    });

    // Remove Boot Row
    $(document).on("click", ".remove-row", function () {
        $(this).closest("tr.boot-row").remove();
        reindexBoots();
    });

    // Add Day Row
    $(document).on("click", ".add-day", function () {
        let bootRow = $(this).closest("tr.boot-row");
        let index = bootRow.data("boot-index");
        let tbody = bootRow.find("table.day-prices-table > tbody");
        let row = `
        <tr>
            <td><input type="number" min="1" class="form-control"
                       name="srs_boots[boots][${index}][prices][day][]"></td>
            <td><input type="number" step="0.01" class="form-control"
                       name="srs_boots[boots][${index}][prices][value][]"></td>
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
        const bootRow = $(this).closest("tr.boot-row");
        const isChecked = $(this).is(":checked");
        const dayTable = bootRow.find("table.day-prices-table");
        const addDayBtn = bootRow.find(".add-day");
        const basePriceWrap = bootRow.find(".base-price-wrapper");
        const extraPriceInput = bootRow.find(".extra-price-input");

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
    $("#boots-table > tbody > tr.boot-row").each(function () {
        const toggle = $(this).find(".rental-toggle");
        if (toggle.length) {
            toggle.trigger("change");
        }
    });

    // Reindex boot rows after removal
    function reindexBoots() {
        $("#boots-table > tbody > tr.boot-row").each(function (i) {
            $(this).attr("data-boot-index", i);

            $(this).find("input, select, textarea").each(function () {
                let name = $(this).attr("name");
                if (name) {
                    name = name.replace(/srs_boots\[boots]\[\d+]/, `srs_boots[boots][${i}]`);
                    $(this).attr("name", name);
                }
            });
        });
    }

});
