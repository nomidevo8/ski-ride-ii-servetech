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
                <input type="number" step="0.01" class="form-control" name="srs_extra_gear[extra_gear][${index}][prices][extra]">
            </td>
            <td>${rentingHtml}</td>
            <td>${typeHtml}</td>
            <td>
                <button type="button" class="btn btn-danger btn-sm remove-row">Remove</button>
            </td>
        </tr>`;
        $("#extra-gear-table > tbody").append(row);
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
